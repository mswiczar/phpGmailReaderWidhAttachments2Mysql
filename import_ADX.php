<?
include_once "dbconnections.php";
dbconnect ();

class MyImportADX
{



    function testHeader($theArrayHeader)
    {
        
        
        
        $arrayHeaderFixed =  array(
                                'Date',
                                'Device category',
                                'Country',
//                                'Device category ID',
//                                'Country ID',
                                'Total code served count',
                                'Unfilled impressions',
                                'Total impressions',
                                'Total clicks',
                                'Total CPM, CPC, CPD, and vCPM revenue ($)',
                                'Total average eCPM ($)',
                                'Total CTR'
                               );
        
//        print "arrayHeaderFixed\n";
//        print_r($theArrayHeader);
//        print "\narrayHeaderFixed\n";
//        print_r($arrayHeaderFixed);
//        print "\n";

        
        
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
    
    
        $sql ="DELETE FROM IMP_GO_xxxxxxxxx_ADX  WHERE elDate = STR_TO_DATE('".$elarray[0]."','%m/%d/%y')";
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
        print "Borrando Contenido para la Fecha: ".$elarray[0] . "\n";
    }




    function insert_into_db($elarray)
    {
        /*
     
         CREATE TABLE `IMP_GO_xxxxxxxxx_ADX` (
         `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
         `elDate` date DEFAULT NULL,
         `elDeviceCategory` varchar(255) DEFAULT NULL,
         `elCountry` varchar(255) DEFAULT NULL,
         `elDeviceCategoryID` varchar(255) DEFAULT NULL,
         `elCountryID` varchar(255) DEFAULT NULL,
         `elTotalCodeServedCount` int(11) DEFAULT NULL,
         `elUnfilledImpressions` int(11) DEFAULT NULL,
         `elTotalImpressions` int(11) DEFAULT NULL,
         `elTotalClicks` int(11) DEFAULT NULL,
         `elTotalCPM` decimal(8,2) DEFAULT NULL,
         `elTotalAverageECPM` decimal(8,2) DEFAULT NULL,
         `elTotalCTR` decimal(8,2) DEFAULT NULL,
         PRIMARY KEY (`id`)
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        

         `elDate` date DEFAULT NULL,
         `elDeviceCategory` varchar(255) DEFAULT NULL,
         `elCountry` varchar(255) DEFAULT NULL,
         `elDeviceCategoryID` varchar(255) DEFAULT NULL,
         `elCountryID` varchar(255) DEFAULT NULL,
         `elTotalCodeServedCount` int(11) DEFAULT NULL,
         `elUnfilledImpressions` int(11) DEFAULT NULL,
         `elTotalImpressions` int(11) DEFAULT NULL,
         `elTotalClicks` int(11) DEFAULT NULL,
         `elTotalCPM` decimal(8,2) DEFAULT NULL,
         `elTotalAverageECPM` decimal(8,2) DEFAULT NULL,
         `elTotalCTR` decimal(8,2) DEFAULT NULL,
        
        */
        if ($elarray[0]=="")
        {
            return true;
        }
        
        $pos = strpos($elarray[0], 'Tota');
        if ($pos === false)
        {
        
        
        $sql = "";
        $sql = $sql . "INSERT INTO IMP_GO_xxxxxxxxx_ADX ";
        $sql = $sql . "(";
            $sql = $sql . "elDate,";
            $sql = $sql . "elDeviceCategory,";
            $sql = $sql . "elCountry,";
            $sql = $sql . "elTotalCodeServedCount,";
            $sql = $sql . "elUnfilledImpressions,";
            $sql = $sql . "elTotalImpressions,";
            $sql = $sql . "elTotalClicks,";
            $sql = $sql . "elTotalCPM,";
            $sql = $sql . "elTotalAverageECPM,";
            $sql = $sql . "elTotalCTR";
        $sql = $sql . ")";
        $sql = $sql . "VALUES";
        $sql = $sql . "(";
            $sql = $sql . "STR_TO_DATE('" . $elarray[0] . "','%m/%d/%y'),"; //`elDate` date DEFAULT NULL,
            $sql = $sql . "'" . $elarray[1] . "',"; //`elDeviceCategory` varchar(255) DEFAULT NULL,
            $sql = $sql . "'" . $elarray[2] . "',"; //elCountry` varchar(255) DEFAULT NULL,
            $sql = $sql . "'" . str_replace(",","",$elarray[3]) . "',"; //`elTotalCodeServedCount` int(11) DEFAULT NULL
            $sql = $sql . "'" . str_replace(",","",$elarray[4]) . "',"; //`elUnfilledImpressions` int(11) DEFAULT NULL,
            $sql = $sql . "'" . str_replace(",","",$elarray[5]) . "',"; // `elTotalImpressions` int(11) DEFAULT NULL
            $sql = $sql . "'" . str_replace(",","",$elarray[6]) . "',"; // `elTotalClicks` int(11) DEFAULT NULL,
            $sql = $sql . "'" . str_replace(",","",$elarray[7]) . "',"; // `elTotalCPM` decimal(8,2) DEFAULT NULL,
            $sql = $sql . "'" . str_replace(",","",$elarray[8]). "',";// `elTotalAverageECPM` decimal(8,2) DEFAULT NULL,
            $sql = $sql . "'" . str_replace("%","",$elarray[9]) . "'";  //`elTotalCTR` decimal(8,2) DEFAULT NULL,
        $sql = $sql . ")";
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);

        }
    }



    public function importGoogleADX($theFile)
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
            if ($row_number < 9)
            {
            }
            else
            {
                if ($row_number==9)
                {
                    if ($this->testHeader($elarray))
                    {
                        
                        print "El archivo contiene las columans correctas. Se procede a importar\n";
                    }
                    else
                    {
                        print "error Header\n";
                        return false;
                    }
                }
                else
                {
                    if ($row_number==10)
                    {
                        $this->delete_from_db($elarray);
                    }
                    $this->insert_into_db($elarray);
                }
            }
            $row_number++;
        }
        fclose($file);
        return true;
    }
}
//importGoogleADX("GoogleADX.csv");
