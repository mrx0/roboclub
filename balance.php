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
					<div id="closedGroups" style="font-size: 70%; color: #999;">Список тех клиентов, у кого за <span style="color: rgba(45, 43, 43, 0.83);">прошлый месяц</span> остались непотраченные деньги</div>
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
								<span style="font-weight: normal; font-size: 70%;"><< '.$monthsName[$prev].'<br>'.$pYear.'</span>
							</a>
							<div class="cellTime" style="text-align: center;">
								<span style="color: #2EB703">'.$monthsName[$month].'</span><br>'.$year.'
							</div>
							<a href="balance.php?m='.$next.'&y='.$nYear.'" class="cellTime ahref" style="text-align: center;">
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
			$query = "SELECT * FROM `journal_finance` WHERE `month` = '{$month}' AND `year` = '{$year}'";
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
								<div class="cellName" style="text-align: center">Дата</div>
								<div class="cellFullName" style="text-align: center">Полное имя</div>
								<div class="cellName" style="text-align: center">Месяц/Год</div>
								<div class="cellTime" style="text-align: center">Сумма</div>
							</li>';
							
				for ($i = 0; $i < count($journal); $i++) {
					$backSummColor = '';
					if ($journal[$i]['type'] == 2){
						$backSummColor = "background-color: rgba(0, 201, 255, 0.5)";
					}
				
					echo '
							<li class="cellsBlock cellsBlockHover" style="width: auto;"">	
								<a href="finance.php?id='.$journal[$i]['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', $journal[$i]['create_time']).'</a>
								<a href="client.php?id='.$journal[$i]['client'].'" class="cellFullName ahref" id="4filter">'.WriteSearchUser('spr_clients', $journal[$i]['client'], 'user_full').'</a>
								<div class="cellName" style="text-align: center">'.$monthsName[$journal[$i]['month']].'/'.$journal[$i]['year'].'</div>
								<div class="cellTime" style="text-align: center; font-size: 110%; font-weight: bold; '.$backSummColor.'">'.$journal[$i]['summ'].'</div>
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

						
						echo '<li class="cellsBlock" style="width: auto; text-align: right; margin-bottom: 20px;">';
						
						foreach ($journal_uch as $key => $value) {
							$timeForPay = strtotime($value['day'].'.'.$value['month'].'.'.$value['year'].' 23:59:59');
							
							if ($value['status'] == 1){
								$backgroundColor = "background-color: rgba(0, 255, 0, 0.5)";
								$journal_ico = '<i class="fa fa-check"></i>';
								$journal_value = 1;
								
								$journal_was++;
								
								//$timeForPay = strtotime($value['day'].'.'.$value['month'].'.'.$value['year'].' 23:59:59');
								
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
								$backgroundColor = "background-color: rgba(255, 0, 0, 0.5)";
								$journal_ico = '<i class="fa fa-times"></i>';
								$journal_value = 2;
								
								$journal_x++;
								
								//$timeForPay = strtotime($value['day'].'.'.$value['month'].'.'.$value['year'].' 23:59:59');
								
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
								$backgroundColor = "background-color: rgba(255, 252, 0, 0.5)";
								$journal_ico = '<i class="fa fa-file-text-o"></i>';
								$journal_value = 3;
								
								$journal_spr++;
								
								$need_cena = 0;
								
							}elseif($value['status'] == 4){
								$backgroundColor = "background-color: rgba(0, 201, 255, 0.5)";
								$journal_ico = '<i class="fa fa-check"></i>';
								$journal_value = 4;
								
								$journal_try++;
								
								//$timeForPay = strtotime($value['day'].'.'.$value['month'].'.'.$value['year'].' 23:59:59');
								
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

							$group = SelDataFromDB('journal_groups', $value['group_id'], 'id');
							
							echo '
								<a href="journal.php?id='.$value['group_id'].'&m='.$value['month'].'&y='.$value['year'].'" class="cellName ahref" style="text-align: center; width: 100px; '.$backgroundColor.'">
									'.$value['day'].'.'.$value['month'].'.'.$value['year'].'<br>
									<span style="font-size: 70%;">';
							if ($group != 0){
								echo $group[0]['name'];	
							}else{
								echo 'ошибка группы';
							}
							echo '
								</span>
								<br>
								'.$journal_ico.'<br>
								<span style="font-size: 70%;">'.$need_cena.' руб.</span>
							</a>';
							
							
							
							
						}
						echo '</li>';
						
					}else{
						echo '<h1>В этом месяце посещений не отмечено.</h1>';
					}
							
							
							
							
				}
				

			}else{
				echo '<h1>В этом месяце платежей не добавлялось.</a>';
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