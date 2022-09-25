<?php
	function db_connect($host,$dbname,$user,$pass){
		$connectionInfo=array("Database"=>$dbname,"UID"=>$user,"PWD"=>$pass);
		$conn=@sqlsrv_connect($host,$connectionInfo);
		return $conn;
	}
	function db_uconnect($xconn){
		sqlsrv_close($xconn);  	
	}
	function db_sqlquery($xconn,$sql){
		$res=@sqlsrv_query($xconn,$sql);
		return $res;
	}
	function db_numrows($res){
		$rows=sqlsrv_num_rows($res);
		return $rows;
	}
	function db_fetcharray($res){
		$row = mysql_fetch_array($res);
		return $row;
	}
?>