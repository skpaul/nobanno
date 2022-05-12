$(document).ready(function() {

    // SwiftNumeric.prepare('.integer');

    SwiftNumeric.prepare('.integer');
    function checkConfirm(){
        var checkStatus = true;
        var check=$("#chkApprove").is(':checked');
        if(check){
            checkStatus = true;
        }else{
            alert("Please Confirm Checkbox.");
            checkStatus = false;
        }
        console.log(check);
        console.log(checkStatus);
        return checkStatus;
    }
   $('form').swiftSubmit({},checkConfirm, null, null, null, null);
    $('.date').datepicker({
        language: 'en',
        dateFormat: 'dd-mm-yyyy',
        autoClose: true,
        onSelect: function(formattedDate, date, inst) {
            $(inst.el).trigger('change');
            $(inst.el).removeClass('error');
        }
    })

    //Allow user to select only year from datepicker
    $('.year').datepicker({
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

    $('form').swiftSubmit({},null, null, null, null, null);
    
    //remove red border -->
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
    //<-- remove red border

    $('.overlayScroll').overlayScrollbars({
        className: 'os-theme-round-light',
        scrollbars: {
            visibility: "auto",
            autoHide: 'leave',
            autoHideDelay: 100
        }
    });
});
