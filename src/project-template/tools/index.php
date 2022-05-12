<?php 
    require_once("../Required.php");
    require_once("prevent_access_if_not_localhost.php");
    $queryString = $_SERVER['QUERY_STRING'];

    
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Menu</title>
        <link rel="shortcut icon" type="image/png" href="images/menu.png"/>
        <style>
            *{
                font-family: Arial, Helvetica, sans-serif;
                box-sizing: border-box  ;
            }
            .container{
                width: 100%;
                max-width: 800px;
                margin: auto;
                display: flex;
                flex-direction: row;
                flex-wrap: wrap;
                justify-content: center;
            }

            a{
                display: inline-flex;
                padding: 10px;
                margin: 20px;
                width: 200px;
                height: 50px;
                justify-content: center;
                align-items: center;
                box-shadow: 0px 0px 4px #acb0b1;
                font-size: 0.8rem;
                text-decoration: none;
                color: gray;
                text-align: center;
                line-height: 1.100rem;
            }
            a:hover{
                box-shadow: 0px 0px 4px #186072;
            }
        </style>
    </head>
    <body>
        <div class="container" style="max-width: 670px;">
                <a href="read-logs.php?<?=$queryString ?>" target="_blank"><img src="images/error-log.png" style="height: 24px; margin-right: 10px;">Errors</a>
                <a href="clear-logs.php?<?=$queryString ?>" target="_blank">Clear Error Logs</a>
                <a href="sql-query.php?<?=$queryString ?>" target="_blank"> <img src="images/sql.png" style="height: 24px; margin-right: 10px;">SQL</a>
                <a href="csv.php?<?=$queryString ?>" target="_blank"> <img src="images/csv.png" style="height: 24px; margin-right: 10px;">CSV</a>
                <a href="html-form-builder.php?<?=$queryString ?>" target="_blank"><img src="images/form.png" style="height: 24px; margin-right: 10px;">Forms</a>

                <a href="prepare-validables.php?<?=$queryString ?>" target="_blank"><img src="images/validables.png" style="height: 24px; margin-right: 10px;">Validables</a>

                <a href="change-token.php?<?=$queryString ?>" target="_blank"><img src="images/token.png" style="height: 24px; margin-right: 10px;">Token</a>
        </div>
        
        <?php
            if(isset($_GET["delete_site_logs"])){

                $my_file = "../site_logs.log";

                if(file_exists($my_file)){
                    unlink($my_file) or die("Couldn't delete file");
                }

                if(!file_exists($my_file)){
                    $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly creates file
                }
            }

            if(isset($_GET["delete_error_logs"])){
                $my_file = "../error_logs.log";

                if(file_exists($my_file)){
                    unlink($my_file) or die("Couldn't delete file");
                }

                if(!file_exists($my_file)){
                    $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file); //implicitly creates file
                }
            }
        ?>
    </body>
</html>




