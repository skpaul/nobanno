<?php
#region imports
require_once('../Required.php');
Required::Logger()->Database()->EnDecryptor()->DbSession();
#endregion

#region declarations
$logger = new Logger(ROOT_DIRECTORY);
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
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
        $sessionId = $endecryptor->decrypt($encSessionId);
        $session = new DbSession($db, "admin_sessions");
        $session->continue($sessionId);
    } catch (\SessionException $th) {
        // $logger->createLog($th->getMessage());
        die($json->fail()->message("Invalid session. Please login again. Error code- 774965")->create());
    } catch (\Exception $exp) {
        die($json->fail()->message("Invalid session. Please login again. Error code- 774965")->create());
    }
#endregion


//
$totalRegistrations = $db->select("SELECT COUNT(*) as quantity FROM `lc_enrolment_registrations`");
$totalApplications = $db->select("SELECT COUNT(*) as quantity FROM `lc_enrolment_cinfo`");
$totalPaids = $db->select("SELECT COUNT(*) as quantity FROM `lc_enrolment_cinfo` WHERE fee=1");
$typeWiseQuantities = $db->select("SELECT applicantType, COUNT(*) as quantity FROM `lc_enrolment_cinfo` GROUP BY applicantType");

$totalRegistration =  $totalRegistrations[0]->quantity;
$totalApplication  =  $totalApplications[0]->quantity;
$totalPaid         =  $totalPaids[0]->quantity;

$sql = 'SELECT date( appliedDateTime ) appliedDate, count(*) quantity FROM lc_enrolment_cinfo GROUP BY  date( appliedDateTime) ORDER by appliedDate';
$dateWiseSubmissionData = $db->select($sql);
?>

<!DOCTYPE html>
<html lang="en-US" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Admin Panel</title>

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="assets/img/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicons/favicon.ico">
    <link rel="manifest" href="<?= BASE_URL ?>/falcon/assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="<?= BASE_URL ?>/falcon/assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">
    <script src="<?= BASE_URL ?>/falcon/assets/js/config.js"></script>
    <script src="<?= BASE_URL ?>/falcon/vendors/overlayscrollbars/OverlayScrollbars.min.js"></script>

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/falcon/vendors/overlayscrollbars/OverlayScrollbars.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/falcon/assets/css/theme-rtl.min.css" rel="stylesheet" id="style-rtl">
    <link href="<?= BASE_URL ?>/falcon/assets/css/theme.min.css" rel="stylesheet" id="style-default">
    <link href="<?= BASE_URL ?>/falcon/assets/css/user-rtl.min.css" rel="stylesheet" id="user-style-rtl">
    <link href="<?= BASE_URL ?>/falcon/assets/css/user.min.css" rel="stylesheet" id="user-style-default">
    <script>
        var isRTL = JSON.parse(localStorage.getItem('isRTL'));
        if (isRTL) {
            var linkDefault = document.getElementById('style-default');
            var userLinkDefault = document.getElementById('user-style-default');
            linkDefault.setAttribute('disabled', true);
            userLinkDefault.setAttribute('disabled', true);
            document.querySelector('html').setAttribute('dir', 'rtl');
        } else {
            var linkRTL = document.getElementById('style-rtl');
            var userLinkRTL = document.getElementById('user-style-rtl');
            linkRTL.setAttribute('disabled', true);
            userLinkRTL.setAttribute('disabled', true);
        }
    </script>


    <!-- <script src="https://unpkg.com/papaparse@latest/papaparse.min.js"></script> -->


    <style>
        /* * {
            font-family: Arial, Helvetica, sans-serif;
        } */

        th>span {
            font-weight: normal;
            font-style: italic;
        }

        /* table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        thead {
            font-size: 14px;
            background-color: lightgray;
        }

        thead tr {}

        th {
            padding: 10px;
            border: 1px solid gray;
        }

        th>span {
            font-weight: normal;
            font-style: italic;
        }

        tbody {

            border-left: 1px solid black;
            border-right: 1px solid black;
        }

        tbody tr {
            border-bottom: 1px solid gray;
            background-color: rgba(211, 211, 211, 0.17);

        }

        tbody tr:hover {
            opacity: 0.7;
            font-weight: 100;
        }

        td {
            text-align: center;
            padding-top: 10px;
            padding-bottom: 10px;
        } */

        .saved {
            background-color: rgba(0, 128, 0, 0.3);
            color: black;
        }

        .failed {
            background-color: rgba(255, 0, 0, 0.3);
            color: black;
        }

        /* #files, */
        #submit,
        #save {
            background: #2196f3;
            color: white;
            border: 1px solid black;
            border-radius: 4px;
            padding: 7px 20px;
            cursor: pointer;
        }

        #submit,
        #save {
            display: none;
            padding: 10px 26px;
            background: #12b315;
        }

        #save {
            padding: 10px 26px;
            margin-top: 20px;
        }

        .button-loader {
            display: none;
        }

        ul li {
            /* margin-bottom: 5px; */
            font-size: 14px;
            line-height: 1.4;
        }

        /* ul span {
            border: 1px solid #cecece;
            background-color: #eeeeee;
            padding: 0 8px;
            border-radius: 4px;
        } */
    </style>

