<?php
# FileName="Connection_php_mysql.htm"
# Type="MYSQL"
# HTTP="true"
$hostname = "www.i-nnovative.net";
$database = "innovati_db_inventory_and_stock_tracking";
$username = "innovati_dbuser";
$password= "@dbuser123";
$Con = mysql_connect($hostname, $username, $password) or trigger_error(mysql_error(),E_USER_ERROR);
//echo "Perfect Connection";
mysql_select_db($database,$Con); 
?>