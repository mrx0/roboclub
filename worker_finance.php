<?php

//worker_finance.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($finance['see_all'] == 1) || $god_mode){
				
				include_once 'DBWork.php';
				include_once 'functions.php';

				$user = SelDataFromDB('spr_workers', $_GET['worker'], 'user');
				
				if ($user != 0){
					echo '
						<header style="margin-bottom: 5px;">
							<h1>История <a href="user.php?id='.$user[0]['id'].'" class="ahref">'.$user[0]['full_name'].'</a></h1>
						</header>';
						
					if (isset($_GET['m']) && isset($_GET['y'])){
						$year = $_GET['y'];
						$month = $_GET['m'];
					}else{
						$year = date("Y");
						$month = date("m");
					}
					
					include_once 'DBWork.php';
					include_once 'functions.php';
					include_once 'filter.php';
					include_once 'filter_f.php';
					
					//Массив с месяцами
					$monthsName = array(
					'01' => 'Январь',
					'02' => 'Февраль',
					'03' => 'Март',
					'04' => 'Апрель',
					'05' => 'Май',
					'06' => 'Июнь',
					'07'=> 'Июль',
					'08' => 'Август',
					'09' => 'Сентябрь',
					'10' => 'Октябрь',
					'11' => 'Ноябрь',
					'12' => 'Декабрь'
					);

					if ((int)$month - 1 < 1){
						$prev = '12';
						$pYear = $year - 1;
					}else{
						$pYear = $year;
						if ((int)$month - 1 < 10){
							$prev = '0'.strval((int)$month-1);
						}else{
							$prev = strval((int)$month-1);
						}
					}
					
					if ((int)$month + 1 > 12){
						$next = '01';
						$nYear = $year + 1;
					}else{
						$nYear = $year;
						if ((int)$month + 1 < 10){
							$next = '0'.strval((int)$month+1);
						}else{
							$next = strval((int)$month+1);
						}
					}
					
					echo '<span style="font-size: 80%; color: #CCC;">Сегодня: <a href="worker_finance.php?worker='.$user[0]['id'].'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>';	
					echo '
						<div id="data">		
							<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
									<a href="worker_finance.php?worker='.$user[0]['id'].'&m='.$prev.'&y='.$pYear.'" class="cellTime ahref" style="text-align: center;">
										<span style="font-weight: normal; font-size: 70%;"><< '.$monthsName[$prev].'<br>'.$pYear.'</span>
									</a>
									<div class="cellTime" style="text-align: center;">
										<span style="color: #2EB703">'.$monthsName[$month].'</span><br>'.$year.'
									</div>
									<a href="worker_finance.php?worker='.$user[0]['id'].'&m='.$next.'&y='.$nYear.'" class="cellTime ahref" style="text-align: center;">
										<span style="font-weight: normal; font-size: 70%;">'.$monthsName[$next].' >><br>'.$nYear.'</span>
									</a>
									
									<div class="cellTime" style="text-align: center; width: auto;">
										<select id="iWantThisMonth">';
					foreach ($monthsName as $val => $name){
						echo '
											<option value="'.$val.'" ', ($val == $month) ? 'selected' : '' ,'>'.$name.'</option>';
					}
					echo '
										</select>
										<input id="iWantThisYear" type="number" value="'.$year.'" min="2000" max="9999" size="4" style="width: 60px;">
										<i class="fa fa-check-square" style="font-size: 130%; color: green; cursor: pointer" onclick="iWantThisDate()"></i>
									</div>
								</li>
								<br>';
					
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 10px;">
							Проведённые занятия
						</li>';
					
					//Присутствовал
					$journal_was = 0;
					//Цена если был
					$need_cena = 0;
					//Общий долг
					$need_summ = 0;
					//Кол-во отсутствий
					$journal_x = 0;
					//Кол-во справок
					$journal_spr = 0;
					//Кол-во пробных
					$journal_try = 0;
					//
					$thisMonthRazn = 0;
					//
					$summRealFakt = 0;
					
					require 'config.php';	
					
					//Смотрим посещения
					$arr = array();
					$journal_uch = array();
					$journal_groups = array();
					$journal_groups_uch = array();
										
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$query = "SELECT * FROM `journal_user` WHERE `user_id` = '".$user[0]['id']."' AND  `month` = '{$month}' AND  `year` = '{$year}' ORDER BY `day`, `group_id` ASC";
					$res = mysql_query($query) or die(mysql_error());
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							$journal_uch[$arr['day']][$arr['group_id']][$arr['client_id']] = $arr['status'];
							$journal_groups_uch[$arr['group_id']][$arr['day']] = TRUE;
						}
					}else{
						$journal_uch = 0;
						$journal_groups_uch = 0;
					}
					//var_dump($journal_groups_uch);
					
					//Все группы тренера
					$journal_groups_temp = SelDataFromDB('journal_groups', $user[0]['id'], 'worker');
					
					if ($journal_groups_temp != 0){
						foreach ($journal_groups_temp as $groupArr){
							$journal_groups[$groupArr['id']] = $groupArr['name'];
						}
					}else{
						$journal_groups = 0;
					}
					
					if ($journal_uch != 0){
						//!!!Настройки цены, тут надо будет переделать
						$arr = array();
						$settings = array();
						
						mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
						mysql_select_db($dbName) or die(mysql_error()); 
						mysql_query("SET NAMES 'utf8'");
						$query = "SELECT * FROM `spr_settings` ORDER BY `time` DESC";
						$res = mysql_query($query) or die(mysql_error());
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								$settings[$arr['name']][$arr['time']] = $arr;
							}
						}else{
							$settings = 0;
						}
						
						//var_dump ($settings);
						
						
						echo '<li class="cellsBlock" style="width: auto; text-align: left; margin-bottom: 20px;">';
						
						//пробегаем по каждому дню
						foreach ($journal_uch as $day => $value) {
							$groupVal = '';
							
							//пробегаем по каждой группе
							foreach ($value as $group_id => $clients_statuses) {
								//Присутствовал
								$journal_was = 0;
								//Цена если был
								//$need_cena = 0;
								//Общий долг
								//$need_summ = 0;
								//Кол-во отсутствий
								$journal_x = 0;
								//Кол-во справок
								$journal_spr = 0;
								//Кол-во пробных
								$journal_try = 0;
								
								$summRealFakt ++;
								
								$backgroundColor = ' background-color: rgba(209, 255, 242, 0.5);';
								
								
								//Пробегаем по каждому клиенту
								foreach ($clients_statuses as $client_id => $status) {
									if ($status == 1){
										$journal_was ++;
									}elseif ($status == 2){
										$journal_x++;
									}elseif ($status == 3){
										$journal_spr++;
									}elseif ($status == 4){
										$journal_try++;
									}else{
									}
								}
								
								if ($journal_try + $journal_spr + $journal_was == 0){
									$backgroundColor = ' background-color: rgba(255, 135, 135, 0.59);';
								}
								if (($journal_try == 0) && ($journal_spr > 0) && ($journal_was == 0)){
									$backgroundColor = ' background-color: rgba(255, 135, 135, 0.59);';
								}
								if (($journal_try > 0) && ($journal_was == 0)){
									$backgroundColor = ' background-color: rgba(105, 170, 255, 0.5);';
								}
								
								$group = SelDataFromDB('journal_groups', $group_id, 'id');
								if ($group != 0){
									$groupValName = $group[0]['name'];	
								}else{
									$groupValName = 'ошибка группы';
								}
								
								$groupVal .= '
									<div class="cellName" style="display: inline-block !important; text-align: center; width: 100px; '.$backgroundColor.'">
										<div>
											<a href="journal.php?id='.$group_id.'&m='.$month.'&y='.$year.'" class="ahref">
												<div>'.$day.'.'.$month.'.'.$year.'</div>
												<div style="font-size: 70%; border-bottom: 1px dashed #CCC; margin-bottom: 5px;">'.$groupValName.'</div>
											</a>
										</div>
										<div style="font-size: 70%;"><b>Отметки</b></div>
										<div style="text-align: left">Было: '.($journal_was + $journal_try).'</div>
										<div style="text-align: left; font-size: 70%; margin-bottom: 5px;">(Пробные: '.$journal_try.')</div>
										<div style="text-align: left">Справка: '.$journal_spr.'</div>
										<div style="text-align: left">Не было: '.$journal_x.'</div>
										<!--<div style="font-size: 70%;">'.$need_cena.' руб.</div>-->
									</div>';
							}
							echo $groupVal;
						}
						echo '</li>';
						
					}else{
						echo '<h1>В этом месяце посещений не отмечено.</h1>';
					}
					
					echo '</ul>';
					
					echo '
						<ul style="margin-left: 0; margin-top: 20px; width: auto; padding: 7px;">
							<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 5px;">
								График занятий c отметками
							</li>
						</ul>';
					
					//Всего должно быть занятий + отметки о проведённых
					$spr_shed_times = SelDataFromDB('spr_shed_time', '', '');
							
					if ($spr_shed_times != 0){
						//var_dump($journal_groups);
						
						$summ_plan_zan = 0;
						$summ_fakt_zan = 0;
						
						if ($journal_groups != 0){
							
							foreach ($journal_groups as $group_id => $value){
								$plan_zan = 0;
								$fakt_zan = 0;
								$group = SelDataFromDB('journal_groups', $group_id, 'id');
								if ($group != 0){
									$groupValName = $group[0]['name'];	
								}else{
									$groupValName = 'ошибка группы';
								}
								echo '
									<ul style="margin-left: 6px; margin-bottom: 5px; border: 1px solid #CCC; width: auto; padding: 7px; background-color: rgba(204, 204, 204, 0.25);">
										<li class="cellsBlock" style="width: auto; text-align: right; margin-bottom: 5px;">
											Расписание группы <
											<a href="journal.php?id='.$group_id.'&m='.$month.'&y='.$year.'" class="ahref">
												'.$groupValName.'
											</a>>
											на '.$monthsName[$month].' '.$year.'
										</li>';
								$weekDays = array();
								echo '
										<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right; font-size: 90%; margin-bottom: 7px;">';
								
								//Шаблон графика для группы
								$spr_shed_templs  = SelDataFromDB('spr_shed_templs', $group_id, 'group');
								//var_dump($spr_shed_templs);
								$spr_shed_templs_arr = json_decode($spr_shed_templs[0]['template'], true);
								//var_dump($spr_shed_templs_arr);
								
								if ($spr_shed_templs != 0){
									for ($i = 1; $i <= count($spr_shed_templs_arr); $i++) {
										//var_dump($spr_shed_templs_arr[$i]['time_id']);
										if ($spr_shed_templs_arr[$i]['time_id'] != 0){
										//var_dump($i);
										//var_dump (getWeekDays(strval($i)));
											if(!isset($weekDays)){
												$weekDays = array_merge(getWeekDays(strval($i), $month, $year));
											}else{
												$weekDays = array_merge($weekDays, getWeekDays(strval($i), $month, $year));
											}
										}
									}
									
									if (!empty($weekDays)){
										sort($weekDays);
										$weekDaysArr = array();
										
										for ($i = 0; $i < count($weekDays); $i++) {
											$itemBgcolor = ' background-color: #FFF;';
											$plan_zan++;
											$summ_plan_zan++;
											
											$weekDaysArr = explode('.', $weekDays[$i]);
											//Если были занятия
											if ($journal_groups != 0){
												if (isset($journal_groups_uch[$group_id][$weekDaysArr[2]])){
													$itemBgcolor = ' background-color: rgba(0, 255, 0, 0.5);';
													
													$fakt_zan++;
													$summ_fakt_zan++;
												}
											}
											
											echo '<div class="cellTime" style="text-align: center; width: 70px; min-width: 70px; '.$itemBgcolor.'">'.$weekDaysArr[2].'</div>';
										}
									}
									
								}else{
									echo '<h3>Для группы не заполнено расписание</h3>';
								}
								echo '
										</li>';
								
								echo '
										<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
											По плану: <span style="font-weight: bold; font-size: 110%; color: rgb(9, 65, 198);">'.$plan_zan.'</span>
										</li>';
								echo '
										<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
											Факт: <span style="font-weight: bold; font-size: 110%; color: rgba(255, 0, 144, 0.9);">'.$fakt_zan.'</span>
										</li>';
								
								echo '
									</ul>';
							}
							
						}else{
							echo '<h3>У тренера нет групп</h3>';
						}
						
						//Всего за месяц
						
						echo '
							<ul style="margin-left: 6px; margin-top: 15px; border: 1px solid #CCC; width: auto; padding: 7px;">
								<li class="cellsBlock" style="width: auto; text-align: right; margin-bottom: 5px;">
									<b>Общее за месяц</b>
								</li>
								<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
									По плану: <span style="font-weight: bold; font-size: 110%; color: rgb(9, 65, 198);">'.$summ_plan_zan.'</span>
								</li>
								<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
									Факт: <span style="font-weight: bold; font-size: 110%; color: rgba(255, 0, 144, 0.9);">'.$summ_fakt_zan.'</span>
								</li>
								<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
									Занятия не в своих группах: <span style="font-weight: bold; font-size: 110%; color: rgba(255, 0, 144, 0.9);">'.($summRealFakt-$summ_fakt_zan).'</span>
								</li>';
						
						echo '
							</ul>';							

					}else{
						echo '<h3>Не заполнен справочник графика времени.</h3>';
					}

					echo '
							</li>
						</ul>';
					
					mysql_close();					
					
					echo '		
						</div>';
						
					echo '
						<script type="text/javascript">
							function iWantThisDate(){
								var iWantThisMonth = document.getElementById("iWantThisMonth").value;
								var iWantThisYear = document.getElementById("iWantThisYear").value;
								
								window.location.replace("worker_finance.php?worker='.$user[0]['id'].'&m="+iWantThisMonth+"&y="+iWantThisYear);
							}
						</script>';
				
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