<?php

//journal.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($scheduler['see_all'] == 1) || $god_mode){
				include_once 'DBWork.php';
				include_once 'functions.php';
				include_once 'filter.php';
				include_once 'filter_f.php';
				if (isset($_GET['id']) && isset($_GET['d']) && isset($_GET['m']) && isset($_GET['y'])){
					$j_group = SelDataFromDB('journal_groups', $_GET['id'], 'group');
					//var_dump ($j_group);
					
					require 'config.php';	
					
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					
					if ($j_group != 0){
						if (($scheduler['edit'] == 1) || $god_mode){
							echo '
								<header style="margin-bottom: 5px;">
									<h1>Редактирование отметки занятий < <a href="group.php?id='.$_GET['id'].'" class="ahref">'.$j_group[0]['name'].'</a> ></h1>
									<span style="color: #575757CC;">Дата: <b style="color: green">'.$_GET['d'].'.'.$_GET['m'].'.'.$_GET['y'].'</b></span>
								</header>';
									
							if ($j_group[0]['close'] == '1'){
								echo '<span style="color:#EF172F;font-weight:bold;">ГРУППА ЗАКРЫТА</span>';
							}
							
							$journal_uch = array();
							$arr = array();
												
							$query = "SELECT * FROM `journal_user` WHERE `group_id` = '{$_GET['id']}' AND  `day` = '{$_GET['d']}' AND  `month` = '{$_GET['m']}' AND  `year` = '{$_GET['y']}'";
							$res = mysql_query($query) or die(mysql_error());
							$number = mysql_num_rows($res);
							if ($number != 0){
								while ($arr = mysql_fetch_assoc($res)){
									array_push($journal_uch, $arr);
								}
							}else{
								$journal_uch = 0;
							}
							//var_dump($journal_uch);
							
							if ($journal_uch != 0){

								echo '<span style="font-size: 80%; color: rgb(125, 125, 125);">Тренер: <a href="user.php?id='.$j_group[0]['worker'].'" class="ahref">'.WriteSearchUser('spr_workers', $j_group[0]['worker'], 'user').'</a></span><br>';	
								
								echo '
									<div id="data">		
										<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">
											<li class="cellsBlock" style="font-weight: bold; width: auto;">	
												<div class="cellPriority" style="text-align: center"></div>
												<div class="cellFullName" style="text-align: center">ФИО клиента</div>
												<div class="cellTime" style="text-align: center; width: 70px; min-width: 70px;">
													<a href="journal.php?id='.$_GET['id'].'" class="ahref" style="text-align: center; font-size: 120%; color: green" title="Журнал группы"><i class="fa fa-calendar"></i></a>
												</div>
												<div class="cellFullName" style="text-align: center">Кто отметил</div>
											</li>';
						
								foreach($journal_uch as $value){
									if ($value['status'] == 1){
										$backgroundColor = "background-color: rgba(0, 255, 0, 0.5)";
										$journal_ico = '<i class="fa fa-check"></i>';

									}elseif($value['status'] == 2){
										$backgroundColor = "background-color: rgba(255, 0, 0, 0.5)";
										$journal_ico = '<i class="fa fa-times"></i>';

									}elseif($value['status'] == 3){
										$backgroundColor = "background-color: rgba(255, 252, 0, 0.5)";
										$journal_ico = '<i class="fa fa-file-text-o"></i>';

									}elseif($value['status'] == 4){
										$backgroundColor = "background-color: rgba(0, 201, 255, 0.5)";
										$journal_ico = '<i class="fa fa-check"></i>';
										
									}else{
										$backgroundColor = '';
										$journal_ico = '-';

									}
									echo '
										<li class="cellsBlock" style="font-weight: bold; width: auto;">	
											<div class="cellPriority" style="text-align: center"></div>
											<div class="cellFullName" style=""><a href="client.php?id='.$value['client_id'].'" class="ahref">'.WriteSearchUser('spr_clients', $value['client_id'], 'user_full').'</a></div>
											<div class="cellTime" style="text-align: center; width: 70px; min-width: 70px; '.$backgroundColor.'">'.$journal_ico.'</div>
											<div class="cellFullName" style="text-align: right"><a href="user.php?id='.$value['user_id'].'" class="ahref">'.WriteSearchUser('spr_workers', $value['user_id'], 'user').'</a></div>
										</li>';
								}
								echo '</ul>';
								
								$j_workers = SelDataFromDB('spr_workers', '', '');
								
								echo '
									<span style="font-size: 80%; color: rgb(150, 150, 150);">
										Здесь вы можете поменять того, кто внёс данные отметки в журнал.<br>Учтите, что новый тренер изменится для всех указанных записей.
									</span>
									<br>
									<br>';
									
								echo '
											<div class="cellsBlock2">
												<div class="cellLeft">Тренер</div>
												<div class="cellRight">';
									echo '
													<select name="worker" id="worker">
														<option value="0">Нет тренера</option>';
									if ($j_workers != 0){
										for ($i=0;$i<count($j_workers);$i++){
											echo '<option value="'.$j_workers[$i]['id'].'"', $j_workers[$i]['id'] == $j_group[0]['worker'] ? ' selected ' : '' ,'>'.$j_workers[$i]['name'].'</option>';
										}
									}
									echo '
													</select>
													<label id="worker_error" class="error">';

								echo '
												</div>
											</div><br>';
											
								echo '
									<div id="errror"></div>
									<input type="button" class="b" value="Применить" onclick=Ajax_change_meets()>
								</div>';

								echo '											
									<script type="text/javascript">			
										function Ajax_change_meets() {
											
											ajax({
												url: "edit_meet_journal_f.php",
												method: "POST",
												
												data:
												{
													group_id: '.$_GET['id'].',
													worker:document.getElementById("worker").value,
													
													d: '.$_GET['d'].',
													m: '.$_GET['m'].',
													y: '.$_GET['y'].',
													
													session_id: '.$_SESSION['id'].'
												},
												success: function(req){
													//document.getElementById("errror").innerHTML = req;
													alert(req);
													//document.getElementById("errror").innerHTML = "";
													location.reload(true);
												}
											});
										}
										
									</script>';
								
								
							}else{
								echo '<h3>В этой группе в тот день не было отметок</h3>';
							}
						}else{
							echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
						}
					}else{
						echo '<h1>Не найдена такая группа</h1><a href="index.php">Вернуться на главную</a>';
					}
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>