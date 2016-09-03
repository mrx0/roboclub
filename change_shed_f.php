<?php 

//change_shed_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			if (isset($_POST['group_id']) && isset($_POST['shedItems']) && isset($_POST['session_id'])){
				include_once 'DBWork.php';
				//var_dump ($_POST);
		
				$j_group = SelDataFromDB('journal_groups', $_POST['group_id'], 'group');
				if ($j_group !=0){	
					
					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$time = time();
					$query = "INSERT INTO `spr_shed_templs` (
						`group`, `template` )
						VALUES (
							'{$_POST['group_id']}', '{$_POST['shedItems']}')
						ON DUPLICATE KEY UPDATE
						`template` = '{$_POST['shedItems']}'
						";
					mysql_query($query) or die($query.' - '.mysql_error());
					mysql_close();
						
					//логирование
					AddLog ('0', $_POST['session_id'], '', 'Изменено расписание группы ['.$_POST['group_id'].']. Расписание ['.$_POST['shedItems'].'].');	
					
					echo '
						Расписание группы изменено.
						<br /><br />
						<a href="add_shed_group.php?id='.$_POST['group_id'].'" class="b">К расписанию</a>
						';
				}else{
					echo '<h1>Не найдена такая группа</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}
?>