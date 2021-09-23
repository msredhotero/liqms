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

namespace Doctrine\DBAL\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Type that maps an SQL DATETIME/TIMESTAMP to a PHP DateTime object.
 *
 * @since 2.0
 */
class DateTimeType extends Type
{
    public function getName()
    {
        return Type::DATETIME;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getDateTimeTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
//        return ($value !== null)
//            ? $value->format($platform->getDateTimeFormatString()) : null;
        if ($value === null) {
            return null;
        }
        /*
        $value = $value->format($platform->getDateTimeFormatString());

        if( strlen($value) == 18 &&
              $platform->getDateTimeFormatString() == 'Y-m-d H:i:s' &&
        */
        //die(var_dump($platform->getDateTimeFormatString()));
        $value = $value->format($platform->getDateTimeFormatString());

        if( strlen($value) == 26 &&
              $platform->getDateTimeFormatString() == 'Y-m-d H:i:s.u' &&
              ($platform instanceof \Doctrine\DBAL\Platforms\SQLServer2008Platform
               ||
              $platform instanceof \Doctrine\DBAL\Platforms\SQLServer2005Platform
              ) )
           $value = substr($value, 0, \strlen($value)-3);
        return $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
//        if ($value === null || $value instanceof \DateTime) {
//            return $value;
//        }
//
//        $val = \DateTime::createFromFormat($platform->getDateTimeFormatString(), $value);
//        if ( ! $val) {
//            throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());
//        }
//        return $val;
        
        if ($value === null) {
            return null;
        }

        $val = \DateTime::createFromFormat($platform->getDateTimeFormatString(),$value);
        $val_opcional1 = \DateTime::createFromFormat('M d Y H:i:s:uA',$value); //"Dec 26 2013 12:00:00:000PM"
        $val_opcional2 = \DateTime::createFromFormat('d/m/Y H:i:s',$value);
        
        if ($val) { return $val; }
        elseif ($val_opcional1) { return $val_opcional1; }
        elseif ($val_opcional2) { return $val_opcional2; }
        else { throw ConversionException::conversionFailedFormat($value, $this->getName(), $platform->getDateTimeFormatString());}
    }
}
