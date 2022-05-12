
$(function(){


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
        dateFormat: "dd-mm-yyyy", 
        autoClose:true,
        onSelect: function(formattedDate, date, inst) {
            $(inst.el).trigger('change');
            $(inst.el).removeClass('error');
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

    // function validationRule() {
    //     if(validationFailed){
    //         return false;
    //     }

    //     //other wise return true.
    //     return true;
    // }

    
    
    // $('form').swiftChanger(null); //Or use null

    $('#application-form').swiftSubmit({
        redirect: true
    }, null, null, null, null, null);



}); //Document.ready//