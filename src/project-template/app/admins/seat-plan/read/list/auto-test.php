<?php

    declare(strict_types=1);

    #region imports
    require_once('../../../../Required.php');

    Required::Logger()
        ->Database()->DbSession()->EnDecryptor()->HttpHeader()->Clock()
        ->adminLeftNav()->headerBrand()->applicantHeaderNav()->footer(2);
    #endregion

    #region declarations
    $logger = new Logger(ROOT_DIRECTORY);
    $db = new Database(DB_SERVER, DB_NAME, DB_USER, DB_PASSWORD);
    $endecryptor = new EnDecryptor();
    #endregion


    $db->connect();
    $db->fetchAsObject();

    #region check session
    if(!isset($_GET["session-id"]) || empty(trim($_GET["session-id"]))){
        HttpHeader::redirect(BASE_URL . "/admins/sorry.php?msg=Invalid session request. Error Code- 41365.");
    }

    $encSessionId = trim($_GET["session-id"]);

    try {
        $sessionId = (int)$endecryptor->decrypt($encSessionId);
        $session = new DbSession($db, "admin_sessions");
        $session->continue($sessionId);
        $roleCode = $session->getData("roleCode");
    } catch (\SessionException $th) {
        // $logger->createLog($th->getMessage());
        HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again. Error Code-456815.");
    } catch (\Exception $exp) {
        HttpHeader::redirect(BASE_URL . "/sorry.php?msg=Invalid session. Please login again. Error Code-774965.");
    }
#endregion



?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Admin || <?= ORGANIZATION_FULL_NAME ?></title>

    <link href="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.theme.min.css" rel="stylesheet">
    <!-- <link href="<?= BASE_URL ?>/assets/DataTables/datatables.min.css" rel="stylesheet"> -->

    <!-- ICON -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

    <script src="<?= BASE_URL ?>/assets/DataTables/plugins/buttons.html5.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/DataTables/plugins/buttons.print.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/DataTables/plugins/dataTables.buttons.min.js"></script>
    <script src="<?= BASE_URL ?>/assets/DataTables/plugins/jszip.min.js"></script>
    <!-- <script src="<?= BASE_URL ?>/assets/DataTables/plugins/pdfmake.min.js"></script> -->
    <script src="<?= BASE_URL ?>/assets/DataTables/plugins/vfs_fonts.js"></script>


    <?php
    Required::metaTags()->favicon()->teletalkCSS(2)->sweetModalCSS()->airDatePickerCSS()->overlayScrollbarCSS();
    ?>


</head>

<body>
    <div class="master-wrapper">
        <header>
            <?php
                echo HeaderBrand::prepare(BASE_URL, true);
                echo ApplicantHeaderNav::prepare(BASE_URL);
            ?>

            <style>
                  .sorting:after,
                    .sorting_asc:after,
                    .sorting_desc:after {
                        content: ' ';
                        display: none !important;
                        opacity: 0.0 !important;
                    }

                .eon {
                    display: inline-block;
                    height: 10px;
                    width: 10px;
                    border-radius: 3px;
                }

                .eon.reapp {
                    background-color: gray;
                }

                .eon.reg {
                    background-color: #18f718;
                }

                .eon.rereg {
                    background-color: #007bff;
                }

                #characterSpan{
    position: absolute;
    visibility: hidden; 
    display: block;
}
#tags{
    width:300px;
}

            </style>
        </header>

        <main class="">
            <div class="container-fluid d-flex flex-wrap">
                <h1 class="width-full mt-150 accent-fg">Seat Plan List
                    <div class="divider"></div>
                </h1>

                <nav class="left-nav">
                    <?php
                    echo AdminLeftNav::CreateFor($roleCode, BASE_URL, $encSessionId);
                    ?>
                </nav>

                <!-- .content starts -->
                <div class="content ">

                    <form class="classic form">
                        <div class="header">Search</div>
                        <div class="row">
                            <div class="col-10">
                                <div class="row">
                                    <div class="col-lg-5">
                                        <div class="field">
                                            <label>Applicant Name</label>
                                            <input name="venue" id="applicantName" type="text" class="validate autoComplete" data-column="venue" data-required="optional" placeholder="type something to get suggestion">
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="field">
                                            <label>Registration No.</label>
                                            <input id="regNo" type="text" class="validate autoComplete" data-column="building" data-required="optional" data-datatype="integer">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="field">
                                            <label>Registration Year</label>
                                            <input id="regYear" type="text" class="validate" data-required="optional" data-datatype="date">
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="field">
                                            <label>Applicant Type</label>
                                            <select id="applicantType" class="validate" data-required="optional" data-datatype="string">
                                                <option value=""></option>
                                                <option value="Regular">Regular</option>
                                                <option value="Re-appeared">Re-appeared</option>
                                                <option value="Re-Registration">Re-Registration</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="row">
                                    <div class="col-lg-2">
                                        <div class="field">
                                            <label>&nbsp;</label>

                                            <input type="submit" class="form-submit-button" value="Search" id="filter">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                    <div class="ui-widget">
  <label for="tags">Tag programming languages: </label>
  <input id="tags" size="50" />
  <span id="characterSpan" style="visibility: hidden;"></span>
