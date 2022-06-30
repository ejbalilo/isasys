<?php
session_start();
$con = mysqli_connect("localhost","root","zC8jzwSnw3MXTdcE","sthunna_isasys");
if(!$con)
	die("Could not connect: " . mysqli_error($con));

mysqli_query($con,"SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));");

mysqli_close($con);
?>
