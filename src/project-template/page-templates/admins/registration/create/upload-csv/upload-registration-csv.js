var inputType = "local";
var stepped = 0, rowCount = 0, errorCount = 0, firstError;
var start, end;
var firstRun = true;
var maxUnparseLength = 10000;

$(function()
{
    var dummyRows = `<tr>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                    </tr>`;

    for (let index = 0; index <5; index++) {
        $("#tbody").append(dummyRows);
    }

    $("#files").change(function(){
        // $("#submit").click();

        stepped = 0;
		rowCount = 0;
		errorCount = 0;
		firstError = undefined;

		var config = buildConfig();
		var input = $('#input').val();

		if (inputType == "remote")
			input = $('#url').val();
		else if (inputType == "json")
			input = $('#json').val();

		// Allow only one parse at a time
		$(this).prop('disabled', true);

		if (!firstRun)
			console.log("--------------------------------------------------");
		else
			firstRun = false;


        if (!$('#files')[0].files.length)
        {
            alert("Please choose at least one file to parse.");
            return enableButton();
        }
        
        $('#files').parse({
            config: config,
            before: function(file, inputElem)
            {
                start = now();
                console.log("Parsing file...", file);
            },
            error: function(err, file)
            {
                console.log("ERROR:", err, file);
                firstError = firstError || err;
                errorCount++;
            },
            complete: function()
            {
                end = now();
                // printStats("Done with all files");
                $("#save").show();
            }
        });
    });


	// $('#insert-tab').click(function()
	// {
	// 	$('#delimiter').val('\t');
	// });


    $('#save').click(function(){
        saveData();

    });
}); //doc.ready ends

function saveData(){
	var submitButton = $("#save");
	var loader = $("#save .button-loader");
	var buttonText = $("#save .button-text");
    var row = $(".unsaved:first");
    if(row.length == 0){
        $(submitButton).removeAttr('disabled');
        $(buttonText).text("Done");
        $(loader).hide();
        return;
    }

    var serial = row.find("td:eq(0)").text();  
    var regNo= row.find("td:eq(1)").text(); 
    var regYear = row.find("td:eq(2)").text(); 
    var name = row.find("td:eq(3)").text(); 
    var father = row.find("td:eq(4)").text(); 
    var universityName = row.find("td:eq(5)").text(); 
    var seniorAdvocateName = row.find("td:eq(6)").text(); 
    var pupilageContractDate = row.find("td:eq(7)").text(); 
    var applicantType = row.find("td:eq(8)").text(); 
    var hasBarAtLaw = row.find("td:eq(9)").text(); 



    var formData = new FormData();
    formData.append("regNo", regNo.trim());
    formData.append("regYear", regYear.trim());
    formData.append("name", name.trim());
    formData.append("fatherName", father.trim());
    formData.append("universityName", universityName.trim());
    formData.append("seniorAdvocateName", seniorAdvocateName.trim());
    formData.append("pupilageContractDate", pupilageContractDate.trim());
    formData.append("applicantType", applicantType.trim());
    formData.append("hasBarAtLaw", hasBarAtLaw.trim());


    $.ajax({
        data:formData,
        contentType: false,
        processData: false,
        type: "POST",
        url: baseUrl + "/admins/registration/create/insert-new-registration.php",
        beforeSend: function(){
            $(submitButton).attr('disabled', 'disabled');
            $(loader).show();
            $(buttonText).text("Saving row " + serial);
        },
        success: function(response){
            var $response;
            try {
                $response = response; // $.parseJSON(response);
            }
            catch(err) {
                console.log(response);
                console.log("Error Code: 12547. Details: " + err);
                $(submitButton).removeAttr('disabled');
                $(buttonText).text("Save");
                $(loader).hide();
                // return false;
            }

            if($response.issuccess){
                if(row.hasClass("failed")){
                    row.removeClass("failed");
                }

                row.addClass("saved").removeClass("unsaved");
                setTimeout(saveData, 100);
            }
            else{
                row.addClass("failed");

                alert($response.message);
                $(loader).hide();
                $(submitButton).removeAttr('disabled', 'disabled');
                $(buttonText).text("Try again").show();
            }
        },
        error: function(jqXHR, textStatus, errorThrown){
            $(submitButton).removeAttr('disabled', 'disabled');
            $(loader).hide();
            $(buttonText).text("Try again").show();
            handleError(jqXHR, textStatus, errorThrown);
        }
    }); //ajax ends

   
}

function handleError(jqXHR, textStatus, errorThrown) {
    var message = "Failed to execute.\n";
    switch (textStatus) {
        case "timeout":
            message += "Operation timeout. Pleasy try again.";
            message += "\n " + jqXHR.statusText + ' (' + jqXHR.status + ')';
            break;
        case "error":
            message += "An error ouccured.";
            message += "\n " + jqXHR.statusText + ' (' + jqXHR.status + ')';
            break;
        case "abort":
            message += "Request aborted.";
            message += "\n " + jqXHR.statusText + ' (' + jqXHR.status + ')';
            break;
        case "parsererror":
            message += "Parser error.";
            message += "\n " + jqXHR.statusText + ' (' + jqXHR.status + ')';
            break;
        default:
            message += "An unexpected error occured.";
            message += "\n " + jqXHR.statusText + ' (' + jqXHR.status + ')';
            break;
    }

    alert(message);

    console.log(jqXHR.status); //statusText: "Not Found"
    console.log(jqXHR.statusText); //statusText: "Not Found"
    console.log(textStatus);
    console.log(errorThrown);
}


