<?php 

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (isset($_POST['group_id']) && isset($_POST['d']) && isset($_POST['m']) && isset($_POST['y']) && isset($_POST['worker'])){
				
					if ($_POST['d'] < 10) $_POST['d'] = '0'.$_POST['d'];
					if ($_POST['m'] < 10) $_POST['m'] = '0'.$_POST['m'];
					
					include_once 'DBWork.php';
				
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$time = time();
					$query = "UPDATE `journal_user` SET `user_id`='{$_POST['worker']}' WHERE `group_id` = '{$_POST['group_id']}' AND `day` = '{$_POST['d']}' AND `month` = '{$_POST['m']}' AND `year` = '{$_POST['y']}'";
					mysql_query($query) or die(mysql_error());
					mysql_close();
					
					//логирование
					AddLog ('0', $_SESSION['id'], '', 'Сменился отметивший на ['.$_POST['worker'].']. Группа ['.$_POST['group_id'].']. Дата ['.$_POST['d'].'.'.$_POST['m'].'.'.$_POST['y'].']');
				
					echo 'Изменение OK';
			}else{
				echo 'Что-то пошло не так';
			}
		}
	}
?>