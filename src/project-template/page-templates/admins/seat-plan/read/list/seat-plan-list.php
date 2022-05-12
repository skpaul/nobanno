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
            </style>
        </header>

        <main class="">
            <div class="container-fluid d-flex flex-wrap">
                <h1 class="width-full mt-150 accent-fg text-center">Seat Plan List
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
                                            <label>Venue Name</label>
                                            <input name="venue" id="venue" type="text" class="validate autoComplete" data-column="venue" data-required="optional" placeholder="type something to get suggestion">
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="field">
                                            <label>Building Name</label>
                                            <input id="building" type="text" class="validate autoComplete" data-column="building" data-required="optional" data-datatype="text">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="field">
                                            <label>Floor No</label>
                                            <input id="floor" type="text" class="validate autoComplete" data-required="optional" data-column="floor" data-datatype="text">
                                        </div>
                                    </div>
                                    <div class="col-lg-2">
                                        <div class="field">
                                            <label>Room No</label>
                                            <input id="room" type="text" class="validate autoComplete" data-required="optional" data-column="room" data-datatype="integer">
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

                    <table id="seatPlanData" class="" style="width: 100%;">
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

            function fillDatatable(venue = '', building = '', floor = '', room = '') {
                var dataTable =

                    $('#seatPlanData').DataTable({
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
                        "searching": false,
                        "ordering": true,
                        // "info": true,
                        // "autoWidth": false,
                        // "responsive": true,



                        "ajax": {
                            url: baseUrl + '/admins/seat-plan/read/list/seat-plan-list-data.php?sid=1',
                            type: "POST",
                            data: {
                                "venue": venue,
                                "building": building,
                                "floor": floor,
                                "room":room
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
                                    return '<a href="' + baseUrl + '/admins/seat-plan/update/edit-seat-plan.php?session-id=<?=$encSessionId?>&request-id=' + data.id + '"><i class="material-icons"> edit </i></a><a href="' + baseUrl + '/admins/seat-plan/read/details/seat-plan-details.php?session-id=<?=$encSessionId?>&request-id=' + data.id + '"><i class="material-icons"> info </i></a><a href="' + baseUrl + '/admins/seat-plan/delete/delete-seat-plan.php?session-id=<?=$encSessionId?>&delete-id=' + data.id + '"><i class="material-icons"> delete </i></a>';
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
                var venue = $('#venue').val();
                var building = $('#building').val();
                var floor = $('#floor').val();
                var room = $('#room').val();
                $('#seatPlanData').DataTable().destroy();
                fillDatatable(venue, building, floor, room);

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
                                term:request.term
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
</body>

</html>