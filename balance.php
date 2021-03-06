<?php

//balance.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		
		if (($finance['see_all'] == 1) || $god_mode){
			
			if (isset($_GET['m']) && isset($_GET['y'])){
				$year = $_GET['y'];
				$month = $_GET['m'];
			}else{
				$year = date("Y");
				$month = date("m");
			}
		
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Остатки</h1>';
			echo '
					<div id="closedGroups" style="color: #999;">Список тех детей, у кого за <span style="color: rgba(45, 43, 43, 0.83);">прошлый месяц</span> остались непотраченные деньги или долги</div>
				</header>';

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
			
			echo '<span style="font-size: 80%; color: #CCC;">Сегодня: <a href="balance.php" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>';	
			echo '
				<div id="data">		
					<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">
						<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
							<a href="balance.php?m='.$prev.'&y='.$pYear.'" class="cellTime ahref" style="text-align: center;">
								<span style="font-weight: normal;"><< '.$monthsName[$prev].'<br>'.$pYear.'</span>
							</a>
							<div class="cellTime" style="text-align: center;">
								<span style="color: #2EB703">'.$monthsName[$month].'</span><br>'.$year.'
							</div>
							<a href="balance.php?m='.$next.'&y='.$nYear.'" class="cellTime ahref" style="text-align: center;">
								<span style="font-weight: normal;">'.$monthsName[$next].' >><br>'.$nYear.'</span>
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
			
			require 'config.php';	
			
			$monthsName_keys = array_keys($monthsName);
			//var_dump($monthsName_keys);
			$key = array_search($month, $monthsName_keys);
			//var_dump($key);
			$key = $key - 1;
			//var_dump($key);
			if ($key <= 0) {$key = 11; $year = $year - 1;}
			$month = $monthsName_keys[$key];
			//var_dump($month);
			//var_dump($year);
			
			$arr = array();
			$journal = array();
										
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			$query = "SELECT `client`, SUM(`summ`) FROM `journal_finance` WHERE `month` = '{$month}' AND `year` = '{$year}' AND `type` <> 2 GROUP BY `client`";
			$res = mysql_query($query) or die(mysql_error());
			$number = mysql_num_rows($res);
			if ($number != 0){
				while ($arr = mysql_fetch_assoc($res)){
					array_push($journal, $arr);
				}
			}else{
				$journal = 0;
			}
			//var_dump($journal);

			if ($journal != 0){
				
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
				

				echo '
							<li class="cellsBlock" style="font-weight:bold; width: auto;"">	
								<div class="cellFullName" style="text-align: center">Полное имя</div>
								<div class="cellName" style="text-align: center">Сумма</div>
							</li>';
							
				for ($i = 0; $i < count($journal); $i++) {
					$backSummColor = '';

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
					
					require 'config.php';	
					
					//Смотрим посещения
					$arr = array();
					$journal_uch = array();
										
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$query = "SELECT * FROM `journal_user` WHERE `client_id` = '".$journal[$i]['client']."' AND  `month` = '{$month}' AND  `year` = '{$year}' ORDER BY `day` ASC";
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

						foreach ($journal_uch as $key => $value) {
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
								
								//$need_cena = 0;
								//$need_cena = $settings['cena1'][$key_time]['value']/2;
								//$need_summ += $settings['cena1'][$key_time]['value']/2;
								foreach($settings['cena1'] as $key_time => $value_time_arr){
									//$need_cena = 0;
									
									//если только одно значение 
									if (count($settings['cena1']) == 1){
										$need_cena = $settings['cena1'][$key_time]['value']/2;
										$need_summ += $settings['cena1'][$key_time]['value']/2;
									}else{
										//Если указанное в посещении время меньше чем текущее в цикле
										if ($timeForPay < $key_time){
											continue;
										}else{
											$need_cena = $settings['cena1'][$key_time]['value']/2;
											$need_summ += $settings['cena1'][$key_time]['value']/2;
											break;
										}
									}
								}
								
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
							}
						}
					}else{
					}
					
					//Смотрим переносы ИЗ ЭТОГО месяца
					$arr = array();
					$journal_rem_last = array();
					
					//Общая внесённая сумма
					$summaRemLast = 0;

					$query = "SELECT * FROM `journal_finance_rem` WHERE `last_month` = '{$month}' AND `last_year` = '{$year}' AND `client`='".$journal[$i]['client']."'";
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

					$query = "SELECT * FROM `journal_finance_rem` WHERE `month` = '{$month}' AND `year` = '{$year}' AND `client`='".$journal[$i]['client']."'";
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
					
					$rezColor = '';
					$znak = '';
					
					if ($journal[$i]['SUM(`summ`)'] - $need_summ - ($summaRemLast + $summaRem) < 0){
						$rezColor = 'rgba(255, 0, 0, 0.86);';
					}elseif($journal[$i]['SUM(`summ`)'] - $need_summ - ($summaRemLast + $summaRem) > 0){
						$rezColor = 'rgba(9, 198, 31, 0.92);';
						$znak = '+';
					}else{
					}
					
					if ($journal[$i]['SUM(`summ`)'] - $need_summ - ($summaRemLast + $summaRem) != 0){
						echo '
							<li class="cellsBlock cellsBlockHover" style="width: auto;"">	
								<a href="client.php?id='.$journal[$i]['client'].'" class="cellFullName ahref" id="4filter">'.WriteSearchUser('spr_clients', $journal[$i]['client'], 'user_full').'</a>
								<a href="client_finance.php?client='.$journal[$i]['client'].'" class="cellName ahref" style="text-align: center; font-size: 110%; font-weight: bold;  color: '.$rezColor.'">'.$znak.''.($journal[$i]['SUM(`summ`)'] - $need_summ).' <i class="fa fa-rub"></i></a>
								<a href="client_finance.php?client='.$journal[$i]['client'].'" class="cellCosmAct ahref" style="text-align: center; width: 20px; min-width: 20px; font-size: 110%;" title="История"><i class="fa fa-rub"></i></a>
								<a href="finance_remove2.php?client='.$journal[$i]['client'].'&m='.$month.'&y='.$year.'&summ='.($journal[$i]['SUM(`summ`)'] - $need_summ).'" class="cellCosmAct ahref" style="color: #999; font-size: 100%; font-weight: bold; width: 30px; min-width: 30px;" title="Перерасчет"><i class="fa fa-arrow-right"></i></a>
							</li>';
					}
				}
			}else{
				echo '<h1>Распределять нечего</h1>';
			}
			echo '
					</ul>';
					
			echo '		
				</div>';
				
			echo '
				<script type="text/javascript">
					function iWantThisDate(){
						var iWantThisMonth = document.getElementById("iWantThisMonth").value;
						var iWantThisYear = document.getElementById("iWantThisYear").value;
						
						window.location.replace("balance.php?m="+iWantThisMonth+"&y="+iWantThisYear);
					}
				</script>';
			
			
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>