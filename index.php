<?php 


	ini_set('display_errors', true);
	error_reporting(E_ALL ^ E_NOTICE);

?>
<?php require_once("/includes/neo4jfunctions.php");?>


<?php

	//sample function calls		
	createStudent_Node('1031110393','Shubhi Gupta','B.Tech','CSE','2011');
	createStudentRoom_rel('1031110393','Manoranjitham','443');

?>

