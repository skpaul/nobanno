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

$sql = "SELECT configId, title, circularFileName, applicationStartDatetime, applicationEndDatetime 
            FROM `post_configurations` 
            WHERE isActive = 1 AND court='Lower Court' AND applicationType = 'Enrolment' 
            ORDER BY configId ASC";
$postConfigs = $db->select($sql);


?>

<!DOCTYPE html>
<html>

<head>
    <title>Home - <?= ORGANIZATION_FULL_NAME ?></title>

    <!--[if lt IE 9]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->

    <?php
    Required::metaTags()->favicon()->teletalkCSS()->sweetModalCSS()->bootstrapGrid();
    ?>


    <style>
        .notice-board {
            display: flex;
            flex-direction: row;
            margin-bottom: 41px;
        }

        .notice-board>.label {
            margin-right: 32px;
            font-weight: 600;
            border: 1px solid #3d4e5e;
            padding: 1px 5px;
            border-radius: 5px;
        }

        #ticker a {
            display: none;
        }
    </style>

    <style>
        body{
            background-color: #F8F9FA;
            background-image: url('assets/images/corners/corners-4/corner-4-right-bottom.png');
            background-position-x: right;
            background-position-y: bottom;
            background-repeat: no-repeat;
            background-size: contain;
        }

        marquee{
            font-size: 15px;
            border-radius: 20px;
            border: 1px solid #F8F9FA;
            margin-bottom: 40px;
        }
        marquee:hover{
            background-color: #FFF;
            border: 1px solid #bfbfbf;
        }

        .marquee-items{
            display: flex; flex-direction:row;
            list-style-position: inside;
            padding: 10px;
        }

        .marquee-items li{
            margin-right: 20px;
        }

        .card-wrapper{
            text-align: center;
        }
        .card{
            display: inline-block;
            border: 1px solid gray;
            border-top-width: 5px;
            border-radius: 6px;
            padding: 30px 50px;
            background-color: #FFF;
        }

        .card-title{
            font-weight: 800;
            color:#3d4e5e;
            font-size: 20px;
            text-align: left;
            margin-bottom: 10px;
        }

        .card-links{
            display: flex;
        }

        .card-links a{
            border: 1px solid #bfbfbf;
            padding: 1px 10px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            text-decoration: none;
            color:#3d4e5e;
        }


        .card-links a:not(:first-child){
            margin-left: 10px;
        }

        .card-links a:hover{
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

                <h2 class="text-center">Admit Card Download</h2>
                <h4 class="text-center" style="font-size: 18px;">Application for enrolment as Advocate</h4>

                <div class="card-wrapper">
                    <div class="card">
                        <div class="card-title">
                           Select Application Session
                        </div>
                        <?php
                        if (count($postConfigs) == 0) {
                            echo "No application session available.";
                        }

                        foreach ($postConfigs as $post) {
                    ?>
                        <a href="admit-card-credential.php?config-id=<?= $endecryptor->encrypt($post->configId) ?>"><?= $post->title ?></a>
                    <?php
                        }
                    ?>
                    </div>
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