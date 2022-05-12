<?php   


    require_once("../CONSTANTS.php");
    require_once("prevent_access_if_not_localhost.php");
    $queryString = $_SERVER['QUERY_STRING'];

    require_once(ROOT_DIRECTORY . "/lib/Logger/SwiftLogger.php"); 
    require_once(ROOT_DIRECTORY . "/lib/Database/ZeroSQL.php");

    $logger = new SwiftLogger(ROOT_DIRECTORY);
    $db = new ZeroSQL();
    $db->Server(DATABASE_SERVER)->User(DATABASE_USER_NAME)->Password(DATABASE_PASSWORD)->Database(DATABASE_NAME)->Connect();

  
?>


<!DOCTYPE html>
<html>
    <head>
        <title>Data Viewer</title>
        <link rel="shortcut icon" type="image/png" href="images/csv.png"/>
        <style>

            *{
                box-sizing: border-box;
                font-family: Arial, Helvetica, sans-serif;
            }
            .container{
                width: 100%;
                margin: auto;
                display: flex;
                flex-direction: row-reverse;
            }

            .form-container{
                width:40%;
                position: relative;
            }

            form{
                position: fixed;
                width: 506px;
            }
            .data-container{
                width: 60%;
            }
            select{
                width: 100%;
                padding:5px;
                margin-bottom: 20px;
            }


            textarea{
                width: 100%;
                height: 250px;
            }

            button{
                width: 159px;
                height: 50px;

            }
            table{
                border: 1px solid lightblue;
                padding: 5px;
                width: 100%;
                margin: auto;
               
                font-size: 0.7rem;
                margin-bottom: 20px;
            }

            tr{

            }

            td{
                background-color: lightblue;
                padding:2px;
            }

            .column{
                font-weight: bold;
                text-align: right;
                width: 45%;
                padding-right: 10px;
            }
            .value{
                padding-left: 10px;
            }
        </style>
    </head>
    <body>
        <div class="container"> 
            <div class="form-container">
                <form action="csv_.php?<?= $queryString;?>" method="post">
                    <textarea id="textarea" name="sql" style=""><?php echo $select_sql; ?></textarea>
                    <div style="text-align:center; padding:15px;">
                        <button id="prepare" name="submit">Export</button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function clearTextarea(){
                var textArea = document.getElementById('textarea');
                textArea.value='';
            }
        </script>
    </body>
</html>