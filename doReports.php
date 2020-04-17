<?
error_reporting(E_ERROR);
ini_set('display_errors', 1);
include_once "setCharSet.php";
include_once "dbconnections.php";
dbconnect ();

function get_countryID($elvalor)
{
    $sql2= "select id from `AUX-COUNTRY-CODES` where value = '". $elvalor."'";
    //echo $sql2 . "\n";
    
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    if ($r2 = mysql_fetch_assoc($result2))
    {
        return $r2['id'];
    }
    else
    {
        return "--";
    }
}

function normalize_IMCROSS ()
{
    $sql ="UPDATE  IMP_IM_xxxxxxxxx_CROSS  SET aDate = elDate";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
    $sql ="UPDATE  IMP_IM_xxxxxxxxx_CROSS  SET aCountry = elCountry";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);


    $sql2= "select * from IMP_IM_xxxxxxxxx_CROSS where  ISNULL(aDevice) = 1";
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    while ($r2 = mysql_fetch_assoc($result2))
    {
        $theID =  $r2['id'];
        $theDevice =$r2['elDevice'];
        $aDevice = "Unknown";
        switch ($theDevice)
        {
            case "Desktop":
                $aDevice = "Desktop";
                break;
            case "Mobile":
                $aDevice = "Mobile";
                break;
            case "Tablet":
                $aDevice = "Desktop";
                break;
        }
        $sql ="UPDATE  IMP_IM_xxxxxxxxx_CROSS  SET aDevice = '".$aDevice ."' where id = '".$theID."'";
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    }
    
}





function normalize_ANALYTICS ()
{
    $sql ="UPDATE  IMP_GO_xxxxxxxxx_ANALYTICS  SET aDate = elDate";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
    $sql2= "select * from IMP_GO_xxxxxxxxx_ANALYTICS where  ISNULL(aDevice) = 1";
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    while ($r2 = mysql_fetch_assoc($result2))
    {
        $theID =  $r2['id'];
        $theDevice =$r2['elDeviceCategory'];
        $aDevice = "Unknown";
        switch ($theDevice)
        {
            case "desktop":
                $aDevice = "Desktop";
                break;
            case "mobile":
                $aDevice = "Mobile";
                break;
        }
        
        $theCountry =  get_countryID($r2['elCountry']);
        
        $sql ="UPDATE  IMP_GO_xxxxxxxxx_ANALYTICS  SET aDevice = '".$aDevice ."' , aCountry = '".$theCountry."' where id = '".$theID."'";
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    }
}




function normalize_ADX ()
{
    $sql ="UPDATE  IMP_GO_xxxxxxxxx_ADX  SET aDate = elDate";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
    $sql2= "select * from IMP_GO_xxxxxxxxx_ADX where  ISNULL(aDevice) = 1";
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    while ($r2 = mysql_fetch_assoc($result2))
    {
        $theID =  $r2['id'];
        $theDevice =$r2['elDeviceCategory'];
        $aDevice = "Unknown";
        switch ($theDevice)
        {
            case "Tablet":
            case "Desktop":
                $aDevice = "Desktop";
                break;
            case "mobile":
            case "Feature phone":
            case "Smartphone":
                
                $aDevice = "Mobile";
                break;
        }
        
        $theCountry =  get_countryID($r2['elCountry']);
        
        $sql ="UPDATE  IMP_GO_xxxxxxxxx_ADX  SET aDevice = '".$aDevice ."' , aCountry = '".$theCountry."' where id = '".$theID."'";
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    }
}



function normalize_ADWORDS ()
{
    $sql ="UPDATE  IMP_GO_xxxxxxxxx_ADWORDS  SET aDate = elDay";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
    $sql2= "select * from IMP_GO_xxxxxxxxx_ADWORDS where  ISNULL(aDevice) = 1";
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    while ($r2 = mysql_fetch_assoc($result2))
    {
        $theID =  $r2['id'];
        $theDevice =$r2['elDevice'];
        $aDevice = "Unknown";
        switch ($theDevice)
        {
            case "tablet":
            case "desktop":
                $aDevice = "Desktop";
                break;
            case "mobile":
                $aDevice = "Mobile";
                break;
        }
        
        
        $sql ="UPDATE  IMP_GO_xxxxxxxxx_ADWORDS  SET aDevice = '".$aDevice ."' , aCountry = elCountryTerritory  where id = '".$theID."'";
        $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    }
}

function normalize_FB ()
{
    $sql ="UPDATE  IMP_FB_xxxxxxxxx_FACEBOOK  SET aDate = elDate , aDevice = TRIM(elDevice) , aCountry = TRIM(elCountry) ";
//    print $sql ."\n";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
}


