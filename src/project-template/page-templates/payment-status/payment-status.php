<?php
    #region Import libraries
        require_once("../../Required.php");
        Required::Logger()->headerBrand()->applicantHeaderNav()->footer();
    #endregion

	#region Class instance declaration & initialization
        $logger = new Logger(ROOT_DIRECTORY);
    #endregion

    $pageTitle = "Payment Status";

?>

<!DOCTYPE html>
<html>
    <head>
        <title> <?= $pageTitle ?> - <?= ORGANIZATION_FULL_NAME ?></title>
        <?php
            Required::html5shiv()->metaTags()->favicon()->css()->sweetModalCSS()->bootstrapGrid()->airDatePickerCSS();
        ?>
    </head>

    <body>
        <div class="master-wrapper">
            <header>
                <?php
                    echo HeaderBrand::prepare(array("baseUrl"=>BASE_URL, "hambMenu"=>true));
                    echo ApplicantHeaderNav::prepare(array("baseUrl"=>BASE_URL));
                ?>
            </header>
            <main>
                <div class="container">
                    <div class="page-title"><?= $pageTitle ?></div>
                    <div class="page-subtitle">Application for enrolment as Advocate</div>
                    <div class="page-description">This is some description of this page [optional]</div>

                    <!-- 
                        <nav class="left-nav">
                        <?php
                        // echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                        ?>
                    </nav> 
                    -->

                    <div class="content">
                        <div class="card">
                            <div class="card-title">This is card title [optional]</div>
                            <div class="card-description">This is card description [optional]</div>

                            <form action="payment-status-processor.php" method="post" class="">
                                <div class="field">
                                    <label class="required">Registration No.</label>
                                    <input type="text" value="" name="regNo" class="validate swiftInteger" data-title="Registration No." data-required="required" data-maxlen="10" data-datatype="integer">
                                </div>

                                <div class="field">
                                    <label class="required">Year</label>
                                    <input type="text" value="" name="regYear" class="validate swiftInteger swiftYear" maxlength="4" data-title="Registration Year" data-required="required" data-maxlen="4" data-datatype="integer">
                                </div>

                                <div class="field">
                                    <label class="required">User Id</label>
                                    <input type="text" value="" name="userId" class="validate upper-case" maxlength="12" data-title="User Id" data-required="required" data-maxlen="12">
                                </div>

                                <input type="submit" class="btn btn-dark btn-large form-submit-button" value="Submit">
                                <!-- <a class="recover" href="auth/recover-user-id.php">Forget User ID? Click here to recover.</a> -->
                            </form>
                        </div>

                    </div><!-- content// -->

                    <!-- <aside style="display: flex; flex-direction: column;">
                        asdsdaf
                    </aside> -->


                </div><!-- container// -->

            </main>
            <footer>
                <?=Footer::prepare(array());
                ?>
            </footer>
        </div>


        <?php
        Required::jquery()->hamburgerMenu()->sweetModalJS()->swiftSubmit()->swiftNumeric()->airDatePickerJS();
        ?>
        <script>
            var base_url = '<?php echo BASE_URL; ?>';
            $(function() {

                // $(".swiftInteger").swiftNumericInput({ allowFloat: false, allowNegative: false });

                $("form").swiftSubmit({
                    redirect: true
                }, null, null, null, null, null);

                $("input[type=text]").on('propertychange change keyup paste input', function() {
                    $(this).removeClass("error");
                });

                $("select").on('change propertychange paste', function() {
                    $(this).removeClass("error");
                });

                $("textarea").on('input propertychange paste', function() {
                    $(this).removeClass("error");
                });

                $("input[type=radio]").change(function() {
                    $(this).closest("div.radio-group").removeClass("error");
                });


                $('.swiftYear').datepicker({
                    language: 'en',
                    dateFormat: "yyyy",
                    autoClose: true,
                    showOn: "button",
                    minView: 'years',
                    view: "years",
                    onSelect: function(formattedDate, date, inst) {
                        $(inst.el).trigger('change');
                        $(inst.el).removeClass('error');
                    }
                })

                SwiftNumeric.prepare('.swiftInteger');
            })
        </script>

    </body>
</html>