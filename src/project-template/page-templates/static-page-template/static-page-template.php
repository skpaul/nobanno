<?php


require_once("../../Required.php");

Required::Logger()
    ->Database()->HttpHeader()->DbSession()
    ->EnDecryptor()
    ->JSON()
    ->ExclusivePermission()->Clock()->headerBrand()->applicantHeaderNav()->footer();


$logger = new Logger(ROOT_DIRECTORY);
$endecryptor = new EnDecryptor();
$json = new JSON();
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$clock = new Clock();
$db->connect();
$db->fetchAsObject();
$session = new DbSession($db, "lc_enrolment_sessions");

//first check if session exists
if (!isset($_GET["session-id"]) || empty(trim($_GET["session-id"]))) {
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session request.");
}

try {
    $encSessionId = trim($_GET["session-id"]);
    $sessionId = $endecryptor->decrypt($encSessionId);
    $session->continue($sessionId);
} catch (\SessionException $th) {
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session request.");
} catch (\Exception $exp) {
    $logger->createLog($exp->getMessage());
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Database operation failed.");
}


//if config id not found in GET variables, redirect to sorry page.
if (!isset($_GET["config-id"]) || empty(trim($_GET["config-id"]))) {
    HttpHeader::redirect(BASE_URL . "/sorry.php");
}
$encConfigId = trim($_GET["config-id"]); //decryption has been done in the following try .. catch block for safety reason.


if (!isset($_GET["registration-id"]) || empty(trim($_GET["registration-id"]))) {
    HttpHeader::redirect(BASE_URL . "/sorry.php");
}
$encRegistrationId = trim($_GET["registration-id"]); //decryption has been done in the following try .. catch block for safety reason.

try {
    $configId = $endecryptor->decrypt($encConfigId);
    $registrationId = $endecryptor->decrypt($encRegistrationId);

    $db->connect();
    $db->fetchAsObject();

    $hasExclusivePermission = ExclusivePermission::hasPermission();

    //If has exclusive permission, don't check isActive status.
    //If does not have exclusive permission, check isActive status.
    if ($hasExclusivePermission) {
        $sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND configId=:configId";
    } else {
        $sql = "SELECT * FROM `post_configurations` WHERE isActive=1 AND court=:court AND applicationType = :applicationType AND configId=:configId";
    }

    $configs = $db->select($sql, array('court' => COURT, "applicationType" => APPLICATION_TYPE, "configId" => $configId));
} catch (\DatabaseException $dbExp) {
    $logger->createLog($dbExp->getMessage());
    HttpHeader::redirect(BASE_URL . "/sorry.php");
} catch (\Exception $exp) {
    $logger->createLog($exp->getMessage());
    HttpHeader::redirect(BASE_URL . "/sorry.php");
}

//whether exclusive permission exists or not, the post configuration must exist.
if (count($configs) != 1) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application is not available.");

$postConfig = $configs[0];

//If does not have exclusive permission, check application start and end datetime.
if (!$hasExclusivePermission) {
    $now = $clock->toDate("now");
    $applicationStartDatetime = $clock->toDate($postConfig->applicationStartDatetime);
    $applicationEndDatetime = $clock->toDate($postConfig->applicationEndDatetime);
    if ($now < $applicationStartDatetime) {
        $time = $clock->toString($postConfig->applicationStartDatetime, DatetimeFormat::BdDatetime());
        HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application will start from $time.");
    }
    if ($now > $applicationEndDatetime) {
        HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application is not available.");
    }
}

$sql = "SELECT * FROM lc_enrolment_registrations WHERE registrationId=:registrationId ";
$registrations = $db->select($sql, array('registrationId' => $registrationId));

if (count($registrations) != 1) {
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Registration not found or invalid registration information.");
}

$registration = $registrations[0];

?>

<!DOCTYPE html>
<html>

    <head>
        <title>Declarations - <?= ORGANIZATION_FULL_NAME ?></title>
        <?php
            Required:: html5shiv()->metaTags()->favicon()->css()->sweetModalCSS()->bootstrapGrid();
        ?>

        <style>
            .card{
                padding: 30px 50px;
                text-align: left;
            }

            ul.declarations{
                list-style-position: inside;
                margin-bottom: 30px;
            }

            ul.declarations>li{
                margin-bottom: 8px;
                list-style: disc;
            }

            .go-to-next{
                border: 1px solid #bfbfbf;
                padding: 5px 12px;
                border-radius: 5px;
                text-decoration: none;
                color:#3d4e5e;
                display: none;
                font-weight: 700;
            }
        
            .go-to-next:hover{
                background-color: rgba(155,155,155,0.1);
            }
        </style>
    </head>

