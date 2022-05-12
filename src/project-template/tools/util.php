<form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>" enctype="multipart/form-data">
    <div style="display: flex; flex-direction:row;">
        <div style="width: 200px;">File Path</div>
        <div style="flex:1;">
            <input type="text" name="filePath" placeholder="i.e. images/photo.png" style="width:100%;">
        </div>
    </div>
    <div>
        <input type="file" name="newFile">
    </div>
    <input type="submit" name="submit" value="submit">
</form>

<?php
    require_once("CONSTANTS.php");
    require_once(ROOT_DIRECTORY . "/lib/error_reporting.php");

    // if($_SERVER['REQUEST_METHOD'] == 'POST'){
    if (isset($_POST['submit'])){
        // never assume the upload succeeded
        if ($_FILES['newFile']['error'] !== UPLOAD_ERR_OK) {
            die("Upload failed with error code " . $_FILES['newFile']['error']);
        }

        if(empty($_FILES['newFile']['name'])){
            die('file is empty');
        }

        $filePath = $_POST["filePath"];
        $DestinationPath = ROOT_DIRECTORY . "/" . $filePath;
        if (file_exists($DestinationPath)) {
            unlink($DestinationPath);
        }

        //$OriginalFileName = $_FILES['newFile']['name'];
        $TempFilePath = $_FILES['newFile']['tmp_name'];
    
        if(move_uploaded_file($TempFilePath,$DestinationPath)){
            exit('Success');
        }
        else{
            exit('Failed');
        }  

    }
    
?>