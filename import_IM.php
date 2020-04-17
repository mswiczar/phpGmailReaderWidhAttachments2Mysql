<?
include_once "dbconnections.php";
dbconnect ();

class MyImportIM
{
    // Declaración de una propiedad
    
    // Declaración de un método
    function testHeader($theArrayHeader)
    {
        
        // $arrayHeaderFixed =  array('Campaign Name','Ad Set Name','Country','Reach','Impressions','Frequency','Result Type','Results','Cost per Result','Amount Spent (USD)','CTR (All)','CPM (Cost per 1,000 Impressions)','Reporting Starts','Reporting Ends');
        
        
        $arrayHeaderFixed =  array("Date","Year","Quarter","Month","Week","Network","Product","Publisher","Site","Type of Ad Unit","Ad Unit","Device","Page Views","Percent Addressable","Addressable Pages","Fill Rate","Pages Served","Interactions","CPI","CTR","Clicks","CPC ($)","Gross Media Revenue ($)","Available eCPM ($)","Served eCPM ($)","Country");
        
        
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
        
        $sql ="DELETE FROM IMP_IM_xxxxxxxxx_CROSS  WHERE elDate = STR_TO_DATE('".$elarray[0]."','%m/%d/%Y')";
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
        print "Borrando Contenido para la Fecha: ".$elarray[0] . "\n";
        
    }


