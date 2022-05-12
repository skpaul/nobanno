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


    if(!isset($_GET["registration-id"])){
        HttpHeader::redirect(BASE_URL . "/admins/registration/read/list/registration-list.php");
    }


    $db->connect();
    $db->fetchAsObject();


    $encId = trim($_GET["registration-id"]);

    $id =  $endecryptor->decrypt($encId);

    $sql = "SELECT * FROM lc_enrolment_registrations WHERE registrationId=:registrationId";
    $regId = $db->select($sql, array('registrationId' => $id));
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
                <h1 class="width-full mt-150 accent-fg text-center">Registration Details
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
                     $encRegistrationId = trim($_GET["registration-id"]);

                     $registrationId =  $endecryptor->decrypt($encRegistrationId);
 
                     $sql = "SELECT * FROM lc_enrolment_registrations WHERE registrationId=:registrationId";
                     $regiData = $db->select($sql, array('registrationId' => $registrationId));
                     if(count($regiData) != 1){
                        die($json->fail()->message("Your Registration Data not found!")->create());
                    }
                   $registration = $regiData[0];
                ?>
                <table>
                    <tbody>
                        <thead>
                            <tr>
                                <th>Reg No</th>
                                <th>Name</th>
                                <th>Father Name</th>
                                <th>SeniorAdvocate Name</th>
                            </tr>
                        </thead>
                        <tr>
                            <td><?=$registration->regNo?></td>
                            <td><?=$registration->name?></td>
                            <td><?=$registration->fatherName?></td>
                            <td><?=$registration->seniorAdvocateName?></td>
                            
                        </tr>
                        
                    </tbody>
                </table>
                    <form action="<?=BASE_URL?>/admins/registration/delete/delete-registration-processor.php?registration-id=<?=$encId?>" method="post" class="d-grid gc-1">    
                        
                        <div class="field mb-050 mt-050">
                           <input type="hidden" name="deleteId" value="<?= $encId ?>">
                            <p>Are you sure? That you want to delete it? You will never get it back.</p>
                            <input type="checkbox" name="checkbox" id="chkDelete">&nbsp;&nbsp; Confirm Delete.
                        </div>
                    
                        <div class="field">
                            <a class="btn" href="<?=BASE_URL?>/admins/registration/read/details/registration-details.php?registration-id=<?=$encId?>">Back</a>
                            <input class="form-submit-button" type="submit" value="Delete">
                        </div>
                     
                    </form>

                </div> <!-- .content ends -->
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