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
        HttpHeader::redirect(BASE_URL . "/admins/sorry.php?msg=Invalid session request.");
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

                .eon.unpaid {
                    background-color: gray;
                }

                .eon.paid {
                    background-color: #18f718;
                }
            </style>
        </header>

        <main class="">
            <div class="container-fluid d-flex flex-wrap">
                <h1 style="width:100%;" class="mt-150 accent-fg">Applications List
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
                                            <label>User ID</label>
                                            <input id="fullName" type="text" class="validate" data-required="optional" placeholder="">
                                        </div>
                                    </div>

                                    <div class="col-lg-2">
                                        <div class="field">
                                            <label>Registration No.</label>
                                            <input id="regNo" type="text" class="validate" data-required="optional" data-datatype="integer">
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


    
                    <table id="meetingData" class="" style="width: 100%;">
                        <thead>
                            <tr>
                                <th class="eonColumn"></th>
                                <th class="serialColumn">No</th>
                                <th class="titleColumn">User ID</th>
                                <th class="titleColumn" title="Click to change order">Reg No</th>
                                <th class="dateColumn" title="Click to change order">Year</th>
                                <th class="startColumn" title="Click to change order">Applicant Name</th>
                                <th class="endColumn" title="Click to change order">Father Name</th>
                                <th title="Click to change order">Required Fee</th>
                                <th title="Click to change order">Fee Paid</th>
                                <th title="Click to change order">Applicant Type</th>
                                <th title="Click to change order">Re-Registration</th>
                                <th title="Click to change order">Bar-At-Law</th>
                                <th title="Click to change order">Applied On</th>
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
        var encSessionId = '<?=$encSessionId?>'
        $(function() {

            // fillDatatable();

            function fillDatatable(userId = '', regNo = '', regYear = '', applicantType = '') {
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
                        "searching": false,
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
                            url: baseUrl + '/admins/application/read/list/application-list-data.php?sid=1',
                            type: "POST",
                            data: {
                                "userId": userId,
                                "regNo": regNo,
                                "regYear": regYear,
                                "applicantType": applicantType
                            }
                            
                        }, //ajax ends

                        // dom: 'Bfrtip',
                        // buttons: [
                        //         {
                        //             extend: 'excelHtml5',
                        //             text:      '<img src="'+ baseUrl +'/assets/DataTables/excel-icon.png" height="32" width="32" alt="Export to Excel" />',
                        //             // text:      '<i class="far fa-file-excel fa-3x"></i>',
                        //             title: 'Registration List',
                        //             exportOptions: {
                        //                 columns: function(column, data, node) {
                        //                     // if (column > countColumns-5) { //exclude all columns greater 15
                        //                     //     return false;
                        //                     // }
                        //                     return true;
                        //                 },
                        //                 modifier: { selected: null }, //make sure all rows show up (not only the filtered ones)

                        //             }
                        //         }
                        //     ],


                        "columnDefs": [{
                                "targets": 0,
                                "name": "eon",
                                // "visible":false,
                                // "searchable":false,
                                "render": function(data, type, row, meta) {
                                    return '<span class="eon ' + data.eon + '"></span>';
                                }
                            },
                            {
                                "targets": 1,
                                "name": "serialNo",
                                "render": function(data, type, row, meta) {
                                    return (meta.row + meta.settings._iDisplayStart + 1) + ".";
                                }
                            },
                            {
                                "targets": 13,
                                "render": function(data, type, row, meta) {
                                    return '<div class="d-flex"><a class="appCopy" data-regno="'+ data.regNo +'" data-regyear="'+ data.regYear +'" data-userid="'+ data.userId +'" href="' + baseUrl + '/admins/registration/read/details/registration-details.php?session-id='+ encSessionId +'&cinfo-id=' + data.cinfoId + '"><i class="material-icons"> info </i></a></div>';
                                }
                            }
                        ], //columnDefs ends

                        "columns": [{
                                "className": 'details-control',
                                "orderable": false,
                                "data": null,
                                "defaultContent": ''
                            },
                            {
                                "className": 'details-control',
                                "orderable": false,
                                "data": null,
                                "defaultContent": ''
                            },
                            {
                                "data": "userId"
                            },
                            {
                                "data": "regNo"
                            },
                            {
                                "data": "regYear"
                            },
                            {
                                "data": "fullName"
                            },
                            {
                                "data": "fatherName"
                            },
                            {
                                "data": "requiredFee"
                            },
                            {
                                "data": "fee"
                            },
                            {
                                "data": "applicantType"
                            },
                            {
                                "data": "isReRegistered"
                            },
                            {
                                "data": "hasBarAtLaw"
                            },
                            {
                                "data": "appliedDatetime"
                            },
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
                var userId = $('#userId').val();
                var regNo = $('#regNo').val();
                var regYear = $('#regYear').val();
                var applicantType = $('#applicantType').val();
                $('#meetingData').DataTable().destroy();
                fillDatatable(userId, regNo, regYear, applicantType);

            });

            $('#filter').click();

            $(".autoComplete").autocomplete({
                source: baseUrl + '/admins/registration/read/list/registration-auto-complete-data.php?id=1',
            });

            $(document).on("click", ".appCopy", function(e){
                e.preventDefault();
                let regNo = $(this).attr("data-regno");
                let regYear = $(this).attr("data-regyear");
                let userId = $(this).attr("data-userid");
                let data = new FormData();
                data.append("regNo", regNo);
                data.append("regYear", regYear);
                data.append("userId", userId);
                $.ajax({
                    url: baseUrl + '/applicant-copy/applicant-copy-process.php',
                    processData:false,
                    contentType:false,
                    type: "POST",
                    data:data,
                    success: function(res){
                        console.log(res);
                        window.open(res.redirecturl, '_blank');
                    }
                });
            });
        })
    </script>
</body>

</html>