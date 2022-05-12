<?php
require_once("../Required.php");
Required::Logger()
    ->Database()->DbSession()
    ->Validable()
    ->EnDecryptor()
    ->JSON()->Heredoc()->HttpHeader()
    ->Helpers()->ExclusivePermission()->Clock();

$logger = new Logger(ROOT_DIRECTORY);
$endecryptor = new EnDecryptor();
$json = new JSON();
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$clock = new Clock();
$validable = new Validable();

$db->connect();
$db->fetchAsObject();

//if post configuration id "config-id" key found in query string, redirect to the sorry page.
if (!isset($_GET["cinfo-id"]) || empty(trim($_GET["cinfo-id"])))
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.");

$encCinfoId = trim($_GET["cinfo-id"]); //decryption has been done in the following try .. catch block for safety reason.

//if decryption throws any exception, redirect to the sorry page.
try {
    $cinfoId =  $endecryptor->decrypt($encCinfoId);
} catch (\Exception $exp) {
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.");
}

$sql = "SELECT 	cinfoId, 
                    registrationId, 
                    userId, 
                    regNo, 
                    regYear, 
                    fullName, 
                    fatherName, 
                    pupilageContractDate, 
                    barName, 
                    seniorAdvocateName,
                    applicantType
            FROM lc_enrolment_cinfo 
            WHERE cinfoId=:cinfoId";
$cinfos = $db->select($sql, array('cinfoId' => $cinfoId));

if (count($cinfos) != 1) {
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application not found.");
}

$cinfo = $cinfos[0];

$sql = 'SELECT llbUni FROM `lc_enrolment_higher_educations` WHERE cinfoId = :cinfoId';
$educations = $db->select($sql, array('cinfoId' => $cinfo->cinfoId));
$education = $educations[0];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registration Correction Request- <?= ORGANIZATION_SHORT_NAME ?></title>
    <?php
    Required::metaTags()->favicon()->teletalkCSS()->bootstrapGrid()->sweetModalCSS()->airDatePickerCSS();
    ?>

    <link href="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.theme.min.css" rel="stylesheet">

    <style>
        .classic {
            padding: 20px;
            width: 700px;
            margin: 0 auto;
        }

        .formSection {
            margin-bottom: 50px !important;
        }

        .steps {
            border: 1px solid;
            display: inline-block;
            padding: 0px 10px;
            border-radius: 18px;
            background-color: gainsboro;
            font-size: 13px;
            font-weight: 600;
            color: darkslategray;
        }

        .btn {
            background-color: #dcdcdc !important;
            border: 2px solid #6b6b6b !important;
            color: #0a0909 !important;
            padding-top: 2px;
            padding-bottom: 2px;
        }

        .previewMode {
            /* border: none !important; */
            border-color: white;
            -webkit-user-select: none;
            outline: none;
            pointer-events: none;
            padding: 0;
        }

        label.changed::after {
            color: white;
        }

        .previewMode>select {
            -webkit-appearance: none;
            -moz-appearance: none;
            text-indent: 1px;
            text-overflow: '';
        }

        .sectionNavigation {
            display: flex;
            text-align: center;
            justify-content: space-between;
        }

        .sectionNavigation .btn {
            display: flex;
        }

        .sectionNavigation .goToPrevSection>img {
            height: 20px;
            margin-top: 2px;
            margin-right: 7px;
        }

        .sectionNavigation .goToNextSection>img {
            height: 20px;
            margin-top: 2px;
            margin-left: 7px;
        }

        /* Left right position */
        /* .sectionNavigation .goToPrevSection {
                float: left;
            }

            .sectionNavigation .goToNextSection,
            .sectionNavigation .form-submit-button
            .sectionNavigation #showPreview{
                float: right;
            } */
        /* Left right position */
    </style>

</head>

<body class="registration-correction-request">
    <!-- <div id="version"></div> -->
    <div class="master-wrapper">
        <header>
            <?php
            require_once(ROOT_DIRECTORY . '/inc/header.php');
            echo prepareHeader(ORGANIZATION_FULL_NAME);
            ?>
        </header>
        <main id="applicant-info">

            <h2 class="text-center">Registration Correction Request</h2>

            <div class="container">
                <form class="classic box-shadow" id="application-form" action="correction-request-processor.php" method="post" enctype="multipart/form-data">

                    <input type="hidden" name="cinfoId" value="<?= $encCinfoId ?>">

                    <div class="row">
                        <div class="col-sm-12 col-lg-4">
                            <div class="field">
                                <label class="required">Registration No.</label>
                                <input type="text" class=""  value="<?= $cinfo->regNo ?>" name="regNo" readonly disabled>
                            </div>
                        </div>
                        <div class="col-sm-12 col-lg-4">
                            <div class="field">
                                <label class="required">Registration Year</label>
                                <input name="regYear" type="text" value="<?= $cinfo->regYear ?>" readonly disabled>
                            </div>
                        </div>

                        <div class="col-sm-12 col-lg-4">
                            <div class="field">
                                <label class="required">Applicant Type</label>
                                <input type="text" value="<?=$cinfo->applicantType?>" disabled readonly>
                            </div>
                        </div>
                    </div>

                    <br>
                    <div class="row">
                        <div class="col">
                            Enter the correct information below in the respective field(s) -
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="field">
                                <label class="required">Name</label>
                                <input name="name" class="validate upper-case" type="text" value="<?= $cinfo->fullName ?>" data-title="Name" data-required="required" data-maxlen="100">
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="required">Father Name</label>
                        <input name="fatherName" class="validate upper-case" type="text" value="<?=$cinfo->fatherName?>" data-required="required" data-maxlen="100">
                    </div>
                    <div class="field">
                        <label class="required">University</label>
                        <input name="universityName" class="validate upper-case" type="text" value="<?=$education->llbUni?>" data-required="required" data-maxlen="100">
                    </div>

                    <div class="row">
                        <div class="col-sm-12 col-lg-8">
                            <div class="field">
                                <label class="required">Senior Advocate Name</label>
                                <input name="seniorAdvocateName" class="validate upper-case" type="text" value="<?= $cinfo->seniorAdvocateName ?>" data-required="required" data-maxlen="100">
                            </div>
                        </div>
                        <div class="col">
                            <div class="field">
                                <label class="required">Pupilage Contract Date</label>
                                <input name="pupilageContractDate" type="text" class="validate swiftDate" value="<?= isset($cinfo->pupilageContractDate) ? $clock->toString($cinfo->pupilageContractDate, DatetimeFormat::BdDate()) : '' ?>" data-required="required" data-datatype="date" placeholder="dd-mm-yyyy">
                            </div>
                        </div>
                    </div>

                    <div class="field">
                        <label class="">Remarks (if any)</label>
                        <textarea name="remarks" class="validate" data-required="optional" data-datatype="string" data-maxlen="150" cols="30" rows="10"></textarea>
                    </div>

                    <div id="submitSection" class="text-center">
                        <input type="submit" class="form-submit-button btn btn-lg btn-success" value="Submit">
                    </div>
                </form>
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
    Required::jquery()->hamburgerMenu()->sweetModalJS()->moment()->mobileValidator()->swiftSubmit()->SwiftNumeric()->airDatePickerJS();
    ?>
    <script src="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.min.js" ;></script>
    <script src="./correction-request.js"></script>

</body>

</html>