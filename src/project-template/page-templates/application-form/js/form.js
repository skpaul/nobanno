function ValidatePhoto(fileInputId, maximumKB, requiredHeight, requiredWidth){
        
    // debugger;
    var fileName = $("#"+ fileInputId +"").val();

    var title = $("#"+ fileInputId +"").attr("title");

    if(fileName =='')
    {
        $.sweetModal({
            content:  title + " required.",
            icon: $.sweetModal.ICON_WARNING
        });
        //showPhotoError('Please select a photo.');
        return false;
    }

    var fileInput = $("#"+ fileInputId + "")[0];
    var selectedFile = fileInput.files[0];
    
    var regex = /^([a-zA-Z0-9\s_\\.\-:])+(.jpeg|.jpg)$/;

    var arrFileName = fileName.split("\\");

    var fileNameee = arrFileName[arrFileName.length-1]; 
    //fileNameSpan.html(arrFileName[arrFileName.length-1]);

    //check whether it is .jpeg or .jpg ---->
    if (!regex.test(fileName.toLowerCase())) {
        $.sweetModal({
            content: title + " invalid. Please select a .jpg file.",
            icon: $.sweetModal.ICON_WARNING
        });
       // showPhotoError('Please select a .jpg file.');
       return false;
    }
    //<---- check whether it is .jpeg or .jpg

    var fileSizeInByte = selectedFile.size;
    var Units = new Array('Bytes', 'KB', 'MB', 'GB');
    var unitPosition = 0;
    while (fileSizeInByte > 900) {
        fileSizeInByte /= 1024; unitPosition++;
    }

    var finalSize = (Math.round(fileSizeInByte * 100) / 100);
    var finalUnitName = Units[unitPosition];

    var fileSizeAndUnit = finalSize + ' ' + finalUnitName;

    //Check file size ----->
    if (finalUnitName != 'KB') {
        $.sweetModal({
            content: title + " size is too large. Maximum size is 100 kilobytes.",
            icon: $.sweetModal.ICON_WARNING
        });
       // showPhotoError('Photo size is too large. Maximum size is 100 kilobytes.');              
       return false;
    }
    else{
        if(finalSize > maximumKB){ 
            $.sweetModal({
                content: title + " size is too large. Maximum size is 100 kilobytes.",
                icon: $.sweetModal.ICON_WARNING
            });
           // showPhotoError('Photo size is too large. Maximum size is 100 kilobytes.');
           return false;
        }
    }

    /*Checks whether the browser supports HTML5*/
    if (typeof (FileReader) != "undefined") {
        var reader = new FileReader();
        //Read the contents of Image File.
        reader.readAsDataURL(fileInput.files[0]);

        reader.onload = function (e) {
            //Initiate the JavaScript Image object.
            var image = new Image();
            //Set the Base64 string return from FileReader as source.
            image.src = e.target.result;
           
            image.onload = function () {  
                if (this.width != requiredWidth) {
                    $.sweetModal({
                        content: title + " width invalid. Width must be " + requiredWidth + " pixel.",
                        icon: $.sweetModal.ICON_WARNING
                    });
                   // showPhotoError('Invalid photo width. Width must be 300 pixel.');
                   return false;
                }                 
                if (this.height != requiredHeight) {
                    $.sweetModal({
                        content: title + " height invalid. Height must be "+ requiredHeight  + " pixel.",
                        icon: $.sweetModal.ICON_WARNING
                    });
                    //showPhotoError('Invalid photo height. Height must be 300 pixel.');
                    return false;
                }
            };
        }
    }

    return true;
}


