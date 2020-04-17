<?php
function dbconnect ()
{
		if (isset($GLOBALS['xxxxxx']))
		{
		
		}
		else
		{
        	$GLOBALS['xxxx'] = mysql_connect("localhost","xxxxx","xxxxxx!",FALSE);
        	if (!mysql_select_db("xxxxxx",$GLOBALS['xxxxxxxx']))
        	{
                die(mysql_error());
       		}
       	}
}

?>
