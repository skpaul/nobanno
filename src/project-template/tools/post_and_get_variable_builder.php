<?php   


    include_once("../CONSTANTS.php");
    require_once(ROOT_DIRECTORY . "/lib/error_reporting.php"); 

    require_once(ROOT_DIRECTORY . "/lib/db_functions_7.php");
   
    $connection= create_database_connection();  
    Select_Database(DATABASE_NAME, $connection);

    //$dbname = "kushtia_bricks";

   
    // Check connection
    if ($connection->connect_error) {
        die("Connection failed: " . $connection->connect_error);
    }

    //check balance of the account creator----   
    
    $sql = "SHOW TABLES FROM " . DATABASE_NAME ;
    $result = $connection->query($sql);
    $tableNames = '<option>Select..</option>';
    while ($row = $result->fetch_array()) {
        $tableNames = $tableNames . '<option value="'. $row[0] .'">'. $row[0] .'</option>' ; //echo "Table: {$row[0]}\n";
    }



    $query = "SELECT * from units";

    if ($result = mysqli_query($connection, $query)) {

        /* Get field information for all columns */
        $finfo = mysqli_fetch_fields($result);

        foreach ($finfo as $val) {
            printf("Name:      %s\n",   $val->name);
            printf("Table:     %s\n",   $val->table);
            printf("Max. Len:  %d\n",   $val->max_length);
            printf("Length:    %d\n",   $val->length);
            printf("charsetnr: %d\n",   $val->charsetnr);
            printf("Flags:     %d\n",   $val->flags);
            printf("Type:      %d\n\n", $val->type);
        }
        mysqli_free_result($result);
    }




    // if ($result = mysqli_query($connection, $query)) {
    
    //     /* Get field information for column 'SurfaceArea' */
    //     // $finfo = mysqli_fetch_field_direct($result, 1);
    
    //     // printf("Name:     %s\n", $finfo->name);
    //     // printf("Table:    %s\n", $finfo->table);
    //     // printf("max. Len: %d\n", $finfo->max_length);
    //     // printf("Flags:    %d\n", $finfo->flags);
    //     // printf("Type:     %d\n", $finfo->type);
    
    //     // while ($fieldinfo=mysqli_fetch_field($result))
    //     // {
    //     //     printf("Name: %s\n",$fieldinfo->name);
    //     //     printf("Table: %s\n",$fieldinfo->table);
    //     //     printf("max. Len: %d\n",$fieldinfo->max_length);
    //     // }
       
    //     var_dump($result->fetch_fields());
    //     $fields = $result->fetch_fields();
    //     echo 'Count ' -
    //     $field_second = $fields[1];
    //     var_dump($field_second->length);
    //     mysqli_free_result($result);
    // }
    $connection->close();
?>


