<?php 
    require_once("../CONSTANTS.php");
    require_once("prevent_access_if_not_localhost.php");
    $queryString = $_SERVER['QUERY_STRING'];
?>


<form action="change-token.php?<?=$queryString?>" method="POST">
    <label for="newToken">New Token</label>
    <input type="password" name="token" value="">
    <input type="submit" name="submit" value="Change">
</form>

<?php 
    if(isset($_POST["submit"])){
        $newToken =trim($_POST["token"]);
        if(empty($newToken)){
            die("Token required");
        }
        file_put_contents("token.txt",$newToken);
        echo "Success";
    }
?>