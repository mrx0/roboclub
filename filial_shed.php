<?php

//add_shed_group.php
//

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				require 'config.php';
				
				$spr_shed_templs_arr = array();
				
				$filial = SelDataFromDB('spr_office', $_GET['id'], 'id');
				
				if ($filial != 0){
					echo '
						<div id="status">
							<header>
								<h2>График филиала < <a href="group.php?id='.$_GET['id'].'" class="ahref">'.$filial[0]['name'].'</a> ></h2>
							</header>';
					if ($filial[0]['close'] == '1'){
						echo '<span style="color:#EF172F;font-weight:bold;">Филиал ЗАКРЫТ</span>';
					}
					if ($filial[0]['close'] != '1'){
						
						$arr = array();
						$rez = array();
						
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						$time = time();
						//$query = "SELECT * FROM `spr_shed_templs` WHERE `id`=$id";
						$query = "SELECT * FROM `spr_shed_templs` WHERE `group` IN (SELECT `id` FROM `journal_groups` WHERE `filial`='{$_GET['id']}')";
						$res = mysql_query($query) or die(mysql_error());
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($rez, $arr);
							}
						}else
							$rez = 0;
						
						mysql_close();
						
						if ($rez !=0){

							$spr_shed_templs = array();
							
							foreach($rez as $value){
								$spr_shed_templs[$value['group']] = json_decode($value['template'], true);
							}
								
							var_dump($spr_shed_templs);
								
							echo '
									<div id="data">';
							echo '
										<div style="margin-bottom: 20px;">
											<div class="cellsBlock">
												<div class="cellTime" style="text-align: center; background-color:#CCC; min-width: 80px;"></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>ПН</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>ВТ</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>СР</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>ЧТ</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>ПТ</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>СБ</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>ВС</b></div>
											</div>';
											
											
							$spr_shed_times = SelDataFromDB('spr_shed_time', '', '');
							
							if ($spr_shed_times != 0){
									
								for ($i = 0; $i < count($spr_shed_times); $i++) {
									echo '
												<div class="cellsBlock cellsBlockHover" style="height: 70px;">
													<div class="cellTime" style="text-align: center; min-width: 80px;">
														<b>'.$spr_shed_times[$i]['from_time'].' - '.$spr_shed_times[$i]['to_time'].'</b>
													</div>';
													
									for ($j = 1; $j <= 7; $j++) {
										
										$bg_color = '';
										$checked = '';
										
										//if ($spr_shed_templs != 0){
										//	if ($spr_shed_templs_arr[$j]['time_id'] == $i+1){
										//		$bg_color = 'background: rgba(0, 255, 0, 0.5);';
										//		$checked = 'checked';
										//	}
										//}
										echo '
													<div class="cellTime" style="text-align: center; '.$bg_color.' min-width: 80px;">';
											echo '
													</div>';
									}
									
									echo '
												</div>';
								}

							}else{
								echo '<h3>Не заполнен справочник графика времени.</h3>';
							}
								
							echo '
								</div>
							</div>';
							
						}else{
							echo 'Нет расписания для этого филиала.';
						}


					}
				}else{
					echo '<h1>Такого филиала нет</h1><a href="filials.php">К филиалам</a>';
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