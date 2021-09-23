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

/**
 * SQL Server implementation for the Connection interface.
 * MSSQL For Linux FreeTDS
 * @since 2.3
 * Based on SQLSrv by Benjamin Eberlei <kontakt@beberlei.de>
 * @author Oslovski Alejandro <aoslovski@ms.gba.gov.ar>
 * @date 2014
 */
class SQLConnection implements \Doctrine\DBAL\Driver\Connection
{
    /**
     * @var resource
     */
    protected $conn;
    
    /**
     * @var resource
     */
    protected $sth;

    /**
     * @var LastInsertId
     */
    public $lastInsertId;
    
    /**
     * @var varchar
     */
    protected $trans;

    public function __construct($serverName, $connectionOptions)
    {
        $this->conn = mssql_connect($serverName, $connectionOptions['UID'], $connectionOptions['PWD']);
        if ( ! $this->conn) {
            throw SQLException::fromSqlErrors();
        }
        $this->lastInsertId = new LastInsertId();
    }

    /**
     * {@inheritDoc}
     */
    public function prepare($sql)
    {
        return new SQLStatement($this->conn, $sql, $this->lastInsertId, null, $this->trans);
    }
    
    /**
     * {@inheritDoc}
     */
    public function query()
    {
		$args = func_get_args();
        $sql = $args[0];
        $stmt = $this->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    /**
     * {@inheritDoc}
     * @license New BSD, code from Zend Framework
     */
    public function quote($value, $type=\PDO::PARAM_STR)
    {
        if (is_int($value)) {
            return $value;
        } else if (is_float($value)) {
            return sprintf('%F', $value);
        }

        return "'" . str_replace("'", "''", $value) . "'";
    }

    /**
     * {@inheritDoc}
     */
    public function exec($statement)
    {
        $stmt = $this->prepare($statement);
        $stmt->execute();
        return $stmt->rowCount();
    }

    /**
     * {@inheritDoc}
     */
    public function lastInsertId($name = null)
    {
        if ($name !== null) {
            $sql = "SELECT IDENT_CURRENT(".$this->quote($name).") AS LastInsertId";
            $stmt = $this->prepare($sql);
            $stmt->execute();

            return $stmt->fetchColumn();
        }

        return $this->lastInsertId->getId();
    }

    /**
     * {@inheritDoc}
     */
    public function beginTransaction()
    {
          //$this->trans = 'BEGIN TRAN ';
          $sql = "BEGIN TRAN";
          $stmt = $this->prepare($sql);
          $stmt->execute();
//        if ( ! sqlsrv_begin_transaction($this->conn)) {
//            throw SQLException::fromSqlErrors();
//        }
    }

    /**
     * {@inheritDoc}
     */
    public function commit()
    {
//        if ( ! sqlsrv_commit($this->conn)) {
//            throw SQLException::fromSqlErrors();
//        }
        $sql = "COMMIT";
        $stmt = $this->prepare($sql);
        $stmt->execute();
    }

    /**
     * {@inheritDoc}
     */
    public function rollBack()
    {
//        if ( ! sqlsrv_rollback($this->conn)) {
//            throw SQLException::fromSqlErrors();
//        }
        $sql = "ROLLBACK";
        $stmt = $this->prepare($sql);
        $stmt->execute();
    }

    /**
     * {@inheritDoc}
     */
    public function errorCode()
    {
        //aparentemente mssql no posee funcion para retornar codigos de error.
        throw new SQLException("Error Codes are not supported!");
//        $errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);
//        if ($errors) {
//            return $errors[0]['code'];
//        }
//        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function errorInfo()
    {
        return mssql_get_last_message();
    }
}

