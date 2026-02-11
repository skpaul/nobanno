<?php
    namespace Nobanno;
    class ImageException extends \Exception
    {
    }

    class Imaging { 
        //<input type="file" name="ApplicantPhoto">
        //$HtmlFileInputName = ApplicantPhoto
        public static function validate($HtmlFileInputName, $title, $WidthRequired, $HeightRequired, $MaxSizeInKB){

            if(empty($_FILES[$HtmlFileInputName])){
                $message = $title . ": image file required."; //Error code- $error_code .
                throw new ImageException($message);
            }

            if($_FILES[$HtmlFileInputName]['name'] == ''){
                $message = $title . ": image file required."; //Error code- $error_code .
                throw new ImageException($message);
            }
            
            // never assume the upload succeeded
            if ($_FILES[$HtmlFileInputName]['error'] !== UPLOAD_ERR_OK) {
                //die("Upload failed with error code " . $_FILES['applicantphoto']['error']);
                $error_code = $_FILES[$HtmlFileInputName]['error'];
                $message = $title . ": invalid image."; //Error code- $error_code .
                throw new ImageException($message);
            }
        
            
            $info = getimagesize($_FILES[$HtmlFileInputName]['tmp_name']);
            if ($info === FALSE) {
                // die("Unable to determine image type of uploaded file");
                $message = $title . ": unable to determine image type of uploaded file";
                throw new ImageException($message);
            }

            if ($info[2] !== IMAGETYPE_JPEG) {
                $message = $title . ": file extension must be .jpg";
                throw new ImageException($message);
            }

            list($width, $height, $type, $attr) = getimagesize($_FILES[$HtmlFileInputName]['tmp_name']);
            // Result like this -
            //  Width: 200
            //  Height: 100
            //  Type: 2
            //  Attribute: width='200' height='100'


            //  Type of image consider like -
            //  1 = GIF
            //  2 = JPG
            //  3 = PNG
            //  4 = SWF
            //  5 = PSD
            //  6 = BMP
            //  7 = TIFF(intel byte order)
            //  8 = TIFF(motorola byte order)
            //  9 = JPC
            //  10 = JP2
            //  11 = JPX
            //  12 = JB2
            //  13 = SWC
            //  14 = IFF
            //  15 = WBMP
            //  16 = XBM


            if(($WidthRequired>0) && ($width != $WidthRequired)){
                $message = $title . ": image width must be $WidthRequired px";
                throw new ImageException($message);
            }

            if(($HeightRequired>0) && ($height != $HeightRequired)){
                $message = $title . ": image height must be $HeightRequired px";
                throw new ImageException($message);
            }

            if($type != 2){
                $message = $title . ": file extension must be .jpg";
                throw new ImageException($message);
            }

            if(empty($_FILES[$HtmlFileInputName]['name'])){
                $message = $title . ": file name invalid";
                throw new ImageException($message);
            }

            $OriginalFileName = $_FILES[$HtmlFileInputName]['name'];
            $TempFilePath = $_FILES[$HtmlFileInputName]['tmp_name'];

            // get uploaded file's extension
            $FileExtension = strtolower(pathinfo($OriginalFileName, PATHINFO_EXTENSION));

            $ValidExtensions = array('jpg');
            if(!in_array($FileExtension, $ValidExtensions)) 
            {
                $message = $title . ": image file extension must be .jpg";
                throw new ImageException($message);
            }

            $FileSizeInByte = filesize($TempFilePath);  //OR, $_FILES["fileToUpload"]["size"]
            $FileSizeInKB= $FileSizeInByte/1024;

            if($FileSizeInKB > $MaxSizeInKB)
            {
                $message = $title . ": file size too big. Max size $MaxSizeInKB KB";
                throw new ImageException($message);
            }
        }

        //<input type="file" name="ApplicantPhoto">
        //$HtmlFileInputName = ApplicantPhoto
        public static function save($HtmlFileInputName, $title, $DestinationPath){

            $OriginalFileName = $_FILES[$HtmlFileInputName]['name'];
            $TempFilePath = $_FILES[$HtmlFileInputName]['tmp_name'];
        
            if(!move_uploaded_file($TempFilePath,$DestinationPath)){
                throw new ImageException("Failed to save " . $title);
            }

            // $photo_size = GetImageSize("$TempFilePath"); //$photo_temp
            // $photo_width = $photo_size[0];
            // $photo_height = $photo_size[1];
        
            // $img_jpeg_p = imagecreatefromjpeg($TempFilePath); //$photo_temp = $_FILES["photo"]["tmp_name"];
            // $img_w_p = 150;
            // $img_h_p = 150;
            // $tmp_p=imagecreatetruecolor($img_w_p,$img_h_p);
            // imagecopyresampled($tmp_p,$img_jpeg_p,0,0,0,0,$img_w_p,$img_h_p,$photo_width,$photo_height);
            // imagejpeg($tmp_p,$DestinationPath, 80); //$output_file_p = "images/photo/$new_photo";
            // imagedestroy($tmp_p);
            // imagedestroy($img_jpeg_p);
        }
    }



 



 