$(function(){

    $('.district-combo').change(function(){

        var districtCombo = $(this);
        districtCombo.attr('disabled','disabled');
        var districtType = $(this).attr('data-districttype'); //districtType = "Present" or "Permanent"
        var thanaCombo = $('#' + districtType + 'Thana');
        thanaCombo.empty();
        var selectedDistrict = districtCombo.val();
      

        $.ajax({
            url: baseUrl + '/application-form/get-thanas.php?district=' + selectedDistrict,
            type: "GET",
            success:function(response){
               // console.log(response);
                var $response = $.parseJSON(response);
                var thanas = $response.data;
                thanaCombo.append('<option value="" selected>select thana</option>');
                $.each(thanas, function(){
                    thanaCombo.append('<option value="' + this.thana_name + '">' + this.thana_name + '</option>');
                })
            },
            complete:function(){
                districtCombo.removeAttr('disabled');
            }
        });
    });
    
    $(".educationDetailsToggle").change(function(e){
        if($(this).is(":checked")){
            $(this).closest("article").find(".toggleVisibleWrapper").removeClass("hidden");
        }
        else{
            // $(this).closest("article").find(".toggleVisibleWrapper").hide();
            $(this).closest("article").find(".toggleVisibleWrapper").addClass("hidden");
        }
    });

    //higherEducationResultType
    $(".higherEducationResultType").change(function(){
        var combo  = $(this);
        var resultType = combo.val();
        var container = combo.closest("article").find(".dynamicContent");
        if(resultType == ""){
            container.empty();
            return;
        }

        var examName = combo.closest("article").find(".examName").val();
        if(examName == ""){
            combo.val('');
            alert("Select an examination"); return;
        }
        //data-exam-type-name
        let examTypeName = combo.attr('data-exam-type-name');
        var url = baseUrl + '/dynamic-content/higher-education-markups.php?result-type='+ resultType +'&exam-name=' + examName + '&exam-type-name=' + examTypeName;
        $.ajax({
            type: "GET",
            url: url,
            dataType: "html",
            success: function(response) {
                // console.log(response);
                container.empty().html(response);
                // $('input.passingYear').datepicker({
                combo.closest("article").find('input.passingYear').datepicker({
                    language: 'en',
                    dateFormat: "yyyy", 
                    autoClose:true,
                    showOn: "button",
                    minView: 'years',
                    view:"years",
                    onSelect: function(formattedDate, date, inst) {
                        $(inst.el).trigger('change');
                        $(inst.el).removeClass('error');
                    }
                });
                

                SwiftNumeric.prepare('.integer');

                // $("input.passingYear, input.totalMarks, input.obtainedMarks").swiftNumericInput({ allowFloat: false, allowNegative: false });

                $("input[type=text]").on('propertychange change keyup paste input', function() {
                    $(this).removeClass("error");
                });
            
                $("select").on('change propertychange paste', function() {
                    $(this).removeClass("error");
                });
                
                $('input.examConcludedDate').datepicker({
                    language: 'en',
                    dateFormat: "dd-mm-yyyy", 
                    autoClose:true,
                    onSelect: function(formattedDate, date, inst) {
                        $(inst.el).trigger('change');
                        $(inst.el).removeClass('error');
                    }
                });

            },
            error: function(a,b,c){
                alert("Failed to load data from server. Please try again.");
                combo.val('');
            }
        });
    });


    $("input.country" ).autocomplete({
        // source:  baseUrl + 'autocompletes/board-university-institutes.php?extra=asdf'
        source: function(request, response) {
            var src= baseUrl + '/autocompletes/countries.php?extra=asdf';
            //var $this = this.element;
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term : request.term,
                },
                success: function(data) {
                    response(data);
                }
            });
        } //source ends
    });

    $("input.suggestSubject" ).autocomplete({
        // source:  baseUrl + 'autocompletes/board-university-institutes.php?extra=asdf'
        source: function(request, response) {
            var src= baseUrl + '/autocompletes/subjects.php?extra=asdf';
            //var $this = this.element;
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term : request.term,
                },
                success: function(data) {
                    response(data);
                }
            });
        } //source ends
    });

    $("input.universityName" ).autocomplete({
        // source:  baseUrl + 'autocompletes/board-university-institutes.php?extra=asdf'
        source: function(request, response) {
            var src= baseUrl + '/autocompletes/board-university-institutes.php?extra=asdf';
            //var $this = this.element;
            //var id = $this.closest(".dynamic-education-container").find("input.idRollRegi").val();
            $.ajax({
                url: src,
                dataType: "json",
                data: {
                    term : request.term,
                },
                success: function(data) {
                    response(data);
                    //passingYear
                }
            });
        } //source ends
    });


    //
    $('select[name="sscExamName"]').change(function(e){
        var selectedValue = $(this).val();
        if(selectedValue=="O Level"){
            $("div.sscDetails").addClass("hidden");
            $("div.oLevelDetails").removeClass("hidden");
        }
        else{
            $("div.sscDetails").removeClass("hidden");
            $("div.oLevelDetails").addClass("hidden");
        }
    });

    $(".sscResultType").change(function(e){
        var selectedValue = $(this).val();
        if(selectedValue=="Division"){
            $(".sscDivisionDetails").removeClass("hidden");
            $(".sscGradeDetails").addClass("hidden");
        }
        else if(selectedValue=="Grade"){
            $(".sscDivisionDetails").addClass("hidden");
            $(".sscGradeDetails").removeClass("hidden");
        }
        else{
            $(".sscDivisionDetails").addClass("hidden");
            $(".sscGradeDetails").addClass("hidden");
        }
    });

    $('select[name="hscExamName"]').change(function(e){
        var selectedValue = $(this).val();
        if(selectedValue=="A Level"){
            $("div.hscDetails").addClass("hidden");
            $("div.aLevelDetails").removeClass("hidden");
        }
        else{
            $("div.hscDetails").removeClass("hidden");
            $("div.aLevelDetails").addClass("hidden");
        }
    });

    $(".hscResultType").change(function(e){
        var selectedValue = $(this).val();
        if(selectedValue=="Division"){
            $(".hscDivisionDetails").removeClass("hidden");
            $(".hscGradeDetails").addClass("hidden");
        }
        else if(selectedValue=="Grade"){
            $(".hscDivisionDetails").addClass("hidden");
            $(".hscGradeDetails").removeClass("hidden");
        }
        else{
            $(".hscDivisionDetails").addClass("hidden");
            $(".hscGradeDetails").addClass("hidden");
        }
    });

    //remove red border
    //propertychange change keyup paste input
    $("input[type=text]").on('propertychange change keyup paste input', function() {
        $(this).removeClass("error");
    });

    $("select").on('change propertychange paste', function() {
        $(this).removeClass("error");
    });

    $("textarea").on('input propertychange paste', function() {
        $(this).removeClass("error");
    });

    $("input[type=radio]").change(function(){
        $(this).closest("div.radio-group").removeClass("error");
    });

    SwiftNumeric.prepare('.swiftInteger');
    SwiftNumeric.prepare('.swiftFloat');

    $('.swiftDate').datepicker({
        language: 'en',
        dateFormat: 'dd-mm-yyyy',
        autoClose: true,
        onSelect: function(formattedDate, date, inst) {
            $(inst.el).trigger('change');
            $(inst.el).removeClass('error');
        }
    })


    // $('.swift-date').datepicker({
    //     language: 'en',
    //     dateFormat: 'dd-mm-yyyy',
    //     autoClose: true,
    //     onSelect: function(formattedDate, date, inst) {
    //         $(inst.el).trigger('change');
    //         $(inst.el).removeClass('error');
    //     }
    // })

    //Allow user to select only year from datepicker
    $('.swiftYear').datepicker({
        language: 'en',
        dateFormat: "yyyy", 
        autoClose:true,
        showOn: "button",
        minView: 'years',
        view:"years",
        onSelect: function(formattedDate, date, inst) {
            $(inst.el).trigger('change');
            $(inst.el).removeClass('error');
        }
    })


    // var m = moment("29/02/2004", "DD-MM-YYYY");
    // //alert(m.isValid());
    
    // var a = moment("29/12/2004", "DD-MM-YYYY");
    // var b = moment("27/12/2004", "DD-MM-YYYY");
    
    // var diffDuration = moment.duration(a.diff(b));
    
    // alert(diffDuration.years()); // 8 years
    // alert(diffDuration.months()); // 5 months
    // alert(diffDuration.days()); // 2 days

    $("#ApplicantPhoto").change(function(){
        var isValid = ValidatePhoto("ApplicantPhoto", 100, 300,300);
        if(isValid){
            var fileInput = this;
            if (fileInput.files && fileInput.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    //$('#photo-preview').attr('src', e.target.result);
                   $('#ApplicantPhotoImage').attr('src', e.target.result);
                }
                reader.readAsDataURL(fileInput.files[0]);
            }
        }
    });

    $("#ApplicantSignature").change(function(){
        var fileInput = this;
        if (fileInput.files && fileInput.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                //$('#photo-preview').attr('src', e.target.result);
               $('#ApplicantSignatureImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(fileInput.files[0]);
            ValidatePhoto("ApplicantSignature", 100,80,300);
        }
    });

    // //PresentThanaCode combo chane handler starts------->
    // $('#PresentThanaCode').change(function(){
    //     $('#PresentThanaName').val($("#PresentThanaCode option:selected").text());
    // });
    // //<----- PresentThanaCode combo chane handler starts

    $('.obtained-marks').on('input',function(e){
        var obtainedMarks =$.trim($(this).val());
        if(obtainedMarks == ''){
            $('#' + degreeName + '_percentage_of_marks').val('');
            return;
        }

        if(isNaN(obtainedMarks)) return;
        obtainedMarks = parseInt(obtainedMarks);

        var degreeName = $(this).attr('data-marks-degree-name');
        var totalMarks = parseInt($('#' + degreeName + '_total_marks').val());

        var percentage_of_marks = (obtainedMarks*100)/totalMarks ;
    
        var percentage_of_marks_textbox = $('#' + degreeName + '_percentage_of_marks');
       
        percentage_of_marks = parseFloat(percentage_of_marks).toFixed(2)
        $('#' + degreeName + '_percentage_of_marks').val(percentage_of_marks);

    });

    // var isChecked =  $("input:radio[name='"+inputName+"']").is(":checked");

    $("input:radio[name='isEngaged']").change(function(){
        var selectedValue =  $(this).val();
        if(selectedValue.toLowerCase() == 'yes'){     
            $("input[name='engagementNature']").closest('.field').removeClass('hidden');        
            $("input[name='engagementPlace']").closest('.field').removeClass('hidden');        
        } else {
            $("input[name='engagementNature']").val('').closest('.field').addClass('hidden');        
            $("input[name='engagementPlace']").val('').closest('.field').addClass('hidden');        
        }
    });

    //isDismissed
    $("input[name=isDismissed]").change(function(e){
        let selectedValue = $(this).val();
        if(selectedValue.toLowerCase() == 'yes'){     
            $("input[name='dismissalDate']").closest('.field').removeClass('hidden');        
            $("input[name='dismissalReason']").closest('.field').removeClass('hidden');        
        } else {
            $("input[name='dismissalDate']").val('').closest('.field').addClass('hidden');        
            $("input[name='dismissalReason']").val('').closest('.field').addClass('hidden');        
        }
    });


    $("input:radio[name='isConvicted']").change(function(){
        var selectedValue =  $(this).val();
        if(selectedValue.toLowerCase() == 'yes'){     
            $("input[name='convictionDate']").closest('.field').removeClass('hidden');        
            $("input[name='convictionParticulars']").closest('.field').removeClass('hidden');        
        } else {
            $("input[name='convictionDate']").val('').closest('.field').addClass('hidden');        
            $("input[name='convictionParticulars']").val('').closest('.field').addClass('hidden');        
        }
    });

    // $("input:radio[name='degreeObtainedFrom']").change(function(){
    $(".degreeObtainedFrom").change(function(){
        var selectedValue =  $(this).val();
    //    console.log($(this).closest("article").html());

        if(selectedValue=="Other"){
            $(this).closest("article").find(".otherCountryName").removeClass("hidden");
            $(this).closest("article").find(".hasEquivalentCertificate").removeClass("hidden");
        }
        else{
            $(this).closest("article").find(".otherCountryName").addClass("hidden");
            $(this).closest("article").find(".hasEquivalentCertificate").addClass("hidden");
        }
    });



    function validationRule() {
        if ($("input[name=mobileNo]").val() != $("input[name=reMobileNo]").val()) {
            $.sweetModal({
                content: 'Mobile No. did not match.',
                icon: $.sweetModal.ICON_WARNING
            });
            $("input[name=mobileNo]").addClass('error');
            $("input[name=reMobileNo]").addClass('error');

            return false;
        }

        // if (isNewApplicant == "yes") {
        //     if (!ValidatePhoto("ApplicantPhoto", 100, 300, 300)) {
        //         return false;
        //     }

        //     if (!ValidatePhoto("ApplicantSignature", 100, 80, 300)) {
        //         return false;
        //     }
        // }

        var checked = $('#DeclarationApproval').is(':checked');
        if (!checked) {
            $.sweetModal({
                content: 'Please provide your consent in the declaration section.',
                icon: $.sweetModal.ICON_WARNING
            });
            return false;
        }
        return true;
    }
    
    $('#application-form').swiftChanger(null); //Or use null

    $('#application-form').swiftSubmit({
        redirect: true
    }, validationRule, null, null, null, null);

}); //Document.ready//