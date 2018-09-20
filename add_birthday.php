<?php 

//add_birthday.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			if (isset($_POST['client']) && isset($_POST['month']) && isset($_POST['year'])){
				//var_dump ($_POST);
				
				$status = 1;
				
				$arr = array();
				$birth_j = array();
				
				require 'config.php';
				
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");

				$query = "SELECT `id`, `status` FROM `journal_birth` WHERE `client_id`='{$_POST['client']}' AND `month`='{$_POST['month']}' AND `year`='{$_POST['year']}'";

				$res = mysql_query($query) or die(mysql_error());
				$number = mysql_num_rows($res);
				if ($number != 0){
					$arr = mysql_fetch_assoc($res);
					
					if ($arr['status'] == 1) $status = 0; else $status = 1;
					
					$query = "UPDATE `journal_birth` SET `status`='$status'  WHERE `id` = '{$arr['id']}'";
					
					mysql_query($query) or die($query.' - '.mysql_error());
					
				}else{
					$query = "INSERT INTO `journal_birth` (
						`client_id`, `month`, `year`, `status`)
						VALUES (
						'{$_POST['client']}', '{$_POST['month']}', '{$_POST['year']}', '$status')";
					mysql_query($query) or die($query.' - '.mysql_error());
				}

				
				mysql_close();
				
				include_once 'DBWork.php';
				//логирование
				AddLog ('0', $_POST['session_id'], '', 'Отметка дня рождения. Ребёнок ['.$_POST['client'].']. Год ['.$_POST['year'].']. Месяц ['.$_POST['month'].']. Status ['.$status.'].');
				
				/*echo '
					Ok типа.
					<br /><br />
					<a href="add_shed_group.php?id='.$_POST['group_id'].'" class="b">К расписанию</a>
					';*/
			}else{
				//echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			//echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}
?>