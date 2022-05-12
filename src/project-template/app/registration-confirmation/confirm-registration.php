<?php
    #region Required
        require_once("../../Required.php");

        Required::Logger()
            ->Database()->DbSession()->HttpHeader()
            ->EnDecryptor()
            ->JSON()
            ->AgeCalculator()
            ->ExclusivePermission()->Clock()->headerBrand()->applicantHeaderNav()->footer();
    #endregion

    #region Variable initialization
        $logger = new Logger(ROOT_DIRECTORY);
        $endecryptor = new EnDecryptor();
        $json = new JSON();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
        $clock = new Clock();
        $hasExclusivePermission = ExclusivePermission::hasPermission();
    #endregion

    #region Database connection
        $db->connect();
        $db->fetchAsObject();
    #endregion

    #region Session 
        $session = new DbSession($db, "lc_enrolment_sessions"); //Must connect to database before initialize session class.

        //first check if session exists
        if (!isset($_GET["session-id"]) || empty(trim($_GET["session-id"]))) {
            HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session.");
        }

        $encSessionId = trim($_GET["session-id"]);
        $sessionId = $endecryptor->decrypt($encSessionId);

        if (!$sessionId)  HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session.");

        try {
            $session->continue($sessionId);
        } catch (\SessionException $th) {
            HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session request. Error Code-456815.");
        }

    #endregion

    #region Check GET variableas
        //if config id not found in GET variables, redirect to sorry page.
        if (!isset($_GET["config-id"]) || empty(trim($_GET["config-id"]))) {
            HttpHeader::redirect(BASE_URL . "/sorry.php");
        }

        $encConfigId = trim($_GET["config-id"]);

        $configId = $endecryptor->decrypt($encConfigId);
        if (!$configId)  HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid post config.");


        if (!isset($_GET["registration-id"]) || empty(trim($_GET["registration-id"]))) {
            HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid registration information.");
        }

        $encRegistrationId = trim($_GET["registration-id"]); //decryption has been done in the following try .. catch block for safety reason.
        $registrationId = $endecryptor->decrypt($encRegistrationId);
        if (!$registrationId)  HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid registration information.");

    #endregion

    #region Check post config
        //If has exclusive permission, don't check isActive status. Otherwise, check isActive status.
        if ($hasExclusivePermission) {
            $sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND configId=:configId";
        } else {
            $sql = "SELECT * FROM `post_configurations` WHERE isActive=1 AND court=:court AND applicationType = :applicationType AND configId=:configId";
        }

        $configs = $db->select($sql, array('court' => COURT, "applicationType" => APPLICATION_TYPE, "configId" => $configId));

        if (count($configs) != 1) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application is not available.");
        $postConfig = $configs[0];
    #endregion

    try {
    } catch (\DatabaseException $dbExp) {
        $logger->createLog($dbExp->getMessage());
        HttpHeader::redirect(BASE_URL . "/sorry.php");
    } catch (\Exception $exp) {
        $logger->createLog($exp->getMessage());
        HttpHeader::redirect(BASE_URL . "/sorry.php");
    }

    #region Check application start & end datetime
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
    #endregion

    $sql = "SELECT * FROM lc_enrolment_registrations WHERE registrationId=:registrationId";
    $registrations = $db->select($sql, array('registrationId' => $registrationId));

    if (count($registrations) != 1) {
        HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Registration not found or invalid registration information.");
    }

    $registration = $registrations[0];

    $canProceedToNext = true;
    $warning = "";

    require_once(ROOT_DIRECTORY . "/functions/check-registration-validity.php");

    $regiValidityResult = checkRegistrationValidity($registration);

    #region Pupilage Contract Date validation 
        $hasValidPupilageContractDuration = true;
        $invalidContractDurationMessage = "";
        if ($regiValidityResult["canProceed"] == true) {

            $applicantType = strtolower($registration->applicantType);
            $hasBarAtLaw = $registration->hasBarAtLaw;
            $isReRegistered = $registration->isReRegistered;
            $pupilageContractDate =  $clock->toDate($registration->pupilageContractDate);

            $sixMonthsPupilageCalculationDate = $clock->toDate($postConfig->sixMonthsPupilageCalculationDate);
            $fiveYearsPupilageCalculationDate = $clock->toDate($postConfig->fiveYearsPupilageCalculationDate);

            #region 6 Months calculation
            if ($pupilageContractDate > $sixMonthsPupilageCalculationDate) {
                $hasValidPupilageContractDuration = false;
                $invalidContractDurationMessage = "Pupilage contract date must be earlier than " . $clock->toString($sixMonthsPupilageCalculationDate, DatetimeFormat::BdDate()) . ".";
            } else {
                $difference = AgeCalculator::calculate($pupilageContractDate, $sixMonthsPupilageCalculationDate, false);

                if ($hasBarAtLaw == 0 && $isReRegistered == 0) {
                    if ($difference->y == 0) {
                        if ($difference->m < 6) {
                            $hasValidPupilageContractDuration = false;
                            $invalidContractDurationMessage = "Minimum six months pupilage duration required.";
                        }
                    }
                }
            }
            #endregion

            #region 5 years calculation
            if ($pupilageContractDate > $fiveYearsPupilageCalculationDate) {
                $hasValidPupilageContractDuration = false;
                $invalidContractDurationMessage = "Pupilage contract date must be earlier than " . $clock->toString($fiveYearsPupilageCalculationDate, DatetimeFormat::BdDate()) . ".";
            } else {
                $difference = AgeCalculator::calculate($pupilageContractDate, $fiveYearsPupilageCalculationDate, false);

                if ($difference->y > 6) {
                    $hasValidPupilageContractDuration = false;
                    $invalidContractDurationMessage = "Pupilage contract period must be equal to or less than six years.";
                }

                //if Years=6, check whether months > 0.
                //If years = 6 and months = 0, check whether days > 0.
                if ($difference->y == 6) {
                    if ($difference->m > 0) {
                        $hasValidPupilageContractDuration = false;
                        $invalidContractDurationMessage = "Pupilage contract period must be equal to or less than six years.";
                    } else {
                        if ($difference->days > 0) {
                            $hasValidPupilageContractDuration = false;
                            $invalidContractDurationMessage = "Pupilage contract period must be equal to or less than six years.";
                        }
                    }
                }
            }
            #endregion

        }
    #endregion
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Confirm Registration - <?= ORGANIZATION_FULL_NAME ?></title>
        <?php
        Required::html5shiv()->gtag()->metaTags()->favicon()->css()->sweetModalCSS()->bootstrapGrid();
        ?>
    </head>

    <body>
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
                    
                    <h2 class="text-center">Confirm Registration</h2>
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
                            <form action="#" method="post">
                                <?php
                                if ($regiValidityResult["canProceed"] == false) {
                                    echo '<div style="color:red; margin-bottom:10px;">' . $regiValidityResult["warning"] . '</div>';
                                }

                                if ($hasValidPupilageContractDuration == false) {
                                    echo '<div style="color:red; margin-bottom:10px;">' . $invalidContractDurationMessage . '</div>';
                                }

                                //check whether there is any declined message
                                // $sql = "SELECT adminMessage FROM lc_enrolment_registrations_update_request WHERE hasApproved<>'pending' AND regNo=:regNo AND regYear=:regYear";
                                ?>
                                <div class="field">
                                    <label>Registration No.</label>
                                    <input type="text" class="no-border" value="<?= $registration->regNo ?>" name="regNo" readonly>
                                </div>

                                <div class="field">
                                    <label>Year</label>
                                    <input type="text" class="no-border" value="<?= $registration->regYear ?>" readonly>
                                </div>
                                <div class="field">
                                    <label>Name :</label>
                                    <input type="text" class="no-border" value="<?= strtoupper($registration->name) ?>" readonly>
                                </div>
                                <div class="field">
                                    <label>Father Name :</label>
                                    <input type="text" class="no-border" value="<?= strtoupper($registration->fatherName) ?>" readonly>
                                </div>
                                <div class="field">
                                    <label>Senior Advocate Name :</label>
                                    <input type="text" class="no-border" value="<?= strtoupper($registration->seniorAdvocateName) ?>" readonly>
                                </div>
                                <div class="field">
                                    <label>Pupilage Contract Date :</label>
                                    <input type="text" class="no-border" value="<?= isset($registration->pupilageContractDate) == true ? $clock->toString($registration->pupilageContractDate, DatetimeFormat::BdDate()) : "" ?>" readonly>
                                </div>

                                <div class="field">
                                    <label>Applicant Type :</label>
                                    <input type="text" class="no-border" value="<?= strtoupper($registration->applicantType) ?>" readonly>
                                </div>

                                <div class="field">
                                    <label>University Name :</label>
                                    <input type="text" class="no-border" value="<?= strtoupper($registration->universityName) ?>" readonly>
                                </div>

                                <br>
                                <!-- <p class="info-note">
                                    Please read the above information carefully. If the information is correct, you may proceed to the application form. 
                                    Otherwise, you can edit the information. <br><br>Your correction will be available only after approval of the Bar Council authority.
                                </p> -->

                                <?php
                                $exclusivePermissionString = "";
                                if (isset($_GET[ExclusivePermission::$propName]) && !empty($_GET[ExclusivePermission::$propName])) {
                                    $exclusivePermissionString = "&" . ExclusivePermission::$propName . "=" . ExclusivePermission::$propValue;
                                }

                                $nextQueryString  = "registration-id=$encRegistrationId&config-id=$encConfigId&session-id=$encSessionId" . $exclusivePermissionString;
                                ?>

                                <div class="buttons" style="justify-content: center !important;">
                                    <!-- <a class="" href="../registration-correction/correction-request.php?<?= $nextQueryString ?>">I want to update the above information</a> -->
                                    <?php
                                    $nextButtonHtml = <<<HTML
                                            <a class="btn" href="../declaration/declaration.php?$nextQueryString">NEXT</a>
                                        HTML;

                                    if ($regiValidityResult["canProceed"] == true && $hasValidPupilageContractDuration == true) {
                                        echo $nextButtonHtml;
                                    }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div><!-- .content -->

                    <!-- 
                    <aside style="display: flex; flex-direction: column;">
                        asdsdaf
                    </aside> 
                    -->

                </div><!-- .container// -->
            </main>
            <footer>
                <?php
                echo Footer::prepare();
                ?>
            </footer>
        </div><!-- .master-wrapper// -->
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
        </script>

    </body>
</html>