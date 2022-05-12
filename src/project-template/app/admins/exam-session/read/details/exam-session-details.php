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
                <h1 class="width-full mt-150 accent-fg">Exam Session Details
                    <div class="divider"></div>
                </h1>

                <nav class="left-nav">
                    <?php
                    echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                </nav>
                

                <!-- .content starts -->
                <div class="content ">
                    <form action="">
                        <div class="d-grid gc-2">
                            <!-- title  --> 
                            <div class="field">  
                                <label class="required">Exam Title</label>
                                <input type="text" value="<?=$examSession->title?>" readonly>
                            </div>
                            <!-- reference  --> 
                            <div class="field">  
                                <label class="required">Exam Reference</label>
                                <input type="text" value="<?=$examSession->referenceNo?>"  readonly>
                            </div>
                        </div>
                       <div class="d-grid gc-3">
                            <!-- circularFileName  --> 
                            <div class="field">  
                                <label class="required">Circular File Name</label>
                                <input type="text" value="<?=$examSession->circularFileName?>" readonly>
                            </div>
                            <!-- court  --> 
                            <div class="field">  
                                <label class="required">Court</label>
                                <input type="text" value="<?=$examSession->court?>" readonly>
                            </div>
                            <!-- applicationType  --> 
                            <div class="field">  
                                <label class="required">Application Type</label>
                                <input type="text" value="<?=$examSession->applicationType?>">
                            </div>
                            <!-- pupilageContractCalculationDate  --> 
                       </div>
                        <div class="d-grid gc-4">
                            <div class="field">  
                                <label class="required">Pupilage Contract Calculation Date</label>
                                <input type="text" value="<?=$clock->toString($examSession->pupilageContractCalculationDate, DatetimeFormat::BdDate())?>" readonly>
                            </div>
                            <!-- isActive  --> 
                            <div class="field">  
                                <label class="required">Active Type</label>
                                <input type="text"value="<?=($examSession->isActive == "1") ? ("Yes") : ("No"); ?>" readonly>
                            </div>
                            <!-- applicationStartDatetime  --> 
                            <div class="field">  
                                <label class="required">Application Start Datetime</label>
                                <input type="text" value="<?=$clock->toString($examSession->applicationStartDatetime, DatetimeFormat::BdDate())?>" readonly>
                            </div>
                            <!-- applicationEndDatetime  --> 
                            <div class="field">  
                                <label class="required">Application End Datetime</label>
                                <input type="text" value="<?=$clock->toString($examSession->applicationEndDatetime, DatetimeFormat::BdDate())?>" readonly>
                            </div>
                            
                        </div>
                        <div class="d-grid gc-2">
                            <!-- admitCardStartDatetime  --> 
                            <div class="field">  
                                <label class="required">Admit Card Start Datetime</label>
                                <input type="text" value="<?=$clock->toString($examSession->admitCardStartDatetime, DatetimeFormat::BdDate())?>" readonly>
                            </div>
                            <!-- admitCardEndDatetime  --> 
                            <div class="field">  
                                <label class="required">Admit Card End Datetime</label>
                                <input type="text" value="<?=$clock->toString($examSession->admitCardEndDatetime, DatetimeFormat::BdDate())?>" readonly>
                            </div>
                        </div>
                        <div class="d-grid gc-3">
                            <!-- regularFeeAmount  --> 
                            <div class="field">  
                                <label class="required">Regular Fee Amount</label>
                                <input type="text" value="<?=$examSession->regularFeeAmount?>" readonly>
                            </div>
                            <!-- reappearFeeAmount  --> 
                            <div class="field">  
                                <label class="required">Reappear Fee Amount</label>
                                <input type="text" value="<?=$examSession->reappearFeeAmount?>" readonly>
                            </div>
                            <!-- lastDateOfTeletalkFeePayment  --> 
                            <div class="field">  
                                <label class="required">Last Date Of Teletalk Fee Payment</label>
                                <input type="text" value="<?=$clock->toString($examSession->lastDateOfTeletalkFeePayment, DatetimeFormat::BdDate())?>" readonly>
                            </div>
                        </div>

                        <div class="d-flex mt-100">
                            <a class="btn bg-primary mr-100" href="<?=BASE_URL?>/admins/exam-session/update/edit-exam-session.php?config-id=<?=$encConfigId?>">Edit</a>
                            <a class="btn bg-primary mr-100" href="<?=BASE_URL?>/admins/exam-session/delete/delete-exam-session.php?config-id=<?=$encConfigId?>">Delete</a>
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