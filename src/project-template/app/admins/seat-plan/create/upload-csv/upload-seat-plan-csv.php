<?php

declare(strict_types=1);

#region imports
require_once('../../../../Required.php');

Required::Logger()
    ->Database()->DbSession()->EnDecryptor()->HttpHeader()->Clock()->adminLeftNav()->headerBrand()->applicantHeaderNav()->footer(2);

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

        <style>
            th>span {
                font-weight: normal;
                font-style: italic;
            }

            .saved {
                background-color: green;
                color: black;
            }

            .failed {
                background-color: red;
                color: black;
            }

            /* #files, */
            #submit,
            #save {
                /* background: #2196f3; */
                /* color: white; */
                border: 1px solid black;
                border-radius: 4px;
                padding: 7px 20px;
                cursor: pointer;

            }

            #submit,
            #save {
                display: none;
                padding: 10px 26px;
                /* background: #12b315; */
            }

            #save {
                padding: 10px 26px;
                margin-top: 20px;
            }

            .button-loader {
                display: none;
            }
        </style>

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
                    <h1 style="width:100%;" class="mt-150 accent-fg">Import Seat Plans
                        <div class="divider"></div>
                    </h1>

                    <nav class="left-nav">
                        <?php
                        echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                        ?>
                    </nav>

                    <!-- .content starts -->
                    <div class="content ">
                        <form action="">
                            <div class="field">
                                <label class="btn outline">
                                    <input class="form-control" id="files" type="file" style="display: none;" accept=".csv">
                                    Browse a .csv file
                                </label>
                            </div>
                        </form>

                        <div style="height: 300px; padding-right:11px;" class="overlayScroll">
                            <table class="fill-parent">
                                <thead>
                                    <tr>
                                        <th></th>
                                        <th>Venue</th>
                                        <th>Building</th>
                                        <th>Floor</th>
                                        <th>Room</th>
                                        <th>Start Roll</th>
                                        <th>End Roll</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody id="tbody">

                                </tbody>
                            </table>
                        </div>
                        <button id="save" class="green">
                            <span class="button-loader">
                                <img src="spinner.svg" height="100%" alt="">
                            </span>
                            <span class="button-text">Save</span>
                        </button>
                    </div> <!-- .content ends -->

                    <aside style="display: flex; flex-direction: column;">
                        <div class="card fill-parent">
                            <h5 class="accent-emphasis-fg">Note:</h5>
                            <ul class="ml-100">
                                <li>File format must be <span>.csv</span></li>
                                <li>Order & quantity of the columns must be same as the following table.</li>
                                <li>Contract date must be in <span>dd-mm-yyyy</span> format i.e. 24-04-2021</li>
                            </ul>
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
            Required::jquery()->hamburgerMenu(2)->adminLeftNavJS()->overlayScrollbarJS();
        ?>
        <script src="papaparse.min.js?v=<?= time() ?>"></script>
        <script src="upload-seat-plan-csv.js?v=<?= time() ?>"></script>
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