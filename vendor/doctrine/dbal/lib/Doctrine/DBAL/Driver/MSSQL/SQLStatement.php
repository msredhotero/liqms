<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\DBAL\Driver\MSSQL;

use PDO;
use IteratorAggregate;
use Doctrine\DBAL\Driver\Statement;

/**
 * SQL Server Statement For libBd library
 * MSSQL For Linux FreeTDS
 * @since 2.3
 * Based on SQLSrv by Benjamin Eberlei <kontakt@beberlei.de>
 * @author Oslovski Alejandro <aoslovski@ms.gba.gov.ar>
 * @date 2014
 */
class SQLStatement implements IteratorAggregate, Statement
{
    protected static $_paramTypeMap = array(
        PDO::PARAM_STR => 's',
        PDO::PARAM_BOOL => 'i',
        PDO::PARAM_NULL => 's',
        PDO::PARAM_INT => 'i',
        PDO::PARAM_LOB => 's' // TODO Support LOB bigger then max package size.
    );
    
    /**
     * MSSQL Resource
     *
     * @var resource
     */
    private $conn;

    /**
     * SQL Statement to execute
     *
     * @var string
     */
    private $sql;

    /**
     * MSSQL Statement Resource
     *
     * @var resource
     */
    private $stmt;
    
     /**
      *  Defined Statement Resource
      *
      * @var resource for mssql_init
      */
    private $_sth;
    
    /**
     * Contains ref values for bindValue()
     *
     * @var array
     */
    protected $_values = array();
    
     /**
      * Used to mock binding in queries
      * 
      * @var array
      */
    protected $_bindedValues;

    /**
     * Parameters to bind
     *
     * @var array
     */
    private $params = array();

    /**
     * Translations
     *
     * @var array
     */
    private static $fetchMap = array(
        PDO::FETCH_ASSOC => MSSQL_ASSOC,
    );

    /**
     * Fetch Style
     *
     * @param int
     */
    private $defaultFetchMode = PDO::FETCH_BOTH;

    /**
     * @var int|null
     */
    public $lastInsertId;

    /**
     * Append to any INSERT query to retrieve the last insert id.
     *
     * @var string
     */
    const LAST_INSERT_ID_SQL = ';SELECT SCOPE_IDENTITY() AS LastInsertId;';

    
    /** Output variables */
    private $outVars = array();
    
    public function __construct($conn, $sql, $lastInsertId = null, $sth = null, $trans = null)
    {
        
        $this->conn = $conn;
        $this->sql = $sql;
        
        /*new block to initialize Stored Procedures */
        if (stripos($sql, 'SP INIT ') === 0) {
            $sp_name = str_replace('SP INIT ','', $sql);
            $sql = $sp_name;
            $this->_sth = mssql_init($sp_name, $this->conn);
        }
        
        $this->sql = $sql;
        
        if (stripos($sql, 'INSERT INTO ') === 0) {
            $this->sql .= self::LAST_INSERT_ID_SQL;
            $this->lastInsertId = $lastInsertId;
        }
    }
    
    /**
     * Replaces the string at $occurance instead of the first
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     * @param int $occurance
     * @return string
     */
    protected function str_replace_occurance($search, $replace, $subject, $occurance)
    {
        $pos = 0;
        for ($i = 0; $i <= $occurance; $i++) {
            $pos = strpos($subject, $search, $pos + strlen($search));
        }

        return substr_replace($subject, $replace, $pos, strlen($search));
    }
    

