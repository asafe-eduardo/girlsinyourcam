<?php

//anti-sql injection

function anti_injection($sql){
	$My = new MySQLiConnection();
	$sql =  mysqli_real_escape_string($My, $sql);

	return $sql;
}
?>