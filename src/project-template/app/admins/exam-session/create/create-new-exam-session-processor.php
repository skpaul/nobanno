<?php 
    // This file is used in upload csv and single registration create page.
    require_once("../../../Required.php");

    Required::Logger()
        ->Database()
        ->Clock()
        ->EnDecryptor()
        ->Imaging()
        ->HttpHeader()
        ->JSON()
        ->Validable();

        $logger = new Logger(ROOT_DIRECTORY);
        $endecryptor = new EnDecryptor();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
        $form = new Validable();
        $clock = new Clock();
        $json = new JSON();
        $db->connect(); $db->fetchAsObject();

        

    try {
        $insert["title"]= strtoupper($form->label("Exam Title")->post("title")->required()->asString(true)->maxLen(20)->validate());
        $insert["reference"]= strtoupper($form->label("Exam Reference")->post("reference")->required()->asString(true)->maxLen(100)->validate());
       
        $insert["court"]= strtoupper($form->label("Court")->post("court")->required()->asString(true)->maxLen(20)->validate());
        $insert["applicationType"]= strtoupper($form->label("Application Type")->post("applicationType")->required()->asString(true)->maxLen(20)->validate());

        $pupilageContractCalculationDate = $form->label("Pupilage Contract Calculation Date")->post("pupilageContractCalculationDate")->required()->asDate()->validate();
        $insert["pupilageContractCalculationDate"] = $clock->toString($pupilageContractCalculationDate, DatetimeFormat::MySqlDate());

        $insert["isActive"] = $form->label("Exam Acitve Type")->post("isActive")->required()->asString(false)->validate();
        $isActiveAnswers = ["yes", "no"];
        if(!in_array(strtolower($insert["isActive"]), $isActiveAnswers)){
            die($json->fail()->message("Invalid Exam Acitve Type. It must be Yes/No.")->create()); 
        }

        if(strtolower($insert["isActive"]) == "yes"){
            $insert["isActive"] = 1;
        }
        else{
            $insert["isActive"] = 0;
        }

        $applicationStartDatetime = $form->label("Application Start Datetime")->post("applicationStartDatetime")->required()->asDate()->validate();
        $insert["applicationStartDatetime"] = $clock->toString($applicationStartDatetime, DatetimeFormat::MySqlDate());

        $applicationEndDatetime = $form->label("Application End Datetime")->post("applicationEndDatetime")->required()->asDate()->validate();
        $insert["applicationEndDatetime"] = $clock->toString($applicationEndDatetime, DatetimeFormat::MySqlDate());

        $admitCardStartDatetime = $form->label("Admit Card Start Datetime")->post("admitCardStartDatetime")->asDate()->validate();
        $insert["admitCardStartDatetime"] = $clock->toString($admitCardStartDatetime, DatetimeFormat::MySqlDate());

        $admitCardEndDatetime = $form->label("Admit Card End Datetime")->post("admitCardEndDatetime")->asDate()->validate();
        $insert["admitCardEndDatetime"] = $clock->toString($admitCardEndDatetime, DatetimeFormat::MySqlDate());

        $insert["regularFeeAmount"]= $form->label("Regular Fee Amount")->post("regularFeeAmount")->required()->asInteger(false)->maxLen(10)->validate();

        $insert["reappearFeeAmount"]= $form->label("Rappear Fee Amount")->post("reappearFeeAmount")->required()->asInteger(false)->maxLen(10)->validate();

        $lastDateOfTeletalkFeePayment = $form->label("Last Date Of Teletalk Fee Payment")->post("lastDateOfTeletalkFeePayment")->required()->asDate()->validate();
        $insert["lastDateOfTeletalkFeePayment"] = $clock->toString($lastDateOfTeletalkFeePayment, DatetimeFormat::MySqlDate());


    } catch (\ValidableException $ve) {
        die($json->fail()->message($ve->getMessage())->create());
    }
    catch (\Exception $exp) {
        $logger->createLog($exp->getMessage());
        die($json->fail()->message($exp->getMessage())->create());
    }

    try{
        $insert["configId"] = $db->insert($insert, "post_configurations");
          $destinationDir = ROOT_DIRECTORY . "/attachments";
          if (!file_exists($destinationDir)) {
              mkdir($destinationDir, 0777, true);
          }

          $fileInput = "circularFileName";
          if(!empty($_FILES[$fileInput]) && $_FILES[$fileInput]['name'] != '' && $_FILES[$fileInput]['error'] === UPLOAD_ERR_OK){
              $OriginalFileName = $_FILES[$fileInput]['name'];
              $TempFilePath = $_FILES[$fileInput]['tmp_name'];

              // get uploaded file's extension
              $FileExtension = strtolower(pathinfo($OriginalFileName, PATHINFO_EXTENSION)); //sharmin.pdf -> pdf

              $FileSizeInByte = filesize($TempFilePath);  //OR, $_FILES["fileToUpload"]["size"]
              $FileSizeInKB= $FileSizeInByte/1024;
              $MaxSizeInKB = 500;

              if($FileSizeInKB > $MaxSizeInKB)
              {
                  $message = $insert["title"] . ": file size too big. Max size $MaxSizeInKB KB";
                  exit($json->fail()->message($message)->create());
              }

              $newFileName = $insert['configId'] . '.' . $FileExtension;  //10.pdf
              $destinationPath = $destinationDir . "/" . $newFileName;
              if (file_exists($destinationPath)) {
                  unlink($destinationPath);
              }

              if(!move_uploaded_file($TempFilePath,$destinationPath)){
                  throw new ImageException("Failed to save " . $insert["title"]);
                  exit($json->fail()->message("Failed to save the file.")->create());
              }
          }

        $sql = "UPDATE post_configurations SET `circularFileName`='$newFileName' WHERE configId=". $insert["configId"];
        $db->update($sql);
        $redirectUrl = BASE_URL . "/admins/exam-session/read/list/exam-session-list.php";
        exit($json->success()->message("Exam Session Created successully.")->redirecturl($redirectUrl)->create());

    }catch (\Exception $exp) {
        if($db->inTransaction()){
            $db->rollBack();
        }
        $logger->createLog($exp->getMessage());
        die($json->fail()->message("Problem in saving data. Please try again.")->create());
    }
    


    // $redirectUrl = BASE_URL . "/admins/exam-session/read/list/exam-session-list.php";

    // exit($json->success()->message("Success")->redirecturl($redirectUrl)->create());
?>