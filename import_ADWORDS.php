<?
include_once "dbconnections.php";
dbconnect ();

class MyImportADWORDS
{


    function testHeader($theArrayHeader)
    {
        
        //print_r($theArrayHeader);

        $arrayHeaderFixed =  array(
                                    'Campaign / Campaign ID',
                                    'Device Category',
                                    'Clicks',
                                    'Cost',
                                    'CPC',
                                    'Sessions',
                                    'Bounce Rate',
                                    'Pages / Session',
                                    'Click Out (Goal 2 Conversion Rate)',
                                    'Click Out (Goal 2 Completions)',
                                    'Click Out (Goal 2 Value)'
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


    function delete_from_db ($elDate)
    {
        
        
        $sql ="DELETE FROM IMP_GO_xxxxxxxxx_ADWORDS  WHERE elDay ='".$elDate."'";
        
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
        print "Borrando Contenido para la Fecha: ".$elDate . "\n";
    }




    function insert_into_db($elarray,$eldate)
    {
        /*
         CREATE TABLE `IMP_GO_xxxxxxxxx_ADWORDS` (
         `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
         `elDay` date DEFAULT NULL,
         `elCampaign` varchar(255) DEFAULT NULL,
         `elCountryTerritory` varchar(255) DEFAULT NULL,
         `elDevice` varchar(255) DEFAULT NULL,
         `elClicks` int(11) DEFAULT NULL,
         `elCost` decimal(8,2) DEFAULT NULL,
         `elCPC` decimal(8,2) DEFAULT NULL,
         `elSessions` int(11) DEFAULT NULL,
         `elBounceRate` decimal(8,2) DEFAULT NULL,
         `elPageSessions` decimal(8,2) DEFAULT NULL,
         `elClickOutGoal2ConvRate` decimal(8,2) DEFAULT NULL,
         `elClickOutGoal2Completion` int(11) DEFAULT NULL,
         `elClickOutGoal2Value` decimal(8,2) DEFAULT NULL,
         PRIMARY KEY (`id`)
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        */
        
        //print_r($elarray);

        $elPais = substr($elarray[0],0,2);
        $elSplit = substr($elarray[0],2,1);
        
        
        if ($elSplit == "-")
        {
        
                $sql = "";
                $sql = $sql . "INSERT INTO IMP_GO_xxxxxxxxx_ADWORDS ";
                $sql = $sql . "(";
                    $sql = $sql . " elDay,";
                    $sql = $sql . " elCampaign,";
                    $sql = $sql . " elCountryTerritory,";
                    $sql = $sql . " elDevice,";
                    $sql = $sql . " elClicks,";
                    $sql = $sql . " elCost,";
                    $sql = $sql . " elCPC,";
                    $sql = $sql . " elSessions,";
                    $sql = $sql . " elBounceRate,";
                    $sql = $sql . " elPageSessions,";
                    $sql = $sql . " elClickOutGoal2ConvRate,";
                    $sql = $sql . " elClickOutGoal2Completion,";
                    $sql = $sql . " elClickOutGoal2Value";
                $sql = $sql . ")";
                $sql = $sql . "VALUES";
                $sql = $sql . "(";
                    $sql = $sql . "'" . $eldate. "',";  //elDay
                    $sql = $sql . "'" . $elarray[0] . "',";  //elCampaign` varchar(255) DEFAULT NULL,
                    $sql = $sql . "'" . $elPais. "',";       //elCountryTerritory` varchar(255) DEFAULT NULL,
                    $sql = $sql . "'" . $elarray[1] . "',";  //elDevice` varchar(255) DEFAULT NULL,
                    $sql = $sql . "'" . $elarray[2] . "',";  //elClicks` int(11) DEFAULT NULL,
                    $sql = $sql . "'" . str_replace("$","",$elarray[3]) . "',";  //elCost` decimal(8,2) DEFAULT NULL,
                    $sql = $sql . "'" . str_replace("$","",$elarray[4]) . "',";  //elCPC` decimal(8,2) DEFAULT NULL,
                    $sql = $sql . "'" . $elarray[5] . "',";  //elSessions` int(11) DEFAULT NULL,
                    $sql = $sql . "'" . str_replace("%","",$elarray[6]) . "',";  //elBounceRate` decimal(8,2) DEFAULT NULL,
                    $sql = $sql . "'" . $elarray[7] . "',";  //elPageSessions` decimal(8,2) DEFAULT NULL,
                    $sql = $sql . "'" . str_replace("%","",$elarray[8]) . "',";  //elClickOutGoal2ConvRate` decimal(8,2) DEFAULT NULL,
                    $sql = $sql . "'" . $elarray[9] . "',";  //elClickOutGoal2Completion` int(11) DEFAULT NULL,
                    $sql = $sql . "'" . str_replace("$","",$elarray[10]) . "'";  //elClickOutGoal2Value` decimal(8,2) DEFAULT NULL,
                $sql = $sql . ")";
            $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
        }
    }



    function importADWORDS($theFile)
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

        $elDateOK ="";

        $row_number=0;
        while(!feof($file))
        {
            $lafila = fgets($file);
            $elarray = str_getcsv($lafila, ",", '"');
            if ($row_number < 6)
            {
                if ($row_number==3)
                {
                    $elDateArray =  explode("-", $lafila);
                    if (count($elDateArray)==2)
                    {
                        $elDate = $elDateArray[1];
                       // print("eldate: ". $elDate . "\n");
                        // print("elDateOK: ". $elDateOK . "\n");
                        $elDateOK = substr($elDate,0,4). "-". substr($elDate,4,2)."-".substr($elDate,6,2);
                    }
                    else
                    {
                        return false;
                    }
               }

            }
            else
            {
                if ($row_number==6)
                {
                    if ($this->testHeader($elarray))
                    {
                        print "El archivo contiene las columans correctas. Se procede a importar\n";
                        $this->delete_from_db($elDateOK);
                    }
                    else
                    {
                        print "error Header\n";
                        return false;
                    }
                }
                else
                {
                    $this->insert_into_db($elarray,$elDateOK );
                }
            }
            $row_number++;
        }
        fclose($file);
        return true;
    }
}
?>

