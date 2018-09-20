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
				//var_dump ($_SESSION);
		
				$god_mode = FALSE;
		
				if ($_SESSION['permissions'] == '777'){
					$god_mode = TRUE;
				}else{
					//Получили список прав
					$permissions = SelDataFromDB('spr_permissions', $_SESSION['permissions'], 'id');	
					//var_dump($permissions);
				}
				if (!$god_mode){
					if ($permissions != 0){
						$scheduler = json_decode($permissions[0]['scheduler'], true);
					}
				}else{
					//Видимость
					$scheduler['see_all'] = 0;
					$scheduler['see_own'] = 0;
					//
					$scheduler['add_new'] = 0;
					$scheduler['add_own'] = 0;
					//
					$scheduler['edit'] = 0;
					//
					$scheduler['close'] = 0;
					//
					$scheduler['reopen'] = 0;
					//
					$scheduler['add_worker'] = 0;
					//
					
				}
		
				$j_group = SelDataFromDB('journal_groups', $_POST['group_id'], 'group');
				
				//Определяем подмены
				$iReplace = FALSE;
				
				require 'config.php';	
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$query = "SELECT * FROM `journal_replacement` WHERE `group_id`='{$_POST['group_id']}' AND `user_id`='{$_SESSION['id']}'";
				$res = mysql_query($query) or die(mysql_error());
				$number = mysql_num_rows($res);
				if ($number != 0){
					$iReplace = TRUE;
				}else{
				}
				mysql_close();	
				
				if ($j_group !=0){	
					
					if (($scheduler['see_all'] == 1) || (($scheduler['see_own'] == 1) && (($j_group[0]['worker'] == $_POST['session_id']) || ($iReplace))) || $god_mode){
						
						$tempJSON = json_decode($_POST['journalItems'], true);
						//var_dump ($tempJSON);
						
						require 'config.php';
						
						foreach($tempJSON as $key => $newStatus){
							$tempArr = explode("_", $key);
							$client_id = $tempArr[0];
							if ($iReplace){
								$user_id = $_SESSION['id'];
							}else{
								$user_id = $j_group[0]['worker'];
							}
							$dateArr = explode(".", $tempArr[1]);
							$day = $dateArr[2];
							$month = $dateArr[1];
							$year = $dateArr[0];
							//var_dump($_POST['group_id'].' + '.$client_id.' + '.$day.' + '.$month.' + '.$year.' -> '.$newStatus.'<br>');
							
							$time = time();
							
							$foreverEdit = FALSE;
							
							$editError = FALSE;
							$editErrorText = '';
							
							$dateForUpdate = strtotime($day.'.'.$month.'.'.$year);
							
							if (($scheduler['see_all'] == 1) || $god_mode){
								$foreverEdit = TRUE;
							}
							
							//!!! время на изменение журнала
							if (($scheduler['see_own'] == 1) && ($time - $dateForUpdate < 60*60*48)) {
								$foreverEdit = TRUE;
							}
							
							mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
							mysql_select_db($dbName) or die(mysql_error()); 
							mysql_query("SET NAMES 'utf8'");
							$query = "SELECT `status` FROM `journal_user` WHERE `group_id` = '{$_POST['group_id']}' AND `client_id` = '{$client_id}' AND `day` = '{$day}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
							$res = mysql_query($query) or die('1->'.mysql_error().'->'.$query);
							$number = mysql_num_rows($res);
							if ($number != 0){
								$res = mysql_fetch_assoc($res);
								mysql_close();
								//var_dump ($res['status']);
								
								if ($newStatus == 0){
									//var_dump ('Надо удалить запись');
									if ($foreverEdit){
										if ($dateForUpdate > $time){
											$editError = TRUE;
											$editErrorText = 'Вы пытаетесь что-то сделать в будущем. Нельзя.';
											break;
										}else{
											mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
											mysql_select_db($dbName) or die(mysql_error()); 
											mysql_query("SET NAMES 'utf8'");
											$query = "DELETE FROM `journal_user` WHERE `group_id` = '{$_POST['group_id']}' AND `client_id` = '{$client_id}' AND `day` = '{$day}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
											mysql_query($query) or die('2->'.mysql_error().'->'.$query);
											mysql_close();
											AddLog ('0', $_POST['session_id'], '', 'Из журнала удалена запись о посещении. Группа ['.$_POST['group_id'].']. Ребёнок ['.$client_id.']. День ['.$day.']. Месяц ['.$month.']. Год ['.$year.']. Статус ['.$res['status'].']');
										}
									}else{
										$editError = TRUE;
										$editErrorText = 'Вы пытаетесь удалить одну из записей задним числом. Нельзя.';
										break;
									}
								}else{
									//var_dump ('Надо обновить запись');
									
									if ($newStatus != $res['status']){
										if ($foreverEdit){
											if ($dateForUpdate > $time){
												$editError = TRUE;
												$editErrorText = 'Вы пытаетесь что-то сделать в будущем. Нельзя.';
												break;
											}else{
												mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
												mysql_select_db($dbName) or die(mysql_error()); 
												mysql_query("SET NAMES 'utf8'");
												$query = "UPDATE `journal_user` SET `status`='{$newStatus}'  WHERE `group_id` = '{$_POST['group_id']}' AND `client_id` = '{$client_id}' AND `day` = '{$day}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
												mysql_query($query) or die('3->'.mysql_error().'->'.$query);
												mysql_close();
												AddLog ('0', $_POST['session_id'], '', 'В журнале изменена запись о посещении. Группа ['.$_POST['group_id'].']. Ребёнок ['.$client_id.']. День ['.$day.']. Месяц ['.$month.']. Год ['.$year.']. Статус ['.$newStatus.']');
											}
										}else{
											$editError = TRUE;
											$editErrorText = 'Вы пытаетесь изменить одну из записей задним числом. Нельзя.';
											break;
										}
									}
								}
							}else{
								if ($newStatus != 0){
									//var_dump ('Надо добавить запись');
									
									if ($foreverEdit){
										if ($dateForUpdate > $time){
											$editError = TRUE;
											$editErrorText = 'Вы пытаетесь что-то сделать в будущем. Нельзя.';
											break;
										}else{
											mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
											mysql_select_db($dbName) or die(mysql_error()); 
											mysql_query("SET NAMES 'utf8'");
											$query = "INSERT INTO `journal_user` (`client_id`, `group_id`, `user_id`, `day`, `month`, `year`, `status`) VALUES ('{$client_id}', '{$_POST['group_id']}', '{$user_id}', '{$day}', '{$month}', '{$year}', '{$newStatus}')";
											mysql_query($query) or die('4->'.mysql_error().'->'.$query);
											mysql_close();
											AddLog ('0', $_POST['session_id'], '', 'В журнал добавлена запись о посещении. Группа ['.$_POST['group_id'].']. Ребёнок ['.$client_id.']. Добавил ['.$user_id.']. День ['.$day.']. Месяц ['.$month.']. Год ['.$year.']. Статус ['.$newStatus.']');
										}
									}else{
										$editError = TRUE;
										$editErrorText = 'Вы пытаетесь добавить запись задним числом. Нельзя.';
										break;
									}
								}
							}
						}
						if ($editError){
							echo $editErrorText;
						}else{
							echo 'В журнал внесены изменения.';
						}
					}else{
						echo 'Не хватает прав доступа.';
					}
				}else{
					echo 'Странно, но не найдена такая группа';
				}
			}else{
				echo 'Что-то пошло не так';
			}
		}else{
			echo 'Что-то пошло не так';
		}
	}
?>