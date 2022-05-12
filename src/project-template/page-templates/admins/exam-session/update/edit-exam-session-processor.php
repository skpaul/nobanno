<?php 
    // This file is used in upload csv and single registration create page.
    require_once("../../../Required.php");

    Required::Logger()
        ->Database()
        ->Clock()
        ->EnDecryptor()
        ->JSON()
        ->Validable();

        $logger = new Logger(ROOT_DIRECTORY);
        $endecryptor = new EnDecryptor();
        $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
        $form = new Validable();
        $clock = new Clock();
        $json = new JSON(); 
         
        $db->connect(); $db->fetchAsObject();

       
            $encConfigId = trim($_GET["config-id"]);

            $configId =  $endecryptor->decrypt($encConfigId);
            try {
                $update["configId"] =$form->label("Config Id")->post("configId")->required()->validate();
                $update["configId"] =  $endecryptor->decrypt($update["configId"]);

                $update["title"]= strtoupper($form->label("Exam Title")->post("title")->required()->asString(true)->maxLen(20)->validate());
                $update["reference"]= strtoupper($form->label("Exam Reference")->post("reference")->required()->asString(true)->maxLen(100)->validate());
                $update["court"]= strtoupper($form->label("Court")->post("court")->required()->asString(true)->maxLen(20)->validate());
                $update["applicationType"]= strtoupper($form->label("Application Type")->post("applicationType")->required()->asString(true)->maxLen(20)->validate());

                $update["pupilageContractCalculationDate"] = $form->label("Pupilage Contract Calculation Date")->post("pupilageContractCalculationDate")->required()->asDate()->validate();
                $update["pupilageContractCalculationDate"] = $clock->toString($update["pupilageContractCalculationDate"], DatetimeFormat::MySqlDate());

                $update["isActive"] = $form->label("Exam Acitve Type")->post("isActive")->required()->asString(false)->validate();
                $isActiveAnswers = ["yes", "no"];
                if(!in_array(strtolower($update["isActive"]), $isActiveAnswers)){
                    die($json->fail()->message("Invalid Exam Acitve Type. It must be Yes/No.")->create()); 
                }

                if(strtolower($update["isActive"]) == "yes"){
                    $update["isActive"] = 1;
                }
                else{
                    $update["isActive"] = 0;
                }

                $update["applicationStartDatetime"] = $form->label("Application Start Datetime")->post("applicationStartDatetime")->required()->asDate()->validate();
                $update["applicationStartDatetime"] = $clock->toString($update["applicationStartDatetime"], DatetimeFormat::MySqlDate());

                $update["applicationEndDatetime"] = $form->label("Application End Datetime")->post("applicationEndDatetime")->required()->asDate()->validate();
                $update["applicationEndDatetime"] = $clock->toString($update["applicationEndDatetime"], DatetimeFormat::MySqlDate());

                $update["admitCardStartDatetime"] = $form->label("Admit Card Start Datetime")->post("admitCardStartDatetime")->required()->asDate()->validate();
                $update["admitCardStartDatetime"] = $clock->toString($update["admitCardStartDatetime"], DatetimeFormat::MySqlDate());

                $update["admitCardEndDatetime"] = $form->label("Admit Card End Datetime")->post("admitCardEndDatetime")->required()->asDate()->validate();
                $update["admitCardEndDatetime"] = $clock->toString($update["admitCardEndDatetime"], DatetimeFormat::MySqlDate());

                $update["regularFeeAmount"]= $form->label("Regular Fee Amount")->post("regularFeeAmount")->required()->asInteger(false)->maxLen(10)->validate();

                $update["reappearFeeAmount"]= $form->label("Reappear Fee Amount")->post("reappearFeeAmount")->required()->asInteger(false)->maxLen(10)->validate();

                $update["lastDateOfTeletalkFeePayment"] = $form->label("Last Date Of Teletalk Fee Payment")->post("lastDateOfTeletalkFeePayment")->required()->asDate()->validate();
                $update["lastDateOfTeletalkFeePayment"] = $clock->toString($update["lastDateOfTeletalkFeePayment"], DatetimeFormat::MySqlDate());


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
                        $message = $update["title"] . ": file size too big. Max size $MaxSizeInKB KB";
                        exit($json->fail()->message($message)->create());
                    }

                    $update["circularFileName"] = $update['configId'] . '.' . $FileExtension;  //10.pdf
                    $destinationPath = $destinationDir . "/" . $update["circularFileName"];
                    if (file_exists($destinationPath)) {
                        unlink($destinationPath);
                    }

                    if(!move_uploaded_file($TempFilePath,$destinationPath)){
                        throw new ImageException("Failed to save " . $update["title"]);
                        exit($json->fail()->message("Failed to save the file.")->create());
                    }
                }

                // var_dump($update);
            } catch (\ValidableException $ve) {
                die($json->fail()->message($ve->getMessage())->create());
            }
            catch (\Exception $exp) {
                $logger->createLog($exp->getMessage());
                die($json->fail()->message($exp->getMessage())->create());
            }
            try{
                $db->update("update post_configurations set 
                title=:title,
                reference=:reference,
                circularFileName=:circularFileName,
                court=:court,
                applicationType=:applicationType,
                pupilageContractCalculationDate=:pupilageContractCalculationDate,
                isActive=:isActive,
                applicationStartDatetime=:applicationStartDatetime,
                applicationEndDatetime=:applicationEndDatetime,
                admitCardStartDatetime=:admitCardStartDatetime,
                admitCardEndDatetime=:admitCardEndDatetime,
                regularFeeAmount=:regularFeeAmount,
                reappearFeeAmount=:reappearFeeAmount,
                lastDateOfTeletalkFeePayment=:lastDateOfTeletalkFeePayment
                where configId=:configId", 
                $update);   
            }catch (\Exception $exp) {
                $logger->createLog($exp->getMessage());
                die($json->fail()->message("Failed to save data.")->create()); 
        
            }
        exit($json->success()->message("Success.")->create());
    ?>