     function insert_into_db($elarray)
    {
        /*
         CREATE TABLE `IMP_IM_xxxxxxxxx_CROSS` (
         `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
         `elDate` date DEFAULT NULL,
         `elYear` int(11) DEFAULT NULL,
         `elQuarter` int(11) DEFAULT NULL,
         `elMonth` int(11) DEFAULT NULL,
         `elWeek` date DEFAULT NULL,
         `elNetwork` varchar(255) DEFAULT NULL,
         `elProduct` varchar(255) DEFAULT NULL,
         `elPublisher` varchar(255) DEFAULT NULL,
         `elSite` varchar(255) DEFAULT NULL,
         `elTypeofAdUnit` varchar(255) DEFAULT NULL,
         `elAdUnit` varchar(255) DEFAULT NULL,
         `elDevice` varchar(255) DEFAULT NULL,
         `elPageViews` int(255) DEFAULT NULL,
         `elPercentAddressable` decimal(5,2) DEFAULT NULL,
         `elAddressablePages` int(11) DEFAULT NULL,
         `elFillRate` decimal(5,2) DEFAULT NULL,
         `elPagesServed` int(11) DEFAULT NULL,
         `elInteractions` int(11) DEFAULT NULL,
         `elCPI` decimal(5,2) DEFAULT NULL,
         `elCTR` decimal(5,2) DEFAULT NULL,
         `elClicks` int(11) DEFAULT NULL,
         `elCPC$` decimal(8,2) DEFAULT NULL,
         `elGrossMediaRevenue$` decimal(8,2) DEFAULT NULL,
         `elAvailableeCPM$` decimal(8,2) DEFAULT NULL,
         `elServedeCPM$` decimal(8,2) DEFAULT NULL,
         `elCountry` varchar(255) DEFAULT NULL,
         PRIMARY KEY (`id`)
         ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
         
         */
        
        //print_r($elarray);
        $sql = "";
        $sql = $sql . "INSERT INTO IMP_IM_xxxxxxxxx_CROSS ";
        $sql = $sql . "(";
        $sql = $sql . "elDate,";
        $sql = $sql . "elYear,";
        $sql = $sql . "elQuarter ,";
        $sql = $sql . "elMonth ,";
        $sql = $sql . "elWeek ,";
        $sql = $sql . "elNetwork ,";
        $sql = $sql . "elProduct ,";
        $sql = $sql . "elPublisher ,";
        $sql = $sql . "elSite ,";
        $sql = $sql . "elTypeofAdUnit ,";
        $sql = $sql . "elAdUnit ,";
        $sql = $sql . "elDevice ,";
        $sql = $sql . "elPageViews ,";
        $sql = $sql . "elPercentAddressable ,";
        $sql = $sql . "elAddressablePages ,";
        $sql = $sql . "elFillRate ,";
        $sql = $sql . "elPagesServed ,";
        $sql = $sql . "elInteractions ,";
        $sql = $sql . "elCPI ,";
        $sql = $sql . "elCTR ,";
        $sql = $sql . "elClicks ,";
        $sql = $sql . "elCPC$ ,";
        $sql = $sql . "elGrossMediaRevenue$ ,";
        $sql = $sql . "elAvailableeCPM$ ,";
        $sql = $sql . "elServedeCPM$ ,";
        $sql = $sql . "elCountry";
        $sql = $sql . ")";
        $sql = $sql . "VALUES";
        $sql = $sql . "(";
        
        $sql = $sql . "STR_TO_DATE('" . $elarray[0] . "','%m/%d/%Y')  ,"; //`elDate` date DEFAULT NULL,
        $sql = $sql . "'" . $elarray[1] . "',"; //`elYear` int(11) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[2] . "',"; //`elQuarter` int(11) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[3] . "',"; //`elMonth` int(11) DEFAULT NULL,
        $sql = $sql . "STR_TO_DATE('" . $elarray[4] . "','%m/%d/%Y')  ,"; //`elWeek` date DEFAULT NULL,
        $sql = $sql . "'" . $elarray[5] . "',"; //`elNetwork` varchar(255) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[6] . "',"; //`elProduct` varchar(255) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[7] . "',"; //`elPublisher` varchar(255) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[8] . "',"; //`elSite` varchar(255) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[9] . "',"; //`elTypeofAdUnit` varchar(255) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[10] . "',"; //`elAdUnit` varchar(255) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[11] . "',"; //`elDevice` varchar(255) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[12] . "',"; //`elPageViews` int(11) DEFAULT NULL,
        
        //$sql = $sql . "'" . $elarray[13] . "',"; //`elPercentAddressable` decimal(5,2) DEFAULT NULL,
        $aux = str_replace("%","",$elarray[13]);
        $sql = $sql . "'" . $aux . "',"; //`elPercentAddressable` decimal(5,2) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[14] . "',"; //`elAddressablePages` int(11) DEFAULT NULL,
        
        //    $sql = $sql . "'" . $elarray[15] . "',"; //`elFillRate` decimal(5,2) DEFAULT NULL,
        $aux = str_replace("%","",$elarray[15]);
        $sql = $sql . "'" . $aux . "',"; //`elFillRate` decimal(5,2) DEFAULT NULL,
        
        $sql = $sql . "'" . $elarray[16] . "',"; //`elPagesServed` int(11) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[17] . "',"; //`elInteractions` int(11) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[18] . "',"; //`elCPI` decimal(5,2) DEFAULT NULL,
        //$sql = $sql . "'" . $elarray[19] . "',"; //`elCTR` decimal(5,2) DEFAULT NULL,
        $aux = str_replace("%","",$elarray[19]);
        $sql = $sql . "'" . $aux . "',"; //`elCTR` decimal(5,2) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[20] . "',"; //`elClicks` int(11) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[21] . "',"; //`elCPC$` decimal(8,2) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[22] . "',"; //`elGrossMediaRevenue$` decimal(8,2) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[23] . "',"; //`elAvailableeCPM$` decimal(8,2) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[24] . "',"; //`elServedeCPM$` decimal(8,2) DEFAULT NULL,
        $sql = $sql . "'" . $elarray[25] . "'"; //`elCountry` varchar(255) DEFAULT NULL,
        $sql = $sql . ")";
        
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
        
        //echo $sql ."<br>";
    }

    public function importIMCross($theFile)
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
            if ($row_number==0)
            {
                if ($this->testHeader($elarray))
                {
                    
                     print ( "El archivo contiene las columans correctas. Se procede a importar\n");
                }
                else
                {
                    //echo "error Header<br>";
                    return false;
                    
                }
            }
            else
            {
                if ($row_number==1)
                {
                    $this->delete_from_db($elarray);
                }
                if (count($elarray)>2)
                {
                    $this->insert_into_db($elarray);
                }
            }
            $row_number++;
        }
        fclose($file);
        return true;
    }
    
    
    
}















//importIMCross("IMCROSS.csv");

?>
