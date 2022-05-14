<?php
    declare(strict_types=1);

    #region Import libraries
        require_once("../../Required.php");
        Required::Logger()
            ->Database()->DbSession()
            ->Validable()
            ->Cryptographer()
            ->JSON()->HttpHeader()
            ->ExclusivePermission()->Clock()->headerBrand()->applicantHeaderNav()->footer()->Helpers();
    #endregion

    #region Variable declaration & initialization
        $logger = new Logger(ROOT_DIRECTORY);  //This must be in first position.
        $crypto = new Cryptographer(SECRET_KEY);
        $json = new JSON();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
        $clock = new Clock();
        $validable = new Validable();
        $hasExclusivePermission = ExclusivePermission::hasPermission();
        $pageTitle = "Application Form";
        $proceedAnyWayQueryString = "";
        if (isset($_GET[ExclusivePermission::$propName]) && !empty($_GET[ExclusivePermission::$propName])) {
            $proceedAnyWayQueryString = "&" . ExclusivePermission::$propName . "=" . ExclusivePermission::$propValue;
        }
    #endregion

    #region Database connection
        $db->connect();
        $db->fetchAsObject();
    #endregion

    #region Session check and validation
        // if (!isset($_GET["session-id"]) || empty(trim($_GET["session-id"]))) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session.");
        // $encSessionId = trim($_GET["session-id"]);
        // $sessionId = $crypto->decrypt($encSessionId);
        // if (!$sessionId)  HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session.");
        // try {
        //     $session = new DbSession($db, "lc_enrolment_sessions");
        //     $session->continue($sessionId);
        // } catch (\SessionException $th) {
        //     HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session.");
        // }
    #endregion

    #region Check Post Config
        if (!isset($_GET["config-id"]) || empty(trim($_GET["config-id"]))) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.");
        $encConfigId = trim($_GET["config-id"]);

        $postConfigId =  $crypto->decrypt($encConfigId);
        if (!$encConfigId)  HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.");

        //If has exclusive permission, don't check isActive status.
        //If does not have exclusive permission, check isActive status.
        if ($hasExclusivePermission)
            $sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND configId=:configId";
        else
            $sql = "SELECT * FROM `post_configurations` WHERE isActive=1 AND court=:court AND applicationType = :applicationType AND configId=:configId";

        $configs = $db->selectMany($sql, array('court' => COURT, "applicationType" => APPLICATION_TYPE, "configId" => $postConfigId));
        if (count($configs) != 1) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application is not available.");
        $postConfig = $configs[0];
    #endregion

    #region Check application start and end datetime
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


    $districts = $db->select("SELECT `name` FROM districts order by `name`");
    $bars = $db->select("SELECT `barName` FROM lc_bar_list order by `barName`");

    function is_child_hidden($value, $value_to_compare)
    {
        if ($value == $value_to_compare) {
            return "";
        } else {
            return "hidden";
        }
    }

?>

