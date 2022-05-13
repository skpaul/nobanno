<?php
namespace Nobanno;
use Nobanno\ClockClasses\DatetimeFormat;


    class Clock{
        public function __construct(string $datetimeZone = "Asia/Dhaka") {
            $this->datetimeZone=new \DateTimeZone($datetimeZone);
        }

        /**
         * toDate()
         * 
         * Returns a php datetime object.
         * 
         * @param string $value
         * 
         * @return Datetime
         */
        public function toDate(string $value):\DateTime
        {
            return new \DateTime($value, $this->datetimeZone);
        }


        /**
         * toDate()
         * 
         * Returns a php datetime object.
         * 
         * @param mixed $value
         * 
         * @param DatetimeFormat $format
         * 
         * 
         */
        public function toString(mixed $value, \DatetimeFormat $format)
        {
            if ($value instanceof \DateTime){
                return $value->format($format);
            }
            else{
                $dt = new \DateTime($value, $this->datetimeZone);
                return $dt->format($format);
            }
        }
    }
?>