<body>
    <!-- <div id="version"></div> -->
    <div class="master-wrapper">
        <header>
            <?php
             echo HeaderBrand::prepare(BASE_URL, false);
             echo ApplicantHeaderNav::prepare(BASE_URL);
            ?>
        </header>
        <main>
            <div class="container">
                <div class="logout-button-container">
                    <a href="<?= BASE_URL ?>/logout.php?session-id=<?= $encSessionId ?>">
                        <img src="<?= BASE_URL ?>/assets/images/logout-icon.png" alt="Logout button" srcset=""> Logout
                    </a>
                </div>

                <h2 class="text-center">Declaration</h2>
                <h4 class="text-center" style="font-size: 18px; margin-bottom:35px;">Application for enrolment as Advocate</h4>

                <!-- 
                    <nav class="left-nav">
                    <?php
                    // echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                    </nav> 
                -->

                <div class="content">
                    <div class="card">
                        <ul class="declarations">
                            <li>
                            I am not engaged in any trade, business or service.
                            </li>
                            <li>
                            I was not dismissed from Government service or public statutory corporation on charge of moral turpitude.
                            </li>
                            <li>
                            No criminal proceeding or proceedings for professional misconduct were instituted against me in any country.
                            </li>
                            <li>
                            I was not convicted by any court in Bangladesh or outside of Bangladesh. 
                            </li>
                            <li>
                            The information I am going to provide in this Online Application are true and accurate.
                            </li>
                            <li>
                            I shall be bound to submit all the necessary hard copy documents, as required by the Exam Rules, to Bar Council Office before the Viva Voce Exam in support of my candidacy.
                            </li>
                            <li>
                            I do hereby solemnly declare that the decision of Enrolment Committee shall be final and binding upon me in respect of any matter which is not specifically mentioned in the MCQ Exam Online Form Fill Up Notice of the Bangladesh Bar Council published on 24th March, 2022, vide memo No. BBC/Enrol/2022/927, Date: 24-03-2022.
                            </li>
                            <li>
                            The statements made above are true to my knowledge and belief and I put my digital signature in this online form fully knowing the contents of it.
                            </li>
    
                            
                        </ul>

                        <div style="text-align: left;">
                            <input type="checkbox" id="chkAgree" value="">&nbsp;&nbsp; I've read the above information carefully.<br><br>
                        </div>

                        <div class="text-center" style="height:25px;">
                            <?php
                                $exclusivePermissionString = "";
                                if (isset($_GET[ExclusivePermission::$propName]) && !empty($_GET[ExclusivePermission::$propName])) {
                                    $exclusivePermissionString = "&" . ExclusivePermission::$propName . "=" . ExclusivePermission::$propValue;
                                }

                                $nextQueryString  = "registration-id=$encRegistrationId&config-id=$encConfigId&session-id=$encSessionId" . $exclusivePermissionString;
                            ?>
                                <a class="go-to-next" id="goToNext" href="../application-form/form.php?<?= $nextQueryString ?>">Next</a>
                        </div>

                    </div>
                </div><!-- .content// -->

                <!-- 
                <aside>
                    This is right aside.
                </aside> 
                -->
            </div><!-- .container// -->
        </main>
        <footer>
            <?php
             echo Footer::prepare();
            ?>
        </footer>
    </div>

    <?php
    Required::jquery()->hamburgerMenu()->sweetModalJS()->swiftSubmit()->SwiftNumeric();
    ?>
    <script>
        var base_url = '<?php echo BASE_URL; ?>';
        $(function() {
            SwiftNumeric.prepare('.integer');

            $("form").swiftSubmit({
                redirect: true
            }, null, null, null, null, null);
        })


         // var isChecked =  $("input:radio[name='"+inputName+"']").is(":checked");

        //Check Button Action
        $("#chkAgree").change(function() {
            let isChecked = $(this).is(':checked');
            if(isChecked)
                $("#goToNext").show();
            else
                $("#goToNext").hide();
        });
    </script>

</body>

</html>