</div>

                    <table id="meetingData" class="" style="width: 100%;">
                        <thead>
                            <tr>
                                <!--<th class="eonColumn"></th>-->
                                <th class="serialColumn">No</th>
                                <th class="titleColumn" title="Click to change order">Venue</th>
                                <th class="dateColumn" title="Click to change order">Building</th>
                                <th class="startColumn" title="Click to change order">Floor</th>
                                <th class="endColumn" title="Click to change order">Room</th>
                                <th>Start Roll</th>
                                <th title="Click to change order">End Roll</th>
                                <th title="Click to change order">Total</th>
                                <!--<th title="Click to change order">Applicant Type</th>
                                <th title="Click to change order">Bar-At-Law</th>-->
                                <th class="linkColum"></th>
                            </tr>
                        </thead>

                    </table>


                </div> <!-- .content ends -->

                <!-- <aside style="display: flex; flex-direction: column;">
                    <div class="card fill-parent">
                       
                    </div>
                </aside> -->
            </div>
        </main>

        <footer>
            <?php
            echo Footer::prepare();
            ?>
        </footer>
    </div> <!-- master-wrapper ends-->

    <script>
        var baseUrl = '<?php echo BASE_URL; ?>';
    </script>
    <?php
    Required::hamburgerMenu(2)->adminLeftNavJS()->overlayScrollbarJS()->moment()->swiftSubmit()->SwiftNumeric()->sweetModalJS()->airDatePickerJS();
    ?>


    <!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script> -->
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" /> -->
    <!-- <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap.min.js"></script> -->
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap.min.css" /> -->
    <!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script> -->

    <script src="<?= BASE_URL ?>/assets/js/plugins/jquery-ui/jquery-ui.min.js" ;></script>

    <!-- Our main Script  -->
    <script>
        $(document).ready(function() {

            SwiftNumeric.prepare('.integer');
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
                autoClose: true,
                showOn: "button",
                minView: 'years',
                view: "years",
                onSelect: function(formattedDate, date, inst) {
                    $(inst.el).trigger('change');
                    $(inst.el).removeClass('error');
                }
            })

            $('form').swiftSubmit({}, null, null, null, null, null);

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

            $("input[type=radio]").change(function() {
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
    </script>



    <!-- Script for datatable -->
    <script>
        var baseUrl = '<?php echo BASE_URL; ?>';
        var sid = '1';
        $(function() {

            // fillDatatable();

            function fillDatatable(applicantName = '', regNo = '', regYear = '', applicantType = '') {
                var dataTable =

                    $('#meetingData').DataTable({
                        "processing": true,
                        "serverSide": true,

                        "initComplete": function(settings, json) {
                            $("#filter").removeAttr("disabled");
                            $("#filter").val("Search");
                        },
                        "pageLength": 20,
                        "lengthChange": false,
                        "bPaginate": false,
                        "searching": true,
                        "paging": true,
                        "info": true,


                        "bDestroy": true,
                        // "retrieve": true,
                        // "paging": true,
                        // "lengthChange": false,
                        // "searching": true,
                        "ordering": true,
                        // "info": true,
                        // "autoWidth": false,
                        // "responsive": true,



                        "ajax": {
                            url: baseUrl + '/admins/seat-plan/read/list/seat-plan-list-data.php?sid=1',
                            type: "POST",
                            data: {
                                "applicantName": applicantName,
                                "regNo": regNo,
                                "regYear": regYear,
                                "applicantType": applicantType
                            }
                            
                        }, //ajax ends

                        dom: 'Bfrtip',
                        buttons: [
                                {
                                    extend: 'excelHtml5',
                                    text:      '<img src="'+ baseUrl +'/assets/DataTables/excel-icon.png" height="32" width="32" alt="Export to Excel" />',
                                    // text:      '<i class="far fa-file-excel fa-3x"></i>',
                                    title: 'Seat Plan List',
                                    exportOptions: {
                                        columns: function(column, data, node) {
                                            // if (column > countColumns-5) { //exclude all columns greater 15
                                            //     return false;
                                            // }
                                            return true;
                                        },
                                        modifier: { selected: null }, //make sure all rows show up (not only the filtered ones)

                                    }
                                }
                            ],


                        "columnDefs": [
                            /*{
                                "targets": 0,
                                "name": "eon",
                                // "visible":false,
                                // "searchable":false,
                                "render": function(data, type, row, meta) {
                                    return '<span class="eon ' + data.eon + '"></span>';
                                }
                            },*/
                            {
                                "targets": 0,
                                "name": "serialNo",
                                "render": function(data, type, row, meta) {
                                    return (meta.row + meta.settings._iDisplayStart + 1) + ".";
                                }
                            },
                            {
                                "targets": 8,
                                "render": function(data, type, row, meta) {
                                    return '<a href="' + baseUrl + '/admins/seat-plan/update/edit-seat-plan.php?id=' + data.id + '"><i class="material-icons"> edit </i></a><a href="' + baseUrl + '/admins/seat-plan/read/details/seat-plan-details.php?id=' + data.id + '"><i class="material-icons"> info </i></a><a href="' + baseUrl + '/admins/seat-plan/delete/delete-seat-plan.php?id=' + data.id + '"><i class="material-icons"> delete </i></a>';
                                }
                            }
                        ], //columnDefs ends

                        "columns": [
                            /*{
                                "className": 'details-control',
                                "orderable": false,
                                "data": null,
                                "defaultContent": ''
                            },*/
                            {
                                "className": 'details-control',
                                "orderable": false,
                                "data": null,
                                "defaultContent": ''
                            },
                            {
                                "data": "venue"
                            },
                            {
                                "data": "building"
                            },
                            {
                                "data": "floor"
                            },
                            {
                                "data": "room"
                            },
                            {

                                "data": "start_roll",
                            },
                            {
                                "data": "end_roll"
                            },
                            {
                                "data": "total"
                            },
                            /*{
                                "data": "applicantType"
                            },
                            {
                                "data": "hasBarAtLaw"
                            },*/

                            {
                                "data": null,
                            }

                        ] //columns ends
                    }); //datatable ends
            }


            $('#filter').click(function(e) {
                e.preventDefault();
                $(this).attr("disabled", "disabled");
                $(this).val("loading ..");
                var applicantName = $('#applicantName').val();
                var regNo = $('#regNo').val();
                var regYear = $('#regYear').val();
                var applicantType = $('#applicantType').val();
                $('#meetingData').DataTable().destroy();
                fillDatatable(applicantName, regNo, regYear, applicantType);

            });


            $('#filter').click();

            $(".autoComplete").autocomplete({
                minLength:1,   
                delay:100,  
                source: function(request, response){
                    let columnName = $(this.element).data('column');
                    let url = baseUrl + '/admins/seat-plan/read/list/seat-plan-auto-complete-data.php?column=' + columnName;
                    $.get(  url,  
                            {
                                typedValue:request.term
                            }, 
                            function(data){
                                response(data);
                            },
                            'json'
                        );
                },
                select: function(event, ui) {
                    //NOTE: USE THIS FUNCTION IF WANT TO CUSTOMIZE THE RESULTING DROPDOWN.
                    //console.log(event.target.name);
                    //console.log($(this).prop('name'));
                    //     
                } 
            });






            // $(".simple").autocomplete({
            //     // minLength:2,   
            //     // delay:500,   
            //     source: baseUrl + '/admins/seat-plan/list/auto-complete-venue-name.php?column=venue',
            //     select: function(event, ui) {
            //         console.log(event.target.name);
            //         console.log($(this).prop('name'));
            //     } 
            //     // source: baseUrl + '/autocompletes/auto-complete-applicant-name.php?columnName=' + $(this).attr('data-column'),
            // });

       


            /*function onSuccess(response) {
                //console.log(response);
                $response = $.parseJSON(response);
                var items = $.parseJSON($response.data);
                var length = items.length;
                var tbody = $("tbody");
                tbody.empty();
                alert(length);
                $.each(items, function(index, item) {
                    var url = baseUrl + "/meeting-details.php?id=" + item.meetingId;
                    var html =
                        `
                        <tr>
                            <td>` + (++index) + `</td>
                            <td>` + item.title + `</td>
                            <td>` + item.meetingDate + `<br>
                                asdf, asdf
                            </td>
                            <td>Meeting Room</td>
                            <td><a href="` + url + `">details</a></td>
                        </tr>
                    `;
                    tbody.append(html);
                })
            }*/


        })
    </script>


        <script>
$(function() {
    var availableTags = [
      "ActionScript",
      "AppleScript",
      "Asp",
      "BASIC",
      "C",
      "C++",
      "Clojure",
      "COBOL",
      "ColdFusion",
      "Erlang",
      "Fortran",
      "Groovy",
      "Haskell",
      "Java",
      "JavaScript",
      "Lisp",
      "Perl",
      "PHP",
      "Python",
      "Ruby",
      "Scala",
      "Scheme",
      "This",
      "is",
      "my",
      "long",
      "version"
    ];
    function split( val ) {
      return val.split( / \s*/ );
    }
    function extractLast( term ) {
      return split( term ).pop();
    }
 
    $( "#tags" )
      .bind( "keydown", function( event ) {
          //can also use this to track when user presses SPACE
        if(event.which===32)
               $('#characterSpan').html($(this).val());
        
        if ( event.keyCode === $.ui.keyCode.TAB &&
            $( this ).data( "autocomplete" ).menu.active ) {
          event.preventDefault();
        }
      })
      .autocomplete({
        minLength: 0,
        //source: baseUrl + '/admins/registration/read/list/registration-auto-complete-data.php?id=1',
        source:  baseUrl + '/admins/seat-plan/read/list/seat-plan-auto-complete-data.php?column=room',
        
        // source: function( request, response ) {
        //   response( $.ui.autocomplete.filter(
        //     availableTags, extractLast( request.term ) ) );
        // },
        focus: function() {
          return false;
        },
        select: function( event, ui ) {
          var terms = split( this.value );
          terms.pop();
          terms.push( ui.item.value );
          terms.push( "" );
          this.value = terms.join( " " );
          //when item selected, add current value to span
          $('#characterSpan').html($(this).val());
          return false;
        },
        open: function( event, ui ) {
            var span = $('#characterSpan');
            var width = span.width();
            width > $('#tags').width() ? 
            width = parseInt($('#tags').position().left + $('#tags').width()) : 
            width = parseInt($('#tags').position().left + width);
            
            $('.ui-autocomplete.ui-menu').css('left', width + 'px');
        }
      });
  });
	
        </script>
</body>

</html>