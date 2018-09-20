<?php 

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['group'])){
					include_once 'DBWork.php';
				
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$time = time();
					$query = "DELETE FROM `journal_groups_clients` WHERE `group_id`='{$_POST['group']}'";
					mysql_query($query) or die(mysql_error());
					mysql_close();
					
					//логирование
					AddLog ('0', $_SESSION['id'], '', 'Все дети удалены из группы ['.$_POST['group'].']');
				
					echo 'Удаление прошло успешно';
			}else{
				echo 'Что-то пошло не так';
			}
		}
	}
?>