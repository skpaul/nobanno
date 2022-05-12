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
                <h1 class="width-full mt-150 accent-fg">Registration Details
                    <div class="divider"></div>
                </h1>

                <nav class="left-nav">
                    <?php
                    echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                </nav>

                <!-- .content starts -->
                <div class="content ">
                <?php

                    $encRegistrationId = trim($_GET["registration-id"]);

                    $registrationId =  $endecryptor->decrypt($encRegistrationId);

                    $sql = "SELECT * FROM lc_enrolment_registrations WHERE registrationId=:registrationId";
                    $regiData = $db->select($sql, array('registrationId' => $registrationId));

                    if(count($regiData) != 1){
                        die($json->fail()->message("Your Registration Data not found!")->create());
                    }
                   $registration = $regiData[0];
                ?>   

                    <form action="" method="">

                    <div class="d-grid gc-2">
                        <!-- regNo  -->
                        <div class="field">
                            <label>Registration No.</label>
                            <input type="text" value="<?=$registration->regNo?>" readonly>
                        </div>

                        <!-- regYear  -->
                        <div class="field">
                            <label>Registration Year</label>
                            <input type="text" value="<?=$registration->regYear?>" readonly>
                        </div>
                    </div>
                        

                    <div class="d-grid gc-3">
                        <!-- name  -->
                        <div class="field">
                            <label>Name</label>
                            <input type="text" value="<?=$registration->name?>" readonly>
                        </div>

                        <!-- fatherName  -->
                        <div class="field">
                            <label>Father Name</label>
                            <input type="text" value="<?=$registration->fatherName?>" readonly>
                        </div>

                        <!-- University Name  -->
                        <div class="field">
                            <label>University Name</label>
                            <input type="text" value="<?=$registration->universityName?>" readonly>
                        </div>
                    </div>
                       
                    <div class="d-grid gc-2">
                        <!-- pupilageContractDate  -->
                        <div class="field">
                            <label>Pupilage Contract Date</label>
                            <input type="text" value="<?=$clock->toString($registration->pupilageContractDate, DatetimeFormat::BdDate())?>" readonly>
                        </div>
                        <!-- seniorAdvocateName  -->
                        <div class="field">
                            <label>Senior Advocate Name</label>
                            <input type="text" value="<?=$registration->seniorAdvocateName?>" readonly>
                        </div>
                    </div>
                       
                    <div class="d-grid gc-2">
                        <!-- applicantType  -->
                        <div class="field">
                            <label>Applicant Type</label>
                            <input type="text" value="<?=$registration->applicantType?>" readonly>
                        </div>
                        <!-- hasBarAtLaw  -->
                        <div class="field">
                            <label>Bar-At-Law</label>
                            <input name="pupilageContractDate" id="pupilageContractDate" title="" type="text" value="<?=($registration->hasBarAtLaw == "1") ? ("Yes") : ("No");?>" readonly>
                            
                        </div>
                    </div>
                        
                        <div class="d-flex mt-100">
                            <a class="btn bg-primary mr-100" href="<?=BASE_URL?>/admins/registration/update/edit-registration.php?registration-id=<?=$encRegistrationId?>&session-id=<?=$encSessionId?>">Edit</a>
                            <!-- <a class="btn bg-primary mr-100" href="<?=BASE_URL?>/admins/registration/delete/delete-registration.php?delete-id=<?=$encRegistrationId?>&session-id=<?=$encSessionId?>">Delete</a> -->
                            <a class="btn outline" href="<?=BASE_URL .'/admins/registration/read/list/registration-list.php?session-id='.$encSessionId?>">Back</a>
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