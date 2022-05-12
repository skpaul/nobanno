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


    if(!isset($_GET["request-id"])){
        HttpHeader::redirect(BASE_URL . "/admins/seat-plan/read/list/seat-plan-list.php?session-id=$encSessionId");
    }



    $encId = trim($_GET["request-id"]);

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
                <h1 class="width-full mt-150 accent-fg text-center">Seat Plan Details
                    <div class="divider"></div>
                </h1>

                <nav class="left-nav">
                    <?php
                    echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                </nav>

                <!-- .content starts -->
                <div class="content ">
                    <?php echo DB_SERVER; ?>
                    <form class="d-grid gc-3">
                        <!-- venue  --> 
                        <div class="field width-100">  
                            <label class="required">Venue</label>
                            <input name="venue" id="venue" title="" class="validate" data-title="venue" data-datatype="string" data-required="required" data-minlen="" data-maxlen="" value="<?=$seatDetail->venue?>" readonly type="text">
                        </div>

                        <!-- building  --> 
                        <div class="field">  
                            <label class="required">Building</label>
                            <input name="building" id="building" title="" class="validate" data-title="building" data-datatype="string" data-required="required" data-minlen="allow null" data-maxlen="" type="text" value="<?=$seatDetail->building?>" readonly>
                        </div>

                        <!-- floor  --> 
                        <div class="field">  
                            <label class="required">Floor</label>
                            <input name="floor" id="floor" title="" class="validate" data-title="floor" data-datatype="string" data-required="required" data-minlen="allow null" data-maxlen="" type="text" value="<?=$seatDetail->floor?>" readonly>
                        </div>

                        <!-- room  --> 
                        <div class="field">  
                            <label class="required">Room</label>
                            <input name="room" id="room" title="" class="validate" data-title="room" data-datatype="integer" data-required="required" data-minlen="" data-maxlen="" type="text" value="<?=$seatDetail->room?>" readonly>
                        </div>
                       
                        <!-- start_roll  --> 
                        <div class="field">  
                            <label class="required">Start Roll</label>
                            <input name="start_roll" id="start_roll" title="" class="validate" data-title="start_roll" data-datatype="integer"  data-required="required"  data-minval="" data-maxval="" type="text" value="<?=$seatDetail->start_roll?>" readonly>
                        </div>
                        <!-- end_roll  --> 
                        <div class="field">  
                            <label class="required">End Roll</label>
                            <input name="end_roll" id="end_roll" title="" class="validate" data-title="end_roll" data-datatype="integer"  data-required="required"  data-minval="" data-maxval="" type="text" value="<?=$seatDetail->end_roll?>" readonly>
                        </div>
                         <!-- total  --> 
                         <div class="field">  
                            <label class="required">Total</label>
                            <input name="total" id="total" title="" class="validate" data-title="total" data-datatype="integer"  data-required="required"  data-minval="" data-maxval="" type="text" value="<?=$seatDetail->total?>" readonly>
                        </div>
                        <div></div>
                        <div></div>

                        <div class="field">
                            <a class="btn outline" href="<?=BASE_URL . '/admins/seat-plan/read/list/seat-plan-list.php?session-id='.$encSessionId?>">Back</a>
                            <a class="btn ml-050 outline" href="<?=BASE_URL . '/admins/seat-plan/update/edit-seat-plan.php?session-id='.$encSessionId.'&request-id='.$encId?>">Edit</a>
                            <a class="btn ml-050 outline" href="<?=BASE_URL . '/admins/seat-plan/delete/delete-seat-plan.php?session-id='.$encSessionId.'&delete-id='.$encId?>">Delete</a>
                        </div>
                        
                        
                     
                    </form>

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

            $('form').swiftSubmit({},null, null, null, null, null);
            
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

                $("input[type=radio]").change(function(){
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