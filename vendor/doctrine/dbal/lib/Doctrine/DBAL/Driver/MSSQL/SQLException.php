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

class SQLException extends \Doctrine\DBAL\DBALException
{
    /**
     * Helper method to turn sql server errors into exception.
     *
     * @return SQLException
     */
    static public function fromSqlErrors()
    {
        //se pueden setear niveles de errores y mensajes usando mssql_min_error_severity y mssql_min_message_severity
        $error = mssql_get_last_message();
        $error .='_WIL';
//        $message = "";
//        foreach ($errors as $error) {
//            $message .= "SQLSTATE [".$error['SQLSTATE'].", ".$error['code']."]: ". $error['message']."\n";
//        }
//        if ( ! $message) {
//            $message = "SQL Server error occured but no error message was retrieved from driver.";
//        }
//
//        return new self(rtrim($message));
        return new self(rtrim($error));
    }
}

