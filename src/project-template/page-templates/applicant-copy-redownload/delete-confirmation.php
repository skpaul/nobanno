<?php

require_once("../Required.php");

Required::Logger()
    ->Database()->DbSession()
    ->Clock()
    ->EnDecryptor()
    ->JSON()
    ->Validable()
    ->AgeCalculator(2)
    ->Imaging()
    ->UniqueCodeGenerator()
    ->Helpers()->ExclusivePermission()->HttpHeader();


$logger = new Logger(ROOT_DIRECTORY);
$endecryptor = new EnDecryptor();
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$form = new Validable();
$clock = new Clock();
$json = new JSON();

if (!isset($_GET["config-id"]) || empty(trim($_GET["config-id"])))
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.");
$encConfigId =  trim($_GET["config-id"]);

try {
    $postConfigId = $endecryptor->decrypt($encConfigId);
} catch (\Throwable $th) {
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.");
}

$db->connect();
$db->fetchAsObject();

#region check session
if (!isset($_GET["session-id"]) || empty(trim($_GET["session-id"])))
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again.");

$encSessionId = trim($_GET["session-id"]);

try {
    $sessionId = $endecryptor->decrypt($encSessionId);
    $session = new DbSession($db, "lc_enrolment_sessions");
    $session->continue($sessionId);
    $cinfoId = $session->getData("cinfoId"); //may raise exception if 'cinfoId' not found in session data.
} catch (\SessionException $th) {
    // $logger->createLog($th->getMessage());
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again.");
} catch (\Exception $exp) {
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again.");
}
#endregion

$sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND configId=:configId";

$configs = $db->select($sql, array('court' => COURT, "applicationType" => APPLICATION_TYPE, "configId" => $postConfigId));
//whether exclusive permission exists or not, the post configuration must exist.
if (count($configs) != 1) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application is not available.");

$postConfig = $configs[0];


$sql = "SELECT * FROM `lc_enrolment_cinfo` WHERE cinfoId=:cinfoId";
$applicant = ($db->select($sql, array("cinfoId" => $cinfoId)))[0];

?>

<!DOCTYPE html>
<html>

<head>
    <title>Delete Application- <?= ORGANIZATION_FULL_NAME ?></title>
    <?php
    Required::metaTags()->favicon()->teletalkCSS();
    ?>

    <link rel="stylesheet" href="css/delete-confirmation.min.css">
</head>

<body class="delete-confirmation-page">
    <div class="master-wrapper">
        <header>
            <?php
            require_once(ROOT_DIRECTORY . '/inc/header.php');
            echo prepareHeader(ORGANIZATION_FULL_NAME);
            ?>

            <?php
            Required::metaTags()->favicon()->teletalkCSS()->bootstrapGrid()->sweetModalCSS();
            ?>
        </header>
        <main class="delete-confirmation">
           
            
            <div class="container">
                <div class="logout-button-container">
                    <a href="<?=BASE_URL?>/logout.php?session-id=<?=$encSessionId?>">
                        <img src="<?=BASE_URL?>/assets/images/logout-icon.png" alt="Logout button" srcset=""> Logout
                    </a>
                </div>

                <h1>Delete Application</h1>
                <div class="card box-shadow">
                   

                    <p><span class="caution">Caution</span> You are going to delete the following application-</p>
                    <div style="text-align: center;">
                        <div class="table-container">
                            <table class="application-info">
                                <tbody>
                                    <tr>
                                        <td>User Id</td>
                                        <td>:</td>
                                        <td><?= $applicant->userId ?></td>
                                    </tr>
                                    <tr>
                                        <td>Applicant Name</td>
                                        <td>:</td>
                                        <td><?= $applicant->fullName ?></td>
                                    </tr>
                                    <tr>
                                        <td>Registration No.</td>
                                        <td>:</td>
                                        <td><?= $applicant->regNo ?></td>
                                    </tr>
                                    <tr>
                                        <td>Registration Year</td>
                                        <td>:</td>
                                        <td><?= $applicant->regYear ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <p>
                        You can not undo this action. However, after delete, you can apply again with the same Registration No. and Registration Year.
                    </p>
                    <p>
                        If you are aware this action, click the button below to delete.
                    </p>

                    <form id="form" action="delete-confirmation-process.php?session-id=<?= $encSessionId ?>" method="post">

                        <input type="hidden" name="cinfoId" value="<?= $endecryptor->encrypt($cinfoId) ?>">
                        <?php
                        $backUrl = BASE_URL . "/applicant-copy/preview.php?session-id=$encSessionId&config-id=$encConfigId";
                        ?>
                        <a href="<?= $backUrl ?>" class="btn btn-success" style="text-decoration: none;">Back</a>
                        <input type="submit" class="form-submit-button btn btn-danger" value="Confirm Delete">
                    </form>
                </div>


            </div>
        </main>
        <footer>
            <?php
            Required::footer();
            ?>
        </footer>
    </div>

    <script>
        var baseUrl = '<?php echo BASE_URL; ?>';
    </script>
    <?php
    Required::jquery()->hamburgerMenu()->sweetModalJS()->moment()->mobileValidator()->swiftSubmit();
    ?>
    <script>
        $(function() {
            $('#form').swiftSubmit({
                redirect: true
            }, null, null, null, null, null);
        })
    </script>
</body>

</html>