<!DOCTYPE html>
<html>

    <head>
        <title><?= $pageTitle ?> - <?= ORGANIZATION_SHORT_NAME ?></title>
        <?php
        Required::gtag()->metaTags()->favicon()->omnicss()->bootstrapGrid()->sweetModalCSS()->airDatePickerCSS();
        ?>

        <link href="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet">
        <link href="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.theme.min.css" rel="stylesheet">

        <style>
            .formSection {
                margin-bottom: 50px !important;
            }

            .steps {
                border: 1px solid;
                display: inline-block;
                padding: 0px 10px;
                border-radius: 18px;
                /* background-color: gainsboro; */
                font-size: 13px;
                font-weight: 600;
                /* color: darkslategray; */
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


            .case {
                border-bottom: 1px solid black;
                margin-bottom: 50px;
            }

            .case>h5 {
                font-size: 19px;
                font-weight: bold;
            }


            .sweet-modal-content{
                color:black;
            }
            /* 34 39 46 */

            .sweet-modal-overlay{
                /* background: radial-gradient(at center, rgba(0,0,0,0.6) 0%, rgba(0,0,0,0.7) 100%); */
                background: radial-gradient(at center, rgba(34,39,46,0.6) 0%, rgba(34,39,46,0.7) 100%);
            }

        </style>

    </head>

    <body>
        <!-- <div id="version"></div> -->
        <div class="master-wrapper">
            <header class="header">
                <?php
                    echo HeaderBrand::prepare(array("baseUrl"=>BASE_URL, "hambMenu"=>true));
                    echo ApplicantHeaderNav::prepare(array("baseUrl"=>BASE_URL));
                ?>
            </header>

            <main class="main">
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

                    <div class="logout-button-container">
                        <a href="<?= BASE_URL ?>/logout.php?session-id=<?= $encSessionId ?>">
                            <img src="<?= BASE_URL ?>/assets/images/logout-icon.png" alt="Logout button" srcset=""> Logout
                        </a>
                    </div>

                    <div class="content">
                        <div class="card">
                            <form class="classic" id="application-form" action="form-processor.php?session-id=<?= $encSessionId ?><?= $proceedAnyWayQueryString ?>" method="post" enctype="multipart/form-data">

                                <input type="hidden" name="registrationId" value="<?= $encRegistrationId ?>">
                                <input type="hidden" name="configId" value="<?= $encConfigId ?>">

                                <!-- General info starts -->
                                <section class="formSection padding-all-25 margin-bottom-25">
                                    <p class="steps fg-subtle accent-emphasis-fg">Step 1 of 6</p>
                                    <h2>Personal Information</h2>
                                    <?php
                                    $name = trim($registration->name);
                                    $name = str_replace("Mr.", "", $name);
                                    $name = str_replace("Mrs.", "", $name);
                                    $name = trim($name);
                                    $readOnly = "readonly";
                                    if (empty($name)) {
                                        $readOnly = "";
                                    }

                                    $fullNameHtml =
                                        <<<HTML
                                        <div class="field">
                                            <label class="required">Name</label>
                                            <input name="fullName" class="validate formControl upper-case" 
                                                type="text" value="{$name}"
                                                {$readOnly}
                                                data-title="Name"
                                                data-required="required"   
                                                data-lang="english"
                                                data-maxlen="100" 
                                                >
                                        </div>
                                    HTML;

                                    $fatherName = trim($registration->fatherName);
                                    $fatherName = str_replace("S/o.", "", $fatherName);
                                    $fatherName = str_replace("D/o.", "", $fatherName);
                                    $fatherName = trim($fatherName);
                                    $readOnly = "readonly";
                                    if (empty($fatherName)) {
                                        $readOnly = "";
                                    }

                                    $fathersNameHtml =
                                        <<<HTML
                                        <div class="field">
                                            <label class="required">Father's Name</label>
                                            <input name="fatherName" class="validate formControl upper-case" 
                                                type="text" value="{$fatherName}" {$readOnly}
                                                data-title="Father's Name"
                                                data-required="required" 
                                                data-lang="english"
                                                data-maxlen="100"
                                                >
                                        </div>
                                    HTML;

                                    $motherNameHtml =
                                        <<<HTML
                                        <div class="field">
                                            <label class="required">Mother's Name</label>
                                            <input name="motherName" class="validate formControl upper-case" type="text" value=""
                                                data-title="Mother's name"
                                                data-required="required" 
                                                data-lang="english"
                                                data-maxlen="100">
                                        </div>
                                    HTML;

                                    $genderOptions =
                                        <<<HTML
                                        <option value=""></option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    HTML;

                                    $genderCombo = <<<HTML
                                        <div class="field">
                                            <label class="required">Gender</label>
                                            <select name="gender" class="validate formControl" 
                                                data-title="Gender"
                                                data-required="required">
                                                $genderOptions
                                            </select>
                                        </div>
                                    HTML;
                                    ?>

                                    <div class="row">
                                        <div class="col-lg-12 col-sm-12">
                                            <?= $fullNameHtml ?>
                                        </div>
                                    </div>

                                    <!-- Father & mother starts -->
                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <?= $fathersNameHtml ?>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <?= $motherNameHtml ?>
                                        </div>
                                    </div>
                                    <!-- Father & mother ends -->

                                    <!-- Gender, DOB and Nationality starts -->
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <?= $genderCombo ?>
                                        </div>
                                        <!-- //date_of_birth field -------->
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="field">
                                                <label class="required">Date of Birth</label>
                                                <input autocomplete="off" name="dob" class="validate swiftDate formControl" data-title="Date of Birth" data-required="required" data-datatype="date" type="text" autocomplete="off" value="">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="field">
                                                <label class="required">Nationality</label>
                                                <input name="nationality" class="validate formControl" data-title="Nationality" data-required="required" type="text" value="Bangladeshi" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Gender, DOB and Nationality ends -->


                                    <div class="note" style="margin-top: 7px; margin-bottom: 0;">
                                        <strong>Note: </strong>You must provide Birth Certificate No. OR, NID No. OR, Passport No.
                                    </div>

                                    <!-- National identity type and number -->
                                    <div class="row">
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="field">
                                                <label class="">Birth Certificate No.</label>
                                                <input name="birthCertNo" class="validate swiftInteger formControl" type="text" data-required="optional" data-maxlen="20" maxlength="20">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="field nidNo">
                                                <label class="">NID No.</label>
                                                <input name="nidNo" class="validate swiftInteger formControl" type="text" data-required="optional" data-maxlen="20" maxlength="20">
                                            </div>
                                        </div>
                                        <div class="col-lg-4 col-sm-12">
                                            <div class="field">
                                                <label class="">Passport No.</label>
                                                <input name="passportNo" class="validate formControl" type="text" data-required="optional" data-maxlen="20" maxlength="20" maxlength="20">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- National identity type and number -->

                                    <div class="sectionNavigation">
                                        <div class="goToNextSection btn btn-default">Next
                                            <img src="<?= BASE_URL ?>/assets/images/next-button.png">
                                        </div>
                                    </div>
                                </section>
                                <!-- General info ends -->

                                <!-- Contact info starts -->
                                <section class="formSection padding-all-25 margin-bottom-25" style="display: none;">
                                    <p class="steps fg-subtle accent-emphasis-fg">Step 2 of 6</p>
                                    <h2>Contact Information Details</h2>

                                    <!-- Mobile number and email -->
                                    <article style="margin-bottom: 5px;">
                                        <div class="row">
                                            <!-- mobileNo -------->
                                            <div class="col-lg-4 col-sm-12">
                                                <div class="field">
                                                    <label class="required">Mobile No.</label>
                                                    <input name="mobileNo" class="validate swiftInteger formControl" data-required="required" data-datatype="mobile" type="text" maxlen="11">
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-sm-12">
                                                <div class="field">
                                                    <label class="required">Retype Mobile No.</label>
                                                    <input name="reMobileNo" class="validate swiftInteger formControl" data-required="required" data-datatype="mobile" data-title="Retype Mobile Number" type="text" maxlen="11">
                                                </div>
                                            </div>
                                            <!-- mobileNo -------->

                                            <!--email -->
                                            <div class="col-lg-4 col-sm-12">
                                                <div class="field">
                                                    <label class="">Email</label>
                                                    <input name="email" data-required="optional" class="validate lower-case formControl" type="text" data-datatype="email" data-maxlen="40" data-title="Email" title="Email Address">
                                                </div>
                                            </div>
                                            <!--email -->
                                        </div>
                                    </article>

                                    <!-- Present address starts -->
                                    <article style="margin-bottom: 5px;">
                                        <h3>Present address</h3>
                                        <div class="row">
                                            <div class="col-lg-12 col-sm-12">
                                                <div class="field">
                                                    <label class="required">Detail Address</label>
                                                    <textarea name="presentAddress" class="validate formControl" data-required="required" data-label="Present address detail" data-maxlen="150" data-title="Present Address"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4 col-sm-12">
                                                <article class="district">
                                                    <label class="required">District</label>
                                                    <select name="presentDist" data-districttype="present" class="presentDistrict validate formControl district-combo" data-required="required" data-title="Present District">
                                                        <option value="">select</option>
                                                        <?php
                                                        foreach ($districts as $district) {
                                                        ?>
                                                            <option value="<?= $district->name ?>"><?= $district->name ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </article>
                                            </div>

                                            <div class="col-lg-4 col-sm-12">
                                                <article class="thana">
                                                    <label class="required">Thana/Upazila</label>
                                                    <select name="presentThana" id="presentThana" class="presentThana validate formControl" data-required="required" data-title="Present Thana/Upazila">

                                                    </select>
                                                </article>
                                            </div>

                                            <div class="col-lg-4 col-sm-12">
                                                <article class="">
                                                    <label class="">Post Code</label>
                                                    <input name="presentGpo" class="validate swiftInteger formControl" data-required="optional" data-exactLen="4" data-title="Present Post Code" type="text" value="" maxlength="4">
                                                </article>
                                            </div>
                                        </div>
                                    </article>
                                    <!-- Present address ends -->

                                    <!-- Permanent address starts -->
                                    <article style="margin-bottom: 5px;">
                                        <h3>Permanent address</h3>

                                        <div class="field">
                                            <label class="required">Detail Address</label>
                                            <textarea name="permanentAddress" class="validate formControl" data-required="required" data-maxlen="150" data-title="Permanent Address"></textarea>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-4 col-sm-12">
                                                <div class="field">
                                                    <label class="required">District</label>

                                                    <select name="permanentDist" data-districttype="permanent" class="permanentDistrict validate formControl district-combo" data-required="required" data-title="Permanent District">
                                                        <option value="">select</option>
                                                        <?php
                                                        foreach ($districts as $district) {
                                                        ?>
                                                            <option value="<?= $district->name ?>"><?= $district->name ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-lg-4 col-sm-12">
                                                <article class="">
                                                    <label class="required">Thana/Upazila</label><img class="label-spinner hidden" src="../../../assets/images/spinner.svg">
                                                    <select name="permanentThana" id="permanentThana" class="permanentThana validate formControl" data-required="required" data-title="Permanent Thana/Upazila">

                                                    </select>

                                                </article>
                                            </div>

                                            <div class="col-lg-4 col-sm-12">
                                                <div class="field">
                                                    <label class="">Post Code</label>
                                                    <input name="permanentGpo" class="validate swiftInteger formControl" data-required="optional" data-exactLen="4" data-title="Permanent Post Code" type="text" value="" maxlength="4">
                                                </div>
                                            </div>
                                        </div>
                                    </article>
                                    <!-- Permanent address ends -->

                                    <div class="sectionNavigation">
                                        <div class="goToPrevSection btn"><img src="<?= BASE_URL ?>/assets/images/prev-button.png">Previous</div>
                                        <div class="goToNextSection btn">Next <img src="<?= BASE_URL ?>/assets/images/next-button.png"></div>
                                    </div>
                                </section>
                                <!-- Contact info ends -->

                                <!-- Education starts -->
                                <section class="formSection padding-all-25 margin-bottom-25" style="display: none;">
                                    <p class="steps fg-subtle accent-emphasis-fg">Step 3 of 6</p>
                                    <H2>Educational Information</H2>

                                    <!-- SSC starts -->
                                    <article class="" style="margin-bottom:35px;">
                                        <h4>S.S.C/Equivalent</h4>

                                        <div class="row">
                                            <div class="col-sm-12 col-lg-6">
                                                <div class="field">
                                                    <label class="required">Exam Name</label>
                                                    <select name="sscExamName" class="validate formControl" data-title="S.S.C/Equivalent Examination Name" data-required="required">
                                                        <option value="">select exam</option>
                                                        <?php
                                                        $sql = "SELECT examination_name AS value, examination_name AS text FROM examinations WHERE level='secondary' AND is_active='yes' ORDER BY serial_number";
                                                        // $db->fetchAsAssoc();
                                                        $secondaryExams = $db->select($sql);
                                                        echo Helpers::createOptions($secondaryExams);
                                                        //required for editing mode
                                                        // echo Helpers::createOptions($secondaryExams, $applicant->secondary_examination_id);
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-lg-6">
                                                <div class="field">
                                                    <label class="required">Board</label>
                                                    <select name="sscBoard" class="validate formControl checkVisibility" data-required="required" data-title="S.S.C/Equivalent Board">
                                                        <option value="">select board</option>
                                                        <?php
                                                        $sql = "SELECT `name` AS value, `name` AS text FROM education_boards ORDER BY `name`";
                                                        $boards = $db->select($sql);
                                                        echo Helpers::createOptions($boards);
                                                        //Required in editing mode
                                                        // echo Helpers::createOptions($boards, $applicant->secondary_education_board_id );
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- SSC Exam Name ends -->

                                        <div class="row">
                                            <div class="col-sm-12 col-lg-4">
                                                <div class="field">
                                                    <label class="required">Roll/ID No.</label>
                                                    <input name="sscRollNo" class="validate formControl swiftInteger checkVisibility" data-title="S.S.C/Equivalent Roll No." data-required="required" data-datatype="integer" maxlength="15" type="text" value="">
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-lg-4">
                                                <div class="field">
                                                    <label class="required">Registration No.</label>
                                                    <input name="sscRegiNo" class="validate formControl swiftInteger checkVisibility" data-title="S.S.C/Equivalent Registration No." data-required="required" data-datatype="integer" maxlength="15" type="text" value="">
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-lg-4">
                                                <div class="field">
                                                    <label class="required">Passing Year</label>
                                                    <input autocomplete="off" name="sscYear" class="validate formControl swiftYear swiftInteger checkVisibility" data-title="S.S.C/Equivalent Passing Year" data-required="required" type="text" value="" maxlength="4" autoComplete="off">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- SSC/Equivalent Details -->
                                        <div class="sscDetails toggleVisibleWrapper">

                                            <!-- SSC Result Type starts -->
                                            <div class="row">
                                                <div class="col-sm-12 col-lg-6">
                                                    <div class="field">
                                                        <label class="required">Group</label>
                                                        <select name="sscGroup" class="validate formControl checkVisibility" data-title="S.S.C/Equivalent Group" data-required="required">
                                                            <option value="">select group</option>
                                                            <option value="Science">Science</option>
                                                            <option value="Humanities">Humanities</option>
                                                            <option value="Business Studies">Business Studies</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 col-lg-6">
                                                    <div class="field">
                                                        <label class="required">Result Type</label>
                                                        <select name="sscResultType" class="sscResultType formControl validate checkVisibility" data-title="S.S.C/Equivalent Result Type" data-required="required">
                                                            <option></option>
                                                            <option value="Division">Division</option>
                                                            <option value="Grade">Grade</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- SSC Result Type ends -->


                                            <!-- SSC Division starts -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="field sscDivisionDetails hidden">
                                                        <label class="required">Division</label>
                                                        <select name="sscDivision" class="formControl validate checkVisibility" data-title="S.S.C/Equivalent Result in Division" data-required="required">
                                                            <option></option>
                                                            <option value="1st Division">1st Division</option>
                                                            <option value="2nd Division">2nd Division</option>
                                                            <option value="3rd Division">3rd Division</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- SSC Division ends -->

                                            <!-- SSC GPA & Scale starts -->
                                            <div class="row">
                                                <div class="col-sm-12 col-lg-6">
                                                    <!-- .sscGradeDetails is required to dynamically show hide -->
                                                    <div class="field sscGradeDetails hidden">
                                                        <label class="required">GPA</label>
                                                        <input name="sscGpa" class="validate formControl swiftFloat checkVisibility" data-title="S.S.C/Equivalent Result in GPA" data-required="required" data-datatype="float" data-minval="0.01" data-maxval="5.00" type="text" placeholder="must be in 0.00 format" maxlength="4">
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 col-lg-6">
                                                    <!-- .sscGradeDetails is required to dynamically show hide -->
                                                    <div class="field sscGradeDetails hidden">
                                                        <label class="required">Scale</label>
                                                        <select name="sscScale" class="validate formControl checkVisibility" data-title="S.S.C/Equivalent GPA Scale" data-required="required">
                                                            <option value="">select scale</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- SSC GPA & Scale ends -->
                                        </div> <!-- SSC/Equivalent Details// -->

                                        <div class="oLevelDetails hidden toggleVisibleWrapper">
                                            <div class="field">
                                                <label class="required">O Level Result Details</label>
                                                <textarea name="oLevelResultDetails" class="validate checkVisibility formControl" data-required="required" placeholder="Result details (separate by comma)"></textarea>
                                            </div>
                                        </div>

                                    </article>
                                    <!-- SSC ends -->


                                    <!-- HSC starts -->
                                    <article style="margin-bottom:35px;">
                                        <h4>H.S.C/Equivalent</h4>
                                        <!-- HSC Exam Name -->
                                        <div class="row">
                                            <div class="col-sm-12 col-lg-6">
                                                <div class="field">
                                                    <label class="required">Exam Name</label>
                                                    <select name="hscExamName" class="validate formControl" data-title="H.S.C/Equivalent Examination Name" data-required="required">
                                                        <option value="">select exam</option>
                                                        <?php
                                                        $sql = "SELECT examination_name AS value, examination_name AS text FROM examinations WHERE level='higher_secondary' AND is_active='yes' ORDER BY serial_number";
                                                        $secondaryExams = $db->select($sql);
                                                        echo Helpers::createOptions($secondaryExams);
                                                        //required for editing mode
                                                        // echo Helpers::createOptions($secondaryExams, $applicant->secondary_examination_id);
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-lg-6">
                                                <div class="field">
                                                    <label class="required">Board</label>
                                                    <select name="hscBoard" class="validate formControl checkVisibility" data-required="required" data-title="H.S.C/Equivalent Board">
                                                        <option value="">select board</option>
                                                        <?php
                                                        $sql = "SELECT `name` AS value, `name` AS text FROM education_boards ORDER BY `name`";
                                                        $boards = $db->select($sql);
                                                        echo Helpers::createOptions($boards);
                                                        //Required in editing mode
                                                        // echo Helpers::createOptions($boards, $applicant->secondary_education_board_id );
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>
                                        <!-- HSC Exam Name ends -->

                                        <div class="row">
                                            <div class="col-sm-12 col-lg-4">
                                                <div class="field">
                                                    <label class="required">Roll/ID No.</label>
                                                    <input name="hscRollNo" class="validate checkVisibility formControl swiftInteger" data-title="H.S.C/Equivalent Roll No." data-required="required" data-datatype="integer" maxlength="15" type="text" value="">
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-lg-4">
                                                <div class="field">
                                                    <label class="required">Registration No.</label>
                                                    <input name="hscRegiNo" class="validate checkVisibility formControl swiftInteger" data-title="H.S.C/Equivalent Registration No." data-required="required" data-datatype="integer" maxlength="15" type="text" value="">
                                                </div>
                                            </div>

                                            <div class="col-sm-12 col-lg-4">
                                                <div class="field">
                                                    <label class="required">Passing Year</label>
                                                    <input name="hscYear" class="validate checkVisibility formControl swiftYear swiftInteger" data-title="H.S.C/Equivalent Passing Year" data-required="required" type="text" value="" maxlength="4" autoComplete="off">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="hscDetails toggleVisibleWrapper">
                                            <!-- HSC Result Type starts -->
                                            <div class="row">
                                                <div class="col-sm-12 col-lg-6">
                                                    <div class="field">
                                                        <label class="required">Group</label>
                                                        <select name="hscGroup" class="validate checkVisibility formControl" data-title="H.S.C/Equivalent Group" data-required="required">
                                                            <option value="">select group</option>
                                                            <option value="Science">Science</option>
                                                            <option value="Humanities">Humanities</option>
                                                            <option value="Business Studies">Business Studies</option>
                                                            <option value="Other">Other</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 col-lg-6">
                                                    <div class="field">
                                                        <label class="required">Result Type</label>
                                                        <select name="hscResultType" class="hscResultType checkVisibility formControl validate" data-title="H.S.C/Equivalent Result Type" data-required="required">
                                                            <option></option>
                                                            <option value="Division">Division</option>
                                                            <option value="Grade">Grade</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- HSC Result Type ends -->

                                            <!-- HSC Division starts -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="field hscDivisionDetails hidden">
                                                        <label class="required">Division</label>
                                                        <select name="hscDivision" class="formControl checkVisibility validate" data-title="H.S.C/Equivalent Result in Division" data-required="required">
                                                            <option></option>
                                                            <option value="1st Division">1st Division</option>
                                                            <option value="2nd Division">2nd Division</option>
                                                            <option value="3rd Division">3rd Division</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- HSC Division ends -->

                                            <!-- HSC GPA & Scale starts -->
                                            <div class="row">
                                                <div class="col-sm-12 col-lg-6">
                                                    <!-- .hscGradeDetails is required to dynamically show hide -->
                                                    <div class="field hscGradeDetails hidden">
                                                        <label class="required">GPA</label>
                                                        <input name="hscGpa" class="validate checkVisibility formControl swiftFloat" type="text" placeholder="must be in 0.00 format" data-title="H.S.C/Equivalent Result in GPA" data-required="required" data-datatype="float" data-minval="0.01" data-maxval="5.00" maxlength="4">
                                                    </div>
                                                </div>

                                                <div class="col-sm-12 col-lg-6">
                                                    <!-- .hscGradeDetails is required to dynamically show hide -->
                                                    <div class="field hscGradeDetails hidden">
                                                        <label class="required">Scale</label>
                                                        <select name="hscScale" class="validate checkVisibility formControl" data-title="H.S.C/Equivalent GPA Scale" data-required="required">
                                                            <option value="">select scale</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- HSC GPA & Scale ends -->
                                        </div>

                                        <div class="aLevelDetails hidden toggleVisibleWrapper">
                                            <div class="field">
                                                <label class="required">A Level Result Details</label>
                                                <textarea name="aLevelResultDetails" class="validate checkVisibility formControl" data-required="required" placeholder="Result details (separate by comma)"></textarea>
                                            </div>
                                        </div>
                                    </article>
                                    <!-- HSC ends -->

                                    <!--  LL.B (Hons.) or LL.B (Pass) starts -->
                                    <article style="margin-bottom:35px;">
                                        <h4>Graduation (LL.B)</h4>
                                        <!-- llbExam -->
                                        <div class="row">
                                            <div class="col-sm-12 col-lg-7">
                                                <div class="field">
                                                    <label class="required">Exam Name</label>
                                                    <!-- .examName is required to dynamically load result details html markup from server -->
                                                    <select name="llbExam" class="examName validate formControl" data-title="Graduation (LL.B) Examination Name" data-required="required">
                                                        <option value="">select exam</option>
                                                        <option value="LL.B (Hons)">LL.B (Hons)</option>
                                                        <option value="LL.B (Pass)">LL.B (Pass)</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-12 col-lg-5">
                                                <div class="field">
                                                    <label class="required">ID/Roll/Registration No.</label>
                                                    <input name="llbId" class="validate formControl" data-title="LL.B ID/Roll/Registration" data-required="required" data-maxlen="20" type="text" maxlength="20" value="" placeholder="">
                                                </div>
                                            </div>
                                        </div>
                                        <!-- llbExam -->

                                        <!-- 
                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="field">
                                                        <label class="label required">Major Subjects</label>
                                                        <input name="llbSubject" class="validate formControl suggestSubject" data-title="Graduation (LL.B) Subject" data-required="required" data-maxlen="100" type="text">
                                                    </div>
                                                </div>
                                            </div> -->

                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="field">
                                                    <label class="label required">Degree Obtained From</label>
                                                    <select name="llbCountryName" class="validate formControl degreeObtainedFrom" data-title="Graduation (LL.B) Degree Obtained From">
                                                        <option value="Bangladesh">Bangladesh</option>
                                                        <option value="Other">Foreign</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- //degree obtained from -->
                                        <!-- .otherCountryName is used to show/hide this row -->
                                        <div class="row otherCountryName hidden">
                                            <div class="col-lg-12 col-sm-12">

                                                <div class="field ">
                                                    <label class="required">Degree Obtained From Country</label>
                                                    <input name="llbOtherCountryName" class="country formControl" type="text" placeholder="write country name" data-required="required" data-title="LL.B Degree Country (Ohter) Name">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- .hasEquivalentCertificate is used to show/hide this row -->
                                        <div class="row hasEquivalentCertificate hidden">
                                            <div class="col-sm-12">
                                                <label class="label required">Has Equivalent Certificate from Bar Council/U.G.C/Ministry of Education?</label>
                                                <div class="radio-group formControl">
                                                    <label class="radio-label">
                                                        <input type="radio" class="validate formControl" name="hasLlbEquivalentCertificate" data-required="required" data-label="LL.B Equivalent Certificate" value="yes" checked>Yes
                                                    </label>
                                                    <label class="radio-label">
                                                        <input type="radio" class="radio formControl" name="hasLlbEquivalentCertificate" value="no">No (Please contact with Bar Council or Concern Authority)
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field">
                                            <label class="required">University/Institute</label>
                                            <input name="llbUni" class="universityName formControl validate" value="<?= $registration->universityName ?>" data-required="required" data-title="LL.B Degree - University/Institute" type="text" readonly>
                                        </div>

                                        <!-- Graduation (LLB) Result Type starts -->
                                        <div class="row">
                                            <div class="col-12">
                                                <div class="field">
                                                    <label class="required">Result Type</label>
                                                    <!-- .higherEducationResultType is required to load result details html markup from server-->
                                                    <select name="llbResultType" class="higherEducationResultType formControl validate" data-examname="llb" data-exam-type-name="Graduation (LL.B)" data-title="Graduation (LL.B) Result Type" data-required="required">
                                                        <option></option>
                                                        <option value="Division">Division</option>
                                                        <option value="Class">Class</option>
                                                        <option value="Grading">Grading</option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div> <!-- Graduation (LLB) Result Type ends -->

                                        <div class="dynamicContent">
                                            <!-- content comes on  .higherEducationResultType change event-->
                                        </div>
                                    </article>
                                    <!-- LL.B (Hons.) or LL.B (Pass) ends -->


                                    <!-- Graduation (in other discipline) starts -->
                                    <article class="" style="margin-bottom:35px;">
                                        <?php // .educationDetailsToggle is required to handle checkbox change event 
                                        ?>
                                        <h4><input name="hasOtherGrad" class="educationDetailsToggle" value="hasOtherGrad" type="checkbox"> Graduation (Other)</h4>
                                        <p class="justify">Optional. If you have any other graduation degree, please put tick on the checkbox above and provide the information below. </p>

                                        <!-- <div class="visibleWrapper" style="display: none;"> -->
                                        <div class="toggleVisibleWrapper hidden">
                                            <!-- graduationOther -->
                                            <div class="row">
                                                <div class="col-sm-12 col-lg-7">
                                                    <div class="field">
                                                        <label class="required">Exam Name</label>
                                                        <!-- .examName is required to dynamically load result details html markup from server -->
                                                        <select name="gradOtherExam" class="examName validate formControl checkVisibility" data-title="Graduation(Other) Examination Name" data-required="required">
                                                            <option value="">select exam</option>
                                                            <option value="B.B.A">B.B.A</option>
                                                            <option value="B.A (Hons)">B.A (Hons)</option>
                                                            <option value="B.A (Pass)">B.A (Pass)</option>
                                                            <option value="B.B.S (Hons)">B.B.S (Hons)</option>
                                                            <option value="B.Com (Hons)">B.Com (Hons)</option>
                                                            <option value="B.Com (Pass)">B.Com (Pass)</option>
                                                            <option value="B.Sc (Hons)">B.Sc (Hons)</option>
                                                            <option value="B.Sc (Pass)">B.Sc (Pass)</option>
                                                            <option value="B.S.S (Hons)">B.S.S (Hons)</option>
                                                            <option value="B.S.S (Pass)">B.S.S (Pass)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-lg-5">
                                                    <div class="field">
                                                        <label class="required">ID/Roll/Registration No.</label>
                                                        <input name="gradOtherId" class="validate formControl checkVisibility" data-title="Graduation (Other) ID/Roll/Registration" data-required="required" data-maxlen="20" type="text" maxlength="20" value="" placeholder="">
                                                    </div>
                                                </div>
                                            </div>


                                            <!-- <div class="row">
                                            <div class="col-sm-12">
                                                <div class="field">
                                                    <label class="label required">Major Subjects</label>
                                                    <input name="gradOtherSubject" class="validate formControl checkVisibility suggestSubject" data-title="Graduation (Other) Subject" data-required="required" data-maxlen="100" type="text">
                                                </div>
                                            </div>
                                        </div> -->


                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="field">
                                                        <label class="label required">Degree Obtained From</label>
                                                        <select name="gradOtherCountryName" class="validate formControl degreeObtainedFrom" data-title="Graduation (Other) Degree Obtained From">
                                                            <option value="Bangladesh">Bangladesh</option>
                                                            <option value="Other">Foreign</option>
                                                        </select>

                                                        <!-- <div class="radio-group formControl">
                                                    <label class="radio-label">
                                                        <input type="radio" 
                                                            class="validate formControl degreeObtainedFrom" 
                                                            name="gradOtherCountryName" 
                                                            data-required="required" 
                                                            data-label="" value="Bangladesh" 
                                                            checked
                                                            >Bangladesh
                                                    </label>
                                                    <label class="radio-label">
                                                        <input type="radio" class="radio degreeObtainedFrom formControl" name="gradOtherCountryName" value="Other">Foreign
                                                    </label>
                                                </div> -->
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- //degree obtained from -->
                                            <!-- .otherCountryName is used to show/hide this row -->
                                            <div class="row otherCountryName hidden">
                                                <div class="col-lg-12 col-sm-12">
                                                    <!-- .dismissalReasonField is required to show/hide this div-->
                                                    <div class="field ">
                                                        <label class="required">Degree Obtained From Country</label>
                                                        <input name="gradOtherOtherCountryName" class="country formControl" type="text" placeholder="write country name" data-required="required" data-title="LL.B Degree Country (Ohter) Name">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- .hasEquivalentCertificate is used to show/hide this row -->
                                            <div class="row hasEquivalentCertificate hidden">
                                                <div class="col-sm-12">
                                                    <label class="label required">Has Equivalent Certificate from Bar Council/U.G.C/Ministry of Education?</label>
                                                    <div class="radio-group formControl">
                                                        <label class="radio-label">
                                                            <input type="radio" class="validate formControl" name="hasGradOtherEquivalentCertificate" data-required="required" data-label="LL.B Equivalent Certificate" value="yes" checked>Yes
                                                        </label>
                                                        <label class="radio-label">
                                                            <input type="radio" class="radio formControl" name="hasGradOtherEquivalentCertificate" value="no">No (Please contact with Bar Council or Concern Authority)
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="field">
                                                <label class="required">University/Institute</label>
                                                <input name="gradOtherUni" class="universityName checkVisibility validate formControl" data-required="required" data-title="Graduation (Other) University/Institute" type="text">
                                            </div>

                                            <!-- Graduation (Other) Result Type starts -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label class="required">Result Type</label>
                                                        <!-- .higherEducationResultType is required to load result details html markup from server-->
                                                        <select name="gradOtherResultType" class="higherEducationResultType formControl validate checkVisibility" data-examname="llb" data-exam-type-name="Graduation (Other)" data-title="Graduation (Other) Result Type" data-required="required">
                                                            <option></option>
                                                            <option value="Division">Division</option>
                                                            <option value="Class">Class</option>
                                                            <option value="Grading">Grading</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- HSC Result Type ends -->

                                            <div class="dynamicContent">
                                                <!-- content comes on  .higherEducationResultType change event-->
                                            </div>
                                        </div>

                                    </article>
                                    <!-- Graduation (in other discipline) ends -->

                                    <!-- Masters/ Post Graduation starts -->
                                    <article style="margin-bottom:35px;">
                                        <?php // .educationDetailsToggle is required to handle checkbox change event 
                                        ?>
                                        <h4><input name="hasMasters" class="educationDetailsToggle" value="hasMasters" type="checkbox"> Post Graduation</h4>
                                        <p>Optional. If you have any post graduation or masters degree, please put tick on the checkbox above and provide the information below. </p>
                                        <div class="toggleVisibleWrapper hidden">
                                            <!-- style="display: none;" -->
                                            <!-- llbExam -->
                                            <div class="row">
                                                <div class="col-sm-12 col-lg-7">
                                                    <div class="field">
                                                        <label class="required">Exam Name</label>
                                                        <!-- .examName is required to dynamically load result details html markup from server -->
                                                        <select name="mastersExam" class="examName validate checkVisibility formControl" data-title="Post Graduation Examination Name" data-required="required">
                                                            <option value="">select exam</option>
                                                            <option value="LL.M">LL.M</option>
                                                            <option value="M.B.A">M.B.A</option>
                                                            <option value="M.B.S">M.B.S</option>
                                                            <option value="M.Sc">M.Sc</option>
                                                            <option value="M.Com">M.Com</option>
                                                            <option value="M.S.S">M.S.S</option>
                                                            <option value="M.A">M.A</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-lg-5">
                                                    <div class="field">
                                                        <label class="required">ID/Roll/Registration No.</label>
                                                        <input name="mastersId" class="validate formControl checkVisibility" type="text" maxlength="20" value="" data-title="LL.B ID/Roll/Registration" data-required="required" data-maxlen="20" placeholder="">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- masteresExam -->


                                            <!-- <div class="row">
                                            <div class="col-sm-12">
                                                <div class="field">
                                                    <label class="label required">Major Subjects</label>
                                                    <input name="mastersSubject" class="validate formControl checkVisibility suggestSubject" data-required="required" data-maxlen="100" type="text">
                                                </div>
                                            </div>
                                        </div> -->

                                            <div class="row">
                                                <div class="col-sm-12">
                                                    <div class="field">
                                                        <label class="label required">Degree Obtained From</label>
                                                        <select name="mastersCountryName" class="validate formControl degreeObtainedFrom">
                                                            <option value="Bangladesh">Bangladesh</option>
                                                            <option value="Other">Foreign</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- //degree obtained from -->
                                            <!-- .mastersCountryName is used to show/hide this row -->
                                            <div class="row otherCountryName hidden">
                                                <div class="col-lg-12 col-sm-12">
                                                    <!-- .dismissalReasonField is required to show/hide this div-->
                                                    <div class="field ">
                                                        <label class="required">Degree Obtained From Country</label>
                                                        <input name="mastersOtherCountryName" class="country formControl" type="text" placeholder="write country name" data-required="required" data-title="Masters Country (Ohter) Name">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- <div class="field">
                                            <label class="required">Degree Obtained From Country</label>
                                            <input name="mastersCountry" class="country checkVisibility formControl" type="text" value="Bangladesh" placeholder="write country name" data-required="required" data-title="Masters Degree Country name">
                                        </div> -->

                                            <!-- .hasEquivalentCertificate is used to show/hide this row -->
                                            <div class="row hasEquivalentCertificate hidden">
                                                <div class="col-sm-12">
                                                    <label class="label required">Has Equivalent Certificate from Bar Council/U.G.C/Ministry of Education?</label>
                                                    <div class="radio-group formControl">
                                                        <label class="radio-label">
                                                            <input type="radio" class="validate formControl" name="hasMastersEquivalentCertificate" data-required="required" data-label="Masters Equivalent Certificate" value="yes" checked>Yes
                                                        </label>
                                                        <label class="radio-label">
                                                            <input type="radio" class="radio formControl" name="hasMastersEquivalentCertificate" value="no">No (Please contact with Bar Council or Concern Authority)
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>



                                            <div class="field">
                                                <label class="required">University/Institute</label>
                                                <input name="mastersUni" class="universityName checkVisibility validate formControl" data-required="required" data-title="University" type="text">
                                            </div>

                                            <!-- Masters Result Type starts -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="field">
                                                        <label class="required">Result Type</label>
                                                        <!-- .higherEducationResultType is required to load result details html markup from server-->
                                                        <select name="mastersResultType" class="higherEducationResultType formControl validate checkVisibility" data-examname="llb" data-exam-type-name="Post Graduation" data-title="Post Graduation Result Type" data-required="required">
                                                            <option></option>
                                                            <option value="Division">Division</option>
                                                            <option value="Class">Class</option>
                                                            <option value="Grading">Grading</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- HSC Result Type ends -->

                                            <div class="dynamicContent">
                                                <!-- content comes on  .higherEducationResultType change event-->
                                            </div>
                                        </div>
                                    </article>
                                    <!-- Post Graduation ends -->



                                    <!-- Bar-at-law starts -->
                                    <article style="margin-bottom:35px;">
                                        <?php // .educationDetailsToggle is required to handle checkbox change event 
                                        ?>
                                        <h4><input name="hasBarAtLaw" class="educationDetailsToggle" value="hasBarAtLaw" type="checkbox"> Bar-at-law</h4>
                                        <p>Optional. Please put tick on the checkbox above and provide the information below. </p>
                                        <div class="toggleVisibleWrapper hidden">
                                            <!-- style="display: none;" -->
                                            <!-- llbExam -->
                                            <div class="row">
                                                <div class="col-sm-12 col-lg-9">
                                                    <div class="field">
                                                        <label class="required">Name of the Institute</label>
                                                        <input name="barAtLawName" class="validate formControl checkVisibility" type="text" maxlength="100" value="" data-required="required" data-maxlen="100">
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-lg-3">
                                                    <div class="field">
                                                        <label class="required">Awarded Year</label>
                                                        <input name="barAtLawYear" type="text" class="validate swiftYear formControl checkVisibility" autocomplete="off" maxlength="4" type="text" data-required="required" data-title="Awarded Year" data-datatype="integer">
                                                    </div>
                                                </div>

                                            </div>
                                            <!-- llbExam -->
                                        </div>
                                    </article>
                                    <!-- Bar-at-law ends -->

















































                                    <div class="sectionNavigation">
                                        <div class="goToPrevSection btn"><img src="<?= BASE_URL ?>/assets/images/prev-button.png">Previous</div>
                                        <div class="goToNextSection btn">Next <img src="<?= BASE_URL ?>/assets/images/next-button.png"></div>
                                    </div>
                                </section>
                                <!-- Education ends -->

                                <!-- Bar Related starts -->
                                <section class="formSection padding-all-25 margin-bottom-25 " style="display: none;">
                                    <p class="steps fg-subtle accent-emphasis-fg">Step 4 of 6</p>
                                    <h2>Bar Related Information</h2>

                                    <div class="row">
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="field">
                                                <label class="required">Registration No.</label>
                                                <input name="regNo" class="formControl" type="text" value="<?= $registration->regNo ?>" readonly />
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-sm-12">
                                            <div class="field">
                                                <label class="required">Registration Year</label>
                                                <input name="regYear" class="formControl validate" type="text" value="<?= $registration->regYear ?>" readonly />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 col-lg-6">
                                            <div class="field">
                                                <label class="required">Applicant Type</label>
                                                <input name="applicantType" class="formControl validate" data-required="required" type="text" value="<?= $registration->applicantType ?>" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-lg-6">
                                            <div class="field">
                                                <label>Pupilage Contract Date</label>
                                                <input name="pupilageContractDate" class="validate formControl" data-required="required" data-datatype="date" type="text" autocomplete="off" value="<?= $clock->toString($pupilageContractDate, DatetimeFormat::BdDate()) ?>" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- //Bar association -->
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-6">
                                            <div class="field">
                                                <label class="required">Name of Bar Association</label>
                                                <?php
                                                if (isset($registration->barName) && !empty($registration->barName)) {
                                                ?>
                                                    <input type="text" name="barName" class="validate formControl" data-required="required" value="<?= strtoupper($registration->barName) ?>" readonly>
                                                <?php
                                                } else {
                                                ?>
                                                    <select name="barName" class="BarName validate formControl" data-required="required" data-title="Name of Bar Association">
                                                        <option value="">Select Bar Name</option>
                                                        <?php
                                                        foreach ($bars as $bar) {
                                                        ?>
                                                            <option value="<?= $bar->barName ?>"><?= $bar->barName ?></option>
                                                        <?php
                                                        }
                                                        ?>
                                                    </select>
                                                <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-lg-6">
                                            <div class="field seniorAdvocateName">
                                                <label class="required">Name of Senior Advocate</label>
                                                <input name="seniorAdvocateName" class="validate formControl upper-case" type="text" value="<?= $registration->seniorAdvocateName ?>" readonly />
                                            </div>
                                        </div>
                                    </div>
                                    <br><br>

                                    <!-- //Case List -->
                                    <?php
                                    $caseDetails = <<<HTML
                                            <div class="row">
                                                <div class="col-sm-12 col-lg-6">
                                                    <div class="field">
                                                        <label class="required">Case Number With Section</label>
                                                        <textarea name="caseNumberWithSection[]" class="validate formControl" data-lang="english" data-required="required" data-maxlen="255" placeholder="bangla not allowed, maximum 255 chars"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-lg-6">
                                                    <div class="field">
                                                        <label class="required">Name of the Court</label>
                                                        <textarea name="nameOfTheCourt[]" class="validate formControl"  data-required="required" data-maxlen="255" placeholder="bangla not allowed, maximum 255 chars"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-lg-6">
                                                    <div class="field">
                                                        <label class="required">Name of the Parties</label>
                                                        <textarea name="nameOfTheParties[]" class="validate formControl" data-required="required" data-maxlen="255"  placeholder="bangla not allowed, maximum 255 chars"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-lg-6">
                                                    <div class="field">
                                                        <label class="required">On behalf of</label>
                                                        <textarea name="onBehalfOf[]" class="validate formControl" data-required="required" data-maxlen="255"  placeholder="bangla not allowed, maximum 255 chars"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-sm-12 col-lg-6">
                                                    <div class="field">
                                                        <label class="required">Present Position</label>
                                                        <textarea name="presentPosition[]" class="validate formControl" data-required="required" data-maxlen="255"  placeholder="bangla not allowed, maximum 255 chars"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        HTML;
                                    ?>

                                    <?php if ($registration->hasBarAtLaw == 0) {  ?>
                                        <h4>List of 5 criminal cases</h4>
                                        <div class="case">
                                            <h5>Criminal Case-1:</h5>
                                            <?= $caseDetails ?>
                                        </div>
                                        <div class="case">
                                            <h5>Criminal Case-2:</h5>
                                            <?= $caseDetails ?>
                                        </div>
                                        <div class="case">
                                            <h5>Criminal Case-3:</h5>
                                            <?= $caseDetails ?>
                                        </div>
                                        <div class="case">
                                            <h5>Criminal Case-4:</h5>
                                            <?= $caseDetails ?>
                                        </div>
                                        <div class="case">
                                            <h5>Criminal Case-5:</h5>
                                            <?= $caseDetails ?>
                                        </div>

                                        <br><br>
                                        <h4>List of 5 civil cases</h4>
                                        <div class="case">
                                            <h5>Civil Case-1:</h5>
                                            <?= $caseDetails ?>
                                        </div>
                                        <div class="case">
                                            <h5>Civil Case-2:</h5>
                                            <?= $caseDetails ?>
                                        </div>
                                        <div class="case">
                                            <h5>Civil Case-3:</h5>
                                            <?= $caseDetails ?>
                                        </div>
                                        <div class="case">
                                            <h5>Civil Case-4:</h5>
                                            <?= $caseDetails ?>
                                        </div>
                                        <div class="case">
                                            <h5>Civil Case-5:</h5>
                                            <?= $caseDetails ?>
                                        </div>
                                    <?php } ?>
                                    <!-- Case List// -->

                                    <div class="sectionNavigation">
                                        <div class="goToPrevSection btn"><img src="<?= BASE_URL ?>/assets/images/prev-button.png">Previous</div>
                                        <div class="goToNextSection btn">Next <img src="<?= BASE_URL ?>/assets/images/next-button.png"></div>
                                    </div>
                                </section>
                                <!-- Bar Related ends -->

                                <!-- Other information starts -->
                                <section class="formSection padding-all-25 margin-bottom-25 " style="display: none;">
                                    <p class="steps fg-subtle accent-emphasis-fg">Step 5 of 6</p>
                                    <h2>Other Information</h2>
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-12">
                                            <div class="field">
                                                <label class="required">Is engaged in any business profession, service or vocation in Bangladesh?</label>
                                                <div class="radio-group formControl">
                                                    <label class="radio-label">
                                                        <input type="radio" class="validate formControl" name="isEngaged" data-required="required" data-label="Engagement status" value="Yes">Yes
                                                    </label>
                                                    <label class="radio-label">
                                                        <input type="radio" class="radio formControl" name="isEngaged" value="No">No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-lg-12">
                                            <div class="field hidden">
                                                <label class="required">Nature of the engagement in any business profession, service or vocation in Bangladesh</label>
                                                <input name="engagementNature" class="validate formControl" data-required="required" data-datatype="string" data-maxlen="150" type="text" autocomplete="off" value="">
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-lg-12">
                                            <div class="field hidden">
                                                <label class="required">Place of the engagement in any business profession, service or vocation in Bangladesh</label>
                                                <input name="engagementPlace" class="validate formControl" data-required="required" data-datatype="string" data-maxlen="100" type="text" autocomplete="off" value="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-12 col-lg-12">
                                            <div class="field">
                                                <label class="required">Declared Insolvent?</label>
                                                <div class="radio-group formControl">
                                                    <label class="radio-label">
                                                        <input type="radio" class="validate formControl" name="declaredInsolvent" data-required="required" data-label="Insolvent status" value="Yes">Yes
                                                    </label>
                                                    <label class="radio-label">
                                                        <input type="radio" class="radio formControl" name="declaredInsolvent" value="No">No
                                                    </label>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- /Dismissal -->
                                    <div class="row">
                                        <div class="col-sm-12 col-lg-12">
                                            <div class="field">
                                                <label class="required">Dismissed from the service of Government or of any public statutory corporation?</label>
                                                <div class="radio-group formControl">
                                                    <label class="radio-label">
                                                        <input type="radio" class="validate formControl" name="isDismissed" data-required="required" data-label="Dismissal status" value="Yes">Yes
                                                    </label>
                                                    <label class="radio-label">
                                                        <input type="radio" class="radio formControl" name="isDismissed" value="No">No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-lg-12">
                                            <div class="field hidden">
                                                <label class="required">Dismissal date</label>
                                                <input type="text" name="dismissalDate" class="validate formControl" data-required="required" data-datatype="date" placeholder="dd-mm-yyyy">
                                            </div>
                                        </div>

                                        <div class="col-sm-12 col-lg-12">
                                            <div class="field hidden">
                                                <label class="required">Dismissal reason</label>
                                                <input name="dismissalReason" class="validate formControl" data-required="required" data-datatype="string" data-maxlen="100" type="text">
                                            </div>
                                        </div>
                                    </div> <!-- Dismissal/ -->

                                    <!-- /Conviction -->
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="field">
                                                <label class="required">Convicted of any offence involving moral turpitude?</label>
                                                <div class="radio-group formControl">
                                                    <label class="radio-label">
                                                        <input type="radio" class="validate formControl" name="isConvicted" data-required="required" data-label="Conviction status" value="Yes">Yes
                                                    </label>
                                                    <label class="radio-label">
                                                        <input type="radio" class="radio formControl" name="isConvicted" value="No">No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="field hidden">
                                                <label class="required">Conviction Date</label>
                                                <input type="text" name="convictionDate" class="validate formControl" data-datatype="date" data-required="required" placeholder="dd-mm-yyyy">
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="field hidden">
                                                <label class="required">Conviction particulars</label>
                                                <input type="text" name="convictionParticulars" data-datatype="string" data-required="required" data-maxlen="150">
                                            </div>
                                        </div>
                                    </div><!-- Conviction/ -->

                                    <!-- // previously been cancelled -->
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="field">
                                                <label class="required">Whether the enrolment of the applicant has previously been cancelled by the Bar council?</label>
                                                <div class="radio-group formControl">
                                                    <label class="radio-label">
                                                        <input type="radio" class="validate formControl" name="isCancelledPreviously" data-required="required" data-label="Previous enrolment cancellation status" value="Yes">Yes
                                                    </label>
                                                    <label class="radio-label">
                                                        <input type="radio" class="radio formControl" name="isCancelledPreviously" value="No">No
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- previously been cancelled // -->

                                    <!-- /Previous roll and year-->
                                    <?php
                                    if ($registration->applicantType == "Re-appeared") {
                                    ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="field">
                                                    <label for="" class="required">Last written/preliminary examination roll no.</label>
                                                    <input type="text" name="lastRoll" class="integer" maxlength="10">
                                                </div>
                                            </div>
                                            <div class="col-lg-12">
                                                <div class="field">
                                                    <label for="" class="validate">Last written/preliminary examination year</label>
                                                    <input autocomplete="off" type="text" name="lastWrittenExamYear" class="validate swiftYear formControl checkVisibility">
                                                </div>
                                            </div>
                                        </div>
                                    <?php
                                    }
                                    ?>
                                    <!-- Previous roll and year // -->

                                    <br><br>
                                    <div class="sectionNavigation">
                                        <div class="goToPrevSection btn"><img src="<?= BASE_URL ?>/assets/images/prev-button.png">Previous</div>
                                        <div class="goToNextSection btn">Next <img src="<?= BASE_URL ?>/assets/images/next-button.png"></div>
                                    </div>
                                </section>
                                <!-- Other information ends -->


                                <section class="formSection padding-all-25 margin-bottom-25" style="display: none;">
                                    <p class="steps fg-subtle accent-emphasis-fg">Step 6 of 6</p>
                                    <!-- Photo upload starts -->
                                    <?php
                                    $photo_path = BASE_URL . "/assets/images/default-photo.jpg";
                                    $signature_path = BASE_URL . "/assets/images/default-signature.jpg";
                                    // if ($isNewApplicant) {
                                    //     $photo_path = BASE_URL . "assets/images/default-photo.jpg";
                                    //     $signature_path = BASE_URL . "assets/images/default-signature.jpg";
                                    // } 
                                    // else {
                                    //     $photo_path = BASE_URL . 'applicant-images/photos/' . $applicant->invoiceCode . '.jpg';
                                    //     $signature_path = BASE_URL . '/applicant-images/signatures/' . $applicant->invoiceCode . '.jpg';
                                    // }
                                    ?>

                                    <div class="uploader field formControl">
                                        <div style="border:0;">
                                            <h2>Upload Photo</h2>
                                            <div class="preview-and-instruction">
                                                <div class="preview">
                                                    <img name="ApplicantPhoto" id="ApplicantPhotoImage" src="<?= $photo_path; ?>" style="width: 150px;">
                                                </div>
                                                <div class="instruction">Photo dimension must be 300X300 pixels and size less than 100 kilobytes.</div>
                                            </div>
                                            <div class="file-input">
                                                <!-- <input type="file" title="Applicant's Photo" name="ApplicantPhoto" id="ApplicantPhoto" class="photo formControl" data-required="required" data-title="Applicant's Photo" accept="image/jpeg"> -->
                                            </div>

                                            <label class="btn outline">
                                                <!-- <input type="file" style="display: none;"> -->

                                                <input type="file" title="Applicant's Photo" name="ApplicantPhoto" id="ApplicantPhoto" class="photo formControl" data-required="required" data-title="Applicant's Photo" accept="image/jpeg" style="display: none;">
                                                Upload anything
                                            </label>
                                        </div>

                                        <div style="border:0;">
                                            <h2>Upload Signature</h2>
                                            <div class="preview-and-instruction">
                                                <div class="preview">
                                                    <img name="ApplicantSignature" id="ApplicantSignatureImage" src="<?php echo $signature_path; ?>" style="width:150px;">
                                                </div>
                                                <div class="instruction">
                                                    Photo dimension must be 300X80 pixels and size less than 100 kilobytes.
                                                </div>
                                            </div>
                                            <div class="file-input">
                                                <input type="file" title="Applicant's Signature" name="ApplicantSignature" id="ApplicantSignature" class="photo formControl" data-required="required" data-title="Applicant's Signature" accept="image/jpeg">
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Photo upload ends -->



                                    <article class="margin-bottom-25">
                                        <h2>Declaration</h2>
                                        <input type="checkbox" id="DeclarationApproval" class="formControl" style="margin-left: -1px; margin-right: 13px; margin-top: 5px;"> I declare that the information provided in this form is correct, true and complete to the best of my knowledge and belief. If any information is found false, incorrect and incomplete or if any ineligibility is detected before or after the examination, any action can be taken against me by the Bar Council.
                                    </article>

                                    <div class="sectionNavigation">
                                        <div class="goToPrevSection btn">Previous</div>
                                        <div class="btn" id="showPreview">Preview</div>
                                    </div>


                                </section>

                                <div id="submitSection" style="display: none;">
                                    <div id="closePreview" class="btn">Back to form</div>
                                    <!-- Submit button is here -->
                                    <?php
                                    if (ENVIRONMENT == "DEVELOPMENT") {
                                        $sumitButton =
                                            <<<HTML
                                            <!-- <div class="btn" id="showPreview" style="margin:auto; position:fixed; top:150px; right:50px;">Preview</div> -->
                                            <!-- <button id="submit-button" class="form-submit-button" style="height:60px; width:200px; margin:auto; position:fixed; top:50px; right:50px;">Submit</button> -->
                                            <!-- <button id="submit" type="button" class="form-submit-button" style="height:60px; width:200px; margin:auto; position:fixed; top:50px; right:50px;">Submit</button> -->

                                            <input type="submit" class="btn btn-dark btn-large form-submit-button" value="Submit" style="height:60px; width:200px; margin:auto; position:fixed; top:50px; right:50px;">

                                        HTML;
                                    } else {
                                        $sumitButton =
                                            <<<HTML
                                        
                                        <!-- <button id="submit" type="button" class="form-submit-button btn btn-lg btn-success">Submit</button> -->
                                        <input type="submit" class="form-submit-button btn btn-lg btn-success" value="Submit">
                                        HTML;
                                    }
                                    echo $sumitButton;
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div><!-- .content// -->

                    <!-- 
                    <aside style="display: flex; flex-direction: column;">
                        asdsdaf
                    </aside> 
                    -->
                </div><!-- .container// -->
            </main>
            <footer class="footer">
                <?= Footer::prepare() ?>
            </footer>
        </div>

        <script>
            var baseUrl = '<?php echo BASE_URL; ?>';
        </script>
        <?php
        Required::jquery()->hamburgerMenu()->sweetModalJS()->airDatePickerJS()->moment()->mobileValidator()->swiftSubmit()->swiftChanger()->SwiftNumeric();
        ?>
        <script src="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.min.js" ;></script>
        <script src="js/form.js?v=<?= time() ?>"></script>

    </body>

</html>