<?php
    class SmsSender{
        public static function sendSms($message, $recipient, $apiPassword){
            // ignore_user_abort(true);
            try {
                file_get_contents('http://bulkmsg.teletalk.com.bd/api/sendSMS', false, stream_context_create([
                    'http' => [
                        'method' => 'POST',
                        'header'  => "Content-type: text/html",
                        'content' => '{
                                        "auth":
                                            {
                                                "username":"MOYS",
                                                "password":"'.$apiPassword.'",
                                                "acode":"1005123"
                                            },
                                        "smsInfo":
                                            {
                                                "message":"'.$message.'",
                                                "msisdn":["'.$recipient.'"]
                                            } 
                                        }'
                    ]
                ]));
            } catch (\Throwable $th) {
                throw $th;
            }
        }
    }

?>