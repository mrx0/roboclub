<?php 

//add_finance_mark.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			if (isset($_POST['finance'])){
				//var_dump ($_POST);
				
				$status = 1;
				
				$arr = array();
				
				require 'config.php';
				
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");

				$query = "SELECT `id`, `mark` FROM `journal_finance` WHERE `id`='{$_POST['finance']}'";

				$res = mysql_query($query) or die(mysql_error());
				$number = mysql_num_rows($res);
				if ($number != 0){
					$arr = mysql_fetch_assoc($res);
					
					if ($arr['mark'] == 1) $status = 0; else $status = 1;
					
					$query = "UPDATE `journal_finance` SET `mark`='$status'  WHERE `id` = '{$arr['id']}'";
					
					mysql_query($query) or die($query.' - '.mysql_error());
					
				}else{
					/*$query = "INSERT INTO `journal_birth` (
						`client_id`, `month`, `year`, `status`)
						VALUES (
						'{$_POST['client']}', '{$_POST['month']}', '{$_POST['year']}', '$status')";
					mysql_query($query) or die($query.' - '.mysql_error());*/
				}

				
				mysql_close();
				
				include_once 'DBWork.php';
				//логирование
				AddLog ('0', $_POST['session_id'], '', 'Отметка о проверке платежа ['.$_POST['finance'].']. ');	
			}else{
			}
		}else{
		}
	}
?>