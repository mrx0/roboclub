<?php

//finances.php
//

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
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
			
			if (isset($_GET['in'])){
				if ($_GET['in'] == 1){
					//нулевой день следующего месяца - это последний день предыдущего
					$firstday = strtotime('1.'.$month.'.'.$year);
					$lastday = mktime(0, 0, 0, $month+1, 0, $year);
					
					//var_dump($firstday);
					//var_dump($lastday);
					
					$pageHeader = 'Все платежи, внесённые в этом месяце';
					$pageHeaderAnother = 'Все платежи, внесённые за этот месяц';
					$link = '?m='.$month.'&y='.$year;
					//$query = "SELECT * FROM `journal_finance` WHERE `month` = '{$month}' AND  `year` = '{$year}'";
					$query = "SELECT * FROM `journal_finance` WHERE `create_time` BETWEEN '{$firstday}' AND '{$lastday}' ORDER BY `filial`, `create_time`";
				}else{
					$pageHeader = 'Все платежи, внесённые за этот месяц';
					$pageHeaderAnother = 'Все платежи, внесённые в этом месяце';
					$link = '?in=1&m='.$month.'&y='.$year;
					//$query = "SELECT * FROM `journal_finance` WHERE `month` = '{$month}' AND  `year` = '{$year}' AND `type`<>'2' ORDER BY `filial`, `create_time`";
					$query = "SELECT * FROM `journal_finance` WHERE `month` = '{$month}' AND  `year` = '{$year}' ORDER BY `filial`, `create_time`";
				}
			}else{
				$pageHeader = 'Все платежи, внесённые за этот месяц';
				$pageHeaderAnother = 'Все платежи, внесённые в этом месяце';
				$link = '?in=1&m='.$month.'&y='.$year;
				//$query = "SELECT * FROM `journal_finance` WHERE `month` = '{$month}' AND  `year` = '{$year}' AND `type`<>'2' ORDER BY `filial`, `create_time`";
				$query = "SELECT * FROM `journal_finance` WHERE `month` = '{$month}' AND  `year` = '{$year}' ORDER BY `filial`, `create_time`";
			}

			
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>'.$pageHeader.'</h1>
					<!--<a href="finances.php'.$link.'" class="" style="border-bottom: 1px dashed #000080; text-decoration: none; font-size: 70%; color: #999; background-color: rgba(252, 252, 0, 0.3);">'.$pageHeaderAnother.'</a> -->
					<a href="filial_finance.php" class="" style="border-bottom: 1px dashed #000080; text-decoration: none; color: #999; background-color: rgba(252, 252, 0, 0.3);">Финансы по филиалам</a>
					<a href="balance.php" class="" style="border-bottom: 1px dashed #000080; text-decoration: none; color: #999; background-color: rgba(252, 252, 0, 0.3);">Остатки</a>
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
			
			echo '<span style="font-size: 80%; color: #CCC;">Сегодня: <a href="finances.php" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>';	
			echo '
				<div id="data">		
					<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">
						<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
							<a href="finances.php?m='.$prev.'&y='.$pYear.'" class="cellTime ahref" style="text-align: center;">
								<span style="font-weight: normal; "><< '.$monthsName[$prev].'<br>'.$pYear.'</span>
							</a>
							<div class="cellTime" style="text-align: center;">
								<span style="color: #2EB703">'.$monthsName[$month].'</span><br>'.$year.'
							</div>
							<a href="finances.php?m='.$next.'&y='.$nYear.'" class="cellTime ahref" style="text-align: center;">
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
						
									
			echo '

				<div style="font-size: 90%; margin-bottom: 10px;">
					<span  style="font-size: 80%; color: rgb(100,100,100);">Чтобы добавить платёж, <br>найдите ребёнка и нажмите <i class="fa fa-rub" style="font-size: 150%;color: green;"></i><i class="fa fa-plus" style="font-size: 90%;color: green;"></i></span>
				</div>';

            echo '
					<div class="cellsBlock2" style="width: 400px; ">';

            echo $block_fast_search_client;

            echo '
					</div>';

            echo '
				<br>';	
			/*echo '
					<p style="margin: 5px 0; padding: 2px; font-size: 80%;">
						Фильтр:
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
					</p>';*/
			
			require 'config.php';	
				
			$arr = array();
			$journal = array();
										
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			//$query = "SELECT * FROM `journal_finance` WHERE `month` = '{$month}' AND  `year` = '{$year}'";
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
				
				$currentFilial = 0;
				$bgFilialColor = '';
				
				echo '
							<li class="cellsBlock" style="font-weight:bold; width: auto;">	
								<div class="cellPriority" style="text-align: center"></div>
								<div class="cellCosmAct" style="text-align: center;"></div>
								<div class="cellName" style="text-align: center">Дата</div>
								<div class="cellFullName" style="text-align: center">Полное имя</div>
								<div class="cellName" style="text-align: center">Месяц/Год</div>
								<div class="cellTime" style="text-align: center">Сумма</div>
							</li>';
				
				$filialSumm = 0;
				$filialSummAmort = 0;
				$filialAmortStr = '';
					
				for ($i = 0; $i < count($journal); $i++) {
					if ($currentFilial != $journal[$i]['filial']){
						$currentFilial = $journal[$i]['filial'];
						$filial = SelDataFromDB('spr_office', $currentFilial, 'id');
						$bgFilialColor = 'background-color: '.$filial[0]['color'];
						//var_dump($filial);

						echo '
							<li class="cellsBlock" style="font-weight: bold; width: auto;">	
								<div class="cellName" style=" width: 660px; text-align: left; background-color: '.$filial[0]['color'].';">'.$filial[0]['name'].'</div>
							</li>';
					}
					
					$backSummColor = '';
					$mark = '';
					$markClass = '';
					$markColor = '';
					
					if ($journal[$i]['mark'] == 1){
						$mark .= '<i class="fa fa-check"></i>';
						$markClass .= ' markChecked';
						$markColor .= ' background-color: rgba(46, 183, 3, 0.48);';
					}else{
						$mark .= '<i class="fa fa-dot-circle-o"></i>';
					}
					
					
					if ($journal[$i]['type'] == 2){
						$backSummColor = "background-color: rgba(0, 201, 255, 0.5)";
						$filialAmortStr .= '
							<li class="cellsBlock cellsBlockHover'.$markClass.'" style="width: auto;">
								<div class="cellPriority" style="text-align: center; '.$bgFilialColor.'"></div>
								<div class="cellCosmAct" style="text-align: center; font-size: 110%; cursor: pointer;'.$markColor.'" onclick="Ajax_markFinance('.$journal[$i]['id'].')">
									'.$mark.'
								</div>
								<a href="finance.php?id='.$journal[$i]['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', $journal[$i]['create_time']).'</a>
								<a href="client.php?id='.$journal[$i]['client'].'" class="cellFullName ahref" id="4filter">'.WriteSearchUser('spr_clients', $journal[$i]['client'], 'user_full').'</a>
								<div class="cellName" style="text-align: center">'.$monthsName[$journal[$i]['month']].'/'.$journal[$i]['year'].'</div>
								<div class="cellTime" style="text-align: center; font-size: 110%; font-weight: bold; '.$backSummColor.'">'.$journal[$i]['summ'].'</div>
							</li>';
					}else{
					
						echo '
								<li class="cellsBlock cellsBlockHover'.$markClass.'" style="width: auto;">
									<div class="cellPriority" style="text-align: center; '.$bgFilialColor.'"></div>
									<div class="cellCosmAct" style="text-align: center; font-size: 110%; cursor: pointer;'.$markColor.'" onclick="Ajax_markFinance('.$journal[$i]['id'].')">
										'.$mark.'
									</div>
									<a href="finance.php?id='.$journal[$i]['id'].'" class="cellName ahref" style="text-align: center">'.date('d.m.y H:i', $journal[$i]['create_time']).'</a>
									<a href="client.php?id='.$journal[$i]['client'].'" class="cellFullName ahref" id="4filter">'.WriteSearchUser('spr_clients', $journal[$i]['client'], 'user_full').'</a>
									<div class="cellName" style="text-align: center">'.$monthsName[$journal[$i]['month']].'/'.$journal[$i]['year'].'</div>
									<div class="cellTime" style="text-align: center; font-size: 110%; font-weight: bold; '.$backSummColor.'">'.$journal[$i]['summ'].'</div>
								</li>';
					}
					
					if (!isset($journal[$i+1]['filial']) || ($currentFilial != $journal[$i+1]['filial'])){
						if ($journal[$i]['type'] == 2){
							$filialSummAmort += $journal[$i]['summ'];
						}else{
							$filialSumm += $journal[$i]['summ'];
						}
						echo $filialAmortStr;
						echo '
							<li class="cellsBlock" style="font-weight: bold; width: auto;">	
								<div class="cellName" style=" width: 630px; text-align: right;">
									Итого: <span style="font-size: 130%; color: red;"><i>'.$filialSumm.'</i></span> руб.
									Аморт.: <span style="font-size: 130%; color: rgba(0, 201, 255, 0.5);"><i>'.$filialSummAmort.'</i></span> руб.
									Всего: <span style="font-size: 130%; color: green;"><i>'.($filialSumm + $filialSummAmort).'</i></span> руб.
								</div>
							</li>';
						$filialSumm = 0;
						$filialSummAmort = 0;
						$filialAmortStr = '';
					}else{
						if ($journal[$i]['type'] == 2){
							$filialSummAmort += $journal[$i]['summ'];
						}else{
							$filialSumm += $journal[$i]['summ'];
						}
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
                        var iWantThisMonth = $("#iWantThisMonth").val();
                        var iWantThisYear =  $("#iWantThisYear").val();
						
						window.location.replace("finances.php?m="+iWantThisMonth+"&y="+iWantThisYear);
					}
				</script>';
			echo '
				<script type="text/javascript">
					function Ajax_markFinance(finance){
						ajax({
							url: "add_finance_mark.php",
							method: "POST",
							
							data:
							{
								finance: finance,
							},
							success: function(req){
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