<?php
    $localHost =  "{$_SERVER['HTTP_HOST']}";//{$_SERVER['REQUEST_URI']}";

    $tokenText = file_get_contents('token.txt');

    if($localHost != "localhost"){
        if(!isset($_GET["token"]) || empty($_GET["token"])){
            die("Access denied");
        }
    
        $token = $_GET["token"];
        if($token !== $tokenText){
            die("Access denied");
        }
    }

?>