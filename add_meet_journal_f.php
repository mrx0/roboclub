<?php 

//add_meet_journal_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			if (isset($_POST['group_id']) && isset($_POST['journalItems']) && isset($_POST['session_id'])){
				include_once 'DBWork.php';
				//var_dump ($_POST);
		
				$j_group = SelDataFromDB('journal_groups', $_POST['group_id'], 'group');
				if ($j_group !=0){	
					
					$tempJSON = json_decode($_POST['journalItems'], true);
					//var_dump ($tempJSON);
					
					require 'config.php';
					
					foreach($tempJSON as $key => $newStatus){
						$tempArr = explode("_", $key);
						$user_id = $tempArr[0];
						$dateArr = explode(".", $tempArr[1]);
						$day = $dateArr[2];
						$month = $dateArr[1];
						$year = $dateArr[0];
						//var_dump($_POST['group_id'].' + '.$user_id.' + '.$day.' + '.$month.' + '.$year.' -> '.$newStatus.'<br>');
						
						$time = time();
						
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						$query = "SELECT `status` FROM `journal_user` WHERE `group_id` = '{$_POST['group_id']}' AND `user_id` = '{$user_id}' AND `day` = '{$day}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
						$res = mysql_query($query) or die('1->'.mysql_error().'->'.$query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							$res = mysql_fetch_assoc($res);
							mysql_close();
							//var_dump ($res['status']);
							
							if ($newStatus == 0){
								//var_dump ('Надо удалить запись');
								
								mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
								mysql_select_db($dbName) or die(mysql_error()); 
								mysql_query("SET NAMES 'utf8'");
								$query = "DELETE FROM `journal_user` WHERE `group_id` = '{$_POST['group_id']}' AND `user_id` = '{$user_id}' AND `day` = '{$day}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
								mysql_query($query) or die('2->'.mysql_error().'->'.$query);
								mysql_close();
								AddLog ('0', $_POST['session_id'], '', 'Из журнала удалена запись о посещении. Группа ['.$_POST['group_id'].']. Клиент ['.$user_id.']. День ['.$day.']. Месяц ['.$month.']. Год ['.$year.']. Статус ['.$res['status'].']');	
							}else{
								//var_dump ('Надо обновить запись');
								
								if ($newStatus != $res['status']){
									mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
									mysql_select_db($dbName) or die(mysql_error()); 
									mysql_query("SET NAMES 'utf8'");
									$query = "UPDATE `journal_user` SET `status`='{$newStatus}'  WHERE `group_id` = '{$_POST['group_id']}' AND `user_id` = '{$user_id}' AND `day` = '{$day}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
									mysql_query($query) or die('3->'.mysql_error().'->'.$query);
									mysql_close();
									AddLog ('0', $_POST['session_id'], '', 'В журнале изменена запись о посещении. Группа ['.$_POST['group_id'].']. Клиент ['.$user_id.']. День ['.$day.']. Месяц ['.$month.']. Год ['.$year.']. Статус ['.$newStatus.']');	
								}
							}
						}else{
							if ($newStatus != 0){
								//var_dump ('Надо добавить запись');
								
								mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
								mysql_select_db($dbName) or die(mysql_error()); 
								mysql_query("SET NAMES 'utf8'");
								$query = "INSERT INTO `journal_user` (`user_id`, `group_id`, `day`, `month`, `year`, `status`) VALUES ('{$user_id}', '{$_POST['group_id']}', '{$day}', '{$month}', '{$year}', '{$newStatus}')";
								mysql_query($query) or die('4->'.mysql_error().'->'.$query);
								mysql_close();
								AddLog ('0', $_POST['session_id'], '', 'В журнал добавлена запись о посещении. Группа ['.$_POST['group_id'].']. Клиент ['.$user_id.']. День ['.$day.']. Месяц ['.$month.']. Год ['.$year.']. Статус ['.$newStatus.']');	
							}
						}
					}
				
					echo 'В журнал внесены изменения.';
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