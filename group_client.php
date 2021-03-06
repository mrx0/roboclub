<?php

//group_client.php
//

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
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
						<div id="request"></div>
						<header>
							<h2>Группа < <a href="group.php?id='.$_GET['id'].'" class="ahref">'.$j_group[0]['name'].'</a> ></h2>
							<span style="font-size: 90%; color: #999">Комментарий: '.$j_group[0]['comment'].'</span>
						</header>';
				if ($j_group[0]['close'] == '1'){
					echo '<span style="color:#EF172F;font-weight:bold;">ЗАКРЫТА</span>';
				}

				echo '
					<script type="text/javascript">
						var group = '.$_GET['id'].';
						var session_id = '.$_SESSION['id'].';
					</script>
				';
				
				
				echo '
						<div id="data">';

				echo '
							<div style="font-size: 90%; color: #999">Филиал: ';
				//Филиалы
				$j_filials = SelDataFromDB('spr_office', $j_group[0]['filial'], 'offices');
				
				if ($j_filials != 0){
					echo '<span style="color: rgb(36,36,36); font-weight: bold;"><a href="filial.php?id='.$j_group[0]['filial'].'" class="ahref">'.$j_filials[0]['name'].'</a></span>';
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
								<span style="color: rgb(36,36,36); font-weight: bold;">';
				if ($j_group[0]['worker'] != 0) {
                    echo '
								    <a href="user.php?id=' . $j_group[0]['worker'] . '" class="ahref">' . WriteSearchUser('spr_workers', $j_group[0]['worker'], 'user') . '</a>';
                }else{
                    echo 'не назначен';
                }
                echo '
								</span>
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

                            $bgColor = '';
                            if ($uch_arr[$i]['status'] == 9){
                                $bgColor = 'background-color: #ABABAB';
                            }

							echo '
									<li class="cellsBlock cellsBlockHover" style="width: auto; '.$bgColor.'">
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
										
							if (($uch_arr[$i]['birthday'] == "-1577934000") || ($uch_arr[$i]['birthday'] == 0)){
								$age = '';
							}else{
								$age = getyeardiff($uch_arr[$i]['birthday']).' лет';
							}
							
							echo '
										<div class="cellTime" style="width: 140px; text-align: center">', (($uch_arr[$i]['birthday'] == "-1577934000") || ($uch_arr[$i]['birthday'] == 0)) ? "не указана" : date("d.m.Y", $uch_arr[$i]["birthday"]) ,' / <b>'.$age.'</b></div>';
							if (($groups['edit'] == 1) || $god_mode){
								echo '
										<div id="delClientFromGroup" clientid="'.$uch_arr[$i]['id'].'" class="cellCosmAct delClientFromGroup" style="text-align: center">
											<i class="fa fa-minus" style="color: red; cursor: pointer;"></i>
										</div>';
							}
							echo '	
									</li>';
						}
					}
					echo '
							</ul>';
					
					if (($groups['edit'] == 1) || $god_mode){
						echo '
							<br>
							<span class="delAllClientFromGroup" style="border-bottom: 1px dashed #000080; text-decoration: none; font-size: 70%; color: red; background-color: rgba(252, 252, 0, 0.3); cursor: pointer;">Удалить всех</span>';
					}
					
					if (($groups['edit'] == 1) || $god_mode){
						echo '<br><br>';

						//Участники, которых нигде нет в группах
						$free_uch_arr = SelDataFromDB('spr_clients', $_GET['id'], 'free_client_group');			
						//var_dump ($free_uch_arr);
						
						if ($free_uch_arr != 0){
							echo '
								<div style="font-size: 90%; margin-bottom: 10px;">
									В эту группу можно добавить: <br>
									<span  style="font-size: 70%; color: rgb(100,100,100);">Отображаются только дети,<br>у которых отмечен район текущей группы</span>
								</div>';
								
							echo '
										<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">';
										
							for ($i = 0; $i < count($free_uch_arr); $i++) {
							    if ($free_uch_arr[$i]['status'] != 9) {
                                    if ((getyeardiff($free_uch_arr[$i]['birthday']) >= $ages[0]['from_age']) && (getyeardiff($free_uch_arr[$i]['birthday']) <= $ages[0]['to_age'])) {
                                        //а не находится ли он в другой группе
                                        $inGroup = '';
                                        $groups = SelDataFromDB('journal_groups_clients', $free_uch_arr[$i]['id'], 'client');
                                        if ($groups != 0) {
                                            //var_dump ($groups);
                                            foreach ($groups as $key => $value) {
                                                $group = SelDataFromDB('journal_groups', $value['group_id'], 'id');
                                                if ($group != 0) {
                                                    $inGroup .= '<a href="group.php?id=' . $value['group_id'] . '" class="ahref" style="padding: 0 4px; background-color: ' . $group[0]['color'] . '">' . $group[0]['name'] . '</a>';
                                                } else {
                                                    $inGroup .= 'ошибка группы';
                                                }
                                            }
                                        } else {
                                            $inGroup .= 'не в группе';
                                        }

                                        echo '
                                                <li class="cellsBlock cellsBlockHover" style="width: auto;">
                                                    <a href="client.php?id=' . $free_uch_arr[$i]['id'] . '" class="cellFullName ahref" id="4filter">' . $free_uch_arr[$i]['full_name'] . '</a>';
                                        echo '
                                                    <div class="cellCosmAct" style="text-align: center">';
                                        if ($free_uch_arr[$i]['sex'] != 0) {
                                            if ($free_uch_arr[$i]['sex'] == 1) {
                                                echo 'М';
                                            }
                                            if ($free_uch_arr[$i]['sex'] == 2) {
                                                echo 'Ж';
                                            }
                                        } else {
                                            echo '-';
                                        }

                                        echo '
                                                    </div>';
                                        if (($free_uch_arr[$i]['birthday'] == "-1577934000") || ($free_uch_arr[$i]['birthday'] == 0)) {
                                            $age = '';
                                        } else {
                                            $age = getyeardiff($free_uch_arr[$i]['birthday']) . ' лет';
                                        }

                                        echo '
                                                    <div class="cellTime" style="width: 140px; text-align: center">', (($free_uch_arr[$i]['birthday'] == "-1577934000") || ($free_uch_arr[$i]['birthday'] == 0)) ? "не указана" : date("d.m.Y", $free_uch_arr[$i]["birthday"]), ' / <b>' . $age . '</b></div>
                                                    <div id="addClientInGroup" class="cellCosmAct addClientInGroup" style="text-align: center" onclick="addClientInGroup(' . $free_uch_arr[$i]['id'] . ', ' . $_GET['id'] . ');">
                                                        <i class="fa fa-plus" style="color: green; cursor: pointer;"></i>
                                                    </div>
                                                    <div id="cellTime" class="cellTime" style="text-align: center; font-size: 80%;">
                                                        ' . $inGroup . '
                                                    </div>
                                                </li>';
                                    }
                                }
							}
								
							echo '
									</ul>';
						}else{
							echo '
								<div style="font-size: 90%; margin-bottom: 10px;">
									Нет кандидатов на распределение в группы в этом районе
								</div>';
						}
						
						/*echo '
									<a href="group_add_client.php?id='.$_GET['id'].'" class="b">Заполнить участниками</a>';*/

						echo '
						<div style="position: absolute; top: 20px; right: 50px; z-index: 101;">';
									
						echo '
							<div style="font-size: 90%; margin-bottom: 10px;">
								Добавление "в лоб": <br>
								<span  style="font-size: 70%; color: rgb(100,100,100);">Поиск и добавление участников,в обход критериев (район, возраст...)</span>
							</div>';

                        echo '
                            <div class="cellsBlock2" style="width: 400px;">';

                                echo $block_fast_search_client;

                                echo '
                            </div>';

                        echo '
                        </div>';
									
						echo '
							<script type="text/javascript">
								$(document).ready(function(){
									
									$(\'.delClientFromGroup\').on(\'click\', function(data){
										var rys = confirm("Вы уверены?");
										if (rys){
											var id = $(this).attr(\'clientid\');
											ajax({
												url: "del_ClientFromGroup_f.php",
												method: "POST",
												
												data:
												{
													id: id,
													group: '.$_GET['id'].',
													session_id: '.$_SESSION['id'].'
												},
												success: function(req){
													//document.getElementById("request").innerHTML = req;
													alert(req);
													location.reload(true);
												}
											})
										}
									})
									$(\'.delAllClientFromGroup\').on(\'click\', function(data){
										var rys = confirm("Это удалит всех из этой группы\n\nВы уверены?");
										if (rys){
											ajax({
												url: "del_ALLClientFromGroup_f.php",
												method: "POST",
												
												data:
												{
													group: '.$_GET['id'].',
													session_id: '.$_SESSION['id'].'
												},
												success: function(req){
													//document.getElementById("request").innerHTML = req;
													alert(req);
													location.reload(true);
												}
											})
										}
									})
								});
							</script>
						';			
									
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