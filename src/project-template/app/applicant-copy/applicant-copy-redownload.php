<?php

#region Import libraries
require_once("../../Required.php");

Required::Logger()
    ->Database()->DbSession()
    ->Clock()
    ->EnDecryptor()
    ->JSON()
    ->Validable()
    ->AgeCalculator(2)
    ->Imaging()
    ->UniqueCodeGenerator()
    ->ExclusivePermission()->HttpHeader()->headerBrand()->applicantHeaderNav()->footer();
#endregion

#region Variable declaration and initialization
$logger = new Logger(ROOT_DIRECTORY);
$endecryptor = new EnDecryptor();
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$form = new Validable();
$clock = new Clock();
$json = new JSON();
$pageTitle = "Applicant Copy";
#endregion

?>

<!DOCTYPE html>
<html>

<head>
    <title><?= $pageTitle ?> - <?= ORGANIZATION_FULL_NAME ?></title>

    <?php
    Required::html5shiv()->metaTags()->favicon()->css()->sweetModalCSS()->bootstrapGrid();
    ?>

    <style>
        /* Override sweet-modal color */
        .sweet-modal-content{
            color: black;
        }

        .sweet-modal-overlay {
            background: radial-gradient(at center, rgba(255, 255, 255, 0.84) 0%, rgba(255, 255, 255, 0.96) 100%);
        }
    </style>
</head>

<body>
    <!-- <div id="version"></div> -->
    <div class="master-wrapper">
        <header>
            <?php
            echo HeaderBrand::prepare(array("baseUrl" => BASE_URL, "hambMenu" => true));
            echo ApplicantHeaderNav::prepare(array("baseUrl" => BASE_URL));
            ?>
        </header>
        <main>
            <div class="container">
                <div class="page-title"><?= $pageTitle ?></div>
                <div class="page-subtitle">Application for enrolment as Advocate</div>
                <div class="page-description">This is some description of this page [optional]</div>
                <!-- <nav class="left-nav">
                    <?php
                    // echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                    </nav> -->

                <div class="content">
                    <div class="card" style="max-width: 500px; margin:auto;">
                        <div class="card-title">This is card title [optional]</div>
                        <div class="card-description">This is card description [optional]</div>

                        <form action="applicant-copy-redownload-processor.php" method="post">
                            <div class="field">
                                <label class="required">Registration No.</label>
                                <input type="text" value="" name="regNo" class="validate swiftInteger" data-title="Registration No." data-required="required" data-maxlen="10" data-datatype="integer">
                            </div>

                            <div class="field">
                                <label class="required">Year</label>
                                <input type="text" value="" name="regYear" class="validate swiftInteger" maxlength="4" data-title="Registration Year" data-required="required" data-maxlen="4" data-datatype="integer">
                            </div>

                            <div class="field">
                                <label class="required">User Id</label>
                                <input type="text" value="" name="userId" class="validate" maxlength="12" data-title="User Id" data-required="required" data-maxlen="12">
                            </div>


                            <input type="submit" class="btn btn-dark btn-large form-submit-button" value="Submit">
                            <!-- <a class="recover" href="auth/recover-user-id.php">Forget User ID? Click here to recover.</a> -->

                        </form>
                    </div><!-- .card// -->
                </div><!-- .content -->

                <!-- 
                    <aside style="display: flex; flex-direction: column;">
                        asdsdaf
                    </aside> 
                    -->
            </div><!-- .container -->

        </main>
        <footer>
            <?=Footer::prepare(array());  ?>
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