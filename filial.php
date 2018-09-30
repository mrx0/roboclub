<?php

//filial.php
//Редактирование филиала

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($offices['see_all'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$filial = SelDataFromDB('spr_office', $_GET['id'], 'id');
				//var_dump($filial);

                if ($filial !=0) {
                    if ($filial[0]['close'] == 1) {
                        $closed = TRUE;
                    } else {
                        $closed = FALSE;
                    }
                }

				echo '
					<div id="status">
						<header>
							<h2>Филиал';

                if (($offices['edit'] == 1) || $god_mode){

                    if (!$closed){
                        echo '
							<a href="edit_filial.php?id='.$filial[0]['id'].'" class=""><img src="img/edit.png" title="Редактировать"></a>';
                    }
                }
                echo '
				            </h2>';
                echo '
						</header>';

                //перешли сюда после добавления ребенка
                if (($groups['edit'] == 1) || $god_mode) {
                    if (isset($_GET['client_id_add'])) {
                        echo '<span style="color: red">Можете добавить ребёнка 
                        <a href="client.php?id='.$_GET['client_id_add'].'" class="ahref">'.WriteSearchUser('spr_clients', $_GET['client_id_add'], 'user_full').'</a> в группу</span><br>
                        <span style="color: red">Нажмите кнопку <i class="fa fa-plus" style="color: green; "></i> напротив нужной группы</span>';
                    }
                }

				echo '
						<div id="data">';
				
				if ($filial !=0){

                    if ($closed){
						echo '<div style="margin-bottom: 10px;"><span style= "background: rgba(255,39,119,0.7);">Филиал закрыт</span></div>';
					}

					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight colorizeText" style="background-color: '.$filial[0]['color'].';">'.$filial[0]['name'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Адрес</div>
									<div class="cellRight">'.$filial[0]['address'].'</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">'.$filial[0]['contacts'].'</div>
								</div>';
					if (($offices['edit'] == 1) || $god_mode){

						if ($closed){

                            echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Закрыть</div>
									<div class="cellRight">
										<a href="close_filial.php?id='.$filial[0]['id'].'&close=1" style="float: right;"><img src="img/reset.png" title="Открыть"></a>';
                            echo '
									</div>
								</div>';
						}

					}
					if (!$closed){
						echo '
								<br /><br />
								
								<a href="filial_shed.php?id='.$filial[0]['id'].'" class="b">Расписание филиала</a>';
					}			

					
					if (($groups['see_all'] == 1) || ($groups['see_own'] == 1) || $god_mode){	
						$journal_groups = SelDataFromDB('journal_groups', $filial[0]['id'], 'filial');
						//var_dump($journal_groups);

						echo '
								<ul style="margin-left: 6px; margin-bottom: 20px; border: 1px solid #CCC; width: auto; padding: 7px;">';
								
						echo '				
									<li class="cellsBlock" style="width: auto; text-align: left; font-size: 80%; color: #777; margin-bottom: 10px;">
										Группы на этом филиале<br>
										<span style="font-size: 80%">Тренеры видят только свои группы, если они есть.</span>
									</li>';
									
						if ($journal_groups != 0){
							
							echo '
											<li class="cellsBlock sticky" style="font-weight:bold; background-color:#FEFEFE;">
												<div class="cellPriority" style="text-align: center"></div>
												<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Название</div>
												<div class="cellCosmAct" style="text-align: center" title="Журнал группы">-</div>
												<div class="cellCosmAct" style="text-align: center" title="Участники группы">-</div>
												<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Филиал</div>
												<div class="cellCosmAct" style="text-align: center" title="Расписание филиала">-</div>
												<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Возраст</div>
												<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Тренер</div>
												<div class="cellName" style="text-align: center; background-color:#FEFEFE;">Участников</div>
												<div class="cellText" style="text-align: center">Комментарий</div>';
							if (($groups['edit'] == 1) || $god_mode){
							    if (isset($_GET['client_id_add'])){
                                    echo '
												<div class="cellCosmAct" style="text-align: center" title="Добавить сюда">-</div>';
                                }
								echo '
												<div class="cellCosmAct" style="text-align: center" title="Редактировать">-</div>
												<div class="cellCosmAct" style="text-align: center" title="Закрыть">-</div>';
							}
							
							$closed_groups = '';
										
							for ($i = 0; $i < count($journal_groups); $i++) {
								
								$result_html = '';
								
								//Если закрыта группа
								if ($journal_groups[$i]['close'] == '1'){
									$bg_color = ' background-color: rgba(161,161,161,1);';
									$cls_img = '<img src="img/reset.png" title="Открыть">';
								}else{
									$bg_color = '';
									$cls_img = '<img src="img/delete.png" title="Закрыть">';						
								}
								
								//Если в группе нет тренера
								if ($journal_groups[$i]['worker'] == 0){
									$trenerValue = '<div class="cellName" style="text-align: center; background-color: rgba(222, 8, 8, 0.51);">не указан</div>';
								}else{
									$trenerValue = '<a href="user.php?id='.$journal_groups[$i]['worker'].'" class="cellName ahref" style="text-align: center;'.$bg_color.'">'.WriteSearchUser('spr_workers', $journal_groups[$i]['worker'], 'user').'</a>';
								}
								
								//Филиалы
								$filials = SelDataFromDB('spr_office', $journal_groups[$i]['filial'], 'offices');
								if ($filials != 0){
									$filial = $filials[0]['name'];
									$filialColor = $filials[0]['color'];
								}else{
									$filial = 'unknown';
									$filialColor = '#FFF';
								}
								
								//Возрасты
								$ages = SelDataFromDB('spr_ages', $journal_groups[$i]['age'], 'ages');
								if ($ages != 0){
									$age = $ages[0]['from_age'].' - '.$ages[0]['to_age'];
								}else{
									$age = 'unknown';
								}
								if ((($journal_groups[$i]['worker'] == $_SESSION['id']) && ($groups['see_all'] != 1) && ($groups['see_own'] == 1) && !$god_mode) || (($groups['see_all'] == 1) || $god_mode)){	
									//временная переменная
									$result_html .= '
											<li class="cellsBlock cellsBlockHover">
												<div class="cellPriority" style="text-align: center; background-color: '.$journal_groups[$i]['color'].';"></div>
												<a href="group.php?id='.$journal_groups[$i]['id'].'" class="cellName ahref colorizeText" style="background-color: '.$filialColor.';">'.$journal_groups[$i]['name'].'</a>
												<a href="journal_new.php?id='.$journal_groups[$i]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: green" title="Журнал группы"><i class="fa fa-calendar"></i></a>
												<a href="group_client.php?id='.$journal_groups[$i]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: rgba(47, 47, 47, 0.93);" title="Участники группы"><i class="fa fa-users"></i></a>
												<a href="filial.php?id='.$filials[0]['id'].'" id="4filter" class="cellName ahref" style="text-align: center;'.$bg_color.'">'.$filial.'</a>
												<a href="filial_shed.php?id='.$filials[0]['id'].'" class="cellCosmAct ahref" style="text-align: center; font-size: 120%; color: rgb(182, 82, 227);" title="Расписание филиала"><i class="fa fa-clock-o"></i></a>
												<div class="cellName" style="text-align: center; '.$bg_color.'">'.$age.'</div>
												'.$trenerValue.'';
									//Сколько участников есть в группе
									$uch_arr = SelDataFromDB('spr_clients', $journal_groups[$i]['id'], 'client_group');
									if ($uch_arr != 0) {
										$result_html .= '
													<div class="cellName" style="text-align: center; '.$bg_color.'">'.count($uch_arr);
									}else{
										$result_html .= '
										<div class="cellName" style="text-align: center;  background-color: rgba(222, 8, 8, 0.51);">0';
									}
									$result_html .= '	
													</div>
													<div class="cellText" style="text-align: left;'.$bg_color.'">'.$journal_groups[$i]['comment'].'</div>';
									if (($groups['edit'] == 1) || $god_mode){
                                        if (isset($_GET['client_id_add'])){
                                            $result_html .= '
												<div class="cellCosmAct" style="text-align: center" title="Добавить сюда" onclick="addClientInGroup('.$_GET['client_id_add'].', '.$journal_groups[$i]['id'].');">
                                                    <i class="fa fa-plus" style="color: green; border: 1px solid #CCC; padding: 3px; cursor: pointer; "></i>
                                                </div>';
                                        }

										$result_html .= '
													<div class="cellCosmAct" style="text-align: center"><a href="edit_group.php?id='.$journal_groups[$i]['id'].'"><img src="img/edit.png" title="Редактировать"></a></div>
													<div class="cellCosmAct" style="text-align: center"><a href="close_group.php?id='.$journal_groups[$i]['id'].'&close=1">'.$cls_img.'</a></div>';
									}
									$result_html .= '
												</li>';
								}	
								
								if ($journal_groups[$i]['close'] == 0){
									echo $result_html;
								}else{
									$closed_groups .= $result_html;
								}
							}
							if (($groups['see_all'] == 1) || $god_mode){	
								echo $closed_groups;
							}
						}else{
							echo '<h1>У тренера нет групп</h1>';
						}
					}
		
				}else{
					echo '<h1>Какая-то ошибка</h1>';
				}
				echo '
						</div>
					</div>';


				echo "
				<script>
                    $(document).ready(function(){
                        //changeTextColor('.colorizeText');
                    })
                </script>";


			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>