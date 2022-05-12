<?php
    $resultType = $_GET["result-type"]; //Division, Class, Grading, Appeared

    $examName =  $_GET["exam-name"];  //LL.B (Hons), LL.B (Pass), BBA, MBA, etc
    $examTypeName = $_GET["exam-type-name"];
    
    $prefix = "";
    
    switch ($examName) {
        case "LL.B (Hons)":
        case "LL.B (Pass)":
            $prefix = "llb";
            break;

        case "LL.M":
        case "M.B.S":
        case "M.B.A":
        case "M.Sc":
        case "M.Com":
        case "M.S.S":
        case "M.A":
            $prefix = "masters";
            break;
        default:
        $prefix = "gradOther";
            break;
    }

    $examConcludedDateHtml = 
    <<<HTML
         <div class="field">
             <label class="required">Exam Concluded Date</label>
             <input name="{$prefix}ExamConcludedDate" class="validate formControl examConcludedDate" type="text" value="" 
             data-required="required" 
             data-title="{$examTypeName} Examination Concluded Date"  
             data-datatype="date" >
         </div>
    HTML;

    $passingYearHtml = 
    <<<HTML
        <div class="row">
             <div class="col-lg-12 col-sm-12">
                <!-- passing_year -->
                <div class="field">
                    <label class="required">Passing Year</label>
                    <input name="{$prefix}PassingYear" type="text" class="passingYear validate formControl integer" autocomplete="off" maxlength="4" type="text"
                    data-required="required" 
                    data-title="{$examTypeName} Passing Year" 
                    data-datatype="integer">
                </div>
             </div>
         </div>
         
    HTML;

    $marksDetails =
    <<<HTML
         <div class="row">
             <div class="col-lg-6 col-sm-12">
                <!-- total_marks -->
                <div class="field">
                    <label class="required">Total Marks in Curriculum</label>
                    <input name="{$prefix}TotalMarks" class="totalMarks validate formControl integer"  type="text" maxlength="4" 
                        data-required="required"
                         data-title="{$examTypeName} Total marks in curriculum" data-datatype="integer" value="">
                </div>
             </div>
             <div class="col-lg-6 col-sm-12">
                <!-- obtained_marks -->
                <div class="field">
                    <label class="required">Obtained Marks</label>
                    <input name="{$prefix}ObtainedMarks" class="validate formControl obtainedMarks integer" type="text" maxlength="4" value=""  
                    data-required="required" 
                    data-title="{$examTypeName} Obtained Marks" 
                    data-datatype="integer"  >
                </div>
             </div>
         </div>
    HTML;

    $gradingDetailsHtml=
    <<<HTML
        <div class="row">
            <!-- <div class="col-sm-12 col-lg-3">
                <div class="field">
                    <label class="required">Grade</label>
                    <input name="{$prefix}Grade" class="validate formControl" data-required="required" data-title="Result in Grade" type="text" value="">
                </div>
            </div> -->
            <div class="col-sm-12 col-lg-6">
                <!--CGPA -->
                <div class="field">
                    <label class="required">CGPA</label>
                    <input name="{$prefix}Cgpa" class="validate formControl" 
                    data-required="required" data-title="{$examTypeName} Result in CGPA" type="text" data-datatype="float" data-minval="0.01" data-maxval="10.00" value="" maxlength="5">
                </div>
            </div>
            <div class="col-sm-12 col-lg-6">
                <!--CGPA Scale-->
                <div class="field">
                    <label class="required">Scale</label>
                    <select name="{$prefix}CgpaScale" class="validate formControl" 
                    data-required="required" data-title="{$examTypeName} CGPA scale" data-datatype="integer">
                        <option value=""></option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                        <option value="6">6</option>
                        <option value="7">7</option>
                        <option value="8">8</option>
                        <option value="9">9</option>
                        <option value="10">10</option>
                    </select>
                </div>
            </div>
        </div>
    HTML;

    if($resultType == "Division"){
        $resultDetailsHtml = 
        <<<HTML
            <!--if Result in division -->
            <div class="row">
                <div class="col-12">
                    <div class="field">
                        <label class="required">Division</label>
                        <select name="{$prefix}Division" class="validate division formControl" 
                        data-required="required" data-title="{$examTypeName} Result in Division" >
                            <option value=""></option>
                            <option value="1st Division">1st Division</option>
                            <option value="2nd Division">2nd Division</option>
                            <option value="3rd Division">3rd Division</option>
                        </select>
                    </div>
                </div>
            </div>
          
            $marksDetails
            $passingYearHtml
        HTML;
    }
   
    
    if($resultType == "Class"){
        $resultDetailsHtml = 
        <<<HTML
             <!--if Result in class -->
             <div class="row">
                <div class="col-12">
                    <div class="field">
                        <label class="required">Class</label>
                        <select name="{$prefix}Class" class="validate formControl" 
                        data-required="required" data-title="{$examTypeName} Result in Class">
                            <option value=""></option>
                            <option value="1st Class">1st Class</option>
                            <option value="2nd Class">2nd Class</option>
                            <option value="3rd Class">3rd Class</option>
                        </select>
                    </div>
                </div>
             </div>
          
            $marksDetails
            $passingYearHtml
        HTML;
    }

    if($resultType == "Grading"){
        $resultDetailsHtml = 
        <<<HTML
            $gradingDetailsHtml
            $passingYearHtml
        HTML;
    }

    if($resultType == "Appeared"){
        $resultDetailsHtml = 
        <<<HTML
            $examConcludedDateHtml
        HTML;
    }
  
    echo $resultDetailsHtml;
?>



