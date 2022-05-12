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
$clock = new Clock();
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


$encConfigId = trim($_GET["config-id"]);
$configId =  $endecryptor->decrypt($encConfigId);

$sql = "SELECT * FROM post_configurations WHERE configId=:configId";
$configData = $db->select($sql, array('configId' => $configId));

if(count($configData) != 1){
    die($json->fail()->message("Your Exam Sesstion Data not found!")->create());
}
$examSession = $configData[0];
?>

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
                <h1 class="width-full mt-150 accent-fg">Edit Exam Session
                    <div class="divider"></div>
                </h1>

                <nav class="left-nav">
                    <?php
                    echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                </nav>

                <!-- .content starts -->
                <div class="content ">
                    <form action="<?=BASE_URL?>/admins/exam-session/update/edit-exam-session-processor.php?config-id=$encConfigId" method="post">
                        <div class="d-grid gc-2">
                        <input type="hidden" name="configId" id="configId" value="<?=$encConfigId?>">
                            <!-- title  --> 
                            <div class="field">  
                                <label class="required">Exam Title</label>
                                <input type="text" name="title" id="title" title="" class="validate" data-title="Exam Title" data-datatype="string" data-required="required" data-minlen="allow null" data-maxlen="150" value="<?=$examSession->title?>">
                            </div>
                            <!-- reference  --> 
                            <div class="field">  
                                <label class="required">Exam Reference</label>
                                <input type="text" name="reference" id="reference" title="" class="validate" data-title="Exam Reference" data-datatype="string" data-required="required" data-minlen="allow null" data-maxlen="100" value="<?=$examSession->referenceNo?>">
                            </div>
                        </div>
                       <div class="d-grid gc-3">
                            <!-- circularFileName  --> 
                            <div class="field">  
                                <label class="required">Circular File Name</label>
                                <input type="file" name="circularFileName" id="circularFileName" title="" class="validate" data-title="Circular File Name" data-required="required" data-minlen="allow null" value="<?=$examSession->circularFileName?>">
                            </div>
                            <!-- court  --> 
                            <div class="field">  
                                <label class="required">Court</label>
                                <input type="text" name="court" id="court" title="" class="validate" data-title="Court" data-datatype="string" data-required="required" data-minlen="allow null" data-maxlen="20" value="<?=$examSession->court?>">
                            </div>
                            <!-- applicationType  --> 
                            <div class="field">  
                                <label class="required">Application Type</label>
                                <input type="text" name="applicationType" id="applicationType" title="" class="validate" data-title="Application Type" data-datatype="string" data-required="required" data-minlen="allow null" data-maxlen="20" value="<?=$examSession->applicationType?>">
                            </div>
                            <!-- pupilageContractCalculationDate  --> 
                       </div>
                        <div class="d-grid gc-4">
                            <div class="field">  
                                <label class="required">Pupilage Contract Calculation Date</label>
                                <input type="text" name="pupilageContractCalculationDate" id="pupilageContractCalculationDate" title="" class="validate date" data-title="Pupilage Contract Calculation Date" data-datatype="date"  data-required="required"  data-minval="" data-maxval="" value="<?=$clock->toString($examSession->pupilageContractCalculationDate, DatetimeFormat::BdDate())?>">
                            </div>
                            <!-- isActive  --> 
                            <div class="field">  
                                <label class="required">Active Type</label>
                                    <select name="isActive" id="isActive" title="" class="validate" data-required="required" >
                                    <option value=""></option>
                                    <option value="yes" <?=$examSession->isActive == "1" ? 'selected' : '' ?>>Yes</option>
                                    <option value="no" <?=$examSession->isActive == "0" ? 'selected' : '' ?>>No</option>
                                </select>
                                    </select>
                            </div>
                            <!-- applicationStartDatetime  --> 
                            <div class="field">  
                                <label class="required">Application Start Datetime</label>
                                <input type="text" name="applicationStartDatetime" id="applicationStartDatetime" title="" class="validate date" data-title="Application Start Datetime"  data-datatype="date"  data-required="required"  data-minval="" data-maxval="" value="<?=$clock->toString($examSession->applicationStartDatetime, DatetimeFormat::BdDate())?>">
                            </div>
                            <!-- applicationEndDatetime  --> 
                            <div class="field">  
                                <label class="required">Application End Datetime</label>
                                <input type="text" name="applicationEndDatetime" id="applicationEndDatetime" title="" class="validate date" data-title="Application End Datetime"  data-datatype="date"  data-required="required"  data-minval="" data-maxval="" value="<?=$clock->toString($examSession->applicationEndDatetime, DatetimeFormat::BdDate())?>">
                            </div>
                            
                        </div>
                        <div class="d-grid gc-2">
                            <!-- admitCardStartDatetime  --> 
                            <div class="field">  
                                <label class="required">Admit Card Start Datetime</label>
                                <input type="text" name="admitCardStartDatetime" id="admitCardStartDatetime" title="" class="validate date" data-title="Admit Card Start Datetime"  data-datatype="date"  data-required="required"  data-minval="" data-maxval="" value="<?=$clock->toString($examSession->admitCardStartDatetime, DatetimeFormat::BdDate())?>">
                            </div>
                            <!-- admitCardEndDatetime  --> 
                            <div class="field">  
                                <label class="required">Admit Card End Datetime</label>
                                <input type="text" name="admitCardEndDatetime" id="admitCardEndDatetime" title="" class="validate date" data-title="Admit Card End Datetime"  data-datatype="date"  data-required="required"  data-minval="" data-maxval="" value="<?=$clock->toString($examSession->admitCardEndDatetime, DatetimeFormat::BdDate())?>">
                            </div>
                        </div>
                        <div class="d-grid gc-3">
                            <!-- regularFeeAmount  --> 
                            <div class="field">  
                                <label class="required">Regular Fee Amount</label>
                                <input type="text" name="regularFeeAmount" id="regularFeeAmount" title="" class="validate integer" data-title="Regular Fee Amount" data-datatype="integer" data-required="required" data-minlen="allow null" data-maxlen="10" value="<?=$examSession->regularFeeAmount?>">
                            </div>
                            <!-- reappearFeeAmount  --> 
                            <div class="field">  
                                <label class="required">Reappear Fee Amount</label>
                                <input type="text" name="reappearFeeAmount" id="reappearFeeAmount" title="" class="validate integer" data-title="Reappear Fee Amount" data-datatype="integer" data-required="required" data-minlen="allow null" data-maxlen="10" value="<?=$examSession->reappearFeeAmount?>">
                            </div>
                            <!-- lastDateOfTeletalkFeePayment  --> 
                            <div class="field">  
                                <label class="required">Last Date Of Teletalk Fee Payment</label>
                                <input type="text" name="lastDateOfTeletalkFeePayment" id="lastDateOfTeletalkFeePayment" title="" class="validate" data-title="Last Date Of Teletalk Fee Payment" data-datatype="date" data-required="required" data-minlen="allow null" data-maxlen="20" placeholder="dd-mm-yyyy" value="<?=$clock->toString($examSession->lastDateOfTeletalkFeePayment, DatetimeFormat::BdDate())?>">
                            </div>
                        </div>

                        <div class="field mt-100">
                            <input class="form-submit-button" type="submit" value="Edit">
                            <a class="btn outline" href="<?=BASE_URL?>/admins/exam-session/read/list/exam-session-list.php">Back</a>
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
        });
    </script>
</body>

</html>