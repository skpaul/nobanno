<?php
    class CSV{
        public static function getCSV($sql, $server, $user, $password, $database){
        
            $connection = mysqli_connect($server, $user, $password, $database); 

            mysqli_set_charset( $connection, 'utf8');

            $query = mysqli_query($connection, $sql);
            $export =  $query;
        
            
            //$fields = mysql_num_fields ( $export );
            $fields = mysqli_num_fields($export);

            $header ='';
            $data = '';
            for ( $i = 0; $i < $fields; $i++ )
            {
                $colObj = mysqli_fetch_field_direct($export,$i);                            
                $col = $colObj->name;

                $header .= $col . ",";
                while( $row = mysqli_fetch_row( $export ) )
                {
                    $line = '';
                    foreach( $row as $value )
                    {                                            
                        if ( ( !isset( $value ) ) || ( $value == "" ) )
                        {
                            $value = "\"\",";
                        }
                        else
                        {
                            $value = str_replace( '"' , '""' , $value );
                            $value = '"' . $value . '"' . ",";
                        }
                        $line .= $value;
                    }
                    $data .= trim( $line ) . "\n";
                }
            }
        
            $data = str_replace( "\r" , "" , $data );
            
            if ( $data == "" )
            {
                $data = "\n(0) Records Found!\n";                        
            }
            
            return "$header\n$data";
            
            //USAGE--------------------
            // $sql = "select * from hc_written_cinfo";
            // $csv = $db->getCSV($sql);
            // header("Content-type: application/octet-stream");
            // header("Content-Disposition: attachment; filename=your_desired_name.csv");
            // header("Pragma: no-cache");
            // header("Expires: 0");
            // print $csv;
        }

        public function query_to_csv($db_conn, $query, $filename, $attachment = false, $headers = true) {
        
            if($attachment) {
                // send response headers to the browser
                header( 'Content-Type: text/csv' );
                header( 'Content-Disposition: attachment;filename='.$filename);

                $fp = fopen('php://output', 'w');
            } else {
                $fp = fopen($filename, 'w');
            }
        
            $result = mysqli_query($db_conn, $query) or die( mysqli_error( $db_conn ) );
        
            if($headers) {
                // output header row (if at least one row exists)
                $row = mysqli_fetch_assoc($result);
                if($row) {
                    fputcsv($fp, array_keys($row));
                    // reset pointer back to beginning
                    mysqli_data_seek($result, 0);
                }
            }
        
            while($row = mysqli_fetch_assoc($result)) {
                fputcsv($fp, $row);
            }
        
            fclose($fp);
        }
    #endregion

    }

?>