<?php
//user.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			$j_group = SelDataFromDB('journal_groups', $_GET['id'], 'group');
			//var_dump($j_group);
		
			if ($j_group != 0){
				echo '
					<div id="status">
						<header>
							<h2>Группа < <a href="group.php?id='.$_GET['id'].'" class="ahref">'.$j_group[0]['name'].'</a> ></h2>
						</header>';
				if ($j_group[0]['close'] == '1'){
					echo '<span style="color:#EF172F;font-weight:bold;">ЗАКРЫТА</span>';
				}

				echo '
						<div id="data">';

				echo '
							<div style="font-size: 90%; color: #999">Филиал: ';
				//Филиалы
				$j_filials = SelDataFromDB('spr_office', $j_group[0]['filial'], 'offices');
				
				if ($j_filials != 0){
					echo '<span style="color: rgb(36,36,36); font-weight: bold;">'.$j_filials[0]['name'].'</span>';
				}else{
					echo 'unknown';
				}
				echo '
							</div>';

				echo '
							<div style="font-size: 90%; color: #999">Возраст: ';
				//Возрасты
				$ages = SelDataFromDB('spr_ages', $j_group[0]['age'], 'ages');
				if ($ages != 0){
					echo '<span style="color: rgb(36,36,36); font-weight: bold;">'.$ages[0]['from_age'].' - '.$ages[0]['to_age'].'</span>';
				}else{
					echo 'unknown';
				}	
				echo '
							</div>
							
							<div style="font-size: 90%; color: #999; margin-bottom: 20px;">Тренер: 
								<span style="color: rgb(36,36,36); font-weight: bold;">'.WriteSearchUser('spr_workers', $j_group[0]['worker'], 'user').'</span>
							</div>';
				
				if ($j_group[0]['close'] != '1'){
					
					//Сколько участников уже есть в группе
					$uch_arr = SelDataFromDB('spr_clients', $_GET['id'], 'client_group');
					
					//var_dump($uch_count);
					echo '
								<div style="font-size: 90%; margin-bottom: 20px;">Всего в группе 
									<span style="color: rgb(36,36,36); font-weight: bold;">';
					if ($uch_arr != 0) echo count($uch_arr);
					else echo 0;
					echo '
									</span> участников
								</div>';
					
					echo '
								<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
					if ($uch_arr != 0){			
						for ($i = 0; $i < count($uch_arr); $i++) { 
							echo '
									<li class="cellsBlock cellsBlockHover" style="width: auto;">
										<a href="client.php?id='.$uch_arr[$i]['id'].'" class="cellFullName ahref" id="4filter">'.$uch_arr[$i]['full_name'].'</a>';
							echo '
										<div class="cellCosmAct" style="text-align: center">';
							if ($uch_arr[$i]['sex'] != 0){
								if ($uch_arr[$i]['sex'] == 1){
									echo 'М';
								}
								if ($uch_arr[$i]['sex'] == 2){
									echo 'Ж';
								}
							}else{
								echo '-';
							}
							
							echo '
										</div>';

							echo '
										<div class="cellTime" style="width: 140px; text-align: center">', $uch_arr[$i]['birthday'] == '-1577934000' ? 'не указана' : date('d.m.Y', $uch_arr[$i]['birthday']) ,' / <b>'.getyeardiff( $uch_arr[$i]['birthday']).' лет</b></div>
										<div class="cellCosmAct" style="text-align: center"> -
										</div>
									</li>';
						}
					}
					echo '
							</ul>';
						
					if (($groups['edit'] == 1) || $god_mode){
						echo '<br /><br />';

						//Участники, которых нигде нет в группах
						$free_uch_arr = SelDataFromDB('spr_clients', $_GET['id'], 'free_client_group');			
						//var_dump ($free_uch_arr);
						
						if ($free_uch_arr != 0){
							echo '
								<div style="font-size: 90%; margin-bottom: 10px;">
									В эту группу можно добавить: <br>
									<span  style="font-size: 70%; color: rgb(100,100,100);">Отображаются только клиенты,<br>у которых отмечен район текущей группы</span>
								</div>';
								
							echo '
										<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
										
							for ($i = 0; $i < count($free_uch_arr); $i++) { 
								echo '
										<li class="cellsBlock cellsBlockHover" style="width: auto;">
											<a href="client.php?id='.$uch_arr[$i]['id'].'" class="cellFullName ahref" id="4filter">'.$free_uch_arr[$i]['full_name'].'</a>';
								echo '
											<div class="cellCosmAct" style="text-align: center">';
								if ($free_uch_arr[$i]['sex'] != 0){
									if ($free_uch_arr[$i]['sex'] == 1){
										echo 'М';
									}
									if ($free_uch_arr[$i]['sex'] == 2){
										echo 'Ж';
									}
								}else{
									echo '-';
								}
								
								echo '
											</div>';

								echo '
											<div class="cellTime" style="width: 140px; text-align: center">', $free_uch_arr[$i]['birthday'] == '-1577934000' ? 'не указана' : date('d.m.Y', $free_uch_arr[$i]['birthday']) ,' / <b>'.getyeardiff( $free_uch_arr[$i]['birthday']).' лет</b></div>
											<div class="cellCosmAct" style="text-align: center"> +
											</div>
										</li>';
							}
								
							echo '
									</ul>';
						}else{
							echo '
								<div style="font-size: 90%; margin-bottom: 10px;">
									Нет кандидатов на распределение в группы в этом районе
								</div>';
						}
						
						echo '
									<a href="group_add_client.php?id='.$_GET['id'].'" class="b">Заполнить участниками</a>';

						echo '
									<br /><br />';
					}
				}
			}else{
				echo '<span style="color: #EF172F; font-weight: bold;">Такой группы нет</span>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>