<?php

    #region Import libraries
        require_once("../Required.php");
        Required::Logger()
                ->Database()->DbSession()->HttpHeader()
                ->EnDecryptor()
                ->JSON()
                ->headerBrand()->applicantHeaderNav()->footer();
    #endregion

	#region Class instance declaration & initialization
        $logger = new Logger(ROOT_DIRECTORY);
        $endecryptor = new EnDecryptor();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    #endregion

	#region Database connection
        $db->connect();
        $db->fetchAsObject();
	#endregion
	
    if (!isset($_GET["cinfo-id"]) || empty(trim($_GET["cinfo-id"])))  HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.");
    $encCinfoId = trim($_GET["cinfo-id"]); 

    $cinfoId = $endecryptor->decrypt($encCinfoId);
    if (!$cinfoId) HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid request.");

    $sql = "select regNo, regYear, fullName, fatherName, fee, requiredFee from lc_enrolment_cinfo where cinfoId=:cinfoId";
    $cinfo = ($db->select($sql, array("cinfoId" => $cinfoId)))[0];

    $pageTitle = "Payment Status";
?>

<!DOCTYPE html>
<html>
    <head>
        <title><?= $pageTitle ?> - <?= ORGANIZATION_FULL_NAME ?></title>
        <?php
        Required::html5shiv()->metaTags()->favicon()->css()->sweetModalCSS()->bootstrapGrid();
        ?>

        <style>
            label {
                font-weight: bold;
            }

            .no-border {
                border: 0 !important;
            }
        </style>
    </head>

    <body>
        <div class="master-wrapper">
            <header>
                <?php
                echo HeaderBrand::prepare(array("baseUrl" => BASE_URL, "hambMenu" => true));
                echo ApplicantHeaderNav::prepare(array("baseUrl" => BASE_URL));
                ?>
            </header>
            <main>
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
                    <div class="content">
                        <div class="card">
                            <div class="card-title">This is card title [optional]</div>
                            <div class="card-description">This is card description [optional]</div>

                            <form action="#" method="post" class="small padding-all-25">

                                <div class="field">
                                    <label>Registration No.</label>
                                    <input type="text" class="no-border" value="<?= $cinfo->regNo ?>" name="regNo" readonly>
                                </div>

                                <div class="field">
                                    <label>Year</label>
                                    <input type="text" class="no-border" value="<?= $cinfo->regYear ?>" readonly>
                                </div>
                                <div class="field">
                                    <label>Name :</label>
                                    <input type="text" class="no-border" value="<?= $cinfo->fullName ?>" readonly>
                                </div>
                                <div class="field">
                                    <label>Father Name :</label>
                                    <input type="text" class="no-border" value="<?= $cinfo->fatherName ?>" readonly>
                                </div>
                                <div class="field">
                                    <label>Fee Amount :</label>
                                    <input type="text" class="no-border" value="TK. <?= $cinfo->requiredFee ?>" readonly>
                                </div>
                                <div class="field">
                                    <label>Status :</label>
                                    <?php
                                    $style  = "";
                                    $paidStatus = "";
                                    if ($cinfo->fee == 1) {
                                        $style  = "background-color:#abe8ab;";
                                        $paidStatus = "Paid";
                                    } else {
                                        $style  = "background-color: #e44242; color: white;";
                                        $paidStatus = "Not paid yet";
                                    }
                                    ?>
                                    <input type="text" style="<?= $style ?>" class="no-border" value="<?= $paidStatus ?>" readonly>
                                </div>
                            </form>
                        </div><!-- .card// -->
                    </div><!-- .content// -->

                    <!-- <aside style="display: flex; flex-direction: column;">
                        asdsdaf
                    </aside> -->
                </div><!-- .container// -->

            </main>
            <footer>
                <?=Footer::prepare(array());?>
            </footer>
        </div>

        <?php
        Required::jquery()->hamburgerMenu()->sweetModalJS()->swiftSubmit()->SwiftNumeric();
        ?>
        <script>
            var base_url = '<?php echo BASE_URL; ?>';
            $(function() {

            })
        </script>

    </body>
</html>