<?php

//journal.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
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
				
				
				$j_group = SelDataFromDB('journal_groups', $_GET['id'], 'group');
				//var_dump ($j_group);
				
				//Определяем подмены
				$iReplace = FALSE;
				
				require 'config.php';	
				
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				
				$query = "SELECT * FROM `journal_replacement` WHERE `group_id`='{$_GET['id']}' AND `user_id`='{$_SESSION['id']}'";
				$res = mysql_query($query) or die(mysql_error());
				$number = mysql_num_rows($res);
				if ($number != 0){
					$iReplace = TRUE;
				}else{
				}
				//mysql_close();	
				
				if ($j_group != 0){
					if (($scheduler['see_all'] == 1) || (($scheduler['see_own'] == 1) && (($j_group[0]['worker'] == $_SESSION['id']) || ($iReplace))) || $god_mode){
						echo '
							<header style="margin-bottom: 5px;">
								<h1>Журнал посещений< <a href="group.php?id='.$_GET['id'].'" class="ahref">'.$j_group[0]['name'].'</a> ></h1>
							</header>';
								
						if ($j_group[0]['close'] == '1'){
							echo '<span style="color:#EF172F;font-weight:bold;">ГРУППА ЗАКРЫТА</span>';
						}else{
							
							require 'config.php';
							
							$weekDays = array();
							
							$spr_shed_times = SelDataFromDB('spr_shed_time', '', '');
							
							if ($spr_shed_times != 0){
								
								//Шаблон графика для группы
								$spr_shed_templs  = SelDataFromDB('spr_shed_templs', $_GET['id'], 'group');
								//var_dump($spr_shed_templs);
								$spr_shed_templs_arr = json_decode($spr_shed_templs[0]['template'], true);
								//var_dump($spr_shed_templs_arr);
								
								if ($spr_shed_templs != 0){
									
									if (isset($_GET['m']) && isset($_GET['y'])){
										$year = $_GET['y'];
										$month = $_GET['m'];
									}else{
										$year = date("Y");
										$month = date("m");
									}
									
									echo '
										<!--<p style="margin: 5px 0; padding: 1px; font-size:80%;">
											Быстрый поиск: 
											<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
										</p>-->';
									
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
									
									//var_dump(!empty($weekDays));
									
									if (!empty($weekDays)){
										
										//var_dump($weekDays);
										sort($weekDays);
										//var_dump($weekDays);
										$weekDaysArr = array();

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
										if (($scheduler['see_all'] == 1) || ($god_mode)){
											echo '<span style="font-size: 80%; color: rgb(125, 125, 125);">Тренер: <a href="user.php?id='.$j_group[0]['worker'].'" class="ahref">'.WriteSearchUser('spr_workers', $j_group[0]['worker'], 'user').'</a></span><br>';	
										}										
										echo '<span style="font-size: 80%; color: rgb(125, 125, 125);">Сегодня: <a href="journal.php?id='.$_GET['id'].'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>';	

										echo '
											<div id="data">		
												<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">
													<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
														<a href="journal.php?id='.$_GET['id'].'&m='.$prev.'&y='.$pYear.'" class="cellTime ahref" style="text-align: center;">
															<span style="font-weight: normal; font-size: 70%;"><< '.$monthsName[$prev].'<br>'.$pYear.'</span>
														</a>
														<div class="cellTime" style="text-align: center;">
															<span style="color: #2EB703">'.$monthsName[$month].'</span><br>'.$year.'
														</div>
														<a href="journal.php?id='.$_GET['id'].'&m='.$next.'&y='.$nYear.'" class="cellTime ahref" style="text-align: center;">
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
													<br>
													
													
													
													<li class="cellsBlock" style="font-weight: bold; width: auto;">	
														<div class="cellPriority" style="text-align: center"></div>
														<div class="cellFullName" style="text-align: center">ФИО</div>
														<!--<div class="cellCosmAct" style="text-align: center"><i class="fa fa-rub"></i></div>-->';
										
										for ($i = 0; $i < count($weekDays); $i++) {
											$weekDaysArr = explode('.', $weekDays[$i]);
											if (($scheduler['see_all'] == 1) || $god_mode){
												echo '
													<a class="cellTime ahref cellsBlockHover" href="meets.php?id='.$_GET['id'].'&d='.$weekDaysArr[2].'&m='.$month.'&y='.$year.'" style="text-align: center; width: 70px; min-width: 70px;">'.$weekDaysArr[2].'</a>';
											}else{
												echo '<div class="cellTime" style="text-align: center; width: 70px; min-width: 70px;">'.$weekDaysArr[2].'</div>';
											}
										}										
					
										echo '				
													</li>';
													
										$uch_arr = SelDataFromDB('spr_clients', $_GET['id'], 'client_group');
										//var_dump($uch_arr);
										
										$journal_uch = array();										
										
										if ($uch_arr != 0){	

											//количество допущенных
											$dopusk = 0;
											
											//!!!Настройки цены, тут надо будет переделать
											$arr = array();
											$settings = array();
											
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

											$arr = array();
											
											$query = "SELECT `client_id`, `day`, `status` FROM `journal_user` WHERE `group_id` = '{$_GET['id']}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
											$res = mysql_query($query) or die(mysql_error());
											$number = mysql_num_rows($res);
											if ($number != 0){
												while ($arr = mysql_fetch_assoc($res)){
													$journal_uch[$arr['client_id']][$arr['day']] = $arr['status'];
												}
											}
											//var_dump($journal_uch);

											for ($i = 0; $i < count($uch_arr); $i++) {

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
												
												echo '
													<li class="cellsBlock cellsBlockHover" style="font-weight: bold; width: auto;">	
														<div class="cellPriority" style="text-align: center;"></div>
														<a href="client.php?id='.$uch_arr[$i]['id'].'" class="cellFullName ahref" id="4filter" style="position: relative;">'.$uch_arr[$i]['full_name'];
														
												$query = "SELECT * FROM `comments` WHERE `dtable`='spr_clients' AND `parent`='{$uch_arr[$i]['id']}'";
												$res = mysql_query($query) or die(mysql_error());
												$number = mysql_num_rows($res);
												if ($number != 0){
													echo '
															<div style="position: absolute; top: 0; right: 3px; color: rgb(247, 188, 50);">
																<i class="fa fa-commenting" title="Есть комментарии"></i>
															</div>';
												}else{
												}

												echo '
														</a>';
												
												$weekDaysArr = array();
												//var_dump($weekDays);
												
												//бегаем по дням
												for ($j = 0; $j < count($weekDays); $j++) {
													$weekDaysArr = explode('.', $weekDays[$j]);
													
													//$timeForPay = strtotime($weekDaysArr[2].'.'.$weekDaysArr[1].'.'.$weekDaysArr[0].' 23:59:59');
													
													if (isset($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]])){
														if ($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]] == 1){
															$backgroundColor = "background-color: rgba(0, 255, 0, 0.5)";
															$journal_ico = '<i class="fa fa-check"></i>';
															$journal_value = 1;
															
															//$journal_was++;
															
															/*foreach($settings['cena1'] as $key_time => $value_time_arr){
																$need_cena = 0;
																
																//если только одно значение 
																if (count($settings['cena1']) == 1){
																	$need_cena = $settings['cena1'][$key_time]['value'];
																	$need_summ += $settings['cena1'][$key_time]['value'];
																}else{
																	//Если указанное в посещении время меньше чем текущее в цикле
																	if ($timeForPay < $key_time){
																		continue;
																	}else{
																		$need_cena = $settings['cena1'][$key_time]['value'];
																		$need_summ += $settings['cena1'][$key_time]['value'];
																		break;
																	}
																}
															}*/

														}elseif($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]] == 2){
															$backgroundColor = "background-color: rgba(255, 0, 0, 0.5)";
															$journal_ico = '<i class="fa fa-times"></i>';
															$journal_value = 2;
								
															//$journal_x++;
															
															/*foreach($settings['cena1'] as $key_time => $value_time_arr){
																$need_cena = 0;
																
																//если только одно значение 
																if (count($settings['cena1']) == 1){
																	$need_cena = $settings['cena1'][$key_time]['value'];
																	$need_summ += $settings['cena1'][$key_time]['value'];
																}else{
																	//Если указанное в посещении время меньше чем текущее в цикле
																	if ($timeForPay < $key_time){
																		continue;
																	}else{
																		$need_cena = $settings['cena1'][$key_time]['value'];
																		$need_summ += $settings['cena1'][$key_time]['value'];
																		break;
																	}
																}
															}*/
															
														}elseif($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]] == 3){
															$backgroundColor = "background-color: rgba(255, 252, 0, 0.5)";
															$journal_ico = '<i class="fa fa-file-text-o"></i>';
															$journal_value = 3;
															
															//$journal_spr++;
															
															//$need_cena = 0;
															
														}elseif($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]] == 4){
															$backgroundColor = "background-color: rgba(0, 201, 255, 0.5)";
															$journal_ico = '<i class="fa fa-check"></i>';
															$journal_value = 4;
								
															$journal_try++;
															
															/*foreach($settings['cena2'] as $key_time => $value_time_arr){
																$need_cena = 0;
																
																//если только одно значение 
																if (count($settings['cena2']) == 1){
																	$need_cena = $settings['cena2'][$key_time]['value'];
																	$need_summ += $settings['cena2'][$key_time]['value'];
																}else{
																	//Если указанное в посещении время меньше чем текущее в цикле
																	if ($timeForPay < $key_time){
																		continue;
																	}else{
																		$need_cena = $settings['cena2'][$key_time]['value'];
																		$need_summ += $settings['cena2'][$key_time]['value'];
																		break;
																	}
																}
															}*/
															
														}else{
															$backgroundColor = '';
															$journal_ico = '-';
															$journal_value = 0;
														}
														
														unset($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]]);
														if (empty($journal_uch[$uch_arr[$i]['id']])){
															unset($journal_uch[$uch_arr[$i]['id']]);
														}
														
													}else{
														$backgroundColor = '';
														$journal_ico = '-';
														$journal_value = 0;
													}
													echo '<div id="'.$uch_arr[$i]['id'].'_'.$weekDays[$j].'" class="cellTime journalItem" style="text-align: center; width: 70px; min-width: 70px; '.$backgroundColor.'" onclick="JournalEdit('.$uch_arr[$i]['id'].', \''.$weekDays[$j].'\');">'.$journal_ico.'</div>';
													echo '<input type="hidden" id="'.$uch_arr[$i]['id'].'_'.$weekDays[$j].'_value" class="journalItemVal" value="'.$journal_value.'">';
												}	
												
												
												//!!!Надо посмотреть все занятия во всех группах, вдруг был в другой. Двойная работа... так бывает
												
												$journal_uch_all = array();
												$arr = array();
												
												$query = "SELECT * FROM `journal_user` WHERE `client_id` = '".$uch_arr[$i]['id']."' AND  `month` = '{$month}' AND  `year` = '{$year}' ORDER BY `day` ASC";
												$res = mysql_query($query) or die(mysql_error());
												$number = mysql_num_rows($res);
												if ($number != 0){
													while ($arr = mysql_fetch_assoc($res)){
														//var_dump($arr);
														array_push($journal_uch_all, $arr);
													}
												}else{
													$journal_uch_all = 0;
												}
												//var_dump($journal_uch_all);
												
												if ($journal_uch_all != 0){
													foreach ($journal_uch_all as $key => $value) {
														
														$timeForPay = strtotime($value['day'].'.'.$value['month'].'.'.$value['year'].' 23:59:59');
														
														if ($value['status'] == 1){
															$journal_was++;
															
															foreach($settings['cena1'] as $key_time => $value_time_arr){
																$need_cena = 0;
																
																//если только одно значение 
																if (count($settings['cena1']) == 1){
																	$need_cena = $settings['cena1'][$key_time]['value'];
																	$need_summ += $settings['cena1'][$key_time]['value'];
																}else{
																	//Если указанное в посещении время меньше чем текущее в цикле
																	if ($timeForPay < $key_time){
																		continue;
																	}else{
																		$need_cena = $settings['cena1'][$key_time]['value'];
																		$need_summ += $settings['cena1'][$key_time]['value'];
																		break;
																	}
																}
															}
															
														}elseif($value['status'] == 2){

															$journal_x++;

															foreach($settings['cena1'] as $key_time => $value_time_arr){
																$need_cena = 0;
																
																//если только одно значение 
																if (count($settings['cena1']) == 1){
																	$need_cena = $settings['cena1'][$key_time]['value'];
																	$need_summ += $settings['cena1'][$key_time]['value'];
																}else{
																	//Если указанное в посещении время меньше чем текущее в цикле
																	if ($timeForPay < $key_time){
																		continue;
																	}else{
																		$need_cena = $settings['cena1'][$key_time]['value'];
																		$need_summ += $settings['cena1'][$key_time]['value'];
																		break;
																	}
																}
															}
															
														}elseif($value['status'] == 3){

															$journal_spr++;
															
															$need_cena = 0;
															
														}elseif($value['status'] == 4){

															$journal_try++;

															foreach($settings['cena2'] as $key_time => $value_time_arr){
																$need_cena = 0;
																
																//если только одно значение 
																if (count($settings['cena2']) == 1){
																	$need_cena = $settings['cena2'][$key_time]['value'];
																	$need_summ += $settings['cena2'][$key_time]['value'];
																}else{
																	//Если указанное в посещении время меньше чем текущее в цикле
																	if ($timeForPay < $key_time){
																		continue;
																	}else{
																		$need_cena = $settings['cena2'][$key_time]['value'];
																		$need_summ += $settings['cena2'][$key_time]['value'];
																		break;
																	}
																}
															}
															
														}else{
															$backgroundColor = '';
															$journal_ico = '-';
															$journal_value = 0;
														}

													}
												}else{
												}
												
												//Смотрим оплаты
												$arr = array();
												$journal_fin = array();
												
												//Общая внесённая сумма
												$summa = 0;

												$query = "SELECT * FROM `journal_finance` WHERE `month` = '{$month}' AND  `year` = '{$year}' AND `client`='".$uch_arr[$i]['id']."'";
												$res = mysql_query($query) or die(mysql_error());
												$number = mysql_num_rows($res);
												if ($number != 0){
													while ($arr = mysql_fetch_assoc($res)){
														array_push($journal_fin, $arr);
													}
												}else{
													$journal_fin = 0;
												}
												//var_dump($journal_fin);

												if ($journal_fin != 0){
													for ($k = 0; $k < count($journal_fin); $k++) { 
														if ($journal_fin[$k]['type'] != 2){
															$summa += $journal_fin[$k]['summ'];
														}
													}
												}else{
												}
												
												//Смотрим переносы ИЗ ЭТОГО месяца
												$arr = array();
												$journal_rem_last = array();
												
												//Общая внесённая сумма
												$summaRemLast = 0;

												$query = "SELECT * FROM `journal_finance_rem` WHERE `last_month` = '{$month}' AND `last_year` = '{$year}' AND `client`='".$uch_arr[$i]['id']."'";
												$res = mysql_query($query) or die(mysql_error());
												$number = mysql_num_rows($res);
												if ($number != 0){
													while ($arr = mysql_fetch_assoc($res)){
														array_push($journal_rem_last, $arr);
													}
												}else{
													$journal_rem_last = 0;
												}
												//var_dump($journal_rem_last);

												if ($journal_rem_last != 0){
													/*echo '
														<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 10px; margin-top: 10px;">
															Перерасчет <b>из</b> этого месяца в другой
														</li>';
													echo '
														<li class="cellsBlock" style="font-weight: bold; width: auto;">	
															<div class="cellName" style="text-align: center">Дата</div>
															<div class="cellName" style="text-align: center">Имя</div>
															<div class="cellName" style="text-align: center">Из месяц/год</div>
															<div class="cellName" style="text-align: center">В месяц/год</div>
															<div class="cellTime" style="text-align: center">Сумма</div>
														</li>';*/
													for ($k = 0; $k < count($journal_rem_last); $k++) {
														
														if ($journal_rem_last[$k]['summ'] < 0){
															/*$SummColor = 'color: rgba(255, 0, 0, 0.86);';
															$znak = 'долг:<br>';
															$znak2 = '';*/
														}elseif($journal_rem_last[$k]['summ'] > 0){
															/*$SummColor = 'color: rgba(9, 198, 31, 0.92);';
															$znak = 'избыток:<br>';
															$znak2 = '+';*/
														}else{
														}
														
														/*echo '
															<li class="cellsBlock cellsBlockHover" style="width: auto;">	
																<a href="remove.php?id='.$journal_rem_last[$k]['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', $journal_rem_last[$k]['create_time']).'</a>
																<a href="client.php?id='.$journal_rem_last[$k]['client'].'" class="cellName ahref" id="4filter">'.WriteSearchUser('spr_clients', $journal_rem_last[$k]['client'], 'user').'</a>
																<div class="cellName" style="text-align: center">'.$monthsName[$journal_rem_last[$k]['last_month']].'/'.$journal_rem_last[$k]['last_year'].'</div>
																<div class="cellName" style="text-align: center">'.$monthsName[$journal_rem_last[$k]['month']].'/'.$journal_rem_last[$k]['year'].'</div>
																<div class="cellTime" style="text-align: center; font-size: 110%;">'.$znak.' <span style="'.$SummColor.' font-weight: bold;">'.$znak2.$journal_rem_last[$k]['summ'].'</span></div>
															</li>';*/
													
														//Если перерасчет сделан ИЗ этого месяца
														if ($month == $journal_rem_last[$k]['month']){				
															$summaRemLast -= $journal_rem_last[$k]['summ'];
														}else{
															$summaRemLast += $journal_rem_last[$k]['summ'];
														}
													}
												}else{
												}
											
												//Смотрим переносы В ЭТОТ месяца
												$arr = array();
												$journal_rem = array();
												
												//Общая внесённая сумма
												$summaRem = 0;

												$query = "SELECT * FROM `journal_finance_rem` WHERE `month` = '{$month}' AND `year` = '{$year}' AND `client`='".$uch_arr[$i]['id']."'";
												$res = mysql_query($query) or die(mysql_error());
												$number = mysql_num_rows($res);
												if ($number != 0){
													while ($arr = mysql_fetch_assoc($res)){
														array_push($journal_rem, $arr);
													}
												}else{
													$journal_rem = 0;
												}
												//var_dump($journal_rem);

												if ($journal_rem != 0){
													/*echo '
														<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 10px; margin-top: 10px;">
															Перерасчет <b>в</b> этот месяца из другого
														</li>';
													echo '
														<li class="cellsBlock" style="font-weight: bold; width: auto;">	
															<div class="cellName" style="text-align: center">Дата</div>
															<div class="cellName" style="text-align: center">Имя</div>
															<div class="cellName" style="text-align: center">Из месяц/год</div>
															<div class="cellName" style="text-align: center">В месяц/год</div>
															<div class="cellTime" style="text-align: center">Сумма</div>
														</li>';*/
													for ($k = 0; $k < count($journal_rem); $k++) { 
													
														if ($journal_rem[$k]['summ'] < 0){
															/*$SummColor = 'color: rgba(255, 0, 0, 0.86);';
															$znak = 'долг:<br>';
															$znak2 = '';*/
														}elseif($journal_rem[$k]['summ'] > 0){
															/*$SummColor = 'color: rgba(9, 198, 31, 0.92);';
															$znak = 'избыток:<br>';
															$znak2 = '+';*/
														}else{
														}
													
														/*echo '
															<li class="cellsBlock cellsBlockHover" style="width: auto;">	
																<a href="remove.php?id='.$journal_rem[$k]['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', $journal_rem[$k]['create_time']).'</a>
																<a href="client.php?id='.$journal_rem[$k]['client'].'" class="cellName ahref" id="4filter">'.WriteSearchUser('spr_clients', $journal_rem[$k]['client'], 'user').'</a>
																<div class="cellName" style="text-align: center">'.$monthsName[$journal_rem[$k]['last_month']].'/'.$journal_rem[$k]['last_year'].'</div>
																<div class="cellName" style="text-align: center">'.$monthsName[$journal_rem[$k]['month']].'/'.$journal_rem[$k]['year'].'</div>
																<div class="cellTime" style="text-align: center; font-size: 110%;">'.$znak.' <span style="'.$SummColor.' font-weight: bold;">'.$znak2.$journal_rem[$k]['summ'].'</span></div>
															</li>';*/
														//Если перерасчет сделан В этот месяц
														if ($month == $journal_rem[$k]['month']){				
															$summaRem -= $journal_rem[$k]['summ'];
														}else{
															$summaRem += $journal_rem[$k]['summ'];
														}
													}
												}else{
												}
												
												//Итог
												//Разница между потрачено и внесено
												$thisMonthRazn = $summa - $need_summ;
												
												//!!!!!Если есть какие-то перерасчеты
												/*if ($summaRemLast + $summaRem != 0){
													$rezColor = '#555';
	
													if ($thisMonthRazn  - ($summaRemLast + $summaRem) < 0){
														$rezColor = 'rgba(255, 0, 0, 0.86);';
													}
													if ($thisMonthRazn  - ($summaRemLast + $summaRem) > 0){
														$rezColor = 'rgba(9, 198, 31, 0.92);';
													}	
													echo '
													<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 10px;">
														Разница с учетом перерасчетов: <span style="font-weight: bold; font-size: 110%; color: '.$rezColor.'">'.($summa - $need_summ - ($summaRemLast + $summaRem)).'</span> руб.
													</li>';
												}*/
												
												if ($thisMonthRazn  - ($summaRemLast + $summaRem) > 0){
													
													$dopusk++;
													
													echo '
														<div id="" class="cellTime" style="text-align: center; width: 20px; min-width: 20px; background-color: rgb(143, 243, 0); color: rgb(62, 56, 56);">
															<i class="fa fa-thumbs-o-up"></i>
														</div>';
														
													$SummColor = '';
												}else{
													echo '
														<div id="" class="cellTime" style="text-align: center; width: 20px; min-width: 20px; background-color: rgb(210, 11, 11); color: #FFF;">
															<i class="fa fa-thumbs-down"></i>
														</div>';
														
													$SummColor = 'color: rgb(210, 11, 11);';
												}
												if (($finance['see_all'] == 1) || $god_mode){
													echo '
														<div id="" class="cellTime" style="text-align: right; width: 70px; min-width: 70px; '.$SummColor.'">
															'.($thisMonthRazn  - ($summaRemLast + $summaRem)).'
														</div>
														<a href="client_finance.php?client='.$uch_arr[$i]['id'].'" class="cellTime ahref" style="text-align: center; width: 20px; min-width: 20px;" title="История"><i class="fa fa-rub"></i></a>';
												}

												echo '				
													</li>';
											}
											
											//Номера упражнений
											echo '
													<li class="cellsBlock" style="font-weight: bold; width: auto; margin-top: 20px;">	
														<div class="cellPriority" style="text-align: center;"></div>
														<div class="cellFullName" style="text-align: center; color: #FF9900; font-weight: normal;">Упражнения</div>';
											for ($i = 0; $i < count($weekDays); $i++) {
												//echo $weekDaysArr[2];
												
												$weekDaysArr = explode('.', $weekDays[$i]);
												
												$query = "SELECT `descr` FROM `journal_exercize` WHERE `group_id` = '{$_GET['id']}' AND `day`= '{$weekDaysArr[2]}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
											
												$res = mysql_query($query) or die(mysql_error());
												$number = mysql_num_rows($res);
												if ($number != 0){
													$arr = mysql_fetch_assoc($res);
													if (($arr['descr'] == '') || ($arr['descr'] == null)){
														echo '<div id="'.$weekDaysArr[2].'" class="cellTime doExercize" style="text-align: center; width: 70px; min-width: 70px; color: #FF9900; cursor: pointer">';
														echo '<i class="fa fa-dot-circle-o"></i>';
													}else{
														echo '<div id="'.$weekDaysArr[2].'" class="cellTime doExercize" style="text-align: center; width: 70px; min-width: 70px; color: #FF9900; cursor: pointer">';													
														echo '<span style="font-weight: normal; color: #666; font-size: 80%;">'.$arr['descr'].'</span>';
													}
												}else{
													echo '<div id="'.$weekDaysArr[2].'" class="cellTime doExercize" style="text-align: center; width: 70px; min-width: 70px; color: #FF9900; cursor: pointer">';
													echo '<i class="fa fa-dot-circle-o"></i>';
												}

												echo '</div>';
											}	
											
											
										}else{
											echo '<h3>В этой группе нет участников</h3>';
											
											//Но если они когда-то были
											/*mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
											mysql_select_db($dbName) or die(mysql_error()); 
											mysql_query("SET NAMES 'utf8'");*/
											
											$query = "SELECT `client_id`, `day`, `status` FROM `journal_user` WHERE `group_id` = '{$_GET['id']}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
											$res = mysql_query($query) or die(mysql_error());
											$number = mysql_num_rows($res);
											if ($number != 0){
												while ($arr = mysql_fetch_assoc($res)){
													$journal_uch[$arr['client_id']][$arr['day']] = $arr['status'];
												}
											}
											//var_dump($journal_uch);
											
										}
													
										echo '
												</ul>';
												

												
										if (count($journal_uch) > 0){
											//var_dump($journal_uch);
											
											echo '
												<span style="font-size: 80%; color: rgb(150, 150, 150);">Ниже перечислены участники, которые ранее были в этой группе и отмечались в журнале.<br>Сейчас этих участников в данной группе нет.</span>
												<br>
												<br>
												<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">
													<li class="cellsBlock cellsBlockHover" style="font-weight: bold; width: auto;">	
														<div class="cellPriority" style="text-align: center"></div>
														<div class="cellFullName" style="text-align: center">ФИО</div>
														';
											
											for ($i = 0; $i < count($weekDays); $i++) {
												$weekDaysArr = explode('.', $weekDays[$i]);
												echo '<div class="cellTime" style="text-align: center; width: 70px; min-width: 70px;">'.$weekDaysArr[2].'</div>';
											}										
						
											echo '				
													</li>';

											foreach ($journal_uch as $us_id => $value) {
												
												echo '
														<li class="cellsBlock" style="font-weight: bold; width: auto;">	
															<div class="cellPriority" style="text-align: center;"></div>
															<a href="client.php?id='.$us_id.'" class="cellFullName ahref" id="4filter" style="position: relative;">'.WriteSearchUser('spr_clients', $us_id, 'user_full');
															
												$query = "SELECT * FROM `comments` WHERE `dtable`='spr_clients' AND `parent`='{$us_id}'";
												//var_dump ($query);
												$res = mysql_query($query) or die(mysql_error());
												$number = mysql_num_rows($res);
												if ($number != 0){
													echo '
																<div style="position: absolute; top: 0; right: 3px; color: rgb(247, 188, 50);">
																	<i class="fa fa-commenting" title="Есть комментарии"></i>
																</div>';
												}else{
												}
												echo '
															</a>';
													
												$weekDaysArr = array();
												//var_dump($weekDays);
													
												for ($j = 0; $j < count($weekDays); $j++) {
													$weekDaysArr = explode('.', $weekDays[$j]);
													if (isset($value[$weekDaysArr[2]])){
														if ($value[$weekDaysArr[2]] == 1){
															$backgroundColor = "background-color: rgba(0, 255, 0, 0.5)";
															$journal_ico = '<i class="fa fa-check"></i>';
															$journal_value = 1;
														}elseif($value[$weekDaysArr[2]] == 2){
															$backgroundColor = "background-color: rgba(255, 0, 0, 0.5)";
															$journal_ico = '<i class="fa fa-times"></i>';
															$journal_value = 2;
														}elseif($value[$weekDaysArr[2]] == 3){
															$backgroundColor = "background-color: rgba(255, 252, 0, 0.5)";
															$journal_ico = '<i class="fa fa-file-text-o"></i>';
															$journal_value = 3;
														}elseif($value[$weekDaysArr[2]] == 4){
															$backgroundColor = "background-color: rgba(0, 201, 255, 0.5)";
															$journal_ico = '<i class="fa fa-check"></i>';
															$journal_value = 4;
														}else{
															$backgroundColor = '';
															$journal_ico = '-';
															$journal_value = 0;
														}
														
														unset($journal_uch[$us_id]);
														
													}else{
														$backgroundColor = '';
														$journal_ico = '-';
														$journal_value = 0;
													}
													//echo '<div id="'.$us_id.'_'.$weekDays[$j].'" class="cellTime journalItem" style="text-align: center; width: 70px; min-width: 70px; '.$backgroundColor.'" onclick="JournalEdit('.$us_id.', \''.$weekDays[$j].'\');">'.$journal_ico.'</div>';
													echo '<div id="'.$us_id.'_'.$weekDays[$j].'" class="cellTime journalItem" style="text-align: center; width: 70px; min-width: 70px; '.$backgroundColor.'">'.$journal_ico.'</div>';
													echo '<input type="hidden" id="'.$us_id.'_'.$weekDays[$j].'_value" class="journalItemVal" value="'.$journal_value.'">';
												}									
												
												echo '				
														</li>';
											}
														
											echo '
													</ul>';
										}
										
										mysql_close();
										
										echo '
												</div>
												<br><br>
												<div id="errror"></div>
												<input type="button" class="b" value="Сохранить изменения" onclick=Ajax_change_journal()>';
										echo '		
												<br><br>
												<span style="font-size: 80%; color: rgb(150, 150, 150);">Если допустили ошибку, то, чтобы увидеть актуальный журнал, <a href="" class="ahref">обновите страницу</a></span>
												
											</div>';

										echo '	
											<script type="text/javascript">
												function JournalEdit(id, data){
													elem = document.getElementById(id + "_" + data);
													elem_val = document.getElementById(id + "_" + data + "_value");';
												
										if (($scheduler['see_all'] == 1) || $god_mode){
											echo '	
													if (elem_val.value == 0){
														elem.style.backgroundColor = "rgba(0, 255, 0, 0.5)";
														elem.innerHTML = "<i class=\"fa fa-check\"></i>";
														elem_val.value = 1;
													}else{
														if (elem_val.value == 1){
															elem.style.backgroundColor = "rgba(255, 0, 0, 0.5)";
															elem.innerHTML = "<i class=\"fa fa-times\"></i>";
															elem_val.value = 2;
														}else{
															if (elem_val.value == 2){
																elem.style.backgroundColor = "rgba(0, 201, 255, 0.5)";
																elem.innerHTML = "<i class=\"fa fa-check\"></i>";
																elem_val.value = 4;
															}else{
																if (elem_val.value == 4){
																	elem.style.backgroundColor = "rgba(255, 252, 0, 0.5)";
																	elem.innerHTML = "<i class=\"fa fa-file-text-o\"></i>";
																	elem_val.value = 3;
																}else{
																	if (elem_val.value == 3){
																		elem.style.backgroundColor = "";
																		elem.innerHTML = "-";	
																		elem_val.value = 0;
																	}
																}
															}
														}
													}';
										}else{
											if ($scheduler['see_own'] == 1){
												echo '	
													if (elem_val.value == 0){
														elem.style.backgroundColor = "rgba(0, 255, 0, 0.5)";
														elem.innerHTML = "<i class=\"fa fa-check\"></i>";
														elem_val.value = 1;
													}else{
														if (elem_val.value == 1){
															elem.style.backgroundColor = "rgba(255, 0, 0, 0.5)";
															elem.innerHTML = "<i class=\"fa fa-times\"></i>";
															elem_val.value = 2;
														}else{
															if (elem_val.value == 2){
																elem.style.backgroundColor = "rgba(0, 201, 255, 0.5)";
																elem.innerHTML = "<i class=\"fa fa-check\"></i>";
																elem_val.value = 4;
															}else{
																	if ((elem_val.value == 3) || (elem_val.value == 4)){
																		elem.style.backgroundColor = "";
																		elem.innerHTML = "-";	
																		elem_val.value = 0;
																	}
															}
														}
													}';
											}
										}
										echo '											
													document.getElementById("errror").innerHTML = "<span style=\"color: blue; font-size: 80%;\">Вы внесли изменения в журнал, не забудьте сохранить.<br>Или обновите страницу для сброса.</span>";
												}
												
												function Ajax_change_journal() {
													
													var items = $(".journalItemVal");
													var resJournalItems = {};
													
													$.each(items, function(){
														//var arr = (this.id).split("_");
														if (this.value == 0){
															resJournalItems[this.id] = "0";
														}
														if (this.value == 1){
															resJournalItems[this.id] = "1";
														}
														if (this.value == 2){
															resJournalItems[this.id] = "2";
														}
														if (this.value == 3){
															resJournalItems[this.id] = "3";
														}
														if (this.value == 4){
															resJournalItems[this.id] = "4";
														}
													});
													//console.log(resJournalItems);
													ajax({
														url: "add_meet_journal_f.php",
														method: "POST",
														
														data:
														{
															group_id: '.$_GET['id'].',
															journalItems: JSON.stringify(resJournalItems),
															session_id: '.$_SESSION['id'].'
														},
														success: function(req){
															//document.getElementById("errror").innerHTML = req;
															alert(req);
															document.getElementById("errror").innerHTML = "";
															//location.reload(true);
														}
													});
												}
												
												
												var elems = document.getElementsByClassName("doExercize"), newInput;
												for (var i=0; i<elems.length; i++) {
													var el = elems[i];
													el.addEventListener("click", function() {
														var thisID = this.id;
														
														var  inputs = this.getElementsByTagName("input");
														if (inputs.length > 0) return;
														if (!newInput) {
															newInput = document.createElement("input");
															newInput.type = "text";
															newInput.maxLength = 7;
															newInput.setAttribute("size", 20);
															newInput.style.width = "40px";
															newInput.addEventListener("blur", function() {
																if (newInput.value == ""){
																	newInput.parentNode.innerHTML = "<i class=\"fa fa-dot-circle-o\"></i>";
																}else{
																	newInput.parentNode.innerHTML = "<span style=\"font-weight: normal; color: #666; font-size: 80%;\">"+newInput.value+"</span>";
																}
																ajax({
																	url: "add_exercize_f.php",
																	method: "POST",
																	
																	data:
																	{
																		group_id: '.$_GET['id'].',
																		data: newInput.value,
																		day: thisID,
																		month: '.$month.',
																		year: '.$year.',
																		session_id: '.$_SESSION['id'].'
																	},
																	success: function(req){
																		//document.getElementById("errror").innerHTML = req;
																		//alert(req);
																		//document.getElementById("errror").innerHTML = "";
																		location.reload(true);
																	}
																});
															}, false)
														}

														newInput.value = this.firstChild.innerHTML;
														this.innerHTML = "";
														this.appendChild(newInput);
														newInput.focus();
														newInput.select()
													}.bind(el), false);
												};
												
											</script>
										';
										echo '
											<script type="text/javascript">
												function iWantThisDate(){
													var iWantThisMonth = document.getElementById("iWantThisMonth").value;
													var iWantThisYear = document.getElementById("iWantThisYear").value;
													
													window.location.replace("journal.php?id='.$_GET['id'].'&m="+iWantThisMonth+"&y="+iWantThisYear);
												}
											</script>';
									}
								}else{
									echo '<h3>Для группы не заполнено расписание</h3>';
								}
							}else{
								echo '<h3>Не заполнен справочник графика времени.</h3>';
							}
						}	
					}else{
						echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
					}
				}else{
					echo '<h1>Не найдена такая группа</h1><a href="index.php">Вернуться на главную</a>';
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