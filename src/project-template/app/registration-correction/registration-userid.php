<?php
require_once("../Required.php");

Required::Logger()
    ->Database()->DbSession()
    ->Clock()
    ->EnDecryptor()
    ->JSON()
    ->Validable()
    ->AgeCalculator(2)
    ->Imaging()
    ->UniqueCodeGenerator()
    ->Helpers()->ExclusivePermission()->HttpHeader();

    $logger = new Logger(ROOT_DIRECTORY);
    $endecryptor = new EnDecryptor();
    $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    $form = new Validable();
    $clock = new Clock();
    $json = new JSON();

?>

<!DOCTYPE html>
<html>

    <head>
        <title>Registration Correction Request - <?=ORGANIZATION_FULL_NAME?></title>
        <!--[if lt IE 9]>
            <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->

        <?php
        Required::metaTags()->favicon()->teletalkCSS()->sweetModalCSS()->bootstrapGrid();
        ?>
        
        <style>
             body{
                background-color: #F8F9FA;
            }
            form{
                background-color: #FFF;
                box-shadow: none !important;
                border: 1px solid gray;
                border-top-width: 5px;
                border-radius: 6px;
                padding: 20px !important;
                max-width: 700px !important;
            }
        </style>
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
                <h2 class="text-center">Registration Correction Request</h2>
                <h4 class="text-center" style="font-size: 18px; margin-bottom:35px;">Application for enrolment as Advocate</h4>

                <form action="registration-userid-processor.php" method="post" class="small box-shadow padding-all-25">

                <div style="margin-left: 19px; line-height:1.7;">
                        <ul>
                            <li>
                                After submitting the request, you can use this form to check the request status.
                            </li>
                            <li>
                                You can submit only one update request against a User Id.
                            </li>
                        </ul>
                    </div>

                    <br><br>

                    <div class="field">
                        <label class="required">Registration No.</label>
                        <input type="text" value="" name="regNo" class="validate swiftInteger" data-title="Registration No."  data-required="required" data-maxlen="10" data-datatype="integer">
                    </div>
                    
                    <div class="field">
                        <label class="required">Year</label>
                        <input type="text" value="" name="regYear" class="validate swiftInteger" maxlength="4" data-title="Registration Year" data-required="required" data-maxlen="4" data-datatype="integer">
                    </div>
                    
                    <div class="field">
                        <label class="required">User Id</label>
                        <input type="text" value="" name="userId" class="validate upper-case" maxlength="12" data-title="User Id" data-required="required" data-maxlen="12">
                    </div>
                    

                    <input type="submit" class="btn btn-dark btn-large form-submit-button" value="Submit">
                    <!-- <a class="recover" href="auth/recover-user-id.php">Forget User ID? Click here to recover.</a> -->

                   
                   
                </form>
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