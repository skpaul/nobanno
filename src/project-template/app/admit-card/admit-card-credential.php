<?php
    require_once("../Required.php");

    Required::Logger()
            ->Database()
            ->Clock()
            ->EnDecryptor()
            ->JSON()  
            ->ExclusivePermission()->HttpHeader();


    $logger = new Logger(ROOT_DIRECTORY);
    $endecryptor = new EnDecryptor();
    $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    $clock = new Clock();
    $db->connect(); $db->fetchAsObject();

    //if config id not found in GET variables, redirect to sorry page.
    if (!isset($_GET["config-id"]) || empty(trim($_GET["config-id"]))) {
        HttpHeader::redirect(BASE_URL . "/sorry.php");
    }

    $encConfigId = trim($_GET["config-id"]); //decryption has been done in the following try .. catch block for safety reason.
    $postConfigId = $endecryptor->decrypt($encConfigId);

    $hasExclusivePermission = ExclusivePermission::hasPermission();
    $sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND configId=:configId";

    $configs = $db->select($sql, array('court' => COURT, "applicationType" => APPLICATION_TYPE, "configId" => $postConfigId));
    
    //whether exclusive permission exists or not, the post configuration must exist.
    if (count($configs) != 1) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application is not available.");

    $postConfig = $configs[0];

?>

<!DOCTYPE html>
<html>

    <head>
        <title>Admit Card - <?=ORGANIZATION_FULL_NAME?></title>
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
                <h2 class="text-center margin-bottom-25">Admit Card Download</h2>
                <?php
                    //admitCardStartDatetime = Null means admit card not available
                    if(!isset($postConfig->admitCardStartDatetime) || empty($postConfig->admitCardStartDatetime)){
                        $html = <<<HTML
                            <div class="text-center">Admit card not yet available to download. Please check back later.</div>
                        HTML;
                    }
                    else{
                        //Check the datetime limitations------>
                        if(isset($postConfig->admitCardStartDatetime) && !empty($postConfig->admitCardStartDatetime) && isset($postConfig->admitCardEndDatetime) && !empty($postConfig->admitCardEndDatetime)                            ){
                            $admitStart = $clock->toDate($postConfig->admitCardStartDatetime);
                            $admitEnd = $clock->toDate($postConfig->admitCardEndDatetime);
                            $currentDatetime = $clock->toDate("now");

                            if($currentDatetime < $admitStart){
                                $message = $admitStart->format("h:i a, d-m-Y.");
                                $html = <<<HTML
                                        <div class="text-center">Admit card will be available from $message</div>
                                    HTML;
                            }
                            else{
                                if($currentDatetime > $admitEnd){
                                    $message = $admitEnd->format("h:i a, d-m-Y.");
                                    $html = <<<HTML
                                            <div class="text-center">Last date of downloading admit card ended on $message</div>
                                        HTML;
                                }
                                else{
                                    $html = <<<HTML
                                            <form action="admit-card-credential-processor.php?config-id=$encConfigId" method="post" class="small box-shadow padding-all-25">
                                            <div class="field">
                                                <label class="required">Registration No.</label>
                                                <input type="text" value="" name="regNo" class="validate" maxlength="20" data-swift-title="User Id" data-swift-required="required" data-swift-maxlen="20">
                                            </div>

                                            <div class="field">
                                                <label class="required">Registration Year</label>
                                                <input type="text" value="" name="regYear" class="validate" maxlength="20" data-swift-title="Password" data-swift-required="required" data-swift-maxlen="20">
                                            </div>
                                            <input type="submit" class="btn btn-dark btn-large form-submit-button" value="Submit">
                                        </form>
                                    HTML;
                                }
                            }
                        }
                        else{
                            $html = <<<HTML
                                        <div>Something happended wrong. Please contact with admin.</div>
                                    HTML;
                        }
                        //<-------Check the datetime limitations
                    }
                   
                    echo $html;

                ?>
              
            </div>

        </main>
        <footer>
            <?php
            Required::footer();
            ?>
        </footer>
    </div>


    <?php
        Required::jquery()->hamburgerMenu()->sweetModalJS()->swiftSubmit()->swiftNumeric();
    ?>
    <script>
        var base_url = '<?php echo BASE_URL; ?>';
        $(function() {

            // $(".swiftInteger").swiftNumericInput({ allowFloat: false, allowNegative: false });

            $("form").swiftSubmit({
                redirect: true
            }, null, null, null, null, null);
        })
    </script>

</body>

</html>