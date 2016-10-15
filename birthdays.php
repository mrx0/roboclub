<?php

//birthdays.php
//Дни рождений

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($clients['see_all'] == 1) || ($clients['see_own'] == 1) || $god_mode){
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
			mysql_select_db($dbName) or die(mysql_error().'->'.$query); 
			mysql_query("SET NAMES 'utf8'");
			
			//$time = time();
			
			if (($clients['see_all'] == 1) || $god_mode){
				$query = "SELECT * FROM `spr_clients` WHERE DATE_FORMAT(`birth`, '%m') = '{$month}' ORDER BY DATE_FORMAT(`birth`, '%d') ASC";
			}elseif ($clients['see_own'] == 1){
				$query = "SELECT * FROM `spr_clients` WHERE DATE_FORMAT(`birth`, '%m') = '{$month}' AND 
				(
				`id` IN (SELECT `client` FROM `journal_groups_clients` WHERE `group_id` IN (SELECT `id` FROM `journal_groups` WHERE `worker`='{$_SESSION['id']}'))
				OR
				`id` IN (SELECT `client` FROM `journal_groups_clients` WHERE `group_id` IN (SELECT `group_id` FROM `journal_replacement` WHERE `user_id`='{$_SESSION['id']}'))
				)
				ORDER BY DATE_FORMAT(`birth`, '%d') ASC";
			}

			$res = mysql_query($query) or die(mysql_error().'->'.$query);
			$number = mysql_num_rows($res);
			if ($number != 0){
				while ($arr = mysql_fetch_assoc($res)){
					array_push($clients_j, $arr);
				}
			}else{
				$clients_j = 0;
			}
			//var_dump($clients_j);
			
			if ($clients_j != 0){
				
				$birth_j = array();
				
				$query = "SELECT `client_id`, `status` FROM `journal_birth` WHERE `month` = '{$month}' AND `year` = '{$year}' AND `status` = 1";

				$res = mysql_query($query) or die($query);
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						$birth_j[$arr['client_id']] = $arr['status'];
					}
				}else{
				}
				//var_dump($birth_j);
				
				echo '
					<p style="margin: 5px 0; padding: 2px;">
						Быстрый фильтр: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
					</p>
					
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight: bold; width: auto;">
								<div class="cellCosmAct" style="text-align: center; border: medium none;"></div>
								<div class="cellName" style="text-align: center">Филиал</div>
								<div class="cellName" style="text-align: center">Группа</div>
								<div class="cellFullName" style="text-align: center">Полное имя</div>
								<div class="cellCosmAct" style="text-align: center">Пол</div>
								<div class="cellTime" style="width: 140px; text-align: center">Дата рождения</div>
							</li>';

				for ($i = 0; $i < count($clients_j); $i++) { 
					$BgColorTodayBirth = '';
				
					if (isset($birth_j[$clients_j[$i]['id']])){
						//if ($birth_j[$clients_j[$i]['id']] == 1){
							$birthColor = ' background-color: rgba(46, 183, 3, 0.48);';
							$presentColor = ' background-color: rgba(46, 183, 3, 0.48);';
						//}
					}else{
						$birthColor = '';
						$presentColor = ' background-color: rgba(255, 83, 83, 0.23);';						
					}
					echo '
							<li class="cellsBlock cellsBlockHover" style="width: auto;">';
					echo '
								<div class="cellCosmAct" style="text-align: center; '.$birthColor.' border: medium none;">';
					if (date("d.m") == date("d.m", $clients_j[$i]['birthday'])){
						$BgColorTodayBirth = 'background-color: #ffa200';
						echo '
									<i class="fa fa-arrow-right"></i>';
					}
					echo '
								</div>';
					
					echo '
								<div class="cellName" style="text-align: center">';
					$filials = SelDataFromDB('spr_office', $clients_j[$i]['filial'], 'offices');
					if ($filials != 0){
						echo '<a href="filial.php?id='.$filials[0]['id'].'" class="ahref">'.$filials[0]['name'].'</a>';	
					}else{
						echo 'Не указан филиал';
					}
					echo '
								</div>';
					
					$group_val = '';
					$group_color = '';
					
					$groups = SelDataFromDB('journal_groups_clients', $clients_j[$i]['id'], 'client');
					if ($groups != 0){
						//var_dump ($groups);
						foreach($groups as $key => $value){
							$group = SelDataFromDB('journal_groups', $value['group_id'], 'id');
							if ($group != 0){
								$group_val .= '<a href="group.php?id='.$value['group_id'].'" class="ahref" style="padding: 0 4px;">'.$group[0]['name'].'</a>';	
								$group_color .= 'background-color: '.$group[0]['color'];
							}else{
								$group_val .= 'ошибка группы';
							}
						}
					}else{
						$group_val .= 'Не в группе';
					}
					
					echo '
								<div class="cellName" style="text-align: center; '.$group_color.'">';
					echo $group_val;
					echo '
								</div>';
								
					echo '
								<a href="client.php?id='.$clients_j[$i]['id'].'" class="cellFullName ahref" id="4filter" style="'.$BgColorTodayBirth.'">'.$clients_j[$i]['full_name'].'</a>';
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
								<div class="cellCosmAct" style="text-align: center; font-size: 110%; cursor: pointer; '.$presentColor.'" onclick="Ajax_giftPresent('.$clients_j[$i]['id'].')">
									<i class="fa fa-gift"></i>
								</div>
							</li>';
				}
				//echo '<div id="errror"></div>';
			}else{
				echo '<h3>В этом месяце нет дней рождений</h3>';
			}
			echo '
					</ul>
				</div>';
			
			mysql_close();	
			
			echo '
				<script type="text/javascript">
					function iWantThisDate(){
						var iWantThisMonth = document.getElementById("iWantThisMonth").value;
						var iWantThisYear = document.getElementById("iWantThisYear").value;
						
						window.location.replace("birthdays.php?m="+iWantThisMonth+"&y="+iWantThisYear);
					}
				</script>';
			
			echo '
				<script type="text/javascript">
					function Ajax_giftPresent(client){
						ajax({
							url: "add_birthday.php",
							method: "POST",
							
							data:
							{
								client: client,
								month: '.$month.',
								year: '.$year.',
								session_id: '.$_SESSION['id'].'
							},
							success: function(req){
								//document.getElementById("errror").innerHTML = req;
								location.reload(true);
							}
						});
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