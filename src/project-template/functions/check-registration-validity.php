<?php
    /**
     * This function checks the validity and health of regNo, regYear, name, fatherName, seniorAdvocateName, pupilageContractDate and applicantType  column in 
     * lc_enrolment_registrations table.
     * 
     * @return array  array("canProceed"=>$bool, "warning"=>$string);
     */
    function checkRegistrationValidity($registration){
        $canProceedToNext = true;
        $warning = "";
        //Applicant must have a valid registration no.
        if(!isset($registration->regNo) || empty($registration->regNo) || $registration->regNo == 0){
            $canProceedToNext = false;
            $warning .= "Registration number required. ";
        }

        if(!isset($registration->regYear) || empty($registration->regYear) || $registration->regYear == 0){
            $canProceedToNext = false;
            $warning .= "Registration year required. "; //Dont trim the trailing blank space.
        }

        if(!isset($registration->name) || empty($registration->name)){
            $canProceedToNext = false;
            $warning .= "Applicant name required. "; //Dont trim the trailing blank space.
        }

        if(!isset($registration->fatherName) || empty($registration->fatherName)){
            $canProceedToNext = false;
            $warning .= "Applicant's father name required. "; //Dont trim the trailing blank space.
        }

        if(!isset($registration->universityName) || empty($registration->universityName)){
            $canProceedToNext = false;
            $warning .= "University name required. "; //Dont trim the trailing blank space.
        }

        //Senior Advocate name is not required for barristers.
        if($registration->hasBarAtLaw != 1){
            if(!isset($registration->seniorAdvocateName) || empty($registration->seniorAdvocateName)){
                $canProceedToNext = false;
                $warning .= "Senior advocate name required. "; //Dont trim the trailing blank space.
            }
        }

        if(!isset($registration->pupilageContractDate) || empty($registration->pupilageContractDate)){
            $canProceedToNext = false;
            $warning .= "Pupilage contract date required. "; //Dont trim the trailing blank space.
        }

        if(!isset($registration->applicantType) || empty($registration->applicantType)){
            $canProceedToNext = false;
            $warning .= "Applicant type required. "; //Dont trim the trailing blank space.
        }

        return array("canProceed"=>$canProceedToNext, "warning"=>$warning);

    }

?>