</head>

<body>
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <div class="container-fluid" data-layout="container-fluid">
            <!-- <div class="container" data-layout="container"> -->
            <script>
                var isFluid = JSON.parse(localStorage.getItem('isFluid'));
                if (isFluid) {
                    var container = document.querySelector('[data-layout]');
                    container.classList.remove('container');
                    container.classList.add('container-fluid');
                }
            </script>
            <nav class="navbar navbar-light navbar-vertical navbar-expand-xl" style="display: none;">
                <script>
                    var navbarStyle = localStorage.getItem("navbarStyle");
                    if (navbarStyle && navbarStyle !== 'transparent') {
                        document.querySelector('.navbar-vertical').classList.add(`navbar-${navbarStyle}`);
                    }
                </script>
                <div class="d-flex align-items-center">
                    <div class="toggle-icon-wrapper">
                        <button class="btn navbar-toggler-humburger-icon navbar-vertical-toggle" data-bs-toggle="tooltip" data-bs-placement="left" title="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                    </div><a class="navbar-brand" href="index.html">
                        <div class="d-flex align-items-center py-3"><img class="me-2" src="<?= BASE_URL ?>/falcon/assets/img/icons/spot-illustrations/admin-logo.png" alt="" width="40" /><span class="font-sans-serif">admins</span></div>
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="navbarVerticalCollapse">
                    <div class="navbar-vertical-content scrollbar">
                        <?php
                        require_once('inc/left-nav.php');
                        ?>

                    </div>
                </div>
            </nav>


            <div class="content">
                <nav class="navbar navbar-light navbar-glass navbar-top navbar-expand" style="display: none;">
                    <button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                    <a class="navbar-brand me-1 me-sm-3" href="index.html">
                        <div class="d-flex align-items-center"><img class="me-2" src="<?= BASE_URL ?>/falcon/assets/img/icons/spot-illustrations/admin-logo.png" alt="" width="40" /><span class="font-sans-serif">admins</span></div>
                    </a>


                </nav>
                <nav class="navbar navbar-light navbar-glass navbar-top navbar-expand-lg" style="display: none;" data-move-target="#navbarVerticalNav" data-navbar-top="combo">
                    <button class="btn navbar-toggler-humburger-icon navbar-toggler me-1 me-sm-3" type="button" data-bs-toggle="collapse" data-bs-target="#navbarVerticalCollapse" aria-controls="navbarVerticalCollapse" aria-expanded="false" aria-label="Toggle Navigation"><span class="navbar-toggle-icon"><span class="toggle-line"></span></span></button>
                    <a class="navbar-brand me-1 me-sm-3" href="index.html">
                        <div class="d-flex align-items-center"><img class="me-2" src="assets/img/icons/spot-illustrations/falcon.png" alt="" width="40" /><span class="font-sans-serif">falcon</span></div>
                    </a>
                    <div class="collapse navbar-collapse scrollbar" id="navbarStandard">
                        <?php
                        include_once('inc/top-nav.php')
                        ?>
                    </div>
                    <?php
                    include_once('inc/right-top.php')
                    ?>
                </nav>
                <script>
                    var navbarPosition = localStorage.getItem('navbarPosition');
                    var navbarVertical = document.querySelector('.navbar-vertical');
                    var navbarTopVertical = document.querySelector('.content .navbar-top');
                    var navbarTop = document.querySelector('[data-layout] .navbar-top');
                    var navbarTopCombo = document.querySelector('.content [data-navbar-top="combo"]');
                    if (navbarPosition === 'top') {
                        navbarTop.removeAttribute('style');
                        navbarTopVertical.remove(navbarTopVertical);
                        navbarVertical.remove(navbarVertical);
                        navbarTopCombo.remove(navbarTopCombo);
                    } else if (navbarPosition === 'combo') {
                        navbarVertical.removeAttribute('style');
                        navbarTopCombo.removeAttribute('style');
                        navbarTop.remove(navbarTop);
                        navbarTopVertical.remove(navbarTopVertical);
                    } else {
                        navbarVertical.removeAttribute('style');
                        navbarTopVertical.removeAttribute('style');
                        navbarTop.remove(navbarTop);
                        navbarTopCombo.remove(navbarTopCombo);
                    }
                </script>



                <h3>Dashboard</h3>

                <div class="card h-lg-100 mb-3">
                    <div class="card-body d-flex align-items-center">
                        <div class="w-100">
                            <h6 class="mb-3 text-800">APPLIED <strong class="text-dark"><?= $totalApplication ?></strong> of <?= $totalRegistration ?></h6>
                            <div class="progress mb-3 rounded-3" style="height: 10px;">
                                <?php
                                $amount = ($totalApplication / $totalRegistration) * 100;
                                $amount = number_format((float)$amount, 2, '.', '');
                                ?>
                                <div class="progress-bar bg-progress-gradient border-end border-white border-2" role="progressbar" style="width: <?= $amount ?>%" aria-valuenow="<?= $amount ?>" aria-valuemin="0" aria-valuemax="100"></div>

                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6 col-xxl-3">
                        <div class="card h-md-100 ecommerce-card-min-width">
                            <div class="card-header pb-0">
                                <h6 class="mb-0 mt-2 d-flex align-items-center">TOTAL REGISTRATIONS
                                    <span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Calculated according to 1 minutes ago.">
                                        <span class="far fa-question-circle" data-fa-transform="shrink-1"></span></span>
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-end">
                                <div class="row">
                                    <div class="col">
                                        <p class="font-sans-serif lh-1 mb-1 fs-4"><?= $totalRegistration ?></p>
                                        <span class="badge badge-soft-success rounded-pill fs--2">&nbsp;</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xxl-3">
                        <div class="card h-md-100 ecommerce-card-min-width">
                            <div class="card-header pb-0">
                                <h6 class="mb-0 mt-2 d-flex align-items-center">TOTAL APPLICATIONS
                                    <span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Calculated according to 1 minutes ago.">
                                        <span class="far fa-question-circle" data-fa-transform="shrink-1"></span></span>
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-end">
                                <div class="row">
                                    <div class="col">
                                        <p class="font-sans-serif lh-1 mb-1 fs-4"><?= $totalApplication ?></p>
                                        <?php
                                        $amount = ($totalApplication / $totalRegistration) * 100;
                                        $amount = number_format((float)$amount, 2, '.', '');
                                        ?>
                                        <span class="badge badge-soft-success rounded-pill fs--2"><?= $amount ?>%</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xxl-3">
                        <div class="card h-md-100 ecommerce-card-min-width">
                            <div class="card-header pb-0">
                                <h6 class="mb-0 mt-2 d-flex align-items-center">FEE PAIDS
                                    <span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Calculated according to 1 minutes ago.">
                                        <span class="far fa-question-circle" data-fa-transform="shrink-1"></span></span>
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-end">
                                <div class="row">
                                    <div class="col">
                                        <p class="font-sans-serif lh-1 mb-1 fs-4"><?= $totalPaid ?></p>
                                        <?php
                                        $amount = ($totalPaid / $totalApplication) * 100;
                                        $amount = number_format((float)$amount, 2, '.', '');
                                        ?>
                                        <span class="badge badge-soft-success rounded-pill fs--2"><?= $amount ?></span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xxl-3">
                        <div class="card h-md-100 ecommerce-card-min-width">
                            <div class="card-header pb-0">
                                <h6 class="mb-0 mt-2 d-flex align-items-center">FEE UNPAIDS
                                    <span class="ms-1 text-400" data-bs-toggle="tooltip" data-bs-placement="top" title="Calculated according to 1 minutes ago.">
                                        <span class="far fa-question-circle" data-fa-transform="shrink-1"></span></span>
                                </h6>
                            </div>
                            <div class="card-body d-flex flex-column justify-content-end">
                                <div class="row">
                                    <div class="col">
                                        <p class="font-sans-serif lh-1 mb-1 fs-4"><?= $totalApplication - $totalPaid ?></p>

                                        <span class="badge badge-soft-success rounded-pill fs--2">&nbsp;</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>


             


                <div class="row g-3 mb-3">
                    <?php
                    foreach ($typeWiseQuantities as $data) {
                        $applicantType = strtoupper($data->applicantType);

                        $amount = ($data->quantity / $totalApplication) * 100;
                        $amount = number_format((float)$amount, 2, '.', '');

                        $html = <<<HTML
                        <div class="col">
                            <div class="card h-md-100 ecommerce-card-min-width">
                                <div class="card-header pb-0">
                                    <h6 class="mb-0 mt-2 d-flex align-items-center">$applicantType
                                        <span class="ms-1 text-400" data-bs-toggle="tooltip" 
                                        data-bs-placement="top" title="Calculated according to 1 minutes ago.">
                                        <span class="far fa-question-circle" data-fa-transform="shrink-1"></span></span></h6>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-end">
                                    <div class="row">
                                        <div class="col">
                                            <p class="font-sans-serif lh-1 mb-1 fs-4">$data->quantity</p>
                                            <span class="badge badge-soft-success rounded-pill fs--2">$amount</span>
                                        </div>
                                    
                                    </div>
                                </div>
                            </div>
                        </div>
                    HTML;
                        echo $html;
                    }
                    ?>
                </div>


              



                <div class="row g-3 mb-3">
                <div class="col-lg-6">
                            <div class="card h-md-100 ecommerce-card-min-width">
                                <div class="card-header pb-0">
                                    <h6 class="mb-0 mt-2 d-flex align-items-center">DATE-WISE SUBMISSION
                                        <span class="ms-1 text-400" data-bs-toggle="tooltip" 
                                        data-bs-placement="top" title="Calculated according to 1 minutes ago.">
                                        <span class="far fa-question-circle" data-fa-transform="shrink-1"></span></span></h6>
                                </div>
                                <div class="card-body d-flex flex-column justify-content-end">
                                <div id="dateWiseSubmissionGraph">

