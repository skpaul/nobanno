<?php
    require_once('../../Required.php');
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php
        Required::gtag()->metaTags()->favicon()->bootstrapGrid()->siteCSS();
        ?>
        <title>বিদ্যালয় কর্তৃপক্ষ লগইন- <?= ORGANIZATION_FULL_NAME ?></title>

    </head>

    <body id="school-login">
        <div class="center-container">
            <div>
                <h1 lang="bn" style="font-size: 1.5rem;">সরকারি বিদ্যালয় কর্তৃপক্ষ লগইন</h1>
                <div class="field">
                    <label for="">ই.আই.আই.এন নম্বর</label>
                    <input type="text" name="eiin" id="eiin" class="integer" maxlength="10" placeholder="i.e. 461547" value="">
                </div>

                <div class="field">
                    <label for="">পাসওয়ার্ড</label>
                    <input type="password" name="password" maxlength="10" value="">
                </div>

                <button class="button" id="submit" name="submit">LOGIN</button>
                <div id="ajax-status"></div>
            </div>
        </div>

        <?php
            Required::JQuery()->SwiftNumeric();
        ?>
        <script src="login.js?v=<?=time();?>"></script>
        <script>
            SwiftNumeric.prepare('.integer');

            history.pushState(null, document.title, location.href);
            window.addEventListener('popstate', function(event) {
                history.pushState(null, document.title, location.href);
            });
        </script>
    </body>
</html>