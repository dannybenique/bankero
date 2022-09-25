<?	function db_connect($host,$dbname,$user,$pass)
	{	$mi_conn=@mysql_connect($host,$user,$pass)or die(header("Location:../index.php?error_login=1"));
		mysql_select_db($dbname,$mi_conn);
		return $mi_conn;
	}
	function db_uconnect($xconn)
	{	mysql_close();  	
	}
	function db_sqlquery($sql,$id)
	{	$res=@mysql_query($sql, $id);
		return $res;
	}
	function db_numrows($res)
	{	$rows=mysql_num_rows($res);
		return $rows;
	}
	function db_fetcharray($res)
	{	$row = mysql_fetch_array($res);
		return $row;
	}
?>