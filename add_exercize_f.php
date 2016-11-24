<?php 

//add_exercize_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		if ($_POST){
			if (isset($_POST['group_id']) && isset($_POST['data']) && isset($_POST['day']) && isset($_POST['month']) && isset($_POST['year'])){
				//var_dump ($_POST);
				
				$arr = array();

				require 'config.php';
					
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");

				$query = "SELECT * FROM `journal_exercize` WHERE `group_id`='{$_POST['group_id']}' AND `day`='{$_POST['day']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}'";

				$res = mysql_query($query) or die(mysql_error());
				$number = mysql_num_rows($res);
				if ($number != 0){
					$arr = mysql_fetch_assoc($res);

					$query = "UPDATE `journal_exercize` SET `descr`='{$_POST['data']}' WHERE `group_id`='{$_POST['group_id']}' AND `day`='{$_POST['day']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}'";
					
					mysql_query($query) or die($query.' - '.mysql_error());
						
				}else{
					if ($_POST['data'] != ''){
						$query = "INSERT INTO `journal_exercize` (
							`group_id`, `day`, `month`, `year`, `descr`)
							VALUES (
							'{$_POST['group_id']}', '{$_POST['day']}', '{$_POST['month']}', '{$_POST['year']}', '{$_POST['data']}')";
						mysql_query($query) or die($query.' - '.mysql_error());
					}
				}
					
				mysql_close();


				}else{
			}
		}else{
		}
	}
?>