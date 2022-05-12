<?php

    declare(strict_types=1);
    #region imports
    require_once('../../../Required.php');

    Required::Logger()
        ->Database()->DbSession()->EnDecryptor()->HttpHeader()->Clock()
        ->adminLeftNav()->headerBrand()->applicantHeaderNav()->footer(2);
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


    if(!isset($_GET["delete-id"])){
        HttpHeader::redirect(BASE_URL . "/admins/seat-plan/read/list/seat-plan-list.php?session-id=$encSessionId");
    }


    $encId = trim($_GET["delete-id"]);

    $seatId =  $endecryptor->decrypt($encId);

    $sql = "SELECT * FROM lc_enrolment_seat_plans WHERE id=:id ";
    $seatDetails = $db->select($sql, array('id' => $seatId));   

    if(count($seatDetails) != 1){
        HttpHeader::redirect(BASE_URL . "/admins/seat-plan/read/list/seat-plan-list.php?session-id=$encSessionId");
    }  

    $seatDetail = $seatDetails[0];

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
                <h1 class="width-full mt-150 accent-fg text-center">Delete Seat Plan
                    <div class="divider"></div>
                </h1>

                <nav class="left-nav">
                    <?php
                    echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                </nav>

                <!-- .content starts -->
                <div class="content p-100">
                    <div>
                        <table>  
                            <div>
                                <tbody>
                                    <tr>
                                        <td>Venue</td>
                                        <td>:</td>
                                        <td><?= $seatDetail->venue ?></td>
                                    </tr>
                                    <tr>
                                        <td>Building</td>
                                        <td>:</td>
                                        <td><?= $seatDetail->building ?></td>
                                    </tr>
                                    <tr>
                                        <td>Floor</td>
                                        <td>:</td>
                                        <td><?= $seatDetail->floor ?></td>
                                    </tr>
                                    <tr>
                                        <td>Room</td>
                                        <td>:</td>
                                        <td><?= $seatDetail->room ?></td>
                                    </tr>
                                    <tr>
                                        <td>Start Roll</td>
                                        <td>:</td>
                                        <td><?= $seatDetail->start_roll ?></td>
                                    </tr>
                                    <tr>
                                        <td>End Roll</td>
                                        <td>:</td>
                                        <td><?= $seatDetail->end_roll ?></td>
                                    </tr>
                                </tbody>
                            </div>
                        </table><br>
                        <p>
                            You can not undo this action. However, after delete, you can apply again with the same Registration No. and Registration Year.
                        </p>
                        <p>
                            If you are aware this action, click the button below to delete.
                        </p><br>
                    </div>
                    <div>
                        <form id="deleteForm" action="<?=BASE_URL?>/admins/seat-plan/delete/delete-seat-plan-processor.php?session-id=$encSessionId&delete-id=$encId" method="post"  class="d-grid gc-3">
                            <div>
                                <input type="hidden" name="deleteSeatID" value="<?= $encId ?>">
                            </div>
                            <div></div>
                            <div></div>
                            <div>
                                <input type="checkbox" name="checkbox" id="chkDelete">&nbsp;&nbsp; Confirm Delete.
                            </div>
                            <div></div>
                            <div></div>
                            <div class="field">
                                <a class="btn outline" href="<?=BASE_URL . '/admins/seat-plan/read/list/seat-plan-list.php?session-id='.$encSessionId?>">Back</a>
                                <input class="form-submit-button ml-050 outline" type="submit" value="Delete">
                            </div>
                        </form>
                    </div>
                </div> <!-- .content ends -->

                <!-- <aside style="display: flex; flex-direction: column;">
                    <div class="card fill-parent">
                       
                    </div>
                </aside> -->
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

            SwiftNumeric.prepare('.integer');
            function checkConfirm(){
                var check=$("#chkDelete").is(':checked');
                if(check){
                    return true;
                }else{
                    alert("Please Confirm Checkbox.");
                    return false;
                }
            }
 
           
           $('form').swiftSubmit({},checkConfirm, null, null, null, null);
        });
    </script>
</body>

</html>