<!DOCTYPE html>
<html>
    <head>
        <title> SQL Builder </title>

        <link href="https://fonts.googleapis.com/css?family=Montserrat|Roboto" rel="stylesheet">

        <style>
            body{
                font-family: 'Roboto', sans-serif;
            }
            h2{
                padding:0;
                margin:0;
                font-size:15px;
                font-family: 'Montserrat', sans-serif;
            }
            .container{
                width:90%; margin:auto;
            }
            #table-names{
                width:80%;
                padding:10px;
                box-sizing:border-box;
            }

            #prepare{
                width:19%;
                padding:10px;
                box-sizing:border-box;
            }

            #variables-container{
                display:flex;
                flex-direction:row;
                margin-top:20px;
            }

            #variables-container>div{
                width:33%;
            }

            #variables-container textarea{
                height:200px;
            }
            th{
                text-align:left;
            }
            table  select{
                width:50px;
                padding:5px 10px;
            }

            table#table{
                border-collapse: collapse;
            }
            #tbody>tr:nth-child(even){
                background-color: #edeeef;
            }
        </style>
    </head>
    <body>
       
        <div class="container"> 
            <select id="table-names"><?php  echo $tableNames; ?></select>  
            <button id="prepare">Prepare</button>
        </div>
       <div id="variables-container" class="container"> 
            <div> 
                <h2>All variables of this table</h2>
                <textarea id="variables" style="width:100%;">
                </textarea>
            </div>
            <div> 
                <h2>All http post variables of this table</h2>
                <textarea id="post-var-textarea" style="width:100%;">
                </textarea>
            </div>        
            <div> 
                <h2>All http get variables of this table</h2>
                    <textarea id="get-var-textarea" style="width:100%;">
                </textarea>
            </div>  
       </div>     

        <div class="container"> 
            <h2>Simple insert statement</h2>
            <textarea id="simple-insert" style="width:99%; height:100px;">
            </textarea>
        </div>  

        <div class="container"> 
            <table id="table" style="width:100%;">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="select-all">
                        </th>
                        <th>
                            Column Name
                        </th>
                        <th>
                            Arguement Type
                        </th>
                    </tr>
                </thead>
                <tbody id="tbody">
                </tbody>
            </table>

            <button id="btn-prepare-statement" style="width:200px; height:40px;">Prepare</button>        
        </div> 
        <div class="container"> 
                <h2>Prepared insert statement</h2>
                <textarea id="prepared-insert" style="width:99%; height:100px;">
                </textarea>
            </div>  
            <div class="container"> 
                <h2>Bind Params</h2>
                <textarea id="bind-param" style="width:99%; height:100px;">
                </textarea>
            </div>  


        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script>
        $(function(){

            var $tableCombo = $('#table-names');
            var columnCombo = $('#column');
            var argType = '<select><option value="i">i</option><option value="d">d</option><option value="s">s</option><option value="b">b</option> </select>';
            $('#prepare').click(function(){
                var tableName = $tableCombo.val();
                var tbody = $('#tbody');
                tbody.empty();
                $.ajax({
                    url: 'get_columns.php?table=' + tableName,
                    success: function(response){
                        console.log(response);
                        var columns = $.parseJSON(response);
                        var variables='';
                        var post_vars='';
                        var get_vars='';

                        
                        
                        var columnNamesForSimpleInsert='';
                        var variableNamesForSimpleInsert='';


                        $.each(columns,function(){
                            //columnCombo.append('<option value="1">'+ this +'</option>');
                            var column= this;
                            variables += '$' + this + '\n';
                            post_vars += '$'+ this + ' = $_POST["'+ this +'"];' + '\n';
                            get_vars += '$'+ this + ' = $_GET["'+ this +'"];' + '\n';
                            columnNamesForSimpleInsert += this + ", ";
                            variableNamesForSimpleInsert += "$" + this + ", " ;

                            var tr = '<tr> \
                                        <td><input type="checkbox"> </td> \
                                        <td>'+ column +'</td> \
                                        <td> '+ argType +'</td> \
                                    </tr>' ; 
                             tbody.append(tr);
                        });

                        $('#variables').val(variables);
                        $('#post-var-textarea').val(post_vars);
                        $('#get-var-textarea').val(get_vars);
                        $('#simple-insert').val("INSERT INTO "+ tableName +" ("+ columnNamesForSimpleInsert +") VALUES("+ variableNamesForSimpleInsert +")");
                    }

                });
            });

            //Start for prepared statement.
            $('#btn-prepare-statement').click(function(){
                var tableName = $tableCombo.val();
                var columnNames=''; var qMarks='';
                var types='';
                    var variables = '';
                $('#tbody > tr').each(function(){
                    var tr = $(this);
                    var checkbox = tr.find("td:first > input[type=checkbox]");
                    
                   
                    if(checkbox.is(':checked')){
                        var columnName = tr.find("td:eq(1)").text();
                        variables += '$' + columnName + ', ';
                        var type = tr.find("td:eq(2) > select").val();
                        types += type;
                        columnNames += columnName + ', ';
                        qMarks += '?, ';
                    }
                });   
                $('#prepared-insert').val('"INSERT INTO '+ tableName +' ('+ columnNames +') VALUES ('+ qMarks +')"');
                $('#bind-param').val('"bind_param("'+ types +'", '+ variables +')"');
            });
        })
        </script>
    </body>
</html>