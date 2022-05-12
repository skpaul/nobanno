$(document).ready(function(){
    $('#submit').click(function(e){
        e.preventDefault();
        let submitButton = $(this);
        let eiin =$.trim($('input[type="text"][name="eiin"]').val());
        let password =$.trim($('input[type="password"][name="password"]').val());


        if(eiin == ''){
            alert("ই.আই.আই.এন নম্বর লিখুন।");
            $('input[type="text"][name="eiin"]').focus();
            return;
        }
        if(password == ''){
            alert("পাসওয়ার্ড লিখুন।");
            $('input[type="password"][name="password"]').focus();
            return;
        }

        let formData = new FormData();
        formData.append('eiin', eiin);
        formData.append('password', password);
        $.ajax({
            url: 'validate-login.php',
            method: 'post',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function(){
                submitButton.html('WAIT ...');
                submitButton.attr('disabled', 'disabled');
            },
            success:function(response){
                console.log(response);
                if(response.issuccess === undefined){
                    alert('Problem in getting response from server.');
                    submitButton.html('TRY AGAIN');
                    submitButton.removeAttr('disabled');
                    return;
                }
                if(response.issuccess == true){
                    submitButton.html('SUCCESS');
                    $('div#ajax-status').html('please wait while redirecting ...');
                    setTimeout(function(){
                        window.location = response.redirecturl;
                    }, 2000);
                }
                else{
                    submitButton.html('TRY AGAIN');
                    submitButton.removeAttr('disabled');
                    alert(response.message);
                }
            }
        });
    });
});
