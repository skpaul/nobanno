<?php

#region imports
require_once('../Required.php');
Required::Logger()->Database()->EnDecryptor();
#endregion

#region declarations
$logger = new Logger(ROOT_DIRECTORY);
$db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
$endecryptor = new EnDecryptor();
#endregion
?>
<!DOCTYPE html>
<html lang="en-US" dir="ltr">

<head>
<script>
            history.pushState(null, null, document.URL);
            window.addEventListener('popstate', function() {
                history.pushState(null, null, document.URL);
            });
        </script>
        
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ===============================================-->
    <!--    Document Title-->
    <!-- ===============================================-->
    <title>Admin Login | Bangladesh Bar Council</title>

    <!-- ===============================================-->
    <!--    Favicons-->
    <!-- ===============================================-->
    <link rel="apple-touch-icon" sizes="180x180" href="../../../assets/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../../../assets/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../../../assets/img/favicons/favicon-16x16.png">
    <link rel="shortcut icon" type="image/x-icon" href="../../../assets/img/favicons/favicon.ico">
    <link rel="manifest" href="../falcon/assets/img/favicons/manifest.json">
    <meta name="msapplication-TileImage" content="../../../assets/img/favicons/mstile-150x150.png">
    <meta name="theme-color" content="#ffffff">
    <script src="../falcon/assets/js/config.js"></script>
    <script src="../falcon/vendors/overlayscrollbars/OverlayScrollbars.min.js"></script>

    <!-- ===============================================-->
    <!--    Stylesheets-->
    <!-- ===============================================-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,600,700%7cPoppins:300,400,500,600,700,800,900&amp;display=swap" rel="stylesheet">
    <link href="../falcon/vendors/overlayscrollbars/OverlayScrollbars.min.css" rel="stylesheet">
    <link href="../falcon/assets/css/theme.min.css" rel="stylesheet" id="style-default">
    <link href="../falcon/assets/css/user.min.css" rel="stylesheet" id="user-style-default">

</head>

<body>
    <!-- ===============================================-->
    <!--    Main Content-->
    <!-- ===============================================-->
    <main class="main" id="top">
        <div class="container" data-layout="container">
            <script>
                var isFluid = JSON.parse(localStorage.getItem('isFluid'));
                if (isFluid) {
                    var container = document.querySelector('[data-layout]');
                    container.classList.remove('container');
                    container.classList.add('container-fluid');
                }
            </script>
            <div class="row flex-center min-vh-100 py-6">
                <div class="col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4"><a class="d-flex flex-center mb-4" href="<?=BASE_URL?>/admins/index.php"><img class="me-2" src="<?=BASE_URL?>/falcon/assets/img/icons/spot-illustrations/admin-logo.png" alt="" width="58" /><span class="font-sans-serif fw-bolder fs-5 d-inline-block">admins</span></a>
                    <div class="card">
                        <div class="card-body p-4 p-sm-5">
                            <div class="row flex-between-center mb-2">
                                <div class="col-auto">
                                    <h5>Log in</h5>
                                </div>
                                <div class="col-auto fs--1 text-600"><span class="mb-0 undefined">or</span> <span><a href="../../../pages/authentication/simple/register.html">Create an account</a></span></div>
                            </div>
                            <form id="main-form" method="POST" action="validate-login.php">
                                <div class="mb-3">
                                    <input name="loginName" class="form-control" type="text" placeholder="" />
                                </div>
                                <div class="mb-3">
                                    <input name="loginPassword" class="form-control" type="password" placeholder="" />
                                </div>
                                <div class="row flex-between-center">
                                    <div class="col-auto">
                                        <div class="form-check mb-0">
                                            <input name="remember" class="form-check-input" type="checkbox" id="basic-checkbox" />
                                        <label class="form-check-label mb-0" for="basic-checkbox">Remember me</label></div>
                                    </div>
                                    <div class="col-auto"><a class="fs--1" href="../../../pages/authentication/simple/forgot-password.html">Forgot Password?</a></div>
                                </div>
                                <div class="mb-3"><button class="btn btn-primary d-block w-100 mt-3" type="submit" name="submit">Log in</button></div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main><!-- ===============================================-->
    <!--    End of Main Content-->
    <!-- ===============================================-->



    <!-- ===============================================-->
    <!--    JavaScripts-->
    <!-- ===============================================-->
    <script src="../falcon/vendors/popper/popper.min.js"></script>
    <script src="../falcon/vendors/bootstrap/bootstrap.min.js"></script>
    <script src="../falcon/vendors/anchorjs/anchor.min.js"></script>
    <script src="../falcon/vendors/is/is.min.js"></script>
    <script src="../falcon/vendors/fontawesome/all.min.js"></script>
    <script src="../falcon/vendors/lodash/lodash.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=window.scroll"></script>
    <script src="../falcon/vendors/list.js/list.min.js"></script>
    <script src="../falcon/assets/js/theme.js"></script>

    <script>
        let baseUrl = '<?= BASE_URL ?>';
    </script>
    <?php
    Required::JQuery();
    ?>

    <script>
        $(document).ready(function() {

            if (localStorage.getItem("loginName") !== null) {
                $("input[name='loginName']").val(localStorage.getItem("loginName"));
            }

            if (localStorage.getItem("loginPassword") !== null) {
                $("input[name='loginPassword']").val(localStorage.getItem("loginPassword"));
            }

            $('.overlayButton').click(function() {
                $(this).hide();
                $(this).closest('.overlay').css("display", "none");
            });

            $('button[name="submit"]').click(function(e) {
                e.preventDefault();


                let submitButton = $(this);
                var formData = new FormData(document.getElementById("main-form"));

                let overlay = $(".overlay");
                overlay.css("display", "flex");
                let overlayMessagebox = $(".overlay").find('.message');
                let spinner = $(".overlay").find('.spinner');
                let overlayButton = $(".overlay").find('.overlayButton');

                spinner.show();
                overlayButton.hide();

                $.ajax({
                    url: 'validate-login.php',
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        submitButton.text('WORKING ....');
                        submitButton.attr('disabled', 'disabled');
                        overlayMessagebox.text("অনুগ্রহ করে অপেক্ষা করুন ... ");
                    },
                    success: function(response) {
                        console.log(response);
                        if (response.issuccess === undefined) {
                            overlay.css("display", "none");
                            alert('Problem in getting response from server.');
                            return;
                        }

                        if(response.issuccess === true) {
                            window.location = response.redirecturl;

                        } else {
                           alert(response.message);
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        submitButton.text("TRY AGAIN");
                        submitButton.removeAttr("disabled");
                        overlayMessagebox.html(xhr.responseText);
                        overlayButton.show();
                        spinner.hide();

                        console.log(xhr.responseText);
                        console.log(xhr);
                        console.log(textStatus + ' || ' + errorThrown);
                    }
                });
            });
        });
    </script>
</body>

</html>