//No Need. This is the original method. Keep this for reference.
// function UploadPhoto(){

//     // never assume the upload succeeded
//     if ($_FILES['applicantphoto']['error'] !== UPLOAD_ERR_OK) {
//         //die("Upload failed with error code " . $_FILES['applicantphoto']['error']);
//         $error_code = $_FILES['applicantphoto']['error'];
//         echo '{"issuccess":false,"message":"Upload failed. Error code-'. $error_code .'"}';
//         exit;
//     }
 
    
//     $info = getimagesize($_FILES['applicantphoto']['tmp_name']);
//         if ($info === FALSE) {
//         // die("Unable to determine image type of uploaded file");
//         echo '{"issuccess":false,"message":"Unable to determine image type of uploaded file."}';
//         exit;
//     }

//     if ($info[2] !== IMAGETYPE_JPEG) {
//         echo '{"issuccess":false,"message":"Invalid file extension. Only .jpg file allowed."}';
//         exit;
//      }

//     list($width, $height, $type, $attr) = getimagesize($_FILES['applicantphoto']['tmp_name']);
//     // Result like this -
//     //  Width: 200
//     //  Height: 100
//     //  Type: 2
//     //  Attribute: width='200' height='100'


//     //  Type of image consider like -
//     //  1 = GIF
//     //  2 = JPG
//     //  3 = PNG
//     //  4 = SWF
//     //  5 = PSD
//     //  6 = BMP
//     //  7 = TIFF(intel byte order)
//     //  8 = TIFF(motorola byte order)
//     //  9 = JPC
//     //  10 = JP2
//     //  11 = JPX
//     //  12 = JB2
//     //  13 = SWC
//     //  14 = IFF
//     //  15 = WBMP
//     //  16 = XBM

//     if($width != 300){
//         echo '{"issuccess":false,"message":"Invalid width. It must be 300px."}';
//         exit;
//     }

//     if($height != 300){
//         echo '{"issuccess":false,"message":"Invalid height. It must be 300px."}';
//         exit;
//     }

//     if($type != 2){
//         echo '{"issuccess":false,"message":"Invalid file type."}';
//         exit;
//     }

//     if( !empty($_FILES['applicantphoto']['name'])){
        
//         $OriginalFileName = $_FILES['applicantphoto']['name'];
//         $TempFilePath = $_FILES['applicantphoto']['tmp_name'];

//         // get uploaded file's extension
//         $FileExtension = strtolower(pathinfo($OriginalFileName, PATHINFO_EXTENSION));

//         $ValidExtensions = array('jpg');
//         if(!in_array($FileExtension, $ValidExtensions)) 
//         {
//             echo '{"issuccess":false,"message":"Invalid file extension."}';
//             exit;
//         }
    
//         $FileSizeInByte = filesize($TempFilePath);  //OR, $_FILES["fileToUpload"]["size"]
//         $FileSizeInKB= $FileSizeInByte/1024;

//         if($FileSizeInKB>100)
//         {
//             echo '{"issuccess":false,"message":"Invalid file size."}';
//             exit;
//         }

//         $userid = "123";
    

//         $newFileName = "$userid.jpg";

    
//         $targetPath = "$newFileName";

    
//         if(move_uploaded_file($TempFilePath,$targetPath)){
//             echo '{"issuccess":true,"url":"hi"}';
//             exit;
//         }
//         else    {
//             echo '{"issuccess":false,"message":"Problem in uploading photo. Please try again."}';
//             exit;
//         }
//     }
//     else{
//         echo '{"issuccess":false,"message":"Problem in uploading photo. Please try again."}';
//         exit;
//     }
// }
 
    
?>