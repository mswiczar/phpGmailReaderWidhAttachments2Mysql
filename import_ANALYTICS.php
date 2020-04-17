<?
include_once "dbconnections.php";
dbconnect ();

class MyImportANALYTICS
{
    function testHeader($theArrayHeader)
    {
        $arrayHeaderFixed =  array(
                                    'Device Category',
                                    'Country',
                                    'Date',
                                    'Click Out (Goal 2 Completions)',
                                    'Sessions',
                                    'Avg. Session Duration',
                                    'Pages / Session',
                                    'Bounce Rate',
                                    'Users',
                                    'AdX Revenue'
                                   
                                   );
        $row_number=0;
        foreach ($theArrayHeader as $valor)
        {
            $pos = strpos($valor, $arrayHeaderFixed[$row_number]);
            if ($pos === false)
            {
                return false;
            }
            $row_number++;
        }
        return true;
    }


    function delete_from_db ($elarray)
    {
        
        
        $sql ="DELETE FROM IMP_GO_xxxxxxxxx_ANALYTICS  WHERE elDate = STR_TO_DATE('".$elarray[2]."','%Y%m%d')";
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
        print "Borrando Contenido para la Fecha: ".$elarray[2] . "\n";
    }




    function insert_into_db($elarray)
    {
        /*
     
         
         CREATE TABLE `IMP_GO_xxxxxxxxx_ANALYTICS` (
         `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
         `elDeviceCategory` varchar(255) DEFAULT NULL,
         `elCountry` varchar(255) DEFAULT NULL,
         `elDate` date DEFAULT NULL,
         `elClickOutGoal2Completions` int(11) DEFAULT NULL,
         `elSessions` int(11) DEFAULT NULL,
         `elAvgSessionDuration` int(11) DEFAULT NULL,
         `elPagesPerSession` decimal(8,2) DEFAULT NULL,
         `elBounceRate` decimal(8,2) DEFAULT NULL,
         `elUsers` int(11) DEFAULT NULL,
         PRIMARY KEY (`id`)
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
         
        
        */
        
        //print_r($elarray);
        $sql = "";
        $sql = $sql . "INSERT INTO IMP_GO_xxxxxxxxx_ANALYTICS ";
        $sql = $sql . "(";
            $sql = $sql . "elDeviceCategory,";
            $sql = $sql . "elCountry,";
            $sql = $sql . "elDate,";
            $sql = $sql . "elClickOutGoal2Completions,";
            $sql = $sql . "elSessions,";
            $sql = $sql . "elAvgSessionDuration,";
            $sql = $sql . "elPagesPerSession,";
            $sql = $sql . "elBounceRate,";
            $sql = $sql . "elUsers";
        $sql = $sql . ")";
        $sql = $sql . "VALUES";
        $sql = $sql . "(";
            $sql = $sql . "'" . $elarray[0] . "',";  //elDeviceCategory
            $sql = $sql . "'" . $elarray[1] . "',";  //elCountry
            $sql = $sql . " STR_TO_DATE('". $elarray[2]."','%Y%m%d') ,";  //elDate
            $sql = $sql . "'" . str_replace(",","",$elarray[3]) . "',";  //elClickOutGoal2Completions
            $sql = $sql . "'" . str_replace(",","",$elarray[4]) . "',";  //elSessions
            $duration = explode(":",$elarray[5]);
            $tiempo = 0;
            $tiempo = ($duration[0] * 60 * 60) + ($duration[1] * 60) + ($duration[2]);
            $sql = $sql . "'" . $tiempo . "',";  //elAvgSessionDuration
            $sql = $sql . "'" . $elarray[6] . "',";  //elPagesPerSession
            $sql = $sql . "'" . str_replace("%","",$elarray[7]) . "',";  //elBounceRate
            $sql = $sql . "'" . str_replace(",","",$elarray[8]) . "' ";  //elUsers
        $sql = $sql . ")";

        //echo $sql ."<br>";
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    }

    


    function importAnalytics($theFile)
    {

        $file = fopen($theFile, "r");
        if ($file)
        {
            
        }
        else
        {
            print ("Unable to open file: ". $theFile ."\n");
            return false;
            
        }
        
        $row_number=0;
        while(!feof($file))
        {
            $lafila = fgets($file);
            $elarray = str_getcsv($lafila, ",", '"');
            if ($row_number < 6)
            {
            }
            else
            {
                if ($row_number==6)
                {
                    if ($this->testHeader($elarray))
                    {
                        print "El archivo contiene las columans correctas. Se procede a importar\n";
                    }
                    else
                    {
                        print "error Header<br>";
                        return false;
                    }
                }
                else
                {
                    if ($row_number==7)
                    {
                        $this->delete_from_db($elarray);
                    }
                    if (count($elarray)>7)
                    {
                        $this->insert_into_db($elarray);
                    }
                }
            }
            $row_number++;
        }
        fclose($file);
        return true;
    }
}
?>
