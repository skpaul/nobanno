<?php

abstract class DatetimeFormatEnum {
    protected $val;

    protected function __construct($arg) {
        $this->val = $arg;
    }

    public function __toString() {
        return $this->val;
    }

    public function __set($arg1, $arg2) {
        throw new \Exception("enum does not have property");
    }

    public function __get($arg1) {
        throw new \Exception("enum does not have property");
    }

    // not really needed
    public function __call($arg1, $arg2) {
        throw new \Exception("enum does not have method");
    }

    // not really needed
    static public function __callStatic($arg1, $arg2) {
        throw new \Exception("enum does not have static method");
    }
}

final class DatetimeFormat extends DatetimeFormatEnum {

    static public function MySqlDate() {
        return new self('Y-m-d');
        // return "";
    }

    static public function MySqlDatetime() {
        return new self("Y-m-d H:i:s");
        // return "";
    }


    /**
     * BdDate()
     * 
     * Format as 'd-m-Y'
     */
    static public function BdDate() {
        return new self("d-m-Y");
        // return "";
    }
    static public function BdTime() {
        return new self("h:i A");
        // return "";
    }
    static public function BdDatetime() {
        return new self("h:i A d-m-Y");
        // return "";
    }
    static public function Custom($format) {
        return new self($format);
        // return "";
    }

    // const MySqlDate =  'Y-m-d';
    // const MySqlDatetime = 'Y-m-d H:i:s';
    // const BdDate = 'd-m-Y';
    // const BdTime =  'h:i A';
    // const BdDatetime = 'h:i A d-m-Y';
}

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
        public function toString(mixed $value, DatetimeFormat $format)
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