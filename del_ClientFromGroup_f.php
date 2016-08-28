<?php 

	//var_dump ($_POST);
	if ($_POST){
		if (isset($_POST['id']) && isset($_POST['group'])){
				include_once 'DBWork.php';
			
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				$query = "DELETE FROM `journal_groups_clients` WHERE `group_id`='{$_POST['group']}' AND `client`='{$_POST['id']}'";
				mysql_query($query) or die(mysql_error());
				mysql_close();
				
				//логирование
				AddLog ('0', $_POST['session_id'], '', 'Клиент ['.$_POST['id'].'] удален из группы ['.$_POST['group'].']');	
			
				echo 'Удаление прошло успешно';
		}else{
			echo 'Что-то пошло не так';
		}
	}
	
?>