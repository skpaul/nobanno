<?php

declare(strict_types=1);

#region imports
require_once('../../../../Required.php');

Required::Logger()
    ->Database()->DbSession()->EnDecryptor()->HttpHeader()->Clock()
    ->adminLeftNav()->headerBrand()->applicantHeaderNav()->footer(2);
#endregion

#region declarations
$logger = new Logger(ROOT_DIRECTORY);
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$endecryptor = new EnDecryptor();
#endregion

$db->connect();
$db->fetchAsObject();

#region check session
    if(!isset($_GET["session-id"]) || empty(trim($_GET["session-id"]))){
        HttpHeader::redirect(BASE_URL . "/admins/sorry.php?msg=Invalid session request. Error Code- 41365.");
    }

    $encSessionId = trim($_GET["session-id"]);

    try {
        $sessionId = (int)$endecryptor->decrypt($encSessionId);
        $session = new DbSession($db, "admin_sessions");
        $session->continue($sessionId);
        $roleCode = $session->getData("roleCode");
    } catch (\SessionException $th) {
        // $logger->createLog($th->getMessage());
        HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again. Error Code-456815.");
    } catch (\Exception $exp) {
        HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again. Error Code-774965.");
    }
#endregion


?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Admin || <?= ORGANIZATION_FULL_NAME ?></title>
    <?php
    Required::metaTags()->favicon()->teletalkCSS(2)->sweetModalCSS()->airDatePickerCSS()->overlayScrollbarCSS();
    ?>
</head>

