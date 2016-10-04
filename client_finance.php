<?php

//client_finance.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($finance['see_all'] == 1) || $god_mode){
				
				include_once 'DBWork.php';
				include_once 'functions.php';

				$client = SelDataFromDB('spr_clients', $_GET['client'], 'user');
				
				if ($client != 0){
					echo '
						<header style="margin-bottom: 5px;">
							<h1>История <a href="client.php?id='.$client[0]['id'].'" class="ahref">'.$client[0]['full_name'].'</a></h1>
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
					
					echo '<span style="font-size: 80%; color: #CCC;">Сегодня: <a href="client_finance.php?client='.$client[0]['id'].'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>';	
					echo '
						<div id="data">		
							<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
									<a href="client_finance.php?client='.$client[0]['id'].'&m='.$prev.'&y='.$pYear.'" class="cellTime ahref" style="text-align: center;">
										<span style="font-weight: normal; font-size: 70%;"><< '.$monthsName[$prev].'<br>'.$pYear.'</span>
									</a>
									<div class="cellTime" style="text-align: center;">
										<span style="color: #2EB703">'.$monthsName[$month].'</span><br>'.$year.'
									</div>
									<a href="client_finance.php?client='.$client[0]['id'].'&m='.$next.'&y='.$nYear.'" class="cellTime ahref" style="text-align: center;">
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
						<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">
							<div class="cellRight">
								<span style="font-size: 70%;">Быстрый поиск клиента</span><br />
								<input type="text" size="50" name="searchdata_fc" id="search_client" placeholder="Введите первые три буквы для поиска" value="" class="who_fc"  autocomplete="off">
								<div id="search_result_fc2"></div>
							</div>
						</div>';		
					
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 10px;">
							Посещенные занятия
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
					$query = "SELECT * FROM `journal_user` WHERE `client_id` = '".$client[0]['id']."' AND  `month` = '{$month}' AND  `year` = '{$year}' ORDER BY `day` ASC";
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
								
								//$need_cena = 0;
								$need_cena = $settings['cena1'][$key_time]['value']/2;
								$need_summ += $settings['cena1'][$key_time]['value']/2;
								
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
					
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 10px;">
							Внесённые оплаты за этот месяц
						</li>';
						
					//Смотрим оплаты
					$arr = array();
					$journal_fin = array();
					
					//Общая внесённая сумма
					$summa = 0;
					
					//mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					//mysql_select_db($dbName) or die(mysql_error()); 
					//mysql_query("SET NAMES 'utf8'");
					$query = "SELECT * FROM `journal_finance` WHERE `month` = '{$month}' AND  `year` = '{$year}' AND `client`='".$client[0]['id']."'";
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
						echo '
									<li class="cellsBlock" style="font-weight: bold; width: auto;">	
										<div class="cellName" style="text-align: center">Дата</div>
										<div class="cellFullName" style="text-align: center">Полное имя</div>
										<div class="cellName" style="text-align: center">Месяц/Год</div>
										<div class="cellTime" style="text-align: center">Сумма</div>
									</li>';
									
						for ($i = 0; $i < count($journal_fin); $i++) { 
							$backSummColor = '';
							if ($journal_fin[$i]['type'] == 2){
								$backSummColor = "background-color: rgba(0, 201, 255, 0.5)";
							}
						
							echo '
									<li class="cellsBlock cellsBlockHover" style="width: auto;">	
										<a href="finance.php?id='.$journal_fin[$i]['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', $journal_fin[$i]['create_time']).'</a>
										<a href="client.php?id='.$journal_fin[$i]['client'].'" class="cellFullName ahref" id="4filter">'.WriteSearchUser('spr_clients', $journal_fin[$i]['client'], 'user_full').'</a>
										<div class="cellName" style="text-align: center">'.$monthsName[$journal_fin[$i]['month']].'/'.$journal_fin[$i]['year'].'</div>
										<div class="cellTime" style="text-align: center; font-size: 110%; font-weight: bold; '.$backSummColor.'">'.$journal_fin[$i]['summ'].'</div>
									</li>';
								
							if ($journal_fin[$i]['type'] != 2){
								$summa += $journal_fin[$i]['summ'];
							}
						}
						

					}else{
						echo '<h1>В этом месяце платежей не вносилось.</h1>';
					}

					
					//Смотрим переносы ИЗ ЭТОГО месяца
					$arr = array();
					$journal_rem_last = array();
					
					//Общая внесённая сумма
					$summaRemLast = 0;

					$query = "SELECT * FROM `journal_finance_rem` WHERE `last_month` = '{$month}' AND `last_year` = '{$year}' AND `client`='".$_GET['client']."'";
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
						echo '
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
							</li>';
						for ($k = 0; $k < count($journal_rem_last); $k++) {
							
							if ($journal_rem_last[$k]['summ'] < 0){
								$SummColor = 'color: rgba(255, 0, 0, 0.86);';
								$znak = 'долг:<br>';
								$znak2 = '';
							}elseif($journal_rem_last[$k]['summ'] > 0){
								$SummColor = 'color: rgba(9, 198, 31, 0.92);';
								$znak = 'избыток:<br>';
								$znak2 = '+';
							}else{
							}
							
							echo '
								<li class="cellsBlock cellsBlockHover" style="width: auto;">	
									<a href="remove.php?id='.$journal_rem_last[$k]['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', $journal_rem_last[$k]['create_time']).'</a>
									<a href="client.php?id='.$journal_rem_last[$k]['client'].'" class="cellName ahref" id="4filter">'.WriteSearchUser('spr_clients', $journal_rem_last[$k]['client'], 'user').'</a>
									<div class="cellName" style="text-align: center">'.$monthsName[$journal_rem_last[$k]['last_month']].'/'.$journal_rem_last[$k]['last_year'].'</div>
									<div class="cellName" style="text-align: center">'.$monthsName[$journal_rem_last[$k]['month']].'/'.$journal_rem_last[$k]['year'].'</div>
									<div class="cellTime" style="text-align: center; font-size: 110%;">'.$znak.' <span style="'.$SummColor.' font-weight: bold;">'.$znak2.$journal_rem_last[$k]['summ'].'</span></div>
								</li>';
						
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

					$query = "SELECT * FROM `journal_finance_rem` WHERE `month` = '{$month}' AND `year` = '{$year}' AND `client`='".$_GET['client']."'";
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
						echo '
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
							</li>';
						for ($k = 0; $k < count($journal_rem); $k++) { 
						
							if ($journal_rem[$k]['summ'] < 0){
								$SummColor = 'color: rgba(255, 0, 0, 0.86);';
								$znak = 'долг:<br>';
								$znak2 = '';
							}elseif($journal_rem[$k]['summ'] > 0){
								$SummColor = 'color: rgba(9, 198, 31, 0.92);';
								$znak = 'избыток:<br>';
								$znak2 = '+';
							}else{
							}
						
							echo '
								<li class="cellsBlock cellsBlockHover" style="width: auto;">	
									<a href="remove.php?id='.$journal_rem[$k]['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', $journal_rem[$k]['create_time']).'</a>
									<a href="client.php?id='.$journal_rem[$k]['client'].'" class="cellName ahref" id="4filter">'.WriteSearchUser('spr_clients', $journal_rem[$k]['client'], 'user').'</a>
									<div class="cellName" style="text-align: center">'.$monthsName[$journal_rem[$k]['last_month']].'/'.$journal_rem[$k]['last_year'].'</div>
									<div class="cellName" style="text-align: center">'.$monthsName[$journal_rem[$k]['month']].'/'.$journal_rem[$k]['year'].'</div>
									<div class="cellTime" style="text-align: center; font-size: 110%;">'.$znak.' <span style="'.$SummColor.' font-weight: bold;">'.$znak2.$journal_rem[$k]['summ'].'</span></div>
								</li>';
							//Если перерасчет сделан В этот месяц
							if ($month == $journal_rem[$k]['month']){				
								$summaRem -= $journal_rem[$k]['summ'];
							}else{
								$summaRem += $journal_rem[$k]['summ'];
							}
						}
					}else{
					}
				
					echo '
						</ul>';
					
					//Амортизационный взнос
					$amortThisYear = '';
					
					$query = "SELECT `id`, `create_time`, `summ`, `year` FROM `journal_finance` WHERE `year` = '{$year}' AND `client`='".$client[0]['id']."' AND `type`='2'";
					$res = mysql_query($query) or die(mysql_error());
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							$amortThisYear .= '
									<li class="cellsBlock cellsBlockHover" style="width: auto;">	
										<a href="finance.php?id='.$arr['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', $arr['create_time']).'</a>
										<div class="cellName" style="text-align: center">'.$arr['year'].'</div>
										<div class="cellTime" style="text-align: center; font-size: 110%; font-weight: bold; background-color: rgba(0, 201, 255, 0.5);">'.$arr['summ'].'</div>
									</li>';
						}
					}else{
						$amortThisYear = '<h1 style="font-size: 100%;">В '.$year.' году амортизационный взнос не вносился.</h1>';
					}

					//Итоговые суммы
					
					$thisMonthRazn = $summa - $need_summ;
					
					echo '
						<ul style="margin-left: 6px; margin-bottom: 20px; border: 1px solid #CCC; width: 500px; padding: 7px;">
							<li class="cellsBlock" style="width: auto; text-align: right; font-size: 80%; color: #777; margin-bottom: 10px;">
								Амортизационный платёж
							</li>		
							<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
								'.$amortThisYear.'
							</li>
						</ul>';
						
					echo '
						<ul style="margin-left: 6px; margin-bottom: 20px; border: 1px solid #CCC; width: 500px; padding: 7px;">';

					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
							Всего внесено за месяц: <span style="font-weight: bold; font-size: 110%; color: #555">'.$summa.'</span> руб.
						</li>';
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 5px;">
							Общая стоимость посещённых занятий: <span style="font-weight: bold; font-size: 110%; color: #555">'.$need_summ.'</span> руб.
						</li>';
					
					$rezColor = '#555';
					if ($summa - $need_summ < 0){
						$rezColor = 'rgba(255, 0, 0, 0.86);';
					}
					if ($summa - $need_summ > 0){
						$rezColor = 'rgba(9, 198, 31, 0.92);';
					}	

					//остаток
					$summ4Remove = $summa-$need_summ;
					
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 10px;">
							Разница денег за месяц <span style="font-size: 75%;">(без учета перерасчета)</span>: <span style="font-weight: bold; font-size: 110%; color: '.$rezColor.'">'.($summa-$need_summ).'</span> руб.
						</li>';
						
					//Если есть какие-то перерасчеты
					if ($summaRemLast + $summaRem != 0){
						$rezColor = '#555';
						
						
						
						if ($summa - $need_summ - ($summaRemLast + $summaRem) < 0){
							$rezColor = 'rgba(255, 0, 0, 0.86);';
						}
						if ($summa - $need_summ - ($summaRemLast + $summaRem) > 0){
							$rezColor = 'rgba(9, 198, 31, 0.92);';
						}	
						echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 10px;">
							Разница с учетом перерасчетов: <span style="font-weight: bold; font-size: 110%; color: '.$rezColor.'">'.($summa - $need_summ - ($summaRemLast + $summaRem)).'</span> руб.
						</li>';
					}
					
					echo '
						</ul>';
						
					//Статистика занятий
					echo '	
						<ul style="font-size: 90%; margin-left: 6px; margin-bottom: 20px; border: 1px solid #CCC; width: 500px; padding: 7px;">';
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
							Был на занятиях: <span style="font-weight: bold; font-size: 110%; color: rgba(9, 198, 31, 0.92);">'.$journal_was.'</span>
						</li>';

					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
							Пропустил: <span style="font-weight: bold; font-size: 110%; color: rgba(255, 0, 0, 0.86);">'.$journal_x.'</span>
						</li>';
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
							Справка: <span style="font-weight: bold; font-size: 110%; color: rgb(249, 151, 5);">'.$journal_spr.'</span>
						</li>';
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 100%; color: #777; margin-bottom: 0px;">
							Пробные: <span style="font-weight: bold; font-size: 110%; color: rgba(0, 201, 255, 0.5);">'.$journal_try.'</span>
						</li>';	
						
					echo '
						</ul>';
						
					if (($finance['add_new'] == 1) || $god_mode){
						echo '
								<a href="add_finance.php?client='.$client[0]['id'].'" class="b">Добавить платёж <i class="fa fa-rub"></i></a><br><br>';
					}
					
					
					//Общая финансовая история
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
					
				
					//Смотрим посещения
					$arr = array();
					$journal_uch = array();
					
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$query = "SELECT * FROM `journal_user` WHERE `client_id` = '".$client[0]['id']."' ORDER BY `day` ASC";
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
						
					
						foreach ($journal_uch as $key => $value) {
							if ($value['status'] == 1){
								
								$journal_was++;
								
								$timeForPay = strtotime($value['day'].'.'.$value['month'].'.'.$value['year'].' 23:59:59');
								
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
								
								$timeForPay = strtotime($value['day'].'.'.$value['month'].'.'.$value['year'].' 23:59:59');
								
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
								$need_cena = $settings['cena1'][$key_time]['value']/2;
								$need_summ += $settings['cena1'][$key_time]['value']/2;
								
							}elseif($value['status'] == 4){
								
								$journal_try++;
								
								$timeForPay = strtotime($value['day'].'.'.$value['month'].'.'.$value['year'].' 23:59:59');
								
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
						
					//Смотрим оплаты
					$arr = array();
					$journal_fin = array();
					
					//Общая внесённая сумма
					$AllSumma = 0;
					//Общая внесённая сумма без амортизации
					$summa = 0;
					
					//mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					//mysql_select_db($dbName) or die(mysql_error()); 
					//mysql_query("SET NAMES 'utf8'");
					$query = "SELECT * FROM `journal_finance` WHERE `client`='".$client[0]['id']."'";
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
					
					$allClientFinance = '';
					
					if ($journal_fin != 0){
									
						for ($i = 0; $i < count($journal_fin); $i++) { 
							$AllSumma += $journal_fin[$i]['summ'];
							if ($journal_fin[$i]['type'] != 2){
								$summa += $journal_fin[$i]['summ'];
								$backSummColor = '';
							}else{
								$backSummColor = 'background-color: rgba(0, 201, 255, 0.5)';
							}
							$allClientFinance .= '
								<li class="cellsBlock cellsBlockHover" style="width: auto;">	
									<a href="finance.php?id='.$journal_fin[$i]['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', $journal_fin[$i]['create_time']).'</a>
									<div class="cellName" style="text-align: center">'.$monthsName[$journal_fin[$i]['month']].'/'.$journal_fin[$i]['year'].'</div>
									<div class="cellTime" style="text-align: center; font-size: 110%; font-weight: bold; '.$backSummColor.'">'.$journal_fin[$i]['summ'].'</div>
								</li>';
						}
					}else{
						$allClientFinance = '<h1 style="font-size: 100%;">Не зафиксировано ни одного платежа.</h1>';
					}
					
					echo '	
						<ul style="font-size: 90%; margin-left: 6px; margin-bottom: 20px; border: 1px solid #CCC; width: 500px; padding: 7px;">';
						
					echo '
						<li class="cellsBlock" style="width: auto; font-size: 90%; color: #777; margin-bottom: 0px; border-bottom: 1px solid #CCC;  ">
							<b>История за всё время</b><br>
							<span style="font-size: 80%;">Если общая разница не сходится с <a href="client_finance.php?client='.$client[0]['id'].'" class="ahref">текущим месяцем ('.$monthsName[date("m")].' '.date("Y").')</a>, перераспределите деньги за прошедшие месяцы.</span>
						</li>';

					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
							Всего внесено: <span style="font-weight: bold; font-size: 90%; color: #555;">'.$summa.'</span>
						</li>';
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 5px;">
							Списано за занятия: <span style="font-weight: bold; font-size: 90%; color: #555;">'.$need_summ.'</span>
						</li>';
						
					$rezColor = '#555';
					if ($summa - $need_summ < 0){
						$rezColor = 'rgba(255, 0, 0, 0.86)';
					}
					if ($summa - $need_summ > 0){
						$rezColor = 'rgba(9, 198, 31, 0.92)';
					}
					if ($month == date("m")){
						if ($thisMonthRazn != $summa-$need_summ){
							$backSummColor = ' background-color: rgba(255, 0, 0, 0.15);';
						}else{
							$backSummColor = '';
						}
					}else{
						$backSummColor = '';
					}
					
					echo '
						<li class="cellsBlock" style="width: auto; text-align: left; font-size: 90%; color: #777; margin-bottom: 15px; border: 1px solid rgba(255, 0, 0, 0.28); padding: 5px;'.$backSummColor.'">
							Разница: <span style="font-weight: bold; font-size: 90%; color: '.$rezColor.';">'.($summa-$need_summ).'</span><br>';
					/*if (($summa-$need_summ-$summ4Remove != 0) && ($summ4Remove != 0)){
						echo '
							Расхождение: <span style="font-weight: bold; font-size: 90%; color: rgba(255, 0, 0, 0.86);">'.$summ4Remove.'</span>';
					}*/
					echo '
						</li>';	
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
							Был на занятиях: <span style="font-weight: bold; font-size: 110%; color: rgba(9, 198, 31, 0.92);">'.$journal_was.'</span>
						</li>';

					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
							Пропустил: <span style="font-weight: bold; font-size: 110%; color: rgba(255, 0, 0, 0.86);">'.$journal_x.'</span>
						</li>';
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
							Справка: <span style="font-weight: bold; font-size: 110%; color: rgb(249, 151, 5);">'.$journal_spr.'</span>
						</li>';
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 10px;">
							Пробные: <span style="font-weight: bold; font-size: 110%; color: rgba(0, 201, 255, 0.5);">'.$journal_try.'</span>
						</li>';	
						
						
					echo '
						<li class="cellsBlock" style="width: 500px; text-align: left; font-size: 90%; color: #777; margin-bottom: 5px; border-bottom: 1px solid #CCC; ">
							<b>Платежи клиента за всё время, включая амортизацию</b>
						</li>';	
						
					echo '
						<li class="cellsBlock" style="width: 500px; text-align: left; font-size: 90%; color: #777; margin-bottom: 5px; border-bottom: 1px solid #CCC; ">
							'.$allClientFinance.'
						</li>';	
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: left; font-size: 90%; color: #777; margin-top: 15px; ">
							Итого: <span style="font-weight: bold; font-size: 110%; color: #555;">'.$AllSumma.'</span> руб.
						</li>';	
						
					echo '
						</ul>';
					
					
					
					mysql_close();					
					
					echo '		
						</div>';
						
					echo '
						<script type="text/javascript">
							function iWantThisDate(){
								var iWantThisMonth = document.getElementById("iWantThisMonth").value;
								var iWantThisYear = document.getElementById("iWantThisYear").value;
								
								window.location.replace("client_finance.php?client='.$client[0]['id'].'&m="+iWantThisMonth+"&y="+iWantThisYear);
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