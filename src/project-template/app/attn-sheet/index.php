<?php 

require_once("../../../Required.php");

Required::SwiftLogger()
->SessionBase()
->ZeroSQL(2)
->SwiftDatetime()
->EnDecryptor()
->JSON()
->Validable();


// // $logger = new SwiftLogger(ROOT_DIRECTORY);
// // $endecryptor = new EnDecryptor();
$db = new ZeroSQL();
$db->Server(DATABASE_SERVER)->Password(DATABASE_PASSWORD)->Database(DATABASE_NAME)->User(DATABASE_USER_NAME);
$db->connect();

// $year = date("Y");



    // require_once("../CONSTANTS.php");
    // require_once(ROOT_DIRECTORY . "/lib/error_reporting.php"); 
    // require_once(ROOT_DIRECTORY . "/lib/db_functions.php");
    // $connection = Create_Db_Connection(null);
    // Select_Database(DATABASE_NAME, $connection);
    // require_once(ROOT_DIRECTORY . "/lib/check_offline.php"); //this script checks whether it is in maintenance.
    // require_once(ROOT_DIRECTORY . "/lib/datetime_functions.php"); 
    // require_once(ROOT_DIRECTORY . "/lib/encryption_decryption.php"); 
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Attendance Sheet- Bangladesh Judicial Service Commission</title>     
        <?php
            require_once(ROOT_DIRECTORY . '/inc/meta_tags.html');
        ?>
        <link rel="icon" type="image/png"  href="images/favicon.png">
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>css/common.css">
        <style>

           .post{
            display:flex; padding: 20px 10px; 
           }

            .post-links>a {
                display:none;
                text-decoration: none;
                color: #78787b;
                border: 1px solid #78787b;
                padding: 2px 5px;
                border-radius: 10px;
                font-size: 13px;
            }

            .apply{
                width: 50px;
            }
            .post-links>a:hover {
                color: #fff;
                background-color: green;
            }

            .post:hover .post-links>a{
                display:inline-block;
            }
            

            .post:hover {
                background-color: rgba(211,209,209, 0.09);
            }
            
            #left-nav-ul a{
                text-decoration:none;
                color:#3aa059;
            }

            #left-nav-ul a:hover{
                text-decoration:underline;
            }
        </style>
    </head>
    <body>
        <!-- <div id="version"></div> -->
        <div id="master-wrapper">
            <header>
                <?php
                   // require_once(ROOT_DIRECTORY . '/inc/header.php');
                ?>
            </header>
            <main>

                <div style="width:95%;max-width: 900px;margin: 0 auto; display: flex;padding-top: 43px;">               
                    <div style="box-shadow: 0 0 4px rgb(128, 126, 126); width: 96%; max-width: 800px; min-height:200px;    margin: 0 auto;         padding: 20px;     font-family: Arial, Helvetica, sans-serif;">
                        <h1 style="text-align:center; color: #21a249; margin-top: 0px;font-size: 24px;">Download Attendance Sheet</h1>
                            <div class="post">
                                <form action="download.php" method="post" style="margin:auto; width:370px;">
                                    <input name="start_roll" type="text" placeholder="starting roll" style="width:220px; display:block; margin:auto;"><br>
                                    <div>
                                        <input name="end_roll" type="text" placeholder="ending roll" style="width:220px;  margin-left: 76px;">
                                    </div>
                                    
                                    <input type="submit" value="Submit" style="display: block; margin: auto; margin-top: 20px;">
                                    <!-- <input type="text" placeholder="enter password"> -->
                                </form>
                            </div>
                        </div>
                </div>
            </main>
          
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>

        <script>
            $(function(){
               
            })
        </script>
    </body>
</html>