    public function bindValue($param, $value, $type = null, $is_output = FALSE, $allow_null = FALSE, $maxLenght = -1)
    {
        //return $this->bindParam($param, $value, $type,  $is_output, $allow_null, $maxLenght);
        if (null === $type) {
            $type = 's';
        } else {
            if (isset(self::$_paramTypeMap[$type])) {
                $type = self::$_paramTypeMap[$type];
            } else {
                throw new MysqliException("Unknown type: '{$type}'");
            }
        } 
        
        $this->_values[$param] = $value;
        $this->_bindedValues[$param] =& $this->_values[$param];
        $this->_bindedValues[0][$param - 1] = $type; //'s';
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function bindParam($column, &$variable, $type = null, $is_output = FALSE, $allow_null = FALSE, $maxLenght = -1)
    {           
        //if (is_numeric($column)) {
          //  throw new SQLException("mssql expects @paramName.");
        //}
        mssql_bind($this->_sth, $column,  $variable, $type, $is_output, $allow_null, $maxLenght);

//        if ($type === \PDO::PARAM_LOB) {
//            $this->params[$column-1] = array($variable, SQLSRV_PARAM_IN, SQLSRV_PHPTYPE_STREAM(SQLSRV_ENC_BINARY), SQLSRV_SQLTYPE_VARBINARY('max'));
//        } else {
//            //$this->params[$column-1] = $variable;
//            mssql_bind($this->_sth, $column,  $variable, $type, $is_output, $allow_null, $maxLenght);
//           //mssql_bind(stmt, paramIndex, value, type, output, allownull, maxLength);
//        }
       
    }

    public function closeCursor()
    {
        if ($this->_sth) {
            mssql_free_statement($this->_sth);
        }
    }

    public function columnCount()
    {
        return sqlsrv_num_fields($this->stmt);
    }

    /**
     * {@inheritDoc}
     */
    public function errorCode()
    {
        //aparentemente mssql no posee funcion para retornar codigos de error.
        throw new SQLException("Error Codes are not supported!");
//        $errors =  sqlsrv_errors(SQLSRV_ERR_ERRORS);
//        if ($errors) {
//            return $errors[0]['code'];
//        }
    }

    /**
     * {@inheritDoc}
     */
    public function errorInfo()
    {
        return mssql_get_last_message();
    }

    public function execute($params = null)
    {
// No usaremos parametros solo el bindParam or bindValue        
//        if ($params) {
//            $hasZeroIndex = array_key_exists(0, $params);
//            foreach ($params as $key => $val) {
//                $key = ($hasZeroIndex && is_numeric($key)) ? $key + 1 : $key;
//                $this->bindValue($key, $val);
//            }
//        }        

        
//        if($this->_sth){//calling SP execution
//            $this->stmt = mssql_execute($this->_sth);
//        }else{//execute simple query
//            $this->stmt = mssql_query($this->sql, $this->conn);
//        }
//        
//        if ( ! $this->_sth && ! $this->stmt ) {
//            throw SQLException::fromSqlErrors();
//        }
//        
//        if ($this->lastInsertId) {
//            mssql_next_result($this->stmt);
//            $this->lastInsertId->setId(mssql_result($this->stmt,0,0));
//            return $this->lastInsertId->getId();
//        }
        
        
        $aux = $this->sql;
        if (null !== $this->_bindedValues) {
            if (null !== $params) {
                if (!$this->_bindValues($params)) {
                    throw new \Exception($this->stmt->error, $this->stmt->errno);
                }
            } else {
                $stmt = $this->sql;
                $j = 0;
                for($i = 1; $i < count($this->_bindedValues); $i++) {
                    if (is_string($this->_bindedValues[$i])) {
                        $value = "'".str_replace("'", "''", $this->_bindedValues[$i])."'";
                        $stmt = $this->str_replace_occurance('?', $value, $stmt, (0 + $j));

                        $amount = substr_count($value, '?');
                        $j += $amount;
                    } elseif (is_null($this->_bindedValues[$i])) {
                        $stmt = $this->str_replace_occurance('?', 'null', $stmt, (0 + $j));
                    } else {
                        $value = (string)$this->_bindedValues[$i];
                        $stmt = $this->str_replace_occurance('?', $value, $stmt, (0 + $j));
                    }
                }
                $this->sql = $stmt;
            }
        }
        
        //$queryResult = mssql_query($this->stmt);
        $pos = strpos($this->sql,'ALTER SESSION');
        if ($pos !== false) {
            
            $this->sql = '';
        
        
        
        } else {
            $this->stmt = mssql_query($this->sql, $this->conn);
        
            $this->sql = $aux;
            if ($this->lastInsertId) {
                mssql_next_result($this->stmt);
                //$this->lastInsertId->setId(mssql_result($queryResult,0,0));
                $this->lastInsertId->setId(mssql_result($this->stmt,0,0));

                return $this->lastInsertId->getId();
            }


            if (! $this->stmt) {
                throw new \Exception('Executing the mssql statement went wrong');
            } else {
                $this->queryResource = $this->stmt;
                return true;
            }
        }
    }
    

    public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
    {
        $this->defaultFetchMode = $fetchMode;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $data = $this->fetchAll();
        return new \ArrayIterator($data);
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetchMode = null)
    {
        $fetchMode = $fetchMode ?: $this->defaultFetchMode;
        if (isset(self::$fetchMap[$fetchMode])) {
            return mssql_fetch_array($this->stmt, self::$fetchMap[$fetchMode]);
        } else if ($fetchMode == PDO::FETCH_OBJ || $fetchMode == PDO::FETCH_CLASS) {
//            $className = null;
//            $ctorArgs = null;
//            if (func_num_args() >= 2) {
//                $args = func_get_args();
//                $className = $args[1];
//                $ctorArgs = (isset($args[2])) ? $args[2] : array();
//            } No se usa ya que mssql no permite setear nombres de clases
            return mssql_fetch_object($this->stmt);
        }

        throw new SQLException("Fetch mode is not supported!");
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchMode = null)
    {
        $className = null;
        $ctorArgs = null;
        if (func_num_args() >= 2) {
            $args = func_get_args();
            $className = $args[1];
            $ctorArgs = (isset($args[2])) ? $args[2] : array();
        }

        $rows = array();
        while ($row = $this->fetch($fetchMode, $className, $ctorArgs)) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($columnIndex = 0)
    {
        $row = $this->fetch(PDO::FETCH_NUM);
        return $row[$columnIndex];
    }

    /**
     * {@inheritdoc}
     */
    public function rowCount()
    {
        return mssql_rows_affected($this->conn);
    }
}

