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


if (!isset($_GET["config-id"]) || empty(trim($_GET["config-id"]))) {
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.", false);
}


try {
    $encConfigId = $_GET["config-id"];
    $postConfigId = $endecryptor->decrypt($encConfigId);
} catch (\Throwable $th) {
    HttpHeader::redirect(BASE_URL . "/sorry.php", false);
}

$db->connect();
$db->fetchAsObject();



$sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND configId=:configId";

$configs = $db->select($sql, array('court' => COURT, "applicationType" => APPLICATION_TYPE, "configId" => $postConfigId));
// die($postConfigId. "her");

//whether exclusive permission exists or not, the post configuration must exist.
if (count($configs) != 1) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application is not available.");

$postConfig = $configs[0];

if (!isset($_GET["cinfo-id"]) || empty(trim($_GET["cinfo-id"]))) {
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.", false);
}

$encCinfoId = $_GET["cinfo-id"];
$cinfoId = $endecryptor->decrypt($encCinfoId);

$sql = "SELECT * FROM `lc_enrolment_cinfo` WHERE cinfoId=:cinfoId";
$applicant = ($db->select($sql, array("cinfoId" => $cinfoId)))[0];

$sql = "SELECT * FROM `lc_enrolment_higher_educations` WHERE cinfoId=:cinfoId";
$hEdu = ($db->select($sql, array("cinfoId" => $cinfoId)))[0];

$smsKeyword = "BAR";


?>

<!DOCTYPE html>
<html>

<head>
    <title>Preivew Application- <?= ORGANIZATION_FULL_NAME ?></title>
    <?php
    Required::metaTags()->favicon()->teletalkCSS();
    if (ENVIRONMENT == "PRODUCTION") {
    ?>
        <script>
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function() {
                history.pushState(null, null, document.URL);
            });
        </script>
    <?php
    }
    ?>

    <style>
        .fixed-width label {
            width: 67px;
        }

        .formHeader>h1 {
            font-size: 17px;
            text-align: center;
        }

        .formHeader h2 {
            font-size: 13px;
            text-align: center;
        }

        .formHeader p {
            font-size: 10px;
            text-align: justify;
            padding: 0 27px;
        }

        tbody tr td:first-child {
            text-align: left !important;
        }



        .qr-container {
            flex: 1;
        }

        .signature-container {
            flex: 1;
            text-align: center;
        }

        img.qr {
            width: 100px;
        }

        .signature-name {
            font-size: 14px;
            border-top: 1px solid dimgray;
        }

       .delete-note{
            width: 660px;
            margin: 0 auto;
            text-align: justify;
            line-height: 1.4;
            font-size: 17px;
       }
                   
    </style>

    <link rel="stylesheet" href="css/preview.min.css">
</head>

