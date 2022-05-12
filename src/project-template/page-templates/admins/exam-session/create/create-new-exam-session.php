<?php

declare(strict_types=1);

#region imports
require_once('../../../Required.php');

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
                <h1 class="width-full mt-150 accent-fg">Create Exam Session
                    <div class="divider"></div>
                </h1>

                <nav class="left-nav">
                    <?php
                        echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                </nav>
                <!-- .content starts -->
                <div class="content ">
                    <form action="<?=BASE_URL?>/admins/exam-session/create/create-new-exam-session-processor.php" method="post">
                        <div class="d-grid gc-2">
                            <!-- title  --> 
                            <div class="field">  
                                <label class="required">Exam Title</label>
                                <input type="text" name="title" id="title" title="" class="validate" data-title="Exam Title" data-datatype="string" data-required="required" data-minlen="allow null" data-maxlen="150" >
                            </div>
                            <!-- reference  --> 
                            <div class="field">  
                                <label class="required">Exam Reference</label>
                                <input type="text" name="reference" id="reference" title="" class="validate" data-title="Exam Reference" data-datatype="string" data-required="required" data-minlen="allow null" data-maxlen="100" >
                            </div>
                        </div>

                       <div class="d-grid gc-2">
                            <!-- court  --> 
                            <div class="field">  
                                <label class="required">Court Name</label>
                                <input type="text" name="court" id="court" title="" class="validate" data-title="Court Name" data-datatype="string" data-required="required" data-minlen="allow null" data-maxlen="20" value="<?=COURT?>">
                            </div>
                            <!-- applicationType  --> 
                            <div class="field">  
                                <label class="required">Application Type</label>
                                <input type="text" name="applicationType" id="applicationType" title="" class="validate" data-title="Application Type" data-datatype="string" data-required="required" data-minlen="allow null" data-maxlen="20" >
                            </div>
                       </div>

                        <div class="d-grid gc-2">
                            <!-- applicationStartDatetime  --> 
                            <div class="field">  
                                <label class="required">Application Start Datetime</label>
                                <input type="text" name="applicationStartDatetime" id="applicationStartDatetime" title="" class="validate inputMask" data-title="Application Start Datetime"  data-datatype="datetime"  data-required="required">
                            </div>
                            <!-- applicationEndDatetime  --> 
                            <div class="field">  
                                <label class="required">Application End Datetime</label>
                                <input type="text" name="applicationEndDatetime" id="applicationEndDatetime" title="" class="validate inputMask" data-title="Application End Datetime"  data-datatype="datetime"  data-required="required">
                            </div>
                            
                        </div>

                        <div class="d-grid gc-2">
                            <!-- admitCardStartDatetime  --> 
                            <div class="field">  
                                <label class="">Admit Card Start Datetime</label>
                                <input type="text" name="admitCardStartDatetime" id="admitCardStartDatetime" title="" class="validate inputMask" data-title="Admit Card Start Datetime"  data-datatype="date">
                            </div>
                            <!-- admitCardEndDatetime  --> 
                            <div class="field">  
                                <label class="">Admit Card End Datetime</label>
                                <input type="text" name="admitCardEndDatetime" id="admitCardEndDatetime" title="" class="inputMask validate" data-title="Admit Card End Datetime"  data-datatype="datetime">
                            </div>
                        </div>

                        <div class="d-grid gc-2">
                            <!-- regularFeeAmount  --> 
                            <div class="field">  
                                <label class="required">Regular Fee Amount</label>
                                <input type="text" name="regularFeeAmount" id="regularFeeAmount" title="" class="validate integer" data-title="Regular Fee Amount" data-datatype="integer" data-required="required" data-minlen="allow null" data-maxlen="10" >
                            </div>
                            <!-- reappearFeeAmount  --> 
                            <div class="field">  
                                <label class="required">Reappear Fee Amount</label>
                                <input type="text" name="reappearFeeAmount" id="reappearFeeAmount" title="" class="validate integer" data-title="Reappear Fee Amount" data-datatype="integer" data-required="required" data-minlen="allow null" data-maxlen="10" >
                            </div>
                        </div>

                        <div class="d-grid gc-2">
                            <!-- pupilageContractCalculationDate  --> 
                            <div class="field">  
                                <label class="required">Pupilage Contract Calculation Date</label>
                                <input type="text" name="pupilageContractCalculationDate" id="pupilageContractCalculationDate" title="" class="validate date" data-title="Pupilage Contract Calculation Date" data-datatype="date"  data-required="required">
                            </div>
                            <!-- lastDateOfTeletalkFeePayment  --> 
                            <div class="field">  
                                <label class="required">Last Date Of Teletalk Fee Payment</label>
                                <input type="text" name="lastDateOfTeletalkFeePayment" id="lastDateOfTeletalkFeePayment" title="" class="validate" data-title="Last Date Of Teletalk Fee Payment" data-datatype="date" data-required="required" data-minlen="allow null" data-maxlen="20" placeholder="dd-mm-yyyy">
                            </div>
                        </div>
                        
                        <div class="d-grid gc-2">
                            <!-- isActive  --> 
                            <div class="field">  
                                <label class="required">Active Type</label>
                                <select name="isActive" id="isActive" title="" class="validate" data-required="required" >
                                    <option value=""></option>
                                    <option value="yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </div>
                            <!-- circularFileName  --> 
                            <div class="field">  
                                <label class="required mb-025">Circular File Name</label>
                                <br>
                                <label for="circularFileName" class="width-100 btn outline">
                                <input type="file" style="display:none" name="circularFileName" id="circularFileName" title="" class="validate" data-title="Circular File Name" data-required="required" data-minlen="allow null" >Upload Anything
                                </label>
                            </div>
                        </div>

                        <div class="field mt-100">
                            <input class="form-submit-button" type="submit" value="Create">
                        </div>
                    </form>

                </div> <!-- .content ends -->

                <!-- <aside style="display: flex; flex-direction: column;">
                    <div class="card fill-parent">
                       
                    </div>
                </aside> -->
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

            $('.inputMask').inputmask("datetime",{
                //mask: "1-2-y",  
                mask: "1-2-y h:s", 
                placeholder: "dd-mm-yyyy hh:mm", //placeholder: "dd-mm-yyyy hh:mm", 
                leapday: "-02-29", 
                separator: "-", 
                alias: "dd/mm/yyyy hh:mm"
            });
        });
    </script>
    <script src="https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js"></script>
</body>

</html>