<?php
namespace Nobanno\ClockClasses;
use Nobanno\Abstractions\Enum;

final class DatetimeFormat extends Enum {

    static public function MySqlDate() {
        // return new self('Y-m-d');
        return "";
    }

    static public function MySqlDatetime() {
        // return new self("Y-m-d H:i:s");
        return "";
    }


    /**
     * BdDate()
     * 
     * Format as 'd-m-Y'
     */
    static public function BdDate() {
        // return new self("d-m-Y");
        return "";
    }
    static public function BdTime() {
        // return new self("h:i A");
        return "";
    }
    static public function BdDatetime() {
        // return new self("h:i A d-m-Y");
        return "";
    }
    static public function Custom($format) {
        // return new self($format);
        return "";
    }

    // const MySqlDate =  'Y-m-d';
    // const MySqlDatetime = 'Y-m-d H:i:s';
    // const BdDate = 'd-m-Y';
    // const BdTime =  'h:i A';
    // const BdDatetime = 'h:i A d-m-Y';
}

?>