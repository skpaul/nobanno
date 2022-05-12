<?php

declare(strict_types=1);

require_once('../Required.php');
Required::Logger()
    ->Database()->DbSession()->EnDecryptor()->HttpHeader()->Clock()
    ->adminLeftNav()->JSON()->headerBrand()->applicantHeaderNav()->footer(2);

$logger = new Logger(ROOT_DIRECTORY);
$endecryptor = new EnDecryptor();
$json = new JSON();
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$clock = new Clock();

$db->connect();
$db->fetchAsObject();

#region check session
if (!isset($_GET["session-id"]) || empty(trim($_GET["session-id"]))) {
    HttpHeader::redirect(BASE_URL . "/admins/sorry.php?msg=Invalid session request.");
}

$encSessionId = trim($_GET["session-id"]);

try {
    $sessionId = (int)$endecryptor->decrypt($encSessionId);
    $session = new DbSession($db, "admin_sessions");
    $session->continue($sessionId);
    $roleCode = $session->getData("roleCode");
} catch (\SessionException $th) {
    // $logger->createLog($th->getMessage());
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again.");
} catch (\Exception $exp) {
    HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again.");
}
#endregion

$totalRegistrations = $db->select("SELECT COUNT(*) as quantity FROM `lc_enrolment_registrations`");
$totalApplications = $db->select("SELECT COUNT(*) as quantity FROM `lc_enrolment_cinfo` where fee=1");
$typeWiseQuantities = $db->select("SELECT applicantType, COUNT(*) as quantity FROM `lc_enrolment_cinfo` WHERE fee=1 GROUP BY applicantType");

$totalRegistration =  $totalRegistrations[0]->quantity;
$totalApplication  =  $totalApplications[0]->quantity;


// $sql = 'SELECT date( appliedDateTime ) appliedDate, count(*) quantity FROM lc_enrolment_cinfo GROUP BY  date( appliedDateTime) ORDER by appliedDate';
// $dateWiseSubmissionData = $db->select($sql);

$totalUpdateRequests = ($db->select("SELECT count(*) as quantity FROM `lc_enrolment_registrations_update_request`"))[0]->quantity;
$totalPendingUpdateRequests = ($db->select("SELECT count(*) as quantity FROM `lc_enrolment_registrations_update_request` where hasApproved = 'pending'"))[0]->quantity;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Dashaboard || <?= ORGANIZATION_FULL_NAME ?></title>
    <!-- <script>document.write('<script src="http://' + (location.host || 'localhost').split(':')[0] + ':35729/livereload.js?snipver=1"></' + 'script>')</script> -->
    <?php
    Required::metaTags()->favicon()->teletalkCSS(2)->sweetModalCSS()->airDatePickerCSS()->overlayScrollbarCSS();
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

        <main class="">
            <div class="container-fluid d-flex flex-wrap">
                <h1 class="width-100 mt-150 accent-fg">Dashboard
                    <div class="divider"></div>
                </h1>

                <nav class="left-nav">
                    <?php
                    echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                </nav>

                <!-- .content starts -->
                <div class="content">
                    <div class="row">
                        <div class="col-6">
                            <div class="card text-center mb-150">
                                TOTAL REGISTRATIONS
                                <h3 class="accent-fg">
                                    <?= $totalRegistration ?>
                                </h3>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card text-center  mb-150">
                                TOTAL APPLICATIONS
                                <h3 class="accent-fg">
                                    <?= $totalApplication ?>
                                </h3>
                            </div>
                        </div>
                       
                        <div class="col-6">
                            <div class="card text-center  mb-150">
                                TOTAL CORRECTION REQUESTS
                                <h3 class="accent-fg">
                                    <?= $totalUpdateRequests ?>
                                </h3>

                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card text-center  mb-150">
                                PENDING CORRECTION REQUESTS
                                <h3 class="accent-fg">
                                    <?= $totalPendingUpdateRequests ?>
                                </h3>
                            </div>
                        </div>
                    </div>

                    <br><br>
                    <div id="calendar" style="font-family:arial; border:1px solid gray;"></div>

                    <br><br>
                    <div id="chart"></div>
                </div> <!-- .content ends -->

                <aside style="display: flex; flex-direction: column;">
                    <div class="card fill-parent">
                        <strong>Type-wise Applications</strong>
                        <br><br>
                        <?php
                            foreach ($typeWiseQuantities as $item) {
                                echo ''. $item->applicantType . ': ' . $item->quantity . '<br>';
                            }
                        ?>
                    </div>
                </aside>
            </div><!-- container// -->
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
        Required::jquery()->hamburgerMenu(2)->adminLeftNavJS()->overlayScrollbarJS();
    ?>

    <link href='<?=BASE_URL?>/assets/js/plugins/fullcalendar-4.3.1/packages/core/main.css' rel='stylesheet' />
    <link href='<?=BASE_URL?>/assets/js/plugins/fullcalendar-4.3.1/packages/daygrid/main.css' rel='stylesheet' />
    <script src='<?=BASE_URL?>/assets/js/plugins/fullcalendar-4.3.1/packages/core/main.js'></script>
    <script src='<?=BASE_URL?>/assets/js/plugins/fullcalendar-4.3.1/packages/daygrid/main.js'></script>

    <script src="https://cdn.plot.ly/plotly-latest.min.js"></script>

    <!-- //Calendar script -->
    <script>
        var baseUrl = '<?php echo BASE_URL; ?>';
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['dayGrid'],
                timeZone: 'Asia/Dhaka',
                // plugins: [ 'dayGrid', 'bootstrap', 'interaction', 'dayGrid', 'timeGrid', 'list' ],


                //   header: {
                //     left: 'prev,next today',
                //     center: 'title',
                //     right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                //   },
                eventSources: [
                    baseUrl + '/admins/calendar-data.php'
                ]
            });

            calendar.render();
        });
    </script> <!--Calendar script// -->

    <script>
        $(function() {
            function getData() {
                // return 100; //Math.random();
                var yVal = 0;
                $.ajax({
                    url: "active-users.php",
                    method: "GET",
                    async: false,
                    success: function(response) {
                        yVal = parseInt(response);
                    }
                });
                return yVal;
            }

            Plotly.plot('chart', [{
                y: [getData()],
                type: 'line'
            }]);

            var cnt = 0;
            setInterval(function() {
                Plotly.extendTraces('chart', {
                    y: [
                        [getData()]
                    ]
                }, [0]);

                // var d = new Date().toLocaleTimeString(); // 11:18:48 AM
                // console.log(d);
                cnt++;

                if (cnt > 50) {
                    Plotly.relayout('chart', {
                        xaxis: {
                            range: [cnt - 50, cnt]
                        }
                    });
                }
            }, 2000);

          

           
        })
    </script>

    <script>
        $(document).ready(function() {
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