<body>
    <div id="master-wrapper">
        <header>
            <?php
            require_once(ROOT_DIRECTORY . '/inc/header.php');
            echo prepareHeader(ORGANIZATION_FULL_NAME);
            ?>
        </header>
        <main class="previewPage">


            <div style="width: 660px; margin:0 auto; margin-bottom:20px; display:flex; justify-content:space-between;">
                <button type="button" class="create-pdf btn btn-dark btn-large">Download</button>
               
            </div>
            <div id="pdfdiv" class="non-responsive printable applicant-copy-preview" style="border: 1px solid; margin:0 auto; width: 660px; padding: 10px 20px;">

                <!-- // Original header -->

                <!-- <section>
                    <div class="header">
                        <div class="left">
                            <div class="brand" style="display: flex;">
                                <div>
                                    <img class="logo" src="<?= BASE_URL ?>/assets/images/bar-logo.png">
                                </div>
                                <div class="govt-org">
                                    <div class="govt">
                                        Government of the People's Republic of Bangladesh</div>
                                    <div class="organization"><?= ORGANIZATION_FULL_NAME ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="right">
                            <div>
                                Applicant's Copy
                            </div>
                        </div>
                    </div>
                </section> -->
                <!-- Original header // -->

                <!-- //Special header for bar council -->
                <section style="position: relative;">
                    <div style="text-align: center;">
                        <img style="width: 4em;" src="<?= BASE_URL ?>/assets/images/bar-logo.png">
                    </div>
                    <div class="certificate" style="text-align: center;">Bangladesh Bar Council</div>
                    <div style="font-size: 0.69rem; text-align:center;">
                        (A Statutory Autonomous Body of the Government Constituted Under President's Order No. 46 of 1972)
                    </div>

                    <?php
                    $photoPath = BASE_URL . "/applicant-images/photos/" . $applicant->userId . ".jpg";
                    ?>
                    <img class="photo" src="<?= $photoPath ?>">
                    <div class="applicant-copy-label">
                        <div>
                            Admit Card
                        </div>
                    </div>
                </section>
                <!--   Special header for bar council// -->
                <?php
                $formHeader = "";
                if ($applicant->applicantType == "Re-appeared") {
                    $formHeader = "<h1>Re-Appear Application Form for Enrolment Examination</h1>";
                } else {
                    $formHeader = "<h1>Application for enrolment as Advocate</h1>";
                    $formHeader .= "<h2>FORM 'A'</h2>";
                    $formHeader .= "<h2>(See Rule 58)</h2>";
                }

                ?>


                <section class="formHeader">
                    <?php
                    echo $formHeader;
                    ?>
                </section>
                <!-- applicant's biodata -->
                <section>
                    <div class="fixed-width">
                        <div class="margin-bottom-10 ">
                            <div class="grid">
                                <div class="col-auto">
                                    <div class="field">
                                        <label class="border-right">User ID</label>
                                        <div class="text"><?= $applicant->userId ?></div>
                                    </div>
                                </div>

                                <div class="col-auto">
                                    <div class="field">
                                        <label class="border-right border-left">Reference</label>
                                        <div class="text"><?= $postConfig->referenceNo ?></div>
                                    </div>
                                </div>
                            </div>
                            <div class="grid border-bottom">
                                <div class="col-auto">
                                    <div class="field">
                                        <label class="border-right">Reg. No.</label>
                                        <div class="text"><?= $applicant->regNo ?></div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="field">
                                        <label class="border-right border-left">Reg. Year</label>
                                        <div class="text"><?= $applicant->regYear ?></div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <!-- <div class="field">
                                        <label class="border-right border-left"></label>
                                        <div class="text"></div>
                                    </div> -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="col-12 flex column">
                            <div class="field border-bottom">
                                <label class="border-right fixed-width">Name</label>
                                <div class="text"><?= $applicant->fullName; ?></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="field ">
                                <label class="border-right fixed-width">Father</label>
                                <div class="text"><?= $applicant->fatherName; ?></div>
                            </div>
                        </div>
                        <div class="col-auto border-left">
                            <div class="field">
                                <label class="fixed-width border-right">Mother</label>
                                <div class="text"><?= $applicant->motherName; ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right fixed-width">Date of Birth</label>
                                <div class="text" style="width: 92px;"><?= $clock->toString($applicant->dob, DatetimeFormat::BdDate()) ?></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right border-left">Gender</label>
                                <div class="text" style="width: 61px;"><?= $applicant->gender; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right border-left">Nationality</label>
                                <div class="text">Bangladeshi</div>
                            </div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right fixed-width">Mobile No.</label>
                                <div class="text"><?= $applicant->mobileNo ?></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right border-left">Email</label>
                                <div class="text"><?= $applicant->email; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="col-auto">
                            <div class="field border-right">
                                <label class="border-right fixed-width">Birth Certificate</label>
                                <div class="text"><?= $applicant->birthCertNo ?></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right">NID</label>
                                <div class="text"><?= $applicant->nidNo ?></div>
                            </div>
                        </div>

                        <div class="col-auto">
                            <div class="field border-left">
                                <label class="border-right">Passport</label>
                                <div class="text"><?= $applicant->passportNo ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right fixed-width">Applicant Type</label>
                                <div class="text"><?= $applicant->applicantType ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right fixed-width">Bar Association Name</label>
                                <div class="text"><?= $applicant->barName ?></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="field border-left">
                                <label class="border-right fixed-width">Senior Advocate Name</label>
                                <div class="text"><?= $applicant->seniorAdvocateName ?></div>
                            </div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right fixed-width">Pupilage Cont. Date</label>
                                <div class="text"><?= $clock->toString($applicant->pupilageContractDate, DatetimeFormat::BdDate()) ?></div>
                            </div>
                        </div>

                        <div class="col-auto">
                            <div class="field border-left">
                                <label class="border-right">Pupilage Duration (Months)</label>
                                <div class="text"><?= $applicant->pupilageDurationInMonth ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Permanent & Present Address -->
                    <div class="grid">
                        <div class="col-auto">
                            <div class="field ">
                                <label class="border-right" style="width: 125px !important;">Present Address</label>
                                <div class="text">
                                    <b>Details: </b> <?php echo strtoupper($applicant->presentAddress); ?> <br>
                                    <b>Thana: </b><?php echo strtoupper($applicant->presentThana); ?>
                                    <b>District: </b><?php echo strtoupper($applicant->presentDist); ?>
                                    <b>Postal Code: </b><?php echo strtoupper($applicant->presentGpo); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right" style="width: 125px !important;">Permanent Address</label>
                                <div class="text">
                                    <b>Details: </b><?php echo strtoupper($applicant->permanentAddress); ?> <br>
                                    <b>Thana: </b><?php echo strtoupper($applicant->permanentThana); ?>
                                    <b>District: </b><?php echo strtoupper($applicant->permanentDist); ?>
                                    <b>Postal Code: </b><?php echo strtoupper($applicant->permanentGpo); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Educations -->
                    <div class="grid">
                        <div class="col-auto">

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Examination/Degree</th>
                                        <th>Year</th>
                                        <th>Board/University/Institute</th>
                                        <th>Division/Class/Grade/CGPA</th>
                                        <th>Roll/ID</th>
                                        <th>Major Subject/Group</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php echo $applicant->sscExamName; ?></td>
                                        <td><?php echo $applicant->sscYear; ?></td>
                                        <td><?php echo strtoupper($applicant->sscBoard); ?></td>
                                        <?php
                                        // $sscResultContent = $applicant->sscGpa . "" . $applicant->sscGrade . "" . $applicant->sscDivision.($applicant->sscScale != "" ? "/".$applicant->sscScale : "");
                                        $sscRetult = $applicant->sscGpa . $applicant->sscGrade . $applicant->sscDivision;
                                        $sscResultBase = $applicant->sscScale != "" ? " out of " . $applicant->sscScale : "";
                                        $sscResultPercentage = ""; //$applicant->sscScale != "" ? " (".(($sscRetult / $applicant->sscScale) * 100)."%)" : "";
                                        $sscResultContent = $sscRetult . $sscResultBase . $sscResultPercentage;
                                        ?>
                                        <td><?php echo $sscResultContent; ?></td>
                                        <td><?php echo strtoupper($applicant->sscRollNo); ?></td>
                                        <td><?php echo strtoupper($applicant->sscGroup); ?></td>
                                    </tr>

                                    <tr>
                                        <td><?php echo $applicant->hscExamName; ?></td>
                                        <td><?php echo $applicant->hscYear; ?></td>
                                        <td><?php echo strtoupper($applicant->hscBoard); ?></td>
                                        <?php
                                        // $sscResultContent = $applicant->hscGpa . "" . $applicant->hscGrade . "" . $applicant->hscDivision.($applicant->hscScale != "" ? "/".$applicant->hscScale : "");
                                        $hscRetult = $applicant->hscGpa . $applicant->hscGrade . $applicant->hscDivision;
                                        $hscResultBase = $applicant->hscScale != "" ? " out of " . $applicant->hscScale : "";
                                        $hscResultPercentage = ""; //$applicant->hscScale != "" ? " (".(($hscRetult / $applicant->hscScale) * 100)."%)" : "";
                                        $hscResultContent = $hscRetult . $hscResultBase . $hscResultPercentage;
                                        ?>
                                        <td><?php echo $hscResultContent; ?></td>
                                        <td><?php echo strtoupper($applicant->hscRollNo); ?></td>
                                        <td><?php echo strtoupper($applicant->hscGroup); ?></td>
                                    </tr>

                                    <?php

                                    //LL.B (Hons/Pass) -->
                                    $examName = $hEdu->llbExam;
                                    $llbResultType = $hEdu->llbResultType;
                                    $result = "";
                                    switch (strtolower($llbResultType)) {
                                        case "appeared":
                                            $llbExamConcludedDate = $datetime->value($hEdu->llbExamConcludedDate)->asdmY();
                                            $passingYear  = "Appeared. Concluded date - " . $llbExamConcludedDate;
                                            $result = "Appeared";
                                            break;
                                        case "division":
                                            $passingYear = $hEdu->llbPassingYear;
                                            $result = $hEdu->llbDivision
                                                . "<br>Marks-" . $hEdu->llbObtainedMarks . " out of " . $hEdu->llbTotalMarks . " (" . $hEdu->llbMarksPercentage . "%)";
                                            break;
                                        case "class":
                                            $passingYear = $hEdu->llbPassingYear;
                                            $result = $hEdu->llbClass
                                                . "<br>Marks-" . $hEdu->llbObtainedMarks . " out of " . $hEdu->llbTotalMarks . " (" . $hEdu->llbMarksPercentage . "%)";
                                            break;
                                        case "grading":
                                            $passingYear = $hEdu->llbPassingYear;
                                            $result = $hEdu->llbCgpa
                                                . " out of " . $hEdu->llbCgpaScale . "<br>(" . $hEdu->llbMarksPercentage . "%)";
                                            break;
                                    }

                                    $University = $hEdu->llbUni;
                                    $llbDetails = '<tr>
                                                            <td>' .  $examName . '</td>
                                                            <td>' . $passingYear . '</td>
                                                            <td>' . strtoupper($University) . '</td>
                                                            <td>' . $result . '</td>
                                                            <td>' . $hEdu->llbId . '</td>
                                                            <td>' . $hEdu->llbSubject . '</td>
                                                        </tr>';
                                    echo $llbDetails;
                                    //LL.B (Hons/Pass) ends.

                                    //Graduation (Others)
                                    if ($hEdu->hasOtherGrad) {
                                        $examName = $hEdu->gradOtherExam;
                                        $gradOtherResultType = $hEdu->gradOtherResultType;
                                        $result = "";
                                        switch (strtolower($gradOtherResultType)) {
                                            case "appeared":
                                                $gradOtherExamConcludedDate = $datetime->value($hEdu->gradOtherExamConcludedDate)->asdmY();
                                                $passingYear  = "Appeared. Concluded date - " . $gradOtherExamConcludedDate;
                                                $result = "Appeared";
                                                break;
                                            case "division":
                                                $passingYear = $hEdu->gradOtherPassingYear;
                                                $result = $hEdu->gradOtherDivision
                                                    . "<br>Marks-" . $hEdu->gradOtherObtainedMarks . " out of " . $hEdu->gradOtherTotalMarks . " (" . number_format($hEdu->gradOtherMarksPercentage) . "%)";
                                                break;
                                            case "class":
                                                $passingYear = $hEdu->gradOtherPassingYear;
                                                $result = $hEdu->gradOtherClass
                                                    . "<br>Marks-" . $hEdu->gradOtherObtainedMarks . " out of " . $hEdu->gradOtherTotalMarks . " (" . number_format($hEdu->gradOtherMarksPercentage, 2) . "%)";
                                                break;
                                            case "grading":
                                                $passingYear = $hEdu->gradOtherPassingYear;
                                                $result = $hEdu->gradOtherCgpa
                                                    . " out of " . $hEdu->gradOtherCgpaScale . "<br>(" . number_format($hEdu->gradOtherMarksPercentage) . "%)";
                                                break;
                                        }

                                        $University = $hEdu->gradOtherUni;
                                        $gradOtherDetails = '<tr>
                                                                    <td>' .  $examName . '</td>
                                                                    <td>' . $passingYear . '</td>
                                                                    <td>' . strtoupper($University) . '</td>
                                                                    <td>' . $result . '</td>
                                                                    <td>' . $hEdu->gradOtherId . '</td>
                                                                    <td>' . $hEdu->gradOtherSubject . '</td>
                                                                </tr>';
                                        echo $gradOtherDetails;
                                    }
                                    //Graduation (Others) ends

                                    //Masters
                                    if ($hEdu->hasMasters) {
                                        $examName = $hEdu->mastersExam;
                                        $mastersResultType = $hEdu->mastersResultType;
                                        $result = "";
                                        switch (strtolower($mastersResultType)) {
                                            case "appeared":
                                                $mastersExamConcludedDate = $datetime->value($hEdu->mastersExamConcludedDate)->asdmY();
                                                $passingYear  = "Appeared. Concluded date - " . $mastersExamConcludedDate;
                                                $result = "Appeared";
                                                break;
                                            case "division":
                                                $passingYear = $hEdu->mastersPassingYear;
                                                $result = $hEdu->mastersDivision
                                                    . "<br>Marks-" . $hEdu->mastersObtainedMarks . " out of " . $hEdu->mastersTotalMarks . " (" . $hEdu->mastersMarksPercentage . "%)";
                                                break;
                                            case "class":
                                                $passingYear = $hEdu->mastersPassingYear;
                                                $result = $hEdu->mastersClass
                                                    . "<br>Marks-" . $hEdu->mastersObtainedMarks . " out of " . $hEdu->mastersTotalMarks . " (" . $hEdu->mastersMarksPercentage . "%)";
                                                break;
                                            case "grading":
                                                $passingYear = $hEdu->mastersPassingYear;
                                                $result = $hEdu->mastersCgpa . " out of " . $hEdu->mastersCgpaScale . "<br>(" . $hEdu->mastersMarksPercentage . "%)";
                                                break;
                                        }

                                        $University = $hEdu->mastersUni;
                                        $mastersDetails = '<tr>
                                                                    <td>' .  $examName . '</td>
                                                                    <td>' . $passingYear . '</td>
                                                                    <td>' . strtoupper($University) . '</td>
                                                                    <td>' . $result . '</td>
                                                                    <td>' . $hEdu->mastersId . '</td>
                                                                    <td>' . $hEdu->mastersSubject . '</td>
                                                                </tr>';
                                        echo $mastersDetails;
                                    }
                                    //Masters ends
                                    ?>

                                </tbody>
                            </table>
                            <!-- ===================================== -->
                        </div>
                    </div>

                    <!-- Bar-at-law starts -->
                    <?php
                    if ($hEdu->hasBarAtLaw) {
                    ?>
                        <div class="grid">
                            <div class="col-auto">
                                <div class="field">
                                    <label class="border-right">Bar-at-law</label>
                                    <div class="text"><?= $hEdu->barAtLawName . ", " . $hEdu->barAtLawYear ?></div>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                    <!-- Bar-at-law ends -->







                    <!--Intended Bar + Insolvent -->
                    <div class="grid">
                        <div class="col-auto ">
                            <div class="field vertical ">
                                <label class="border-bottom">Name of the Bar Association which he/she intends to join</label>
                                <div class="text">
                                    <?= $applicant->barName ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto border-left">
                            <div class="field vertical">
                                <label class="border-bottom">Whether the applicant has been declared insolvent</label>

                                <div class="text">
                                    <?= $applicant->declaredInsolvent ?>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="grid">
                        <div class="col-auto">
                            <div class="field vertical">
                                <label class="border-bottom">Whether he/she is engaged in any business profession, service or vocation in Bangladesh. If so, the nature thereof and the place at which it is carried on</label>
                                <div class="text">
                                    <?php
                                    if ($applicant->isEngaged == "No") {
                                        echo "Not applicable.";
                                    } else {
                                        echo  "$applicant->engagementNature. $applicant->engagementPlace.";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>




                    <!-- Dismissal -->
                    <div class="grid">
                        <div class="col-auto">
                            <div class="field vertical">
                                <label class="border-bottom">Whether the applicant has been dismissed from the service of Government or of a public statutory corporation, if so, date and reason thereof</label>
                                <div class="text">
                                    <?php
                                    if ($applicant->isDismissed == "No") {
                                        echo "Not applicable.";
                                    } else {
                                        $dismissalDate = $clock->toString($applicant->dismissalDate, DatetimeFormat::BdDate());
                                        echo  "Dismissal date- $dismissalDate. Dismissal reason- $applicant->dismissalReason.";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conviction -->
                    <div class="grid">
                        <div class="col-auto">
                            <div class="field vertical">
                                <label class="border-bottom">Whether the applicant has been convicted of any offence involving moral turpitude, if so, date and particulars thereof</label>
                                <div class="text">
                                    <?php
                                    if ($applicant->isConvicted == "No") {
                                        echo "Not applicable.";
                                    } else {
                                        $convictionDate = $clock->toString($applicant->convictionDate, DatetimeFormat::BdDate());
                                        echo  "Conviction date- $convictionDate. Conviction particulars- $applicant->convictionParticulars.";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- previous cancellation -->
                    <div class="grid">
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right">Whether the enrolment of the applicant has previously been cancelled by the Bar council</label>

                                <div class="text">
                                    <?= $applicant->isCancelledPreviously ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- last exam and roll -->
                    <?php
                        if($applicant->applicantType == "Re-appeared"){
                    ?>
                        <div class="grid">
                            <div class="col-auto">
                                <div class="field">
                                    <label class="border-right">Last Roll</label>
                                    <div class="text">
                                        <?= $applicant->lastRoll?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="field">
                                    <label class="border-right border-left">Last Written Exam Year</label>
                                    <div class="text">
                                        <?= $applicant->lastWrittenExamYear?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                    ?>
                    
                    <!-- An undertaking -->
                    <div class="grid">
                        <div class="col-auto">
                            <div class="field vertical">
                                <label class="border-bottom">An undertaking by the applicant</label>

                                <div class="text">
                                    I do hereby undertake that shall become a member of a recognized <span style="text-decoration:underline;"><?= $applicant->barName ?></span> Bar Association and obtain enrolment sanad within 06 months of my enrolment.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid">
                        <div class="col-auto">
                            <div class="field ">
                                <label class="border-right fixed-width">Declaration</label>
                                <div class="text">
                                    I declare that the information provided in this form is correct, true and complete to the best of my knowledge and belief. If any information is found false, incorrect and incomplete or if any ineligibility is detected before or after the examination, any action can be taken against me by the Bar Council.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid border-bottom">
                        <div class="col-auto">
                            <div class="field border-right">
                                <label class="border-right fixed-width">Applied On</label>
                                <div class="text"><?= $clock->toString($applicant->appliedDatetime, DatetimeFormat::Custom("d-m-Y h:i A")) ?></div>
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="field">
                                <label class="border-right fixed-width">Printed On</label>
                                <div class="text"><?= $clock->toString("now", DatetimeFormat::Custom("d-m-Y h:i A")) ?></div>

                            </div>
                        </div>
                    </div>
                                      
                    <div style="page-break-after: always;">                         
                    </div>
                    
                    <div class="signature" style="justify-content: unset;">
                        <div class="qr-container">
                            <?php
                            $qrPath = BASE_URL . "/applicant-images/qr-codes/" . $applicant->userId . ".png";
                            ?>
                            <img class="qr" src="<?= $qrPath ?>">
                        </div>
                        <div class="signature-container">
                            <?php
                            $photoPath = BASE_URL . "/applicant-images/signatures/" . $applicant->userId . ".jpg";
                            ?>
                            <img class="signature" src="<?= $photoPath ?>">
                            <span class="signature-name"><?= $applicant->fullName ?></span>
                        </div>
                    </div>
                </section>

                <section>
                    <footer>
                        <div class="copyright">
                            ©<?php echo date("Y"); ?>, <?= ORGANIZATION_FULL_NAME ?>, All Rights Reserved.
                        </div>

                        <div class="powered-by">
                            Powered By:
                            <a href="http://www.teletalk.com.bd/" target="_blank">
                                <img class="logo" alt="teletalk Logo" title="Powered By: Teletalk" src="<?= BASE_URL ?>/assets/images/teletalk-logo.png">
                            </a>
                        </div>
                    </footer>
                </section>
            </div>
          
        </main>
        <footer>
            <?php
            Required::footer();
            ?>
        </footer>
    </div>

    <?php
    Required::jquery()->hamburgerMenu()->html2pdf();
    ?>
    <script>
        history.pushState(null, document.title, location.href);
        window.addEventListener('popstate', function(event) {
            history.pushState(null, document.title, location.href);
        });

        $(function() {
            $(".create-pdf").click(function() {
                $('#linetest').addClass('linetest');

                var element = document.getElementById('pdfdiv');
                var opt = {
                    margin: 0.5,
                    filename: 'Applicant Copy - <?= $applicant->userId ?>.pdf',
                    image: {
                        type: 'jpeg',
                        quality: 1
                    },
                    html2canvas: {
                        scale: 4,
                        dpi: 300
                    },
                    jsPDF: {
                        unit: 'in',
                        format: 'A4',
                        orientation: 'portrait'
                    }
                    // ,pagebreak: { before: '.beforeClass', after: ['#after1', '#after2'], avoid: 'img' }
                };

                // html2pdf(element, opt);



                html2pdf().from(element).set(opt).toPdf().get('pdf').then(function (pdf) {
                    var totalPages = pdf.internal.getNumberOfPages();

                    for (i = 1; i <= totalPages; i++) {
                        console.log(i);
                        pdf.setPage(i);
                        pdf.setFontSize(10);
                        pdf.setTextColor(150);
                        pdf.text('Page ' + i + ' of ' + totalPages, pdf.internal.pageSize.getWidth()/2, pdf.internal.pageSize.getHeight() - 0.3);
                    } 
                }).save().then(function(){
                    $('#linetest').removeClass('linetest');
                });


                


                // $('#linetest').addClass('linetest');







            });
        })
    </script>
</body>

</html>