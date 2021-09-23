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

namespace Doctrine\DBAL\Driver\ODBCSQL;

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
class ODBCStatement implements IteratorAggregate, Statement
{
    protected static $_paramTypeMap = array(
        PDO::PARAM_STR => 's',
        PDO::PARAM_BOOL => 'i',
        PDO::PARAM_NULL => 's',
        PDO::PARAM_INT => 'i',
        PDO::PARAM_LOB => 's' // TODO Support LOB bigger then max package size.
    );

    protected $_conn;
    protected $_stmt;

    /**
     * @var null|false|array
     */
    protected $_columnNames;

    /**
     * @var null|array
     */
    protected $_rowBindedValues;

    /**
     * @var array
     */
    protected $_bindedValues;

    /**
     * Contains ref values for bindValue()
     *
     * @var array
     */
    protected $_values = array();

    protected $_defaultFetchStyle = PDO::FETCH_BOTH;

    //   -------------------------   \\
    /**
     * fix to make sure all rows are returned when more than one row is being fetched
     * @var array
     */
    protected $_fetchResult;

    /**
     * fix to make sure i can count rows etc.
     * @var Resource
     */
    protected $queryResource;

    public function __construct($conn, $prepareString)
    {
        $this->_conn = $conn;
        $this->_stmt =  $prepareString ;

        $stringArr = str_split ($prepareString);
        $stringSize = count($stringArr);
    }

    /**
     * {@inheritdoc}
     */
    public function bindParam($column, &$variable, $type = null, $length = null)
    {
        throw new \NotImplementedException('bindParam not yet implemented');
    }

    /**
     * {@inheritdoc}
     */
    public function bindValue($param, $value, $type = null)
    {
        $this->_values[$param] = $value;
        $this->_bindedValues[$param] =& $this->_values[$param];
        $this->_bindedValues[0][$param - 1] = 's';

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($params = null)
    {

        if (null !== $this->_bindedValues) {
            if (null !== $params) {
                if (!$this->_bindValues($params)) {
                    throw new \Exception($this->_stmt->error, $this->_stmt->errno);
                }
            } else {

                $stmt = $this->_stmt;

                $j = 0;
                for($i = 1; $i < count($this->_bindedValues); $i++) {
                    if (is_string($this->_bindedValues[$i])
                            || is_null($this->_bindedValues[$i])) {
                        $value = "'".str_replace("'", "''", $this->_bindedValues[$i])."'";
                        $stmt = $this->str_replace_occurance('?', $value, $stmt, (0 + $j));

                        $amount = substr_count($value, '?');
                        $j += $amount;
                    } else {
                        $value = (string)$this->_bindedValues[$i];
                        $stmt = $this->str_replace_occurance('?', $value, $stmt, (0 + $j));
                    }
                }
                $this->_stmt = $stmt;
            }
        }
        //die(var_dump($this->_stmt));
        $pos = strpos($this->_stmt,'ALTER SESSION');
        if ($pos !== false) {
            $this->_stmt = '';
        }
        //die(var_dump($this->_stmt));
        $queryResult = odbc_exec($this->_conn,$this->_stmt);
            
        
        
        
        if (! $queryResult) {
            throw new \Exception('Executing the mssql statement went wrong');
        } else {
            $this->queryResource = $queryResult;
            
            
            return true;
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

    /**
     * Bind a array of values to bound parameters
     *
     * @param array $values
     * @return boolean
     */
    private function _bindValues($values)
    {
        foreach ($values as $key => $value) {
            $this->_bindedValues[$key] = $value;
        }

        return true;
    }

    /**
     * @return null|false|array
     */
    private function _fetch()
    {
        $ret = odbc_exec($this->_conn,$this->_stmt);

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch($fetchStyle = null)
    {
        
        
        if ($this->_fetchResult == null) {
            //$query = $this->_fetch();
            //$this->_fetchResult = $query;
            $res = array();
            //die(var_dump($this->_stmt));
            //$datos = odbc_exec($this->_conn,$this->_stmt);
            $datos = odbc_exec($this->_conn,$this->_stmt);
            while ($row = odbc_fetch_array( $datos )) {
                $res[] = $row;
            }
            odbc_close($this->_conn);
            //die(var_dump($res));
            return $res;
            //die(var_dump(mssql_fetch_array(mssql_query($this->_stmt)) ));
        } else {
            
            odbc_next_result ($this->_fetchResult);
            $query = $this->_fetchResult;
        }

        if (null === $query) {
            return null;
        }

        if (false === $query) {
            throw new \Exception("query == false, fetch failed");
        }

        $fetchStyle = $fetchStyle ?: $this->_defaultFetchStyle;

        switch ($fetchStyle) {
            case PDO::FETCH_NUM:
                $ret = odbc_fetch_array($query, odbc_num_rows($this->_fetchResult));
                
                return $ret;

            case PDO::FETCH_ASSOC:
                $ret = odbc_fetch_array($query);
                
                return $ret;

            case PDO::FETCH_BOTH:
                $ret = odbc_fetch_array($query);

                return $ret;

            default:
                throw new \Exception("Unknown fetch type '{$fetchStyle}'");
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fetchAll($fetchStyle = null)
    {
        $fetchStyle = $fetchStyle ?: $this->_defaultFetchStyle;

        $a = array();
        $rows = $this->fetch($fetchStyle);

        foreach ($rows as $row) {
            $a[] = $row;
        }
        return $a;
    }

    /**
     * {@inheritdoc}
     */
    public function fetchColumn($columnIndex = 0)
    {
        $row = $this->fetch(PDO::FETCH_NUM);
        if (null === $row) {
            return null;
        }
        return $row[$columnIndex];
    }

    /**
     * {@inheritdoc}
     */
    public function errorCode()
    {
        throw new \Exception('Not implemented exception');
    }

    /**
     * {@inheritdoc}
     */
    public function errorInfo()
    {
        throw new \Exception('Not implemented exception');
    }

    /**
     * {@inheritdoc}
     */
    public function closeCursor()
    {
        unset($this->_stmt);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function rowCount()
    {
        if ($this->queryResource == true) {
            return 0;
        }
        return odbc_num_rows($this->queryResource);
    }
    
        /**
     * nextRowset
     * Advances to the next rowset in a multi-rowset statement handle
     *
     * Some database servers support stored procedures that return more than one rowset
     * (also known as a result set). The nextRowset() method enables you to access the second
     * and subsequent rowsets associated with a PDOStatement object. Each rowset can have a
     * different set of columns from the preceding rowset.
     *
     * @return boolean                      Returns TRUE on success or FALSE on failure.
     */
       public function nextRowset()
       {
           return $this->_stmt->nextRowset();
       }

    /**
     * {@inheritdoc}
     */
    public function columnCount()
    {
        return odbc_num_rows($this->queryResource);
    }

    /**
     * {@inheritdoc}
     */
    public function setFetchMode($fetchMode = PDO::FETCH_BOTH, $arg2 = null, $arg3 = null)
    {
        $this->_defaultFetchStyle = $fetchMode;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        $data = $this->fetchAll($this->_defaultFetchStyle);
        return new \ArrayIterator($data);
    }
}

