<?php 

//add_replacement_f.php
//Функция для добавления подмены

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		
		if ($_POST){
			if (isset($_POST['group_id']) && isset($_POST['worker'])){
				include_once 'DBWork.php';
				
				$j_group = SelDataFromDB('journal_groups', $_POST['group_id'], 'group');
				$j_worker = SelDataFromDB('spr_workers', $_POST['worker'], 'worker_id');
				if (($j_group !=0) && ($j_worker != 0)){	
					//var_dump ($j_group);

					if ($j_group[0]['worker'] != $j_worker[0]['id']){
						
						require 'config.php';
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						
						$query = "SELECT * FROM `journal_replacement` WHERE `group_id` = '".$_POST['group_id']."' AND  `user_id` = '{$_POST['worker']}'";
						$res = mysql_query($query) or die(mysql_error());
						$number = mysql_num_rows($res);
						if ($number != 0){
							echo '<h1>Уже есть такая подмена в этой группе</h1><a href="">Назад</a>';
						}else{
							$time = time();
							$query = "INSERT INTO `journal_replacement` (
								`group_id`, `user_id`) 
								VALUES (
								'{$_POST['group_id']}', '{$_POST['worker']}') ";	
							mysql_query($query) or die(mysql_error());

							//логирование
							AddLog ('0', $_SESSION['id'], '', 'Группе ['.$_POST['group_id'].'] назначен в подмену тренер ['.$_POST['worker'].'].');	
						
							echo '
								Группе <a href="group.php?id='.$_POST['group_id'].'" class="ahref">'.$j_group[0]['name'].'</a> назначен на подмену тренер <a href="user.php?id='.$j_worker[0]['id'].'" class="ahref">'.$j_worker[0]['name'].'</a>.
								<br /><br />
								<a href="replacements.php" class="b">Все подмены</a>';	
						}	

					}else{
						echo '<h1>Нельзя добавить подменой тренера этой группы</h1><a href="">Назад</a>';
					}
				}else{
					echo '<h1>Не найдена группа или тренер</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}

		}
	}
?>