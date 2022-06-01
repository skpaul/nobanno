<?php
    // namespace Nobanno;
    class Greetings{
        public static function greet():string{
            $hour = (new \DateTime("now", new \DateTimeZone("Asia/Dhaka")))->format('H');
            $salutation = "Hello!";
            if ($hour < 12) {
                 $salutation = "Good Morning!"; // "শুভ সকাল";
            } elseif ($hour > 11 && $hour < 18) {
                 $salutation = "Good Afternoon!"; // "শুভ অপরাহ্ণ";
            } elseif ($hour > 17) {
                 $salutation = "Good Evening!"; // "শুভ সন্ধ্যা";
            }    


            return  $salutation; // . $message == "" ? "" : ", $message";
        }
    }

?>