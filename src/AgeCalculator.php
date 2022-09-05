<?php

//LAST MODIFIED: 2020-08-23 07:08 PM.


class AgeCalculator{

    /**
     * calculate()
     * 
     * Calculate age between two dates.
     * 
     * NOTE: parameters must be valid php datetime objects.
     * 
     * @param DateTime $from 
     * @param DateTime $to 
     * @return DateInterval $interval
     */
    public static function calculate($from, $to, $addOneDay = true):DateInterval{
        try {
            if($addOneDay){
                $to->add(new DateInterval('P1D')); //PHP 5 >= 5.3.0, PHP 7, PHP 8
            }

            if($from > $to){
                $fromDate = $from->format("d-m-Y");
                $toDate = $to->format("d-m-Y");
                throw new Exception("$fromDate must be earlier than $toDate.");
            }

            $interval = $from->diff($to);
            
            return $interval;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    
    //This is bangla method. Works nicely.
    private static function calculateAge($dob_year, $dob_month, $dob_day, $date_limit){
        
        $date_limit_x	= date('Y-m-d', strtotime($date_limit. "+0 days"));
        $cir_year	= substr($date_limit_x, 0,4);
        $cir_month	= substr($date_limit_x, 5, -3);
        $cir_day	= substr($date_limit_x, -2);
        
        if ($dob_month == 1 || $dob_month == 3 || $dob_month == 5 || $dob_month == 7 || $dob_month == 8 || $dob_month == 10 || $dob_month == 12) {$day_of_month = 31;}
        if ($dob_month == 4 || $dob_month == 6 || $dob_month == 9 || $dob_month == 11) {$day_of_month = 30;}
        if ($dob_month == 2)
        {
            $day_of_month = 28;
            //Check Leapyear
            if(($dob_year % 400 == 0) || (($dob_year % 4 == 0) && ($dob_year % 100 != 0))){$day_of_month = 29;}
        }
        //DAY
        if($cir_day < $dob_day)
        {
            $cir_day = $cir_day + $day_of_month;
            $dob_month++;
        }
        $days = ($cir_day - $dob_day) + 1;
        // MONTH
        if($cir_month < $dob_month)
        {
            $cir_month = $cir_month + 12;
            $dob_year++;
        }
        $months = $cir_month - $dob_month;
        //YEAR
        $years = $cir_year - $dob_year;
        
        if($days >= 30){
            $days = $days-30;
            $months++;
            if($months >= 12){
                $months = $months - 12;
                $years++;
            }
        }

        $interval = new DateInterval( "P".$years."Y".$months."M".$days."DT0H0M0S" );
        return $interval;
    }

    function findage($dob)
    {
        $localtime = getdate(strtotime("2022-02-28"));
        // $localtime = date("Y-m-d", "2022-02-28");
        $today = $localtime['mday']."-".$localtime['mon']."-".$localtime['year'];
        $dob_a = explode("-", $dob);
        $today_a = explode("-", $today);
        $dob_d = $dob_a[0];$dob_m = $dob_a[1];$dob_y = $dob_a[2];
        $today_d = $today_a[0];$today_m = $today_a[1];$today_y = $today_a[2];
        $years = $today_y - $dob_y;
        $months = $today_m - $dob_m;
        $days = $today_d - $dob_d;
        if ($today_m.$today_d < $dob_m.$dob_d) 
        {
            $years--;
            $months = 12 + $today_m - $dob_m;
        }
    
        if ($today_d < $dob_d) 
        {
            $months--;
        }
    
        $firstMonths=array(1,3,5,7,8,10,12);
        $secondMonths=array(4,6,9,11);
        $thirdMonths=array(2);
    
        if($today_m - $dob_m == 1) 
        {
            if(in_array($dob_m, $firstMonths)) 
            {
                array_push($firstMonths, 0);
            }
            elseif(in_array($dob_m, $secondMonths)) 
            {
                array_push($secondMonths, 0);
            }elseif(in_array($dob_m, $thirdMonths)) 
            {
                array_push($thirdMonths, 0);
            }
        }
        echo "<br><br> Age is $years years $months months $days days.";
    }

    /**
     * validateAge()
     * 
     * Checks whether an age is between minimum and maximum years.
     * 
     * @param DateInterval $interval contains years, months and days.
     * @param Int $minimumAge 
     * @param Int $maximumAge 
     * @param DateTime $ageCalulcateDate
     * 
     * @return true
     * 
     * @throws Exception if age is not between $minimumAge and $maximumAge.
     */
    public static function validateAge(DateInterval $interval, int $minimumAge, int $maximumAge, DateTime $ageCalulcateDate):bool
    {
        $date = $ageCalulcateDate->format("d-m-Y");
        if($interval->y < $minimumAge){
            throw new Exception("Age must be equal or greater than $minimumAge years.<br> As of $date, your age is ". $interval->y ." years ". $interval->m ." months ". $interval->d ." days.");
        }

        if($interval->y > $maximumAge){
            throw new Exception("Age must be equal or less than $maximumAge years.<br> As of $date, your age is ". $interval->y ." years ". $interval->m ." months ". $interval->d ." days.");
        }
        else{
            if($interval->y == $maximumAge){
                if($interval->m > 0){
                    throw new Exception("Age must be equal or less than $maximumAge years.<br> As of $date, your age is ". $interval->y ." years ". $interval->m ." months ". $interval->d ." days.");
                }
                else{
                    if($interval->d > 0){
                        throw new Exception("Age must be equal or less than $maximumAge years.<br> As of $date, your age is ". $interval->y ." years ". $interval->m ." months ". $interval->d ." days.");
                    }
                }
            }
        }

        return true;
    }   
}


?>