// function printStats(msg)
// {
// 	if (msg)
// 		console.log(msg);
// 	console.log("       Time:", (end-start || "(Unknown; your browser does not support the Performance API)"), "ms");
// 	console.log("  Row count:", rowCount);
// 	if (stepped)
// 		console.log("    Stepped:", stepped);
// 	console.log("     Errors:", errorCount);
// 	if (errorCount)
// 		console.log("First error:", firstError);
// }



function buildConfig()
{
	return {
		delimiter: ',', //The delimiting character. Usually comma or tab. Default is comma.
		header: false, //Keys data by field name rather than an array.
		dynamicTyping: false, //Turns numeric data into numbers and true/false into booleans.
		skipEmptyLines: true,  //By default, empty lines are parsed;
		preview: 0,  //If > 0, stops parsing after this many rows.
		step: stepped ? stepFunction : undefined,  //Results are delivered row by row to a step function. Use with large inputs that would crash the browser.
		encoding: 'UTF-8',
		worker: true, //$('#worker').prop('checked'),
		comments: '',  //$('#comments').val(),
		complete: completeFunction, ////Executes after each line in csv
		error: errorFn,
		download: inputType == "remote"
	};
}

function stepFunction(results, parser)
{
	stepped++;
	if (results)
	{
		if (results.data)
			rowCount += results.data.length;
		if (results.errors)
		{
			errorCount += results.errors.length;
			firstError = firstError || results.errors[0];
		}
	}
}

//Executes after each line in csv
function completeFunction(results)
{
	end = now();

	if (results && results.errors)
	{
		if (results.errors)
		{
			errorCount = results.errors.length;
			firstError = results.errors[0];
		}
		if (results.data && results.data.length > 0)
			rowCount = results.data.length;
	}

	// printStats("Parse complete");
	// console.log("    Results:", results);
    var data = results.data;
    // console.log(data);
    var firstRow = data[0];
    var columnCount = firstRow.length;
    if(columnCount != 9){
        alert("Invalid columns quantity");
        return;
    }

    $("#tbody").empty();
    var isFirstRow = true;
    var counter = 1;
    data.forEach(function(value){
        if(isFirstRow){
            isFirstRow = false;
        }
        else{
            var regNo = value[0];
            var regYear = value[1];
            var name = value[2];
            var father = value[3];
            var universityName = value[4];
            var seniorAdvocateName = value[5];
            var pupCntDate = value[6];
            // var regNo = value[1];
            // var regYear = value[2];
            // var name = value[3];
            // var father = value[4];
            // var universityName = value[5];
            // var seniorAdvocateName = value[6];
            // var pupCntDate = value[7];


            //Date Format Here
            pupCntDate = new Date(pupCntDate);
            let yyyy = pupCntDate.getFullYear();
            let mm = pupCntDate.getMonth() + 1; // Months start at 0!
            let dd = pupCntDate.getDate();

            if (dd < 10) dd = '0' + dd;
            if (mm < 10) mm = '0' + mm;

            var pupilageContractDate = dd + '-' + mm + '-' + yyyy;


            var applicantType = value[7];
            //var harLaw = value[8].toUpperCase();

            // Convert 0,1 to Yes,No
            var haslaw = value[8];
            if(haslaw==="0"){
                var hasBarAtLaw="No";
            }else if(haslaw==="1"){
                var hasBarAtLaw="Yes";
            }

            if( typeof regNo != 'undefined' &&
            typeof regYear != 'undefined' &&
            typeof name != 'undefined' &&       
            typeof father != 'undefined' &&
            typeof universityName != 'undefined' &&
            typeof seniorAdvocateName != 'undefined' &&
            typeof pupilageContractDate != 'undefined' &&
            typeof applicantType != 'undefined' &&
            typeof hasBarAtLaw != 'undefined'

            ){
                var html = `<tr class="unsaved">
                                <td>`+ counter +`</td>
                                <td contenteditable="true">`+ regNo +`</td>
                                <td contenteditable="true">`+ regYear +`</td>
                                <td contenteditable="true">` + name +`</td>
                                <td contenteditable="true">` + father +`</td>
                                <td contenteditable="true">` + universityName +`</td>
                                <td contenteditable="true">` + seniorAdvocateName +`</td>
                                <td contenteditable="true">` + pupilageContractDate +`</td>
                                <td contenteditable="true">` + applicantType +`</td>
                                <td contenteditable="true">` + hasBarAtLaw +`</td>
                            </tr>`;
                $("#tbody").append(html);
                counter++;
            }
          
        }
       
    });
	// icky hack
	setTimeout(enableButton, 100);
}

function errorFn(err, file)
{
	end = now();
	console.log("ERROR:", err, file);
	enableButton();
}

function enableButton()
{
	$('#submit').prop('disabled', false);
}

function now()
{
	return typeof window.performance !== 'undefined'
			? window.performance.now()
			: 0;
}