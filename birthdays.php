<?php

//birthdays.php
//Дни рождений

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($clients['see_all'] == 1) || $god_mode){
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
			
			$offices = SelDataFromDB('spr_office', '', '');
			//var_dump ($offices);
			$filter = FALSE;
			$sw = '';
			$filter_rez = array();
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Список дней рождений</h1>
				</header>';

									
			if (isset($_GET['m']) && isset($_GET['y'])){
				$year = $_GET['y'];
				$month = $_GET['m'];
			}else{
				$year = date("Y");
				$month = date("m");
			}
			
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
				
			echo '<span style="font-size: 80%; color: rgb(125, 125, 125);">Сегодня: <a href="birthdays.php?" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>';		
				
			echo '
				<div id="data">		
					<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">
						<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
							<a href="birthdays.php?m='.$prev.'&y='.$pYear.'" class="cellTime ahref" style="text-align: center;">
								<span style="font-weight: normal; font-size: 70%;"><< '.$monthsName[$prev].'<br>'.$pYear.'</span>
							</a>
							<div class="cellTime" style="text-align: center;">
								<span style="color: #2EB703">'.$monthsName[$month].'</span><br>'.$year.'
							</div>
							<a href="birthdays.php?m='.$next.'&y='.$nYear.'" class="cellTime ahref" style="text-align: center;">
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

			//нулевой день следующего месяца - это последний день предыдущего
			$firstday = strtotime('1.'.$month.'.'.$year);
			$lastday = mktime(0, 0, 0, $month+1, 0, $year);

			
			require 'config.php';
			
			//Смотрим посещения
			$arr = array();
			$clients_j = array();
			
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение ");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			
			$time = time();
			
			$query = "SELECT * FROM `spr_clients` WHERE DATE_FORMAT(`birth`, '%m') = '{$month}'";

			$res = mysql_query($query) or die($query);
			$number = mysql_num_rows($res);
			if ($number != 0){
				while ($arr = mysql_fetch_assoc($res)){
					array_push($clients_j, $arr);
				}
			}else{
				$clients_j = 0;
			}
			
			if ($clients_j != 0){
				echo '
					<p style="margin: 5px 0; padding: 2px;">
						Быстрый фильтр: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
					</p>
					
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold;">	
								<div class="cellFullName" style="text-align: center">Полное имя</div>';
			
				echo '
								<div class="cellCosmAct" style="text-align: center">Пол</div>
								<div class="cellTime" style="width: 140px; text-align: center">Дата рождения</div>
								<div class="cellText" style="text-align: center">Контакты</div>
								<div class="cellText cellComment" style="text-align: center">Комментарий</div>
							</li>';

				for ($i = 0; $i < count($clients_j); $i++) { 
					echo '
							<li class="cellsBlock cellsBlockHover">
								<a href="client.php?id='.$clients_j[$i]['id'].'" class="cellFullName ahref" id="4filter">'.$clients_j[$i]['full_name'].'</a>';
					echo '
								<div class="cellCosmAct" style="text-align: center">';
					if ($clients_j[$i]['sex'] != 0){
						if ($clients_j[$i]['sex'] == 1){
							echo 'М';
						}
						if ($clients_j[$i]['sex'] == 2){
							echo 'Ж';
						}
					}else{
						echo '-';
					}
					
					echo '
								</div>';
					if (($clients_j[$i]['birthday'] == "-1577934000") || ($clients_j[$i]['birthday'] == 0)){
						$age = '';
					}else{
						$age = getyeardiff( $clients_j[$i]['birthday']).' лет';
					}
					echo '
								<div class="cellTime" style="width: 140px; text-align: center">', (($clients_j[$i]['birthday'] == '-1577934000') || ($clients_j[$i]['birthday'] == 0)) ? 'не указана' : date('d.m.Y', $clients_j[$i]['birthday']) ,' / <b>'.$age.'</b></div>
								<div class="cellText">'.$clients_j[$i]['contacts'].'</div>
								<div class="cellText cellComment">'.$clients_j[$i]['comments'].'</div>
							</li>';
				}
			}else{
				echo '<h3>В этом месяце нет дней рождений</h3>';
			}
			echo '
					</ul>
				</div>';
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>