/*
 $sql2= "select id from `AUX-COUNTRY-CODES` where value = '". $elvalor."'";
 //echo $sql2 . "\n";
 
 $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
 if ($r2 = mysql_fetch_assoc($result2))
 {
 return $r2['id'];
 }
 else
 {
 return "--";
 }
 */

function GetGoogleAdwordsSpend($theDate,$theCountry,$theDevice)
{
    $sql2= "select sum(elcost) as valor from `IMP_GO_xxxxxxxxx_ADWORDS` "
    . " where aDate = '". $theDate."'"
    . " and   aCountry = '".$theCountry."'"
    . " and aDevice = '".$theDevice."'"
    . " group by aDate, aCountry, aDevice";
   // echo $sql2. "<br>";
    
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    if ($r2 = mysql_fetch_assoc($result2))
    {
        return $r2['valor'];
    }
    else
    {
        return "0";
    }
}

function GetFacebookAdAccountSpend($theDate,$theCountry,$theDevice)
{
    $sql2= "select sum(elAmountSpentUSD) as valor from `IMP_FB_xxxxxxxxx_FACEBOOK` "
    . " where aDate = '". $theDate."'"
    . " and   aCountry = '".$theCountry."'"
    . " and aDevice = '".$theDevice."'"
    . " group by aDate, aCountry, aDevice";
    // echo $sql2. "<br>";
    
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    if ($r2 = mysql_fetch_assoc($result2))
    {
        return $r2['valor'];
    }
    else
    {
        return "0";
    }

}



function GoogleDoubleClickAdExchangeRevenue($theDate,$theCountry,$theDevice)
{
    $sql2= "select sum(elTotalCPM) as valor from `IMP_GO_xxxxxxxxx_ADX` "
    . " where aDate = '". $theDate."'"
    . " and   aCountry = '".$theCountry."'"
    . " and aDevice = '".$theDevice."'"
    . " group by aDate, aCountry, aDevice";
    // echo $sql2. "<br>";
    
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    if ($r2 = mysql_fetch_assoc($result2))
    {
        return $r2['valor'];
    }
    else
    {
        return "0";
    }

}


function GetIntentMediaRevenue($theDate,$theCountry,$theDevice)
{
    $sql2= "select (sum(`elGrossMediaRevenue$`) * 0.728) as valor from `IMP_IM_xxxxxxxxx_CROSS` "
    . " where aDate = '". $theDate."'"
    . " and   aCountry = '".$theCountry."'"
    . " and aDevice = '".$theDevice."'"
    . " group by aDate, aCountry, aDevice";
    // echo $sql2. "<br>";
    
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    if ($r2 = mysql_fetch_assoc($result2))
    {
        return $r2['valor'] ;
    }
    else
    {
        return "0";
    }
    
    
}



function GetlickoutRevenue($theDate,$theCountry,$theDevice)
{
    
    $sql2= "select (sum(`elClickOutGoal2Completions`) * 0.1) as valor from `IMP_GO_xxxxxxxxx_ANALYTICS` "
    . " where aDate = '". $theDate."'"
    . " and   aCountry = '".$theCountry."'"
    . " and aDevice = '".$theDevice."'"
    . " group by aDate, aCountry, aDevice";
    // echo $sql2. "<br>";
    
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    if ($r2 = mysql_fetch_assoc($result2))
    {
        return $r2['valor'] ;
    }
    else
    {
        return "0";
    }
    
}








function GetSessions($theDate,$theCountry,$theDevice)
{
    $sql2= "select sum(`elSessions`)  as valor from `IMP_GO_xxxxxxxxx_ANALYTICS` "
    . " where aDate = '". $theDate."'"
    . " and   aCountry = '".$theCountry."'"
    . " and aDevice = '".$theDevice."'"
    . " group by aDate, aCountry, aDevice";
    // echo $sql2. "\n";
    
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    if ($r2 = mysql_fetch_assoc($result2))
    {
        return $r2['valor'] ;
    }
    else
    {
        return "0";
    }
    
}