<body>
    <div class="master-wrapper">
        <header>
            <?php
            echo HeaderBrand::prepare(BASE_URL, true);
            echo ApplicantHeaderNav::prepare(BASE_URL);
            ?>
        </header>

        <main class="">
            <div class="container-fluid d-flex flex-wrap">
                <h1 class="width-full mt-150 accent-fg">New Registrations
                    <div class="divider"></div>
                </h1>

                <nav class="left-nav">
                    <?php
                    echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                </nav>

                <!-- .content starts -->
                <div class="content ">

                    <form action="<?=BASE_URL?>/admins/registration/create/insert-new-registration.php?session-id=<?=$encSessionId?>" method="post">
                        <div class="d-grid gc-2">
                            <!-- regNo  -->
                            <div class="field">
                                <label class="required">Registration No.</label>
                                <input name="regNo" id="regNo" title="" class="validate integer" data-datatype="integer" data-required="required" data-maxlen="5" maxlength="5" type="text">
                            </div>

                            <!-- regYear  -->
                            <div class="field">
                                <label class="required">Registration Year</label>
                                <input name="regYear" id="regYear" title="" class="validate integer year" data-datatype="string" data-required="required" data-exactlen="4" type="text">
                            </div>
                        </div>
                    
                        <div class="d-grid gc-2">
                             <!-- name  -->
                            <div class="field">
                                <label class="required">Name</label>
                                <input name="name" id="name" title="" class="validate ucase" data-datatype="string" data-required="required"  data-maxlen="150" maxlength="150" type="text">
                            </div>

                            <!-- fatherName  -->
                            <div class="field">
                                <label class="required">Father Name</label>
                                <input name="fatherName" id="fatherName" title="" class="validate ucase" data-datatype="string" data-required="required" data-maxlen="150" type="text" maxlength="150">
                            </div>
                            
                        </div>

                        <div class="d-grid gc-1">
                            <!-- universityName  --> 
                            <div class="field">  
                                <label class="required">University Name</label>
                                <input name="universityName" id="universityName" title="" class="validate ucase" data-title="universityName" data-datatype="string" data-required="required"  data-maxlen="150" maxlength="150" type="text">
                            </div>
                        </div>
                        
                        <div class="d-grid gc-2">
                            <!-- pupilageContractDate  -->
                            <div class="field">
                                <label class="required">Pupilage Contract Date</label>
                                <input name="pupilageContractDate" id="pupilageContractDate" title="" class="validate date" data-title="Pupilage Contract Date" data-datatype="date" data-required="required" type="text">
                            </div>
                            <!-- seniorAdvocateName  -->
                            <div class="field">
                                <label class="required">Senior Advocate Name</label>
                                <input name="seniorAdvocateName" id="seniorAdvocateName" title="" class="validate ucase" data-datatype="string" data-required="required" data-maxlen="150" maxlength="150" type="text">
                            </div>
                        </div>
            
                        <?php
                            $applicantTypes = $db->select("select applicantTypeName as `name` from lc_applicant_types order by applicantTypeId");   
                            $applicantTypeOptions = "";
                            foreach ($applicantTypes as $type) {
                                $option = $type->name;
                                $applicantTypeOptions .= <<<HTML
                                    <option value="$option">$option</option>
                                HTML;
                            }
                        ?>
                       
                     

                       <div class="row">
                           <div class="col">
                                <!-- applicantType  -->
                                <div class="field">
                                    <label class="required">Applicant Type</label>
                                    <select name="applicantType" id="" title="Applicant Type" class="validate" data-required="required" >
                                        <option value="">Select</option>
                                        <?=$applicantTypeOptions?>
                                    
                                    </select>
                                </div>
                           </div>
                           <div class="col">
                                <!-- hasBarAtLaw  -->
                                <div class="field">
                                    <label class="required">Re-Registration?</label>
                                    <select name="isReRegistered" title="Re-Registration Information" class="validate" data-required="required" >
                                        <option value=""></option>
                                        <option value="yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                           </div>
                           <div class="col">
                                <!-- hasBarAtLaw  -->
                                <div class="field">
                                    <label class="required">Bar-At-Law?</label>
                                    <select name="hasBarAtLaw" title="Bar-at-law Information" class="validate" data-required="required" >
                                        <option value=""></option>
                                        <option value="yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                           </div>
                       </div>

                        <div class="field">
                            <input class="form-submit-button" type="submit" value="Submit">
                        </div>
                    </form>
                </div> <!-- .content ends -->
            </div>
        </main>

        <footer>
            <?php
            echo Footer::prepare();
            ?>
        </footer>
    </div> <!-- master-wrapper ends-->

    <script>
        var baseUrl = '<?php echo BASE_URL; ?>';
    </script>
    <?php
    Required::jquery()->hamburgerMenu(2)->adminLeftNavJS()->overlayScrollbarJS()->moment()->swiftSubmit()->SwiftNumeric()->sweetModalJS()->airDatePickerJS();
    ?>

    <script>
        $(document).ready(function() {

            SwiftNumeric.prepare('.integer');
            $('.date').datepicker({
                language: 'en',
                dateFormat: 'dd-mm-yyyy',
                autoClose: true,
                onSelect: function(formattedDate, date, inst) {
                    $(inst.el).trigger('change');
                    $(inst.el).removeClass('error');
                }
            })

            //Allow user to select only year from datepicker
            $('.year').datepicker({
                language: 'en',
                dateFormat: "yyyy", 
                autoClose:true,
                showOn: "button",
                minView: 'years',
                view:"years",
                onSelect: function(formattedDate, date, inst) {
                    $(inst.el).trigger('change');
                    $(inst.el).removeClass('error');
                }
            })

            $('form').swiftSubmit({},null, null, null, null, null);
            
            //remove red border -->
                //propertychange change keyup paste input
                $("input[type=text]").on('propertychange change keyup paste input', function() {
                    $(this).removeClass("error");
                });

                $("select").on('change propertychange paste', function() {
                    $(this).removeClass("error");
                });

                $("textarea").on('input propertychange paste', function() {
                    $(this).removeClass("error");
                });

                $("input[type=radio]").change(function(){
                    $(this).closest("div.radio-group").removeClass("error");
                });
            //<-- remove red border

            $('.overlayScroll').overlayScrollbars({
                className: 'os-theme-round-light',
                scrollbars: {
                    visibility: "auto",
                    autoHide: 'leave',
                    autoHideDelay: 100
                }
            });
        });
    </script>
</body>

</html>