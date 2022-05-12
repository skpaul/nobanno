<?php
    #region Import libraries
        require_once("Required.php");

        Required::Logger()
            ->Database()
            ->EnDecryptor()
            //->JSON()
            ->Clock()
            ->headerBrand()
            ->applicantHeaderNav()->footer();
    #endregion

    #region Library instance declaration & initialization
        $logger = new Logger(ROOT_DIRECTORY);
        $endecryptor = new EnDecryptor();
        $clock = new Clock();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    #endregion

    #region Database connection
        $db->connect();
        $db->fetchAsObject();
    #endregion

    $msg = "That is all we know at this moment. If you're middle of something, sorry for the inconvenience.";

    if(isset($_GET["msg"]) || !empty($_GET["msg"]))
        $msg = $_GET["msg"];

?>

<!DOCTYPE html>
<html>

<head>
    <title><?=$pageTitle?> - <?= ORGANIZATION_FULL_NAME ?></title>
    <?php
        Required::gtag()->html5shiv()->metaTags()->favicon()->css()->sweetModalCSS()->bootstrapGrid();
    ?>


</head>

<body>
    <!-- <div id="version"></div> -->
    <div class="master-wrapper">
        <header>
            <?php
                echo HeaderBrand::prepare(array("baseUrl"=>BASE_URL, "hambMenu"=>true));
                echo ApplicantHeaderNav::prepare(array("baseUrl"=>BASE_URL));
            ?>
        </header>

        <main>
                <div class="container">
                    <!-- 
                    <nav class="left-nav">
                    <?php
                        // echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                        ?>
                    </nav> 
                    -->

                    <div class="content">
                        <div class="page-title"><?= $pageTitle ?></div>
                        <div class="page-subtitle"><?= $postConfig->title ?></div>
                        <!-- <div class="page-description">This is some description of this page [optional]</div> -->

                        <br>

                        <div style="display: flex; flex-direction: column; align-items: center; height: 100vh;">

                                <div><img style="height: 243px;" src="<?= BASE_URL ?>/assets/images/sorry.jpg"></div>

                                <div style="font-family: ARIAL, sans-serif; font-size: 47px; color: darkgray;  margin-bottom: 5px;">Couldn't Continue</div>
                                <div style="font-family: ARIAL, sans-serif; font-size: 20px; padding: 20px; color: #5e6161;"><?=$msg?></div>

                                <a href="<?php echo BASE_URL; ?>/" style="background-color: #4db240; padding: 20px; text-decoration: none; color: white; font-family: arial, sans-serif; letter-spacing: 0.04245rem; border-radius: 7px;">Go to Home</a>

                        </div>

                       
                    </div><!-- .content -->

                    <!-- 
                    <aside style="display: flex; flex-direction: column;">
                        asdsdaf
                    </aside> 
                    -->

                </div><!-- .container -->
            </main>
            <footer>
                <?php
                echo Footer::prepare(array());
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