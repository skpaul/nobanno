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
if (!isset($_GET["session-id"]) || empty(trim($_GET["session-id"]))) {
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
                <h1 class="width-full mt-150 accent-fg">Approve Request
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
                    if (!isset($_GET["request-id"]) || empty(trim($_GET["request-id"]))) {
                        HttpHeader::redirect(BASE_URL . "/sorry.php");
                    }

                    $encRequestId = trim($_GET["request-id"]);

                    try {
                        $requestId =  $endecryptor->decrypt($encRequestId);
                    } catch (\Exception $exp) {
                        $logger->createLog($exp->getMessage());
                        HttpHeader::redirect(BASE_URL . "/sorry.php");
                    }

                    $requestDatas = $db->select("SELECT * FROM lc_enrolment_registrations_update_request WHERE requestId=$requestId");
                    if (count($requestDatas) != 1) {
                        //HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Registration not found or invalid registration information.");
                    }
                    $requestData = $requestDatas[0];
                    ?>

                    <?php if (strtolower($requestData->hasApproved) != "pending") { ?>
                        <div class="mb-100 accent-fg">
                            <h3>This request already <?= $requestData->hasApproved ?>.</h3>
                        </div>
                    <?php } ?>

                    <?php
                    // $cinfos = $db->select("SELECT * FROM lc_enrolment_registrations WHERE regNo = $requestData->regNo AND regYear=$requestData->regYear");
                    // $cinfo = $cinfos[0];

                    $cinfo = ($db->select("SELECT * FROM lc_enrolment_cinfo WHERE cinfoId = $requestData->cinfoId"))[0];
                    $education = ($db->select("SELECT llbUni FROM lc_enrolment_higher_educations WHERE cinfoId = $requestData->cinfoId"))[0];
                    ?>

                    <div class="mb-150">
                        <span class="accent-emphasis-fg bold">Reg. No. :</span>&nbsp;&nbsp;&nbsp;<?= $cinfo->regNo ?>
                        <span class="accent-emphasis-fg bold ml-100">Reg year :</span>&nbsp;&nbsp;&nbsp;<?= $cinfo->regYear ?>
                        <span class="accent-emphasis-fg bold ml-100"> Mobile :</span>&nbsp;&nbsp;&nbsp;<?= $cinfo->mobileNo ?><br>
                        <span class="accent-emphasis-fg bold">Remarks :</span>&nbsp;&nbsp;&nbsp;<?= $requestData->remarks ?>
                    </div>

                    <table>
                        <thead>
                            <tr>
                                <th style="width: 300px;"></th>
                                <th style="min-width: 300px; text-align:left;">&nbsp;&nbsp;&nbsp; Existing Data</th>
                                <th style="min-width: 300px; text-align:left;">&nbsp;&nbsp;&nbsp; Change Request</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $redColor = "";
                            function isIdentical($oldValue, $newValue)
                            {
                                if (strtolower(trim($oldValue)) === strtolower(trim($newValue))) {
                                    $redColor = "";
                                } else {
                                    $redColor = "color:#ec3f3f;";
                                }

                                return $redColor;
                            }
                            ?>
                            <tr>
                                <td>Applicant Name</td>
                                <td><?= strtoupper($cinfo->fullName) ?></td>
                                <td style="<?= isIdentical($cinfo->fullName, $requestData->name) ?>"><?= strtoupper($requestData->name) ?></td>
                            </tr>
                            <tr>
                                <td>Father Name</td>
                                <td><?= strtoupper($cinfo->fatherName) ?></td>
                                <td style="<?= isIdentical($cinfo->fatherName, $requestData->fatherName) ?>"><?= strtoupper($requestData->fatherName) ?></td>
                            </tr>
                            <tr>
                                <td>University Name</td>
                                <td><?= strtoupper($education->llbUni) ?></td>
                                <td style="<?= isIdentical($education->llbUni, $requestData->universityName) ?>"><?= strtoupper($requestData->universityName) ?></td>
                            </tr>

                            <tr>
                                <td>Senior Advocate Name</td>
                                <td><?= strtoupper($cinfo->seniorAdvocateName) ?></td>
                                <td style="<?= isIdentical($cinfo->seniorAdvocateName, $requestData->seniorAdvocateName) ?>"><?= strtoupper($requestData->seniorAdvocateName) ?></td>
                            </tr>
                            <tr>
                                <td>Pupilage Contract Date</td>
                                <td><?= isset($cinfo->pupilageContractDate) ? $clock->toString($cinfo->pupilageContractDate, DateTimeFormat::BdDate()) : "" ?></td>
                                <td style="<?= isIdentical($cinfo->pupilageContractDate, $requestData->pupilageContractDate) ?>"><?= $clock->toString($requestData->pupilageContractDate, DateTimeFormat::BdDate()) ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="field mt-100">
                        <form class="approvalForm mr-100" action="<?= BASE_URL ?>/admins/registration-update-request/read/details/registration-update-request-approve.php?session-id=<?= $encSessionId ?>" class="text-left" method="POST">
                            <div class="field mb-050 mt-050">
                                <input type="hidden" name="requestId" value="<?= $encRequestId ?>">
                                <input type="checkbox" name="checkbox" id="chkApprove">&nbsp;&nbsp; I am sure to approve.
                            </div>
                            <button class="form-submit-button btn btn-outline" type="submit">Approve</button>
                            <a class="btn outline" href="<?= BASE_URL ?>/admins/registration-update-request/read/list/registration-update-request-list.php?session-id=<?= $encSessionId ?>">Back</a>
                        </form>
                    </div>
                </div> <!-- .content ends -->

                <aside style="display: flex; flex-direction: column;">
                    <div class="card fill-parent">
                        <form class="decliningForm" action="<?= BASE_URL ?>/admins/registration-update-request/read/details/registration-update-request-decline.php?session-id=<?= $encSessionId ?>" class="text-left" method="POST">
                            <div class="field mb-050 mt-050">
                                <input type="hidden" name="requestId" value="<?= $encRequestId ?>">
                                 This message will be displayed to applicant
                                <textarea name="adminMessage" placeholder="" style="height: 120px;"></textarea>
                                <input type="checkbox" class="declineConfirmChk" name="declineConfirm">&nbsp;&nbsp; I am sure to decline.
                            </div>
                            <button class="form-submit-button btn btn-outline" type="submit">Decline</button>
                        </form>
                    </div>
                </aside>

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

            function checkConfirm() {
                var check = $("#chkApprove").is(':checked');
                if (check) {
                    return true;
                } else {
                    alert("Please Confirm.");
                    return false;
                }
            }

            $('.approvalForm').swiftSubmit({}, checkConfirm, null, null, null, null);

            function checkDecline() {
                var check = $(".declineConfirmChk").is(':checked');
                if (check) {
                    return true;
                } else {
                    alert("Please Confirm.");
                    return false;
                }
            }

            $('.decliningForm').swiftSubmit({}, checkDecline, null, null, null, null);

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
                autoClose: true,
                showOn: "button",
                minView: 'years',
                view: "years",
                onSelect: function(formattedDate, date, inst) {
                    $(inst.el).trigger('change');
                    $(inst.el).removeClass('error');
                }
            })



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

            $("input[type=radio]").change(function() {
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