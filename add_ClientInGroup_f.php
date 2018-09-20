<?php 

	//var_dump ($_POST);
	if ($_POST){
		if (isset($_POST['id']) && isset($_POST['group'])){
				include_once 'DBWork.php';
			
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				
				$query = "SELECT * FROM `journal_groups_clients` WHERE `client` = '{$_POST['id']}'";
				$res = mysql_query($query) or die(mysql_error());
				$number = mysql_num_rows($res);
				if ($number != 0){
					echo 'Участник уже состоит в группе.';
				}else{
					$time = time();
					$query = "INSERT INTO `journal_groups_clients` (
						`group_id`, `client`) 
						VALUES (
						'{$_POST['group']}', '{$_POST['id']}') ";				
					mysql_query($query) or die(mysql_error());
					mysql_close();
					
					//логирование
					AddLog ('0', $_POST['session_id'], '', 'Ребёнок ['.$_POST['id'].'] добавлен в группу ['.$_POST['group'].']');
				
					echo 'Добавление прошло успешно';
				}
		}else{
			echo 'Что-то пошло не так';
		}
	}
	
?>