<?php 

//add_meet_journal_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		if ($_POST){
			if (isset($_POST['group_id']) && isset($_POST['journalItems'])){
				include_once 'DBWork.php';
				//var_dump ($_SESSION);

                //разбираемся с правами
                $god_mode = FALSE;

                require_once 'permissions.php';
		
				$j_group = SelDataFromDB('journal_groups', $_POST['group_id'], 'group');
				
				//Определяем подмены
				$iReplace = FALSE;

                $msql_cnnct = ConnectToDB ();
				
				$query = "SELECT * FROM `journal_replacement` WHERE `group_id`='{$_POST['group_id']}' AND `user_id`='{$_SESSION['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

				if ($number != 0){
					$iReplace = TRUE;
				}else{
				}

				if ($j_group !=0){	
					
					if (($scheduler['see_all'] == 1) || (($scheduler['see_own'] == 1) && (($j_group[0]['worker'] == $_SESSION['id']) || ($iReplace))) || $god_mode){
						
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
							if (($scheduler['see_own'] == 1) && ($time - $dateForUpdate < 60*60*72)) {
								$foreverEdit = TRUE;
							}

                            $query = "SELECT `status` FROM `journal_user` WHERE `group_id` = '{$_POST['group_id']}' AND `client_id` = '{$client_id}' AND `day` = '{$day}' AND  `month` = '{$month}' AND  `year` = '{$year}'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                            $number = mysqli_num_rows($res);

							if ($number != 0){
								$res = mysqli_fetch_assoc($res);
								//var_dump ($res['status']);
								
								if ($newStatus == 0){
									//var_dump ('Надо удалить запись');
									if ($foreverEdit){
										if (($dateForUpdate > $time) && !$god_mode){
											$editError = TRUE;
											$editErrorText = 'Вы пытаетесь что-то сделать в будущем. Нельзя.';
											break;
										}else{

											$query = "DELETE FROM `journal_user` WHERE `group_id` = '{$_POST['group_id']}' AND `client_id` = '{$client_id}' AND `day` = '{$day}' AND  `month` = '{$month}' AND  `year` = '{$year}'";

                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

											AddLog ('0', $_SESSION['id'], '', 'Из журнала удалена запись о посещении. Группа ['.$_POST['group_id'].']. Ребёнок ['.$client_id.']. День ['.$day.']. Месяц ['.$month.']. Год ['.$year.']. Статус ['.$res['status'].']');
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
                                            if (($dateForUpdate > $time) && !$god_mode){
												$editError = TRUE;
												$editErrorText = 'Вы пытаетесь что-то сделать в будущем. Нельзя.';
												break;
											}else{
												$query = "UPDATE `journal_user` SET `status`='{$newStatus}'  WHERE `group_id` = '{$_POST['group_id']}' AND `client_id` = '{$client_id}' AND `day` = '{$day}' AND  `month` = '{$month}' AND  `year` = '{$year}'";

												$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

												AddLog ('0', $_SESSION['id'], '', 'В журнале изменена запись о посещении. Группа ['.$_POST['group_id'].']. Ребёнок ['.$client_id.']. День ['.$day.']. Месяц ['.$month.']. Год ['.$year.']. Статус ['.$newStatus.']');
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
                                        if (($dateForUpdate > $time) && !$god_mode){
											$editError = TRUE;
											$editErrorText = 'Вы пытаетесь что-то сделать в будущем. Нельзя.';
											break;
										}else{
											$query = "INSERT INTO `journal_user` (`client_id`, `group_id`, `user_id`, `day`, `month`, `year`, `status`) VALUES ('{$client_id}', '{$_POST['group_id']}', '{$user_id}', '{$day}', '{$month}', '{$year}', '{$newStatus}')";

											$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

											AddLog ('0', $_SESSION['id'], '', 'В журнал добавлена запись о посещении. Группа ['.$_POST['group_id'].']. Ребёнок ['.$client_id.']. Добавил ['.$user_id.']. День ['.$day.']. Месяц ['.$month.']. Год ['.$year.']. Статус ['.$newStatus.']');
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