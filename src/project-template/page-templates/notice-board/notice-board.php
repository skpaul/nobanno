<?php
require_once("../Required.php");

Required::Logger()
    ->Database()
    ->EnDecryptor()
    ->JSON()
    ->Helpers()->Clock();


$logger = new Logger(ROOT_DIRECTORY);
$endecryptor = new EnDecryptor();
$clock = new Clock();
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$db->connect();
$db->fetchAsObject();

$sql = "SELECT * FROM `notice_boards` 
        WHERE court='Lower Court' AND applicationType = 'Enrolment' 
        ORDER BY noticeId DESC";
$notices = $db->select($sql);

?>

<!DOCTYPE html>
<html>

<head>
    <title>Notices - <?= ORGANIZATION_FULL_NAME ?></title>
    <!--[if lt IE 9]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->

    <?php
    Required::metaTags()->favicon()->teletalkCSS()->sweetModalCSS()->bootstrapGrid();
    ?>




    <style>
                body{
                    background-color: #F8F9FA;
                    background-image: url('assets/images/corners/corners-4/corner-4-right-bottom.png');
                    background-position-x: right;
                    background-position-y: bottom;
                    background-repeat: no-repeat;
                    background-size: contain;
                }

              

                .card-wrapper{
                    text-align: center;
                }
                .card{
                    /* display: inline-block; */
                    border: 1px solid gray;
                    border-top-width: 5px;
                    border-radius: 6px;
                    padding: 30px 50px;
                    background-color: #FFF;
                    width: 100%;
                    max-width: 650px;
                    margin:auto;
                    margin-bottom: 20px;
                    text-align: left;
                }

                .card-title{
                    font-weight: 800;
                    color:#3d4e5e;
                    font-size: 15px;
                    text-align: left;
                    margin-bottom: 20px;
                }

                .file-link{
                    border: 1px solid #bfbfbf;
                    padding: 1px 10px;
                    /* display: flex; */
                    justify-content: center;
                    align-items: center;
                    border-radius: 8px;
                    text-decoration: none;
                    color:#3d4e5e;
                    font-size: 13px;
                    letter-spacing: 1px;
                }

                .file-link:hover{
                    background-color: #ededed;
                }
            </style>
</head>

<body>
   
    <div class="master-wrapper">
        <header>
            <?php
            require_once(ROOT_DIRECTORY . "/inc/header.php");
            echo prepareHeader(ORGANIZATION_FULL_NAME);
            ?>

          
        </header>
        <main>




            <div class="container">

                <h2 class="text-center">Notices</h2>
                <h4 class="text-center" style="font-size: 18px;">Application for enrolment as Advocate</h4>

                <br><br>
                <div class="card-wrapper">
                    <?php
                    if (count($notices) == 0) {
                        echo "no application.";
                    }
                    foreach ($notices as $notice) {
                        if (isset($notice->noticeFileName) && !empty($notice->noticeFileName)) {
                            // $noticeFileName= $notice->noticeFileName;
                            $div = <<<HTML
                                <a class="file-link" href="notice-files/{$notice->noticeFileName}">Download</a>
                                
                            HTML;
                        } else {
                            $div = "";
                        }
                    ?>
                        <div class="card">
                            <div style="font-size: 13px; font-style:italic;"><?=$clock->toString($notice->createDatetime, DatetimeFormat::BdDate())?></div>
                            <div class="card-title"><?= $notice->title ?></div>
                            <?=$div?>
                        </div>
                    <?php
                    }
                    ?>
                </div>

            </div>
        </main>
        <footer>
            <?php
            Required::footer();
            ?>
        </footer>
    </div>


    <?php
    Required::jquery()->hamburgerMenu();
    ?>
    <script>
        var base_url = '<?php echo BASE_URL; ?>';
        $(function() {
            $('#test1').mouseover(function() {
                this.setAttribute('scrollamount', 0, 0);
                $(this).trigger('stop');
            }).mouseout(function() {
                this.setAttribute('scrollamount', 5, 0);
                $(this).trigger('start');
            });

           
            function fades($div, cb) {
                $div.fadeIn(300, function() {
                    myTimeout = setTimeout(function() {
                        $div.fadeOut(400, function() {
                            var $next = $div.next();
                            if ($next.length > 0) {
                                fades($next, cb);
                            } else {
                                // The last element has faded away, call the callback
                                cb();
                            }
                        }); //fadeout ends


                    }, 2000); //setTimfeout ends
                });
            }





            function startFading($firstDiv) {
                fades($firstDiv, function() {
                    startFading($firstDiv);
                });
            }

            startFading($(".a:first-child"));

        }) //document.ready ends.
    </script>

</body>

</html>