function updateReport ()
{
    
    $sql2= "select * from TotalByDayByCountryByDevice order by id ";
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    while ($r2 = mysql_fetch_assoc($result2))
    {
        $theID        =  $r2['id'];
        $theDate      =  $r2['aDate'];
        $theCountry   =  $r2['aCountry'];
        $theDevice    =  $r2['aDevice'];
        
        $GoogleAdwordsSpend                 = GetGoogleAdwordsSpend($theDate,$theCountry,$theDevice);
        $FacebookAdAccountSpend             = GetFacebookAdAccountSpend($theDate,$theCountry,$theDevice);
        $GoogleDoubleClickAdExchangeRevenue = GoogleDoubleClickAdExchangeRevenue($theDate,$theCountry,$theDevice);
        $IntentMediaRevenue                 = GetIntentMediaRevenue($theDate,$theCountry,$theDevice);
        $ClickoutRevenue                    = GetlickoutRevenue($theDate,$theCountry,$theDevice);
        $aSessions                           = GetSessions($theDate,$theCountry,$theDevice);

        $sql = "";
        $sql = $sql . "UPDATE  TotalByDayByCountryByDevice " ;
        $sql = $sql . " SET  " ;
        $sql = $sql . "`GoogleAdwordsSpend` ='".$GoogleAdwordsSpend ."', ";
        $sql = $sql . "`FacebookAdAccountSpend` ='".$FacebookAdAccountSpend ."',  ";
        $sql = $sql . "`GoogleDoubleClickAdExchangeRevenue` ='".$GoogleDoubleClickAdExchangeRevenue ."', ";
        $sql = $sql . "`IntentMediaRevenue` ='".$IntentMediaRevenue ."', ";
        $sql = $sql . "`ClickoutRevenue` ='".$ClickoutRevenue ."', ";
        $sql = $sql . "`Sessions` ='".$aSessions ."' ";
        $sql = $sql . " where id = '".$theID."'";
        
        
     //echo $sql . "\n ";
       $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);

    }
    
    
    //TotalMediaSpend
    $sql ="update  TotalByDayByCountryByDevice  set TotalMediaSpend = GoogleAdwordsSpend + FacebookAdAccountSpend";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
    //TotalRevenue
    $sql ="update  TotalByDayByCountryByDevice   set TotalRevenue = GoogleDoubleClickAdExchangeRevenue + IntentMediaRevenue + ClickoutRevenue";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);

    //Profit
    $sql ="update  TotalByDayByCountryByDevice  set Profit = TotalRevenue - TotalMediaSpend ";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
    
    //ProfitMargin
    $sql ="update  TotalByDayByCountryByDevice  set ProfitMargin = Profit /  TotalMediaSpend  * 100";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);


    
    //----------RPS----------
    
    //ADXRPS
    $sql ="update  TotalByDayByCountryByDevice  set ADXRPS = GoogleDoubleClickAdExchangeRevenue /  Sessions ";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
    //IntentMediaRPS
    $sql ="update  TotalByDayByCountryByDevice  set IntentMediaRPS = IntentMediaRevenue /  Sessions ";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
    //ClickoutRPS
    $sql ="update  TotalByDayByCountryByDevice  set ClickoutRPS = ClickoutRevenue /  Sessions ";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
    //TotalRPS
    $sql ="update  TotalByDayByCountryByDevice  set TotalRPS = TotalRevenue /  Sessions ";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);
    
}


function doReport()
{

    
    $sql ="DELETE FROM TotalByDayByCountryByDevice";
    $result = mysql_query ($sql,$GLOBALS['db_xxxxxxxxx']);

    $sql2= "INSERT INTO TotalByDayByCountryByDevice (aDate , aCountry , aDevice )    ";
    $sql2= $sql2. "select aDate , aCountry ,aDevice  from ";
    $sql2= $sql2. "( ";
    $sql2= $sql2. " select  aDate , aCountry ,aDevice  from IMP_GO_xxxxxxxxx_ADWORDS   UNION ";
    $sql2= $sql2. " select  aDate , aCountry ,aDevice  from IMP_GO_xxxxxxxxx_ADX       UNION ";
    $sql2= $sql2. " select  aDate , aCountry ,aDevice  from IMP_GO_xxxxxxxxx_ANALYTICS UNION ";
    $sql2= $sql2. " select  aDate , aCountry ,aDevice  from IMP_FB_xxxxxxxxx_FACEBOOK  UNION ";
    $sql2= $sql2. " select  aDate , aCountry ,aDevice  from IMP_IM_xxxxxxxxx_CROSS ";
    $sql2= $sql2. ")  A";
    
    
    //echo $sql2 ."\n";
    $result2 = mysql_query ($sql2,$GLOBALS['db_xxxxxxxxx']);
    
    updateReport();
    
    
    
}







function normalizeIt()
{
    normalize_FB();
    normalize_IMCROSS();
    normalize_ANALYTICS();
    normalize_ADX();
    normalize_ADWORDS();
}





normalizeIt();
doReport();





?>