</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                        <div class="card h-lg-100 overflow-hidden mb-3">
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col">
                                <h6 class="mb-0">TYPE-WISE SUBMISSIONS</h6>
                            </div>
                           
                        </div>
                    </div>
                    <div class="card-body p-0 ">
                        <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                            <div class="col ps-card py-1 position-static">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xl me-3">
                                        <div class="avatar-name rounded-circle bg-soft-primary text-dark"><span class="fs-0 text-primary">&nbsp;</span></div>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">REGULAR</a><span class="badge rounded-pill ms-2 bg-200 text-primary">38%</span></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col py-1">
                                <div class="row flex-end-center g-0">
                                    <div class="col-auto pe-2">
                                        <div class="fs--1 fw-semi-bold">100</div>
                                    </div>
                                    <div class="col-5 pe-card ps-2">
                                        <div class="progress bg-200 me-2" style="height: 5px;">
                                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 38%" aria-valuenow="38" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                            <div class="col ps-card py-1 position-static">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xl me-3">
                                        <div class="avatar-name rounded-circle bg-soft-success text-dark"><span class="fs-0 text-success">&nbsp;</span></div>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">RE-APPEARED</a><span class="badge rounded-pill ms-2 bg-200 text-primary">79%</span></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col py-1">
                                <div class="row flex-end-center g-0">
                                    <div class="col-auto pe-2">
                                        <div class="fs--1 fw-semi-bold">457</div>
                                    </div>
                                    <div class="col-5 pe-card ps-2">
                                        <div class="progress bg-200 me-2" style="height: 5px;">
                                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 79%" aria-valuenow="79" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row g-0 align-items-center py-2 position-relative border-bottom border-200">
                            <div class="col ps-card py-1 position-static">
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xl me-3">
                                        <div class="avatar-name rounded-circle bg-soft-info text-dark"><span class="fs-0 text-info">&nbsp;</span></div>
                                    </div>
                                    <div class="flex-1">
                                        <h6 class="mb-0 d-flex align-items-center"><a class="text-800 stretched-link" href="#!">RE-REGISTRATION</a><span class="badge rounded-pill ms-2 bg-200 text-primary">90%</span></h6>
                                    </div>
                                </div>
                            </div>
                            <div class="col py-1">
                                <div class="row flex-end-center g-0">
                                    <div class="col-auto pe-2">
                                        <div class="fs--1 fw-semi-bold">100</div>
                                    </div>
                                    <div class="col-5 pe-card ps-2">
                                        <div class="progress bg-200 me-2" style="height: 5px;">
                                            <div class="progress-bar rounded-pill" role="progressbar" style="width: 90%" aria-valuenow="90" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                       
                    </div>
                   
                </div>

                        </div>
                </div>








                <footer class="footer">
                    <div class="row g-0 justify-content-between fs--1 mt-4 mb-3">
                        <div class="col-12 col-sm-auto text-center">
                            <p class="mb-0 text-600">Teletalk <span class="d-none d-sm-inline-block">| </span><br class="d-sm-none" /> 2022 &copy; <a href="#">Teletalk</a></p>
                        </div>
                        <div class="col-12 col-sm-auto text-center">
                            <p class="mb-0 text-600">v3.6.0</p>
                        </div>
                    </div>
                </footer>
            </div>

        </div>
    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->



    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="<?= BASE_URL ?>/falcon/vendors/popper/popper.min.js"></script>
    <script src="<?= BASE_URL ?>/falcon/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="<?= BASE_URL ?>/falcon/vendors/anchorjs/anchor.min.js"></script>
    <script src="<?= BASE_URL ?>/falcon/vendors/is/is.min.js"></script>
    <script src="<?= BASE_URL ?>/falcon/vendors/echarts/echarts.min.js"></script>
    <script src="<?= BASE_URL ?>/falcon/vendors/fontawesome/all.min.js"></script>
    <script src="<?= BASE_URL ?>/falcon/vendors/lodash/lodash.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="<?= BASE_URL ?>/falcon/vendors/list.js/list.min.js"></script>
    <script src="<?= BASE_URL ?>/falcon/assets/js/theme.js"></script>

    <?php
    Required::JQuery();
    ?>

    <!--  -->
    <script>
        let baseUrl = '<?= BASE_URL ?>';
        $(document).ready(function() {
            if (sessionStorage.getItem("adminId") === null) {
                window.location = baseUrl + "/admins/index.php";
            }
        });
    </script>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        // Load the Visualization API and the corechart package.
        google.charts.load('current', {
            'packages': ['corechart']
        });

        // Set a callback to run when the Google Visualization API is loaded.
        google.charts.setOnLoadCallback(drawChart);

        // Callback that creates and populates a data table,
        // instantiates the pie chart, passes in the data and
        // draws it.
        function drawChart() {

            // Create the data table.
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'Date');
            data.addColumn('number', 'Applications');
            data.addColumn({
                role: 'style'
            });

            <?php

            foreach ($dateWiseSubmissionData as $barData) {
                $dd = new DateTime($barData->appliedDate, new DateTimeZone("Asia/Dhaka"));

            ?>
                var randomColor = Math.floor(Math.random() * 16777215).toString(16);
                data.addRows([
                    ['<?= $dd->format("d-m-Y") ?>', <?= $barData->quantity ?>, '#' + randomColor]
                ]);
            <?php
            }
            ?>


            // Set chart options
            var options = {
                legend: {
                    position: 'none'
                },
                'width': 700,
                'height': 500
            };

            // Instantiate and draw our chart, passing in some options.
            var chart = new google.visualization.BarChart(document.getElementById('dateWiseSubmissionGraph'));
            chart.draw(data, options);
        }
    </script>
</body>

</html>