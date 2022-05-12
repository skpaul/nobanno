<?php

    #region Import libraries
        require_once("../../Required.php");
        Required::Logger()->headerBrand()->applicantHeaderNav()->footer();
    #endregion

    #region Class instance declaration & initialization
        $logger = new Logger(ROOT_DIRECTORY);
    #endregion
    
    $pageTitle = "Recover User ID";

?>

<!DOCTYPE html>
<html>

    <head>
        <title><?= $pageTitle ?> - <?= ORGANIZATION_FULL_NAME ?></title>
        <?php
        Required:: html5shiv()->metaTags()->favicon()->css()->sweetModalCSS()->bootstrapGrid()->airDatePickerCSS();
        ?>
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

                    <!-- .content starts -->
                    <div class="content ">
                        <div class="card">
                            <div class="card-title">This is card title [optional]</div>
                            <div class="card-description">This is card description [optional]</div>

                            <form action="recover-userid-processor.php" method="post" class="padding-all-25">
                                <div class="field">
                                    <label class="required">Registration No.</label>
                                    <input type="text" value="" name="regNo" class="validate swiftInteger" data-title="Registration No." data-required="required" data-maxlen="10" data-datatype="integer">
                                </div>

                                <div class="field">
                                    <label class="required">Year</label>
                                    <input type="text" value="" name="regYear" class="validate swiftInteger swiftYear" maxlength="4" data-title="Registration Year" data-required="required" data-maxlen="4" data-datatype="integer">
                                </div>

                                <div class="field">
                                    <label class="required">Mobile No.</label>
                                    <input type="text" value="" name="mobileNo" class="validate swiftInteger" maxlength="13" data-required="required" data-datatype="mobile" data-maxlen="13">
                                </div>


                                <input type="submit" class="btn btn-dark btn-large form-submit-button" value="Submit">
                                <!-- <a class="recover" href="auth/recover-user-id.php">Forget User ID? Click here to recover.</a> -->

                            </form>
                        </div><!-- .card -->

                        <div class="recover-status text-center margin-top-20" style="font-size: 15px;">

                        </div>
                    </div><!-- .content -->

                    <!-- <aside style="display: flex; flex-direction: column;">
                        asdsdaf
                    </aside> -->
                </div><!-- .container -->
            </main>
            <footer>
                <?= Footer::prepare(array()); ?>
            </footer>
        </div>


        <?php
        Required::jquery()->hamburgerMenu()->sweetModalJS()->swiftSubmit()->swiftNumeric()->airDatePickerJS();
        ?>
        <script>
            var base_url = '<?php echo BASE_URL; ?>';
            $(function() {

                // $(".swiftInteger").swiftNumericInput({ allowFloat: false, allowNegative: false });

                function onSuccess(response) {
                    $('div.recover-status').html(response.message);
                }
                $("form").swiftSubmit({
                    redirect: true
                }, null, null, onSuccess, null, null);

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