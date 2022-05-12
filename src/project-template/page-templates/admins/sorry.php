<?php
require_once("../Required.php");

Required::Logger()
    ->Database()
    ->EnDecryptor()
    ->JSON()
    ->Helpers();


$logger = new Logger(ROOT_DIRECTORY);
$msg = "That is all we know at this moment. If you're middle of something, sorry for the inconvenience.";

if(isset($_GET["msg"]) || !empty($_GET["msg"]))
    $msg = $_GET["msg"];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Couldn't Continue - <?= ORGANIZATION_FULL_NAME ?></title>
    <!--[if lt IE 9]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->

    <?php
    Required::metaTags()->favicon()->teletalkCSS()->sweetModalCSS()->bootstrapGrid();
    ?>


</head>

<body>
    <!-- <div id="version"></div> -->
    <div class="master-wrapper">
        <header>
            <?php
            require_once(ROOT_DIRECTORY . "/inc/header.php");
            echo prepareHeader(ORGANIZATION_FULL_NAME);
            ?>
        </header>
        <main>
            <div class="container">
                <div style="display: flex; flex-direction: column; align-items: center; height: 100vh;">

                    <div><img style="height: 243px;" src="<?= BASE_URL ?>/assets/images/sorry.jpg"></div>

                    <div style="font-family: ARIAL, sans-serif; font-size: 47px; color: darkgray;  margin-bottom: 5px;">Couldn't Continue</div>
                    <div style="font-family: ARIAL, sans-serif; font-size: 20px; padding: 20px; color: #5e6161;"><?=$msg?></div>

                    <a href="<?php echo BASE_URL; ?>/admins/" style="background-color: #4db240; padding: 20px; text-decoration: none; color: white; font-family: arial, sans-serif; letter-spacing: 0.04245rem; border-radius: 7px;">start again</a>

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


        })
    </script>

</body>

</html>