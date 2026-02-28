<?php
namespace Nobanno;
require_once("Abstractions/Enum.php");

final class DatetimeFormat extends Enum
{

    static public function MySqlDate()
    {
        return new self('Y-m-d');
    }
    static public function MySqlDatetime()
    {
        return new self("Y-m-d H:i:s");
    }

    static public function BdDate()
    {
        return new self("d-m-Y");
    }
    static public function BdTime()
    {
        return new self("h:i A");
    }
    static public function BdDatetime()
    {
        return new self("h:i A d-m-Y");
    }
    static public function Custom($format)
    {
        return new self($format);
    }

    /**
     * Get the format string value.
     *
     * @return string
     */
    public function getValue(): string
    {
        return $this->val;
    }

    // const MySqlDate =  'Y-m-d';
    // const MySqlDatetime = 'Y-m-d H:i:s';
    // const BdDate = 'd-m-Y';
    // const BdTime =  'h:i A';
    // const BdDatetime = 'h:i A d-m-Y';
}

class Clock
{
    protected \DateTimeZone $datetimeZone;
    public function __construct(string $datetimeZone = "Asia/Dhaka")
    {
        $this->datetimeZone = new \DateTimeZone($datetimeZone);
    }

    #region Core Methods
    /**
     * @deprecated Use toDatetime() instead.
     *
     * toDate()  
     * 
     * Returns a php datetime object.
     * 
     * @param string $value
     * 
    * @return \DateTime
     */
    public function toDate(string $value): \DateTime
    {
        return new \DateTime($value, $this->datetimeZone);
    }

    /**
     * toDatetime()
     * 
     * Returns a php datetime object.
     * 
     * @param string $value
     * 
    * @return \DateTime
     */
    public function toDatetime(string $value): \DateTime
    {
        return new \DateTime($value, $this->datetimeZone);
    }

    /**
     * toString()
     * 
     * Returns a formatted date string.
     * 
     * @param mixed $value  DateTime object or date string.
     * @param string|DatetimeFormat $format  Format string or DatetimeFormat instance.
     * @return string
     */
    public function toString(mixed $value, string | DatetimeFormat $format): string
    {
        $formatStr = $format instanceof DatetimeFormat ? $format->getValue() : $format;
        if ($value instanceof \DateTime) {
            return $value->format($formatStr);
        } else {
            $dt = new \DateTime($value, $this->datetimeZone);
            return $dt->format($formatStr);
        }
    }
    #endregion

    #region Hour manipulation
    public function addHours(int $hoursToAdd, mixed $datetime): \DateTime
    {
        $dt = $datetime instanceof \DateTime ? clone $datetime : new \DateTime($datetime, $this->datetimeZone);
        return $dt->add(new \DateInterval("PT{$hoursToAdd}H"));
    }

    public function deductHours(int $hoursToSubtract, mixed $datetime): \DateTime
    {
        $dt = $datetime instanceof \DateTime ? clone $datetime : new \DateTime($datetime, $this->datetimeZone);
        return $dt->sub(new \DateInterval("PT{$hoursToSubtract}H"));
    }
    #endregion

    #region Day manipulation
    public function addDays(int $daysToAdd, mixed $datetime): \DateTime
    {
        $dt = $datetime instanceof \DateTime ? clone $datetime : new \DateTime($datetime, $this->datetimeZone);
        return $dt->add(new \DateInterval("P{$daysToAdd}D"));
    }

    public function deductDays(mixed $datetime, int $daysToSubtract): \DateTime
    {
        $dt = $datetime instanceof \DateTime ? clone $datetime : new \DateTime($datetime, $this->datetimeZone);
        return $dt->sub(new \DateInterval("P{$daysToSubtract}D"));
    }
    #endregion

    #region month manipulation
    public function addMonths(int $monthsToAdd, mixed $datetime): \DateTime
    {
        $dt = $datetime instanceof \DateTime ? clone $datetime : new \DateTime($datetime, $this->datetimeZone);
        return $dt->add(new \DateInterval("P{$monthsToAdd}M"));
    }

    public function deductMonths(int $monthsToSubtract, mixed $datetime): \DateTime
    {
        $dt = $datetime instanceof \DateTime ? clone $datetime : new \DateTime($datetime, $this->datetimeZone);
        return $dt->sub(new \DateInterval("P{$monthsToSubtract}M"));
    }
    #endregion

    #region Year manipulation
    public function addYears(int $yearsToAdd, mixed $datetime): \DateTime
    {
        $dt = $datetime instanceof \DateTime ? clone $datetime : new \DateTime($datetime, $this->datetimeZone);
        return $dt->add(new \DateInterval("P{$yearsToAdd}Y"));
    }

    public function deductYears(int $yearsToSubtract, mixed $datetime): \DateTime
    {
        $dt = $datetime instanceof \DateTime ? clone $datetime : new \DateTime($datetime, $this->datetimeZone);
        return $dt->sub(new \DateInterval("P{$yearsToSubtract}Y"));
    }
    #endregion

}
?>