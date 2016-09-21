<?php

//filial_finance.php
//Финансы филиала.

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
			
		if (($finance['add_new'] == 1) || $god_mode){
			echo '
				<header>
					<h1>Финансы филиалов</h1>
				</header>';			
		
			if (isset($_GET['m']) && isset($_GET['y'])){
				$year = $_GET['y'];
				$month = $_GET['m'];
			}else{
				$year = date("Y");
				$month = date("m");
			}
		
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
					
			echo '<span style="font-size: 80%; color: #CCC;">Сегодня: <a href="filial_finance.php" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>';	
					echo '
						<div id="data">		
							<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">
								<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
									<a href="filial_finance.php?m='.$prev.'&y='.$pYear.'" class="cellTime ahref" style="text-align: center;">
										<span style="font-weight: normal; font-size: 70%;"><< '.$monthsName[$prev].'<br>'.$pYear.'</span>
									</a>
									<div class="cellTime" style="text-align: center;">
										<span style="color: #2EB703">'.$monthsName[$month].'</span><br>'.$year.'
									</div>
									<a href="filial_finance.php?m='.$next.'&y='.$nYear.'" class="cellTime ahref" style="text-align: center;">
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
					<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
						<div class="cellPriority" style="text-align: center"></div>
						<div class="cellOffice" style="width: 180px; text-align: center">Филиал</div>
						<div class="cellText" style="text-align: center">Остаток</div>
					</li>';
		
			include_once 'DBWork.php';
			$filials = SelDataFromDB('spr_office', '', '');
			//var_dump ($filials);
		
			$closed_filials = '';
			
			if ($filials !=0){
				for ($i = 0; $i < count($filials); $i++) {
					
					$summa = 0;
					
					//хочу получить все платежи этого филиала за указанный месяц
					require 'config.php';	
					
					//Смотрим посещения
					$arr = array();
					$journal_fin = array();
										
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$query = "SELECT `summ` FROM `journal_finance` WHERE `filial` = '{$filials[$i]['id']}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
					$res = mysql_query($query) or die(mysql_error());
					$number = mysql_num_rows($res);
					if ($number != 0){
						while ($arr = mysql_fetch_assoc($res)){
							array_push($journal_fin, $arr);
							$summa = $summa + $arr['summ'];
						}
					}else{
						//$journal_fin = 0;
					}
					//var_dump($summa);

					$result_html = '';
					
					if ($filials[$i]['close'] == 0){
						$bg_color = '';
						$cls_img = '<img src="img/delete.png" title="Закрыть">';
					}else{
						$bg_color = 'background-color: rgba(161,161,161,1);';
						$cls_img = '<img src="img/reset.png" title="Открыть">';
					}
					
					$result_html .= '
								<li class="cellsBlock cellsBlockHover" style="font-weight: bold; width: auto; text-align: right;">
									<div class="cellPriority" style="text-align: center; background-color: '.$filials[$i]['color'].';"></div>
									<div class="cellOffice" style="width: 180px; text-align: center;'.$bg_color.'" id="4filter"><a href="filial.php?id='.$filials[$i]['id'].'" class="ahref">'.$filials[$i]['name'].'</a></div>
									<div class="cellText" style="text-align: right;'.$bg_color.'">'.$summa.' руб.</div>
								</li>';
								
					if ($filials[$i]['close'] == 0){
						echo $result_html;
					}else{
						$closed_filials .= $result_html;
					}
				}
				echo $closed_filials;
			}

			echo '
					</ul>
				</div>';
			echo '
				<script type="text/javascript">
					function iWantThisDate(){
						var iWantThisMonth = document.getElementById("iWantThisMonth").value;
						var iWantThisYear = document.getElementById("iWantThisYear").value;
						
						window.location.replace("filial_finance.php?m="+iWantThisMonth+"&y="+iWantThisYear);
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