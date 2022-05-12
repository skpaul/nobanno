<?php
#region Import libraries
require_once("../../Required.php");

Required::Logger()
    ->Database()
    ->EnDecryptor()
    ->JSON()->HttpHeader()
    ->ExclusivePermission()->Clock()->headerBrand()->applicantHeaderNav()->footer();
#endregion

#region Class instance declaration & initialization
$logger = new Logger(ROOT_DIRECTORY); //This must be in first position.
$clock = new Clock();
$endecryptor = new EnDecryptor();
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$hasExclusivePermission = ExclusivePermission::hasPermission();
$pageTitle = "Verify Registration";
#endregion

#region Database connection
$db->connect();
$db->fetchAsObject();
#endregion

#region Check post configuration ('post_configuration' table)
if (!isset($_GET["config-id"]) || empty(trim($_GET["config-id"])))
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.");

$encConfigId = trim($_GET["config-id"]);
$configId = $endecryptor->decrypt($encConfigId);
if (!$configId)  HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.");

if ($hasExclusivePermission) {
    //If has exclusive permission, don't check isActive status
    $sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND configId=:configId";
} else {
    //If does not have exclusive permission, check isActive status.
    $sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND isActive=1 AND configId=:configId";
}

try {
    $configs = $db->select($sql, array('court' => COURT, "applicationType" => APPLICATION_TYPE, "configId" => $configId));
} catch (\DatabaseException $dbExp) {
    $logger->createLog($dbExp->getMessage());
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Something is wrong. Error Code- 4417.");
} catch (\Exception $exp) {
    $logger->createLog($exp->getMessage());
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Something is wrong. Error Code- 4417.");
}

if (count($configs) != 1) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application not available.");

$postConfig = $configs[0];
#endregion

#region Check application start & end datetime
//If does not have exclusive permission, check application start and end datetime.
if (!$hasExclusivePermission) {
    $now = $clock->toDate("now");
    $applicationStartDatetime = $clock->toDate($postConfig->applicationStartDatetime);
    $applicationEndDatetime = $clock->toDate($postConfig->applicationEndDatetime);

    // die("Application will start from " . $clock->toString($postConfig->applicationStartDatetime, DatetimeFormat::BdDatetime()));
    if ($now < $applicationStartDatetime) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application not available.");
    if ($now > $applicationEndDatetime) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application not available.");
}
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
        .sweet-modal-content {
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
                <!-- 
                    <nav class="left-nav">
                        <?php
                        // echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                        ?>
                    </nav> 
                    -->

                <div class="content">
                    <div class="card" style="max-width: 500px; margin:auto;">
                        <div class="card-title">This is card title [optional]</div>
                        <div class="card-description">This is card description [optional]</div>
                        <?php
                        $permissionQueryString = "";
                        if (isset($_GET[ExclusivePermission::$propName]) && !empty($_GET[ExclusivePermission::$propName])) {
                            $permissionQueryString = ExclusivePermission::$propName . "=" . ExclusivePermission::$propValue;
                        }
                        ?>

                        <form id="application-form" action="verify-registration-processor.php?<?= $permissionQueryString ?>" method="post">
                            <input type="hidden" name="configId" value="<?= $encConfigId ?>">
                            <div class="field">
                                <label class="required">Registration No.</label>
                                <input type="text" value="" name="regNo" class="validate swiftInteger" data-title="Registration No." data-required="required" data-maxlen="10" data-datatype="integer">
                            </div>

                            <div class="field">
                                <label class="required">Year</label>
                                <input type="text" value="" name="regYear" class="validate swiftInteger" maxlength="4" data-title="Registration Year" data-required="required" data-maxlen="4" data-datatype="integer">
                            </div>

                            <div class="field">
                                <label class="required">Applicant Type</label> <!-- {$createSerial($infoSerialNo)} -->
                                <select name="applicantType" class="formControl validate" data-required="required">
                                    <option value=""></option>
                                    <option value="Regular">Regular</option>
                                    <option value="Re-appeared">Re-appeared</option>
                                    <!-- <option value="Re-Registration">Re-Registration</option> -->
                                </select>
                            </div>

                            <input type="submit" class="btn btn-dark btn-large form-submit-button" value="Submit">
                            <!-- <a class="recover" href="auth/recover-user-id.php">Forget User ID? Click here to recover.</a> -->
                        </form>
                    </div><!-- .card -->
                </div><!-- .content -->

                <!-- 
                    <aside style="display: flex; flex-direction: column;">
                        asdsdaf
                    </aside> 
                    -->
            </div><!-- .container -->
        </main>
        <footer>
            <?= Footer::prepare(array()) ?>
        </footer>
    </div>

    <?php
    Required::jquery()->hamburgerMenu()->sweetModalJS()->moment()->mobileValidator()->swiftSubmit()->SwiftNumeric();
    ?>
    <script>
        var base_url = '<?php echo BASE_URL; ?>';
        $(function() {
            SwiftNumeric.prepare('.integer');
            $("#application-form").swiftSubmit({}, null, null, null, null, null);
        })
    </script>
</body>

</html>