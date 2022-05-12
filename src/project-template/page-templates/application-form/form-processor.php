<?php 
    require_once("../Required.php");

    Required::Logger()
        ->Database()->DbSession()
        ->Clock()
        ->EnDecryptor()
        ->JSON()
        ->Validable()
        ->AgeCalculator(2)
        ->Imaging()
        ->UniqueCodeGenerator()
        ->Helpers()->ExclusivePermission()->HttpHeader();

    $logger = new Logger(ROOT_DIRECTORY);
    $endecryptor = new EnDecryptor();
    $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    $form = new Validable();
    $clock = new Clock();
    $json = new JSON();

    $db->connect(); $db->fetchAsObject();

    #region check session
        if(!isset($_GET["session-id"]) || empty(trim($_GET["session-id"]))){
            die($json->fail()->message("Invalid session. Please login again. Error code- 774965")->create());
        }

        $encSessionId = trim($_GET["session-id"]);

        try {
            $sessionId = $endecryptor->decrypt($encSessionId);
            $session = new DbSession($db, "lc_enrolment_sessions");
            $session->continue($sessionId);
        } catch (\SessionException $th) {
            die($json->fail()->message("Invalid session. Please login again.")->create());
        } catch (\Exception $exp) {
            die($json->fail()->message("Invalid session. Please login again.")->create());
        }
    #endregion

    $registrationId = $session->getData("registrationId");

    //if post configuration id "config-id" key found in query string, redirect to the sorry page.
    if(!isset($_POST["configId"]) || empty(trim($_POST["configId"])))
        die($json->fail()->redirecturl(BASE_URL . "/sorry.php?msg=This is not a valid request.")->create());
    $encConfigId = trim($_POST["configId"]); //decryption has been done in the following try .. catch block for safety reason.
    try {
        $postConfigId =  $endecryptor->decrypt($encConfigId);
    } catch (\Exception $exp) {
        HttpHeader::redirect(BASE_URL . "/sorry.php");
    }

    $hasExclusivePermission = ExclusivePermission::hasPermission();
 
    //If has exclusive permission, don't check isActive status.
    //If does not have exclusive permission, check isActive status.
    if($hasExclusivePermission)
        $sql = "SELECT * FROM `post_configurations` WHERE court=:court AND applicationType = :applicationType AND configId=:configId";
    else
        $sql = "SELECT * FROM `post_configurations` WHERE isActive=1 AND court=:court AND applicationType = :applicationType AND configId=:configId";

    $configs = $db->select($sql, array('court' => COURT, "applicationType"=>APPLICATION_TYPE, "configId"=>$postConfigId ));  
    //whether exclusive permission exists or not, the post configuration must exist.
    if(count($configs) != 1){
        die($json->fail()->message("Application not available.")->create());
    }

    $postConfig = $configs[0];

    //If does not have exclusive permission, check application start and end datetime.
    if(!$hasExclusivePermission){    
        $now = $clock->toDate("now");
        $applicationStartDatetime = $clock->toDate($postConfig->applicationStartDatetime); 
        $applicationEndDatetime = $clock->toDate($postConfig->applicationEndDatetime); 
        if($now < $applicationStartDatetime) 
            die($json->fail()->message("Application will start from " . $clock->toString($postConfig->applicationStartDatetime, DatetimeFormat::BdDate()))->create());
        if($now > $applicationEndDatetime) 
            die($json->fail()->message("Application is not available")->create());
            // HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Application is not available.");
    }

    #region Read registration details
        $sql = "SELECT * FROM lc_enrolment_registrations WHERE registrationId=:registrationId ";
        $registrations = $db->select($sql, array('registrationId' => $registrationId));   
        if(count($registrations) != 1) 
            die($json->fail()->message("Registration information not found.")->create());
        $registration = $registrations[0];
    #endregion

    #region Whether applied previously or not.
        // $sql = "SELECT cinfoId FROM `lc_enrolment_cinfo` WHERE regNo=:regNo AND regYear=:regYear";
        // $alreadyApplied = $db->select($sql, array("regNo"=>$registration->regNo, "regYear"=>$registration->regYear));
        // if(count($alreadyApplied) > 0){
        //     die($json->fail()->message("You already applied.")->create());
        // }
    #endregion
    
    #region transfer data from registration to cinfo
        $cinfo["postConfigId"] = $postConfig->configId;
        $cinfo["registrationId"] = trim($registration->registrationId);
        $cinfo["regNo"] = trim($registration->regNo);
        $cinfo["regYear"] = trim($registration->regYear);
        $cinfo["applicantType"] = $registration->applicantType;
        $cinfo["formType"] = $registration->applicantType;
    #endregion
    
    #region Validate form except higher educations
        try {
            
            $cinfo["fullName"] = strtoupper($form->label("Applicant's name")->post("fullName")->required()->asString(true)->maxLen(100)->validate());
            $cinfo["fatherName"] = strtoupper($form->label("Father's name")->post("fatherName")->required()->asString(true)->maxLen(100)->validate());
            $cinfo["motherName"] = strtoupper($form->label("Mother's name")->post("motherName")->required()->asString(true)->maxLen(100)->validate());

            $dateOfBirth = $form->label("Date of birth")->post("dob")->required()->asDate()->validate();
            $cinfo["dob"] = $dateOfBirth->format('Y-m-d');
            
            $cinfo["gender"] = $form->label("Gender")->post("gender")->required()->asString(true)->maxLen(6)->validate();
            $cinfo["mobileNo"] = $form->label("Mobile no.")->post("mobileNo")->required()->asMobile()->maxLen(13)->validate();

            $reMobileNo = $form->label("Retype mobile no.")->post("reMobileNo")->required()->asMobile()->maxLen(13)->validate();

            if($cinfo["mobileNo"] !=  $reMobileNo)
                die($json->fail()->message("Mobile No. and retype mobile no. did not match.")->create());
            
            
            $cinfo["email"] = $form->label("Email")->post("email")->asEmail()->maxLen(40)->default("")->validate();
            $cinfo["nationality"] = $form->label("Nationality")->post("nationality")->required()->asString(true)->maxLen(50)->validate();
            
            $cinfo["birthCertNo"] = $form->label("Birth Certificate No.")->post("birthCertNo")->optional()->asNumeric()->maxLen(30)->default(NULL)->validate();

            $cinfo["nidNo"]  = $form->label("NID No.")->post("nidNo")->optional()->asNumeric()->maxLen(30)->default(NULL)->validate();

            $cinfo["passportNo"]  = $form->label("Passport No.")->post("passportNo")->optional()->asString(true)->maxLen(30)->default(NULL)->validate();
            
            if($cinfo["birthCertNo"] == NULL && $cinfo["nidNo"] == NULL && $cinfo["passportNo"] == NULL)
                die($json->fail()->message("Birth Certificate or NID or Passport No. required.")->create());
            

            $cinfo["presentAddress"] = $form->label("Present address")->post("presentAddress")->required()->asString(true)->maxLen(150)->validate();
            $cinfo["presentDist"] = $form->label("Present district")->post("presentDist")->required()->asString(true)->maxLen(100)->validate();
            $cinfo["presentThana"] = $form->label("Present thana")->post("presentThana")->required()->asString(true)->maxLen(100)->validate();
            $cinfo["presentGpo"] = $form->label("Present postal code")->post("presentGpo")->asNumeric()->maxLen(4)->default("")->validate();
            
            $cinfo["permanentAddress"] = $form->label("Permanent address")->post("permanentAddress")->required()->asString(true)->maxLen(150)->validate();
            $cinfo["permanentDist"] = $form->label("Permanent district")->post("permanentDist")->required()->asString(true)->maxLen(100)->validate();
            $cinfo["permanentThana"] = $form->label("Permanent thana")->post("permanentThana")->required()->asString(true)->maxLen(100)->validate();
            $cinfo["permanentGpo"] = $form->label("Permanent postal code")->post("permanentGpo")->asNumeric()->maxLen(4)->default("")->validate();
            
            #region SSC result validation
                $cinfo["sscExamName"] = $form->label("S.S.C/Equivalent examination name")->post("sscExamName")->required()->asString(true)->maxLen(50)->validate();
                $cinfo["sscRollNo"] = $form->label("S.S.C/Equivalent roll no.")->post("sscRollNo")->required()->asNumeric()->maxLen(20)->validate();
                $cinfo["sscRegiNo"] = $form->label("S.S.C/Equivalent registration no.")->post("sscRegiNo")->required()->asNumeric()->maxLen(20)->validate();
                $cinfo["sscYear"] = $form->label("S.S.C/Equivalent year")->post("sscYear")->required()->asInteger(false)->maxLen(11)->validate();
                $cinfo["sscBoard"] = $form->label("S.S.C/Equivalent board")->post("sscBoard")->required()->asString(true)->maxLen(100)->validate();

                if(strtolower($cinfo["sscExamName"]) == "o level"){
                    $cinfo["oLevelResultDetails"] = $form->label("O Level result details")->post("oLevelResultDetails")->required()->asString(true)->maxLen(255)->validate();
                }else{
                    $cinfo["sscResultType"] = $form->label("S.S.C/Equivalent result type")->post("sscResultType")->required()->asString(true)->maxLen(20)->validate();
                    if (strtolower($cinfo["sscResultType"] ) == 'division') 
                        $cinfo["sscDivision"] = $form->label("S.S.C Division")->post("sscDivision")->required()->asString(true)->maxLen(20)->validate();
                        
                    if (strtolower($cinfo["sscResultType"]) == 'grade'){
                        $cinfo["sscGpa"] = $form->label("S.S.C Gpa")->post("sscGpa")->required()->asFloat(true)->maxLen(4)->validate();
                        $cinfo["sscScale"] = $form->label("S.S.C Scale")->post("sscScale")->required()->asFloat(true)->maxLen(1)->validate();
    
                        if($cinfo["sscGpa"] > $cinfo["sscScale"])
                            die($json->fail()->message("S.S.C/Equivalent G.P.A  must be equal to or less than scale.")->create());
                    }        
    
                    $cinfo["sscGroup"] = $form->label("S.S.C Group")->post("sscGroup")->required()->asString(true)->maxLen(20)->validate();
                }
            #endregion

            #region HSC result validation
                $cinfo["hscExamName"] = $form->label("H.S.C Exam Name")->post("hscExamName")->required()->asString(true)->maxLen(50)->validate();
                $cinfo["hscRollNo"] = $form->label("H.S.C Roll No")->post("hscRollNo")->required()->asNumeric()->maxLen(20)->validate();
                $cinfo["hscRegiNo"] = $form->label("H.S.C Registration No.")->post("hscRegiNo")->required()->asNumeric()->maxLen(20)->validate();
                $cinfo["hscYear"] = $form->label("H.S.C Year")->post("hscYear")->required()->asInteger(false)->maxLen(11)->validate();
                $cinfo["hscBoard"] = $form->label("H.S.C Board")->post("hscBoard")->required()->asString(true)->maxLen(100)->validate();

                if(strtolower($cinfo["hscExamName"] ) == "a level"){
                    $cinfo["aLevelResultDetails"] = $form->label("A Level Details")->post("aLevelResultDetails")->required()->asString(true)->maxLen(255)->validate();
                }else{
                    $cinfo["hscResultType"] = $form->label("H.S.C ResultType")->post("hscResultType")->required()->asString(true)->maxLen(20)->validate();
                    if (strtolower($cinfo["hscResultType"] ) == 'division') {
                        $cinfo["hscDivision"] = $form->label("H.S.C Division")->post("hscDivision")->required()->asString(true)->maxLen(20)->validate();
                    }
                    
                    if (strtolower($cinfo["hscResultType"]) == 'grade'){
                        $cinfo["hscGpa"] = $form->label("H.S.C Gpa")->post("hscGpa")->required()->asFloat(true)->maxLen(4)->validate();
                        $cinfo["hscScale"] = $form->label("H.S.C Scale")->post("hscScale")->required()->asFloat(true)->maxLen(1)->validate();
    
                        if($cinfo["hscGpa"] > $cinfo["hscScale"])
                            die($json->fail()->message("H.S.C/Equivalent G.P.A  must be equal to or less than scale.")->create());
                    }  
    
                    $cinfo["hscGroup"] = $form->label("H.S.C Group")->post("hscGroup")->required()->asString(true)->maxLen(20)->validate();
                }
            #endregion

            $cinfo["pupilageContractDate"] = $registration->pupilageContractDate;
        
            //engagement --->
                $cinfo["isEngaged"] = $form->label("Engagement Status")->post("isEngaged")->required()->asString(false)->maxLen(10)->validate();
                if($cinfo["isEngaged"] == "Yes"){
                    $cinfo["engagementNature"] = $form->label("Nature of engagement")->post("engagementNature")->required()->asString(true)->maxLen(150)->validate();
                    $cinfo["engagementPlace"] = $form->label("Place of engagement")->post("engagementPlace")->required()->asString(true)->maxLen(100)->validate();
                }
            //<--- engagement

            $cinfo["declaredInsolvent"] = $form->label("Insolvent declaration information")->post("declaredInsolvent")->required()->asString(false)->maxLen(10)->validate();

            //Dismissal -- >
                $cinfo["isDismissed"] = $form->label("Dismissal status")->post("isDismissed")->required()->asString(false)->maxLen(10)->validate();
                if($cinfo["isDismissed"]=="Yes"){
                    $cinfo["dismissalDate"] = $form->label("Dismissal date")->post("dismissalDate")->required()->asDate()->validate();
                    $cinfo["dismissalDate"] = $clock->toString( $cinfo["dismissalDate"], DatetimeFormat::MySqlDate());
                    $cinfo["dismissalReason"] = $form->label("Dismissal reason")->post("dismissalReason")->required()->asString(true)->maxLen(100)->validate();
                }
            //<-- Dismissal

            //Conviction -- >
                $cinfo["isConvicted"] = $form->label("Conviction status")->post("isConvicted")->required()->asString(false)->maxLen(10)->validate();
                if($cinfo["isConvicted"]=="Yes"){
                    $cinfo["convictionDate"] = $form->label("Conviction date")->post("convictionDate")->required()->asDate()->validate();
                    $cinfo["convictionDate"] = $clock->toString( $cinfo["convictionDate"], DatetimeFormat::MySqlDate());
                    $cinfo["convictionParticulars"] = $form->label("Conviction particulars")->post("convictionParticulars")->required()->asString(true)->maxLen(150)->validate();
                }
            //<-- Conviction

            $cinfo["isCancelledPreviously"] = $form->label("Previous enrolment cancellation information")->post("isCancelledPreviously")->required()->asString(false)->maxLen(10)->validate();

            // die($registration->applicantType);
            if( strtolower($registration->applicantType) === "re-appeared"){
                $cinfo["lastRoll"] = $form->label("Last written examination roll no.")->post("lastRoll")->required()->asInteger(false)->maxLen(10)->validate();
                $cinfo["lastWrittenExamYear"] = $form->label("Last written examination year")->post("lastWrittenExamYear")->required()->asInteger(false)->exactLen(4)->validate();
            }

            $cinfo["barName"]= strtoupper($form->label("Bar Name")->post("barName")->required()->asString(true)->maxLen(100)->validate());
            $cinfo["seniorAdvocateName"]= strtoupper($registration->seniorAdvocateName);

            // $cinfo["appliedDatetime"] = $clock->toString("now", DatetimeFormat::MySqlDatetime());

        } catch (\ValidableException $ve) {
            die($json->fail()->message($ve->getMessage())->create());
        }
        catch (\Exception $exp) {
            $logger->createLog($exp->getMessage());
            die($json->fail()->message($exp->getMessage())->create());
        }
    #endregion

    #region Higher Education
        try{
            #region LL.B
                $higherEducations["llbExam"] = $form->label("LL.B Exam")->post("llbExam")->required()->asString(true)->maxLen(50)->validate();
                $higherEducations["llbId"]= $form->label("LL.B ID/Roll/Regi No.")->post("llbId")->required()->asString(true)->maxLen(20)->default(NULL)->validate();

                $llbCountryName = $form->label("LL.B Country Name")->post("llbCountryName")->required()->asString(true)->maxLen(50)->validate();
                if($llbCountryName == "Bangladesh"){
                    $higherEducations["llbCountry"] = "Bangladesh";
                }
                else{
                    $higherEducations["llbCountry"] = $form->label("LL.B Country Name")->post("llbOtherCountryName")->required()->asString(true)->maxLen(50)->validate();
                    $hasLlbEquivalentCertificate = $form->label("LL.B Equivalent Certificate")->post("hasLlbEquivalentCertificate")->required()->asString(false)->maxLen(3)->validate();
                    if(strtolower($hasLlbEquivalentCertificate) != "yes"){
                        die($json->fail()->message("LL.B Equivalent Certificate of Bar Council is required.")->create());
                    }
                }
            
                // $higherEducations["llbUni"]= $form->label("LLB. University")->post("llbUni")->required()->asString(true)->maxLen(250)->default(NULL)->validate();

                $higherEducations["llbUni"] = $registration->universityName;

                $higherEducations["llbResultType"]= $form->label("LL.B Result Type")->post("llbResultType")->required()->asString(true)->maxLen(10)->default(NULL)->validate();
                // $higherEducations["llbSubject"]= $form->label("LL.B Subject")->post("llbSubject")->required()->asString(true)->maxLen(100)->validate();

                //Exam concluded date & Passing Year
                switch (strtolower($higherEducations["llbResultType"])) {
                    case 'division':
                    case 'class':    
                    case 'grading':           
                        $higherEducations["llbExamConcludedDate"] =NULL;
                        $higherEducations["llbPassingYear"] = $form->label("LL.B Passing Year")->post("llbPassingYear")->required()->asInteger(false)->maxLen(4)->default(NULL)->validate();

                        break; //end of division, class, grading switch
                    case "appeared":
                        $higherEducations["llbDivision"] = NULL;
                        $higherEducations["llbClass"] = NULL;
                        $higherEducations["llbCgpa"] = NULL;
                        $higherEducations["llbCgpaScale"]= NULL;
                        $higherEducations["llbPassingYear"] = NULL;
                        $higherEducations["llbTotalMarks"] = NULL;
                        $higherEducations["llbObtainedMarks"] = NULL;
                        $higherEducations["llbMarksPercentage"] = NULL;

                        $llbExamConcludedDate = $form->label("LL.B Exam Concluded Date")->post("llbExamConcludedDate")->required()->asDate()->validate();                    

                        
                        if($llbExamConcludedDate > $applicationEnd){
                            $msg = $json->fail()->message("LL.B Examination Appeared Date Must Be Less Than application end date.")->create();
                            die($msg);
                        }
                        $higherEducations["llbExamConcludedDate"] =$clock->toString($llbExamConcludedDate, DatetimeFormat::MySqlDate());
                        break;
                    default:
                        break;
                }
            
                //Data for total marks, obtained marks & percentage of marks
                switch (strtolower($higherEducations["llbResultType"])) {
                    case 'division':
                    case 'class':    
                        $higherEducations["llbTotalMarks"] = $form->label("LL.B Total Marks")->post("llbTotalMarks")->required()->asInteger(false)->maxLen(4)->validate();
                        $higherEducations["llbObtainedMarks"] = $form->label("LL.B Obtained Marks")->post("llbObtainedMarks")->required()->asInteger(false)->maxLen(4)->validate();

                        if($higherEducations["llbObtainedMarks"] > $higherEducations["llbTotalMarks"]){
                            die($json->fail()->message("LL.B obtained marks must be less than total marks.")->create());
                        }

                        $higherEducations["llbMarksPercentage"] = ($higherEducations["llbObtainedMarks"] / $higherEducations["llbTotalMarks"]) * 100; 
                        break;
                    case 'grading':           
                            $higherEducations["llbTotalMarks"] = NULL;
                            $higherEducations["llbObtainedMarks"] = NULL;
                            $higherEducations["llbMarksPercentage"] = NULL;
                        break; //end of division, class, grading switch
                
                    default:
                        break;
                }
            
                //Division/Class/CGPA
                switch (strtolower($higherEducations["llbResultType"])) {
                    case 'division':
                        $higherEducations["llbDivision"] = $form->label("LL.B Division")->post("llbDivision")->required()->asString(true)->maxLen(20)->validate();
                        break;
                    case 'class':    
                        $higherEducations["llbClass"] = $form->label("LL.B Class")->post("llbClass")->required()->asString(true)->maxLen(20)->validate();
                        break;
                    case 'grading':           
                        $higherEducations["llbCgpa"] = $form->label("LL.B CGPA")->post("llbCgpa")->required()->asFloat(false)->maxLen(4)->validate();
                        $higherEducations["llbCgpaScale"] = $form->label("LL.B CGPA Scale")->post("llbCgpaScale")->required()->asInteger(false)->maxLen(4)->maxVal(10)->validate();
                        if($higherEducations["llbCgpa"] > $higherEducations["llbCgpaScale"]){
                            // die($json->fail()->message("LL.B CGPA must be less than Scale.")->create());
                        }
                        //(cgpa*80)/scale
                        $higherEducations["llbMarksPercentage"] = ($higherEducations["llbCgpa"] * 80)/ $higherEducations["llbCgpaScale"];

                        break;
                    default:
                        break;
                } // //Division/Class/CGPA ends

                $higherEducations["llbCourseDuration"]=  NULL; //Not in form
                $higherEducations["llbGrade"]=  NULL; //Not in form
            #endregion
        
            #region Graduation (Other)
                $hasOtherGrad = $form->label("Has Graduation Other")->post("hasOtherGrad")->optional()->asString(false)->maxLen(12)->default("No")->validate();
                if($hasOtherGrad == "hasOtherGrad"){
                    $higherEducations["hasOtherGrad"] = 1;
                }
                else{
                    $higherEducations["hasOtherGrad"] = 0;
                }

                //If someone has LL.B (Pass), he must have graduation in other discipline.
                if($higherEducations["llbExam"] == "LL.B (Pass)" && $higherEducations["hasOtherGrad"] == 0){
                    die($json->fail()->message("Graduation (Other) information required.")->create());
                }

                if($higherEducations["hasOtherGrad"]){
                    $higherEducations["gradOtherExam"] = $form->label("Graduation (Other) Exam")->post("gradOtherExam")->required()->asString(true)->maxLen(50)->default(NULL)->validate();
                    $higherEducations["gradOtherId"] = $form->label("Graduation (Other) ID/Roll/Regi No.")->post("gradOtherId")->required()->asString(true)->maxLen(20)->default(NULL)->validate();
                
                
                    $gradOtherCountry= $form->label("Graduation (Other) Country")->post("gradOtherCountryName")->required()->asString(true)->maxLen(50)->validate();
                
                    if($gradOtherCountry == "Bangladesh"){
                        $higherEducations["gradOtherCountry"] = "Bangladesh";
                    }
                    else{
                        $higherEducations["gradOtherCountry"] = $form->label("Graduatoin (Other) Country Name")->post("gradOtherOtherCountryName")->required()->asString(true)->maxLen(50)->validate();

                        $hasGradOtherEquivalentCertificate = $form->label("Graduation (Other) Equivalent Certificate")->post("hasGradOtherEquivalentCertificate")->required()->asString(false)->maxLen(3)->validate();
                        if(strtolower($hasGradOtherEquivalentCertificate) != "yes"){
                            die($json->fail()->message("Graduation (Other) Equivalent Certificate of Bar Council is required.")->create());
                        }
                    }
                
                    $higherEducations["gradOtherUni"]= $form->label("Graduation (Other) university name")->post("gradOtherUni")->required()->asString(true)->maxLen(250)->default(NULL)->validate();

                    $higherEducations["gradOtherResultType"] = $form->label("Graduation (Other) result type")->post("gradOtherResultType")->required()->asString(true)->maxLen(10)->validate();

                    // $higherEducations["gradOtherSubject"]= $form->label("Graduation (Other) major subject name(s)")->post("gradOtherSubject")->required()->asString(true)->maxLen(100)->validate();

                //Exam concluded date & Passing Year
                    switch (strtolower($higherEducations["gradOtherResultType"])) {
                        case 'division':
                        case 'class':    
                        case 'grading':           
                            $higherEducations["gradOtherExamConcludedDate"]=NULL;
                            $higherEducations["gradOtherPassingYear"]= $form->label("Graduation (Other) Passing Year")->post("gradOtherPassingYear")->required()->asInteger(false)->maxLen(4)->default(NULL)->validate();

                            break; //end of division, class, grading switch
                        case "appeared":
                            $higherEducations["gradOtherDivision"]= NULL;
                            $higherEducations["gradOtherClass"]= NULL;
                            $higherEducations["gradOtherCgpa"]= NULL;
                            $higherEducations["gradOtherCgpaScal"]= NULL;
                            $higherEducations["gradOtherPassingYear"]= NULL;
                            $higherEducations["gradOtherTotalMarks"]= NULL;
                            $higherEducations["gradOtherObtainedMarks"]= NULL;
                            $higherEducations["gradOtherMarksPercentage"]= NULL;

                            $gradOtherExamConcludedDate = $form->label("Graduation (Other) Exam Concluded Date")->post("gradOtherExamConcludedDate")->required()->asDate()->validate();

                        
                            if($gradOtherExamConcludedDate > $applicationEnd){
                                $msg = $json->fail()->message("Graduation (Other) Examination Appeared Date must be less than application end date")->create();
                                die($msg);
                            }
                            $higherEducations["gradOtherExamConcludedDate"]=
                                $clock->toString($gradOtherExamConcludedDate, DatetimeFormat::MySqlDate());
                            break;
                        default:
                            break;
                    }
                
                    //Data for total marks, obtained marks & percentage of marks
                    switch (strtolower($higherEducations["gradOtherResultType"])) {
                        case 'division':
                        case 'class':    
                            $higherEducations["gradOtherTotalMarks"] = $form->label("Graduation (Other) Total Marks")->post("gradOtherTotalMarks")->required()->asInteger(false)->maxLen(4)->validate();
                            $higherEducations["gradOtherObtainedMarks"] = $form->label("Graduation (Other) Obtained Marks")->post("gradOtherObtainedMarks")->required()->asInteger(false)->maxLen(4)->validate();

                            if($higherEducations["gradOtherObtainedMarks"] > $higherEducations["gradOtherTotalMarks"]){
                                die($json->fail()->message("Graduation (Other) Obtained marks must be less than total marks.")->create());
                            }

                            $higherEducations["gradOtherMarksPercentage"] = number_format(($higherEducations["gradOtherObtainedMarks"] / $higherEducations["gradOtherTotalMarks"]) * 100, 2); 
                            break;
                        case 'grading':           
                                $higherEducations["gradOtherTotalMarks"] = NULL;
                                $higherEducations["gradOtherObtainedMarks"] = NULL;
                                $higherEducations["gradOtherMarksPercentage"] = NULL;
                            break; //end of division, class, grading switch
                    
                        default:
                            break;
                    }
                
                    //Division/Class/CGPA
                    switch (strtolower($higherEducations["gradOtherResultType"])) {
                        case 'division':
                            $higherEducations["gradOtherDivision"] = $form->label("Graduation (Other) Division")->post("gradOtherDivision")->required()->asString(true)->maxLen(20)->validate();
                            break;
                        case 'class':    
                            $higherEducations["gradOtherClass"] = $form->label("Graduation (Other) Class")->post("gradOtherClass")->required()->asString(true)->maxLen(20)->validate();
                            break;
                        case 'grading':           
                            $higherEducations["gradOtherCgpa"] = $form->label("Graduation (Other) CGPA")->post("gradOtherCgpa")->required()->asFloat(false)->maxLen(4)->validate();
                            $higherEducations["gradOtherCgpaScale"] = $form->label("Graduation (Other) CGPA Scale")->post("gradOtherCgpaScale")->required()->asFloat(false)->maxLen(4)->validate();

                            if($higherEducations["gradOtherCgpa"] > $higherEducations["gradOtherCgpaScale"]){
                                die($json->fail("Graduation (Other) CGPA must be less than Scale.")->create());
                            }

                            //(cgpa*80)/scale
                            $higherEducations["gradOtherMarksPercentage"] 
                                    = number_format(($higherEducations["gradOtherCgpa"] * 80)/ $higherEducations["gradOtherCgpaScale"], 2);

                            break;
                        default:
                            break;
                    } // //Division/Class/CGPA ends

                    $higherEducations["gradOtherCourseDuration"]= NULL; //Not in form
                    $higherEducations["gradOtherGrade"] = NULL; //Not in form

                } //if hasOtherGrad = true   
    
            #endregion

            #region Masters
                $hasMasters = $form->label("")->post("hasMasters")->optional()->asString(false)->maxLen(12)->default("")->validate();
                if($hasMasters == "hasMasters"){
                    $higherEducations["hasMasters"]= 1;
                }
                else{
                    $higherEducations["hasMasters"]= 0;
                }
            
                if($higherEducations["hasMasters"]){
                    $higherEducations["mastersExam"] = $form->label("Masters Exam")->post("mastersExam")->required()->asString(true)->maxLen(50)->default(NULL)->validate();
                    $higherEducations["mastersId"] = $form->label("Masters ID/Roll/Regi No.")->post("mastersId")->required()->asString(true)->maxLen(20)->default(NULL)->validate();

                    // $higherEducations["mastersSubject"] = $form->label("Masters Subject")->post("mastersSubject")->required()->asString(true)->maxLen(100)->validate();
                    
                    $mastersCountry = $form->label("Masters Country")->post("mastersCountryName")->required()->asString(true)->maxLen(50)->default(NULL)->validate();
                    if($mastersCountry == "Bangladesh"){
                        $higherEducations["mastersCountry"] = "Bangladesh";
                    }
                    else{
                        $higherEducations["mastersCountry"] = $form->label("Masters Degree Country Name")->post("mastersOtherCountryName")->required()->asString(true)->maxLen(50)->validate();

                        $hasMastersEquivalentCertificate = $form->label("Masters Equivalent Certificate")->post("hasMastersEquivalentCertificate")->required()->asString(false)->maxLen(3)->validate();
                        if($hasMastersEquivalentCertificate == "no"){
                            die($json->fail()->message("Masters Equivalent Certificate of Bar Council is required.")->create());
                        }
                    }

                    $higherEducations["mastersUni"] = $form->label("Masters University")->post("mastersUni")->required()->asString(true)->maxLen(250)->default(NULL)->validate();

                    $higherEducations["mastersResultType"] = $form->label("Masters Result Type")->post("mastersResultType")->required()->asString(true)->maxLen(10)->validate();

                   

                //Exam concluded date & Passing Year
                    switch (strtolower($higherEducations["mastersResultType"])) {
                        case 'division':
                        case 'class':    
                        case 'grading':           
                            $higherEducations["mastersExamConcludedDate"] = NULL;
                            $higherEducations["mastersPassingYear"] = $form->label("Masters Passing Year")->post("mastersPassingYear")->required()->asInteger(false)->maxLen(4)->default(NULL)->validate();

                            break; //end of division, class, grading switch
                        case "appeared":
                            $higherEducations["mastersDivision"]= NULL;
                            $higherEducations["mastersClass"]= NULL;
                            $higherEducations["mastersCgpa"]= NULL;
                            $higherEducations["mastersCgpaScal"]= NULL;
                            $higherEducations["mastersPassingYear"]= NULL;
                            $higherEducations["mastersTotalMarks"]= NULL;
                            $higherEducations["mastersObtainedMarks"]= NULL;
                            $higherEducations["mastersMarksPercentage"]= NULL;

                            $ecd = $form->label("Masters Exam Concluded Date")->post("mastersExamConcludedDate")->required()->asDate()->validate();
                            $higherEducations["mastersExamConcludedDate"] = $clock->toString($ecd, DatetimeFormat::MySqlDate());
                            
                            if($ecd > $applicationEnd){
                                $msg = $json->fail()->message("Masters Examination Appeared Date Must Be Less Than " . $clock->toString($applicationEnd,   DatetimeFormat::BdDate()))->create();
                                die($msg);
                            }

                            break;
                        default:
                            break;
                    }
                
                    //Data for total marks, obtained marks & percentage of marks
                    switch (strtolower($higherEducations["mastersResultType"])) {
                        case 'division':
                        case 'class':    
                            $higherEducations["mastersTotalMarks"] = $form->label("Masters Total Marks")->post("mastersTotalMarks")->required()->asInteger(false)->maxLen(4)->validate();
                            $higherEducations["mastersObtainedMarks"] = $form->label("Masters Obtained Marks")->post("mastersObtainedMarks")->required()->asInteger(false)->maxLen(4)->validate();

                            if($higherEducations["mastersObtainedMarks"] > $higherEducations["mastersTotalMarks"]){
                                $msg =$json->fail()->message("Masters obtained marks must be less than total marks.")->create(); die($msg);
                            }

                            $higherEducations["mastersMarksPercentage"] = ($higherEducations["mastersObtainedMarks"] / $higherEducations["mastersTotalMarks"]) * 100; 
                            break;
                        case 'grading':           
                                $higherEducations["mastersTotalMarks"] = NULL;
                                $higherEducations["mastersObtainedMarks"] = NULL;
                                $higherEducations["mastersMarksPercentage"] = NULL;
                            break; //end of division, class, grading switch
                    
                        default:
                            break;
                    }
                
                    //Division/Class/CGPA
                    switch (strtolower($higherEducations["mastersResultType"])) {
                        case 'division':
                            $higherEducations["mastersDivision"] = $form->label("Masters Division")->post("mastersDivision")->required()->asString(true)->maxLen(20)->validate();
                            break;
                        case 'class':    
                            $higherEducations["mastersClass"] = $form->label("Masters Class")->post("mastersClass")->required()->asString(true)->maxLen(20)->validate();
                            break;
                        case 'grading':           
                            $higherEducations["mastersCgpa"] = $form->label("Masters CGPA")->post("mastersCgpa")->required()->asFloat(false)->maxLen(4)->validate();
                            $higherEducations["mastersCgpaScale"] = $form->label("Masters CGPA Scale")->post("mastersCgpaScale")->required()->asFloat(false)->maxLen(4)->validate();

                            if($higherEducations["mastersCgpa"] > $higherEducations["mastersCgpaScale"]){
                                $msg = $json->fail()->message("Masters CGPA must be less than Scale.")->create(); die($msg);
                            }

                            //(cgpa*80)/scale
                            $higherEducations["mastersMarksPercentage"] 
                                    = ($higherEducations["mastersCgpa"] * 80)/ $higherEducations["mastersCgpaScale"];
                            break;
                        default:
                            break;
                    } // //Division/Class/CGPA ends
                    
                    $higherEducations["mastersCourseDuration"]= NULL; //Not in form
                    $higherEducations["mastersGrade"] = NULL; //Not in form

                } //if has masters      
        
            #endregion
    
            #region Bar-at-law
                if($registration->hasBarAtLaw==1){
                    //required
                    $hasBarAtLaw = $form->label("Bar-at-law information")->post("hasBarAtLaw")->required()->asString(false)->maxLen(15)->validate();
                }
                else{
                    // optional
                    $hasBarAtLaw = $form->label("Bar-at-law information")->post("hasBarAtLaw")->optional()->asString(false)->maxLen(15)->default("")->validate();
                }

                if($hasBarAtLaw == "hasBarAtLaw"){
                    $higherEducations["hasBarAtLaw"] = 1;
                }
                else{
                    $higherEducations["hasBarAtLaw"] = 0;
                }

                if($higherEducations["hasBarAtLaw"]){
                    $higherEducations["barAtLawName"] = $form->label("Name of the Bar of Bar-at-law")->post("barAtLawName")->required()->asString(true)->maxLen(100)->validate();
                    $higherEducations["barAtLawYear"] = $form->label("Year of the Bar-at-law")->post("barAtLawYear")->required()->asNumeric(false)->maxLen(4)->validate();
                }
            #endregion    
        }
        catch (\ValidableException $ve) {
            $msg = $json->fail()->message($ve->getMessage())->create(); die($msg);
        }
        catch (\Exception $exp) {
            $logger->createLog($exp->getMessage());
            $msg = $json->fail()->message("Something is wrong.")->create(); die($msg);
        }
    #endregion

    #region Case history
        if($registration->hasBarAtLaw == 0){
            $arrCaseNumberWithSection = $_POST['caseNumberWithSection'];
            $arrNameOfTheCourt = $_POST['nameOfTheCourt'];
            $arrNameOfTheParties = $_POST['nameOfTheParties'];
            $arrOnBehalfOf = $_POST['onBehalfOf'];
            $arrPresentPosition = $_POST['presentPosition'];
    
            try {
                $caseIndex = 1;
                $caseNo = 1;
    
                $cases = [];
                foreach( $arrCaseNumberWithSection as $key => $n ) {
                    if($caseNo > 5){
                        $caseNo = 1;
                    }
                    if($caseIndex < 6){
                        $caseType = "criminal";
                    }
                    else{
                        $caseType = "civil";
                    }
                    $caseNumberWithSection = $n;
                    $nameOfTheCourt = $arrNameOfTheCourt[$key];
                    $nameOfTheParties = $arrNameOfTheParties[$key];
                    $onBehalfOf = $arrOnBehalfOf[$key];
                    $presentPosition = $arrPresentPosition[$key];
    
                    $case["caseType"] =  $caseType;
                    $case["caseNumberWithSection"] = strtoupper($form->label("Case Number with Section ($caseType case- $caseNo)")->value($caseNumberWithSection)->required()->asString(true)->maxLen(255)->validate());
                    $case["nameOfTheCourt"] = strtoupper($form->label("Name of the Court ($caseType case- $caseNo)")->value($nameOfTheCourt)->required()->asString(true)->maxLen(255)->validate());
                    $case["nameOfTheParties"] = strtoupper($form->label("Name of the Parties ($caseType case- $caseNo)")->value($nameOfTheParties)->required()->asString(true)->maxLen(255)->validate());
                    $case["onBehalfOf"] = strtoupper($form->label("On behalf of ($caseType case- $caseNo)")->value($onBehalfOf)->required()->asString(true)->maxLen(255)->validate());
                    $case["presentPosition"] = strtoupper($form->label("Present Position ($caseType case- $caseNo)")->value($presentPosition)->required()->asString(true)->maxLen(255)->validate());
                    $cases[] = $case;
                    $caseIndex += 1;
                    $caseNo += 1;
                }
            } catch (\ValidableException $th) {
                die($json->fail()->message($th->getMessage())->create());
            }
        }

    #endregion

    #region Pupilage Contract Date validation 
        //NOTE:: Validation has been done in confirm-registration.php. In order to avoid logic duplicacy, the validation is removed from here. 
    #endregion

    #region Fee 
        if(strtolower($registration->applicantType) == "regular"){
            $cinfo["requiredFee"] = $postConfig->regularFeeAmount;
        } else if(strtolower($registration->applicantType) == "re-appeared"){
            $cinfo["requiredFee"] = $postConfig->reappearFeeAmount;
        }
    #endregion

    #region Photo, Signature and Scan copy validation
        try {
            Imaging::validate("ApplicantPhoto", "Applicant Photo" ,300,300,100);
            Imaging::validate("ApplicantSignature","Applicant signature",300,80,100);
          
        } catch (\Exception $exp) {
            $msg =$json->fail()->message($exp->getMessage())->create();
            die($msg);
        }
    #endregion

    #region Save data, save photos, create QR code
        try{
            $cinfo["examYear"]= $postConfig->examYear; 
            $cinfo["referenceNo"]= $postConfig->referenceNo; 
            $cinfo["referenceDate"]= $postConfig->referenceDate; 
            $cinfo["applicationTypeCode"]= $postConfig->applicationTypeCode; 
            $cinfo["sixMonthsPupilageCalculationDate"]= $postConfig->sixMonthsPupilageCalculationDate; 
            $cinfo["fiveYearsPupilageCalculationDate"]= $postConfig->fiveYearsPupilageCalculationDate; 
            $cinfo["appliedDatetime"] = $clock->toString("now", DatetimeFormat::MySqlDatetime());

            $uniqueCode = new UniqueCodeGenerator($db);
            $cinfo["userId"] =  $uniqueCode->generate(8,"userId", "lc_enrolment_cinfo", $postConfig->userIdPrefix); //LE
            $transactionStatus = $db->beginTransaction();
            $cinfo["cinfoId"] = $db->insert($cinfo, "lc_enrolment_cinfo");     //$db->insert($cinfo)->execute();
            $higherEducations["cinfoId"] =  $cinfo["cinfoId"];
            $higherEducations["userId"] =  $cinfo["userId"];
            $db->insert($higherEducations, "lc_enrolment_higher_educations");
           
            if($registration->hasBarAtLaw == 0){
                foreach ($cases as $case) {
                    $case["userId"] = $cinfo["userId"];
                    $case["cinfoId"] =  $cinfo["cinfoId"];
                    $case["submitDatetime"] = $cinfo["appliedDatetime"];
                    $db->insert($case, "lc_case_list");
                }
            }

        
            $photoDirectory = ROOT_DIRECTORY . "/applicant-images/photos";
            if (!file_exists($photoDirectory)) {
                mkdir($photoDirectory, 0777, true);
            }
            $photoPath = $photoDirectory . "/" . $cinfo["userId"] . '.jpg';
            if (file_exists($photoPath)) {
                unlink($photoPath);
            }
            Imaging:: save("ApplicantPhoto", "Applicant's photo" , $photoPath);
        
            $signatureDirectory = ROOT_DIRECTORY . "/applicant-images/signatures";
            if (!file_exists($signatureDirectory)) {
                mkdir($signatureDirectory, 0777, true);
            }
            $signaturePath = $signatureDirectory . "/" . $cinfo["userId"] . '.jpg';
            if (file_exists($signaturePath)) {
                unlink($signaturePath);
            }
            Imaging:: save("ApplicantSignature", "Applicant's signature" ,$signaturePath);
    

            $qrDirectory = ROOT_DIRECTORY . "/applicant-images/qr-codes";
            if (!file_exists($qrDirectory)) {
                mkdir($qrDirectory, 0777, true);
            }
            $qrPath = $qrDirectory . "/" . $cinfo["userId"] . '.png';
            if (file_exists($qrPath)) {
                unlink($qrPath);
            }
                
            require_once(ROOT_DIRECTORY ."/lib/phpqrcode/qrlib.php");
           
            $errorCorrectionLevel = 'L';
            $errorCorrectionLevel ='H'; // array('L','M','Q','H')    
            $matrixPointSize = 4;
            $qrContent = "Regi-$registration->regNo,Year-$registration->regYear";
            QRcode::png($qrContent, $qrPath, $errorCorrectionLevel,  10, 1);  
            // $filename = $PNG_TEMP_DIR. $cinfo->userId .'.png';
            // QRcode::png("forkan", $filename, $errorCorrectionLevel,  10, 1);  

            //QRtools::timeBenchmark();      // benchmark
            //QR Code ends <--

            $db->commit();
            // $db->end stopTransaction();
          
        }
        catch (\Exception $exp) {
            if($db->inTransaction())
                $db->rollBack();

            $logger->createLog($exp->getMessage());
            die($json->fail()->message("Problem in saving data. Please try again.")->create());
        }
  
    #endregion

    $session->setData("cinfoId", $cinfo["cinfoId"]);

    $url = BASE_URL . "/applicant-copy/preview.php?session-id=$encSessionId&config-id=$encConfigId&sm=1";
    exit($json->success()->redirecturl($url)->create());
?>