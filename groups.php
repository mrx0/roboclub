<?php

//groups.php
//Группы

    require_once 'header.php';
	require_once 'header_tags.php';
	
	if ($enter_ok){
		//var_dump($_SESSION);
		if (($groups['see_all'] == 1) || ($groups['see_own'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';
			include_once 'filter.php';
			include_once 'filter_f.php';
			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Группы</h1>';
			
			if (($groups['add_new'] == 1) || $god_mode){
				echo '
						<a href="add_group.php" class="b">Добавить группу</a>';
			}
			
			$journal_groups = 0;
			
			if (($groups['see_all'] == 1) || $god_mode){
				$journal_groups = SelDataFromDB('journal_groups', '', '');
			}elseif ($groups['see_own'] == 1){
				$journal_groups = SelDataFromDB('journal_groups', $_SESSION['id'], 'worker');
			}
			
			echo '
				</header>';
			
			
			if ($journal_groups != 0){
				var_dump ($journal_groups);
				
				echo '
					<p style="margin: 5px 0; padding: 1px; font-size:80%;">
						Быстрый поиск: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
					</p>';
					
				echo '
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Дата</div>
								<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Пациент</div>
								<div class="cellCosmAct" style="text-align: center">-</div>';
				if (($stom['see_all'] == 1) || $god_mode){
					echo '<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Врач</div>';
				}

				echo '
								<div class="cellText" style="text-align: center">Комментарий</div>
							</li>';
				
				//!!!!!!тест санации Sanation ($journal);
				
				for ($i = 0; $i < count($journal); $i++) {
					$rez_color = '';
					
					$journal_ex_bool = FALSE;
					
					if ((isset($filter_rez['pervich'])) && ($filter_rez['pervich'] == true)){
						$query = "SELECT * FROM `journal_tooth_ex` WHERE `pervich` = 1 AND `id` = '{$journal[$i]['id']}' ORDER BY `id` DESC";
						$res = mysql_query($query) or die($query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							$journal_ex_bool = true;
						}else{
						}
					}
					if ((isset($filter_rez['pervich']) && $journal_ex_bool) || (!isset($filter_rez['pervich']))){
					//if (($journal[$i]['create_time'] >= $datestart)  && ($journal[$i]['create_time'] <= $datefinish)){
						//Надо найти клиента
						$clients = SelDataFromDB ('spr_clients', $journal[$i]['client'], 'client_id');
						if ($clients != 0){
							$client = $clients[0]["name"];
							if ($clients[0]["birthday"] != -1577934000){
								$cl_age = getyeardiff($clients[0]["birthday"]);
							}else{
								$cl_age = 0;
							}
						}else{
							$client = 'unknown';
							$cl_age = 0;
						}
						
						//Дополнительно
						$dop = array();
						$dop_img = '';
						$query = "SELECT * FROM `journal_tooth_ex` WHERE `id` = '{$journal[$i]['id']}'";
						$res = mysql_query($query) or die($query);
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								array_push($dop, $arr);
							}
							
						}
						//var_dump ($dop);
						if (!empty($dop)){
							if ($dop[0]['insured'] == 1){
								$dop_img .= '<img src="img/insured.png" title="Страховое">';
							}
							if ($dop[0]['pervich'] == 1){
								$dop_img .= '<img src="img/pervich.png" title="Первичное">';
							}
							if ($dop[0]['noch'] == 1){
								$dop_img .= '<img src="img/night.png" title="Ночное">';
							}
						}
						
						echo '
							<li class="cellsBlock cellsBlockHover">
									<a href="task_stomat_inspection.php?id='.$journal[$i]['id'].'" class="cellName ahref" title="'.$journal[$i]['id'].'">'.date('d.m.y H:i', $journal[$i]['create_time']).' '.$dop_img.'</a>
									<a href="client.php?id='.$journal[$i]['client'].'" class="cellName ahref" '.$id4filter4worker.'>'.$client.'</a>';
						
						if (Sanation2($journal[$i]['id'], $journal[$i], $cl_age)){
							$rez_color = "style= 'background: rgba(87,223,63,0.7);'";
						}else{
							$rez_color = "style= 'background: rgba(255,39,119,0.7);'";
						}
						echo '
									<div class="cellCosmAct" '.$rez_color.'>
										<a href="#" onclick="window.open(\'task_stomat_inspection_window.php?id='.$journal[$i]['id'].'\',\'test\', \'width=700,height=350,status=no,resizable=no,top=200,left=200\'); return false;">
											<img src="img/tooth_state/1.png">
										</a>	
									</div>';
									
						if (($stom['see_all'] == 1) || $god_mode){
							echo '<a href="user.php?id='.$journal[$i]['worker'].'" class="cellName ahref" '.$id4filter4upr.'>'.WriteSearchUser('spr_workers', $journal[$i]['worker'], 'user').'</a>';
						}		
						
						/*echo '
								<div class="cellName">!!!ТИП</div>';*/
						
						$decription = array();
						$decription_temp_arr = array();
						$decription_temp = '';
						
						/*!!!ЛАйфхак для посещений из-за переделки структуры бд*/
						/*foreach($journal[$i] as $key => $value){
							if (($key != 'id') && ($key != 'office') && ($key != 'client') && ($key != 'create_time') && ($key != 'create_person') && ($key != 'last_edit_time') && ($key != 'last_edit_person') && ($key != 'worker') && ($key != 'comment')){
								$decription_temp_arr[mb_substr($key, 1)] = $value;
							}
						}*/
						
						//var_dump ($decription_temp_arr);
						
						$decription = $decription_temp_arr;

						//array_multisort($data_nomer, SORT_NUMERIC, $decription);
						
						//var_dump ($decription);		
						//var_dump ($actions_stomat);		
						
						//for ($j = 1; $j <= count($actions_stomat)-2; $j++) { 
						/*foreach ($actions_stomat as $key => $value) { 
							$cell_color = '#FFFFFF';
							$action = '';
							if ($value['active'] != 0){
								if (isset($decription[$value['id']])){
									if ($decription[$value['id']] != 0){
										$cell_color = $value['color'];
										$action = 'V';
									}
									echo '<div class="cellCosmAct" style="text-align: center; background-color: '.$cell_color.';">'.$action.'</div>';
								}else{
									echo '<div class="cellCosmAct" style="text-align: center"></div>';
								}
							}
						}*/
						
						echo '
									<div class="cellText">'.$journal[$i]['comment'].'</div>
							</li>';
					}
				}
				echo '
						</ul>
					</div>';
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>