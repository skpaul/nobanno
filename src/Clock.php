<?php

    require_once("Abstractions/Enum.php");

    final class DatetimeFormat extends Enum {
    
        static public function MySqlDate() {
            return new self('Y-m-d');
        }
        static public function MySqlDatetime() {
            return new self("Y-m-d H:i:s");
        }


        /**
         * BdDate()
         * 
         * Format as 'd-m-Y'
         */
        static public function BdDate() {
            return new self("d-m-Y");
        }
        static public function BdTime() {
            return new self("h:i A");
        }
        static public function BdDatetime() {
            return new self("h:i A d-m-Y");
        }
        static public function Custom($format) {
            return new self($format);
        }

        // const MySqlDate =  'Y-m-d';
        // const MySqlDatetime = 'Y-m-d H:i:s';
        // const BdDate = 'd-m-Y';
        // const BdTime =  'h:i A';
        // const BdDatetime = 'h:i A d-m-Y';
    }

    class Clock{
        public function __construct(string $datetimeZone = "Asia/Dhaka") {
            $this->datetimeZone=new DateTimeZone($datetimeZone);
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
        public function toDate(string $value):DateTime
        {
            return new DateTime($value, $this->datetimeZone);
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
         * @return string
         */
        public function toString(mixed $value, DatetimeFormat $format):string
        {
            if ($value instanceof DateTime){
                return $value->format($format);
            }
            else{
                $dt = new DateTime($value, $this->datetimeZone);
                return $dt->format($format);
            }
        }

        /**
         * addDays()
         * 
         * Add days to a datetime object.
         * 
         * @param DateTime $datetime The datetime value to modify.
         * @param int $daysToAdd The quantity of days to add, for example '7'
         * 
         * @return DateTime The modified datetime.
        */
        public function addDays(DateTime $datetime, int $daysToAdd){
            return  $datetime->add(new DateInterval("P" . $daysToAdd . "D"));
        }

        /**
         * deductDays()
         * 
         * Deduct days from a datetime object.
         * 
         * @param DateTime $datetime The datetime value to modify.
         * @param int $daysToSubtract The quantity of days to subtract, for example '7'
         * 
         * @return DateTime The modified datetime.
        */
        public function deductDays(DateTime $datetime, int $daysToSubtract){
            return  $datetime->sub(new DateInterval("P" . $daysToSubtract . "D"));
        }
    }
?>