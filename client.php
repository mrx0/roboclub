<?php

//client.php
//


//!!!!!!
// Добавили в базу
//ALTER TABLE  `spr_clients` ADD  `status` INT( 1 ) UNSIGNED NOT NULL DEFAULT  '0';

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';

            $msql_cnnct = ConnectToDB ();

			$client = SelDataFromDB('spr_clients', $_GET['id'], 'user');
			//var_dump($user);
			
			if ($client != 0){

                $add_group_str = '';

                $filials = SelDataFromDB('spr_office', $client[0]['filial'], 'offices');

                if ($filials != 0){
                    $filial_id = $filials[0]['id'];
                    $filial_str = '<a href="filial.php?id='.$filials[0]['id'].'" class="ahref">'.$filials[0]['name'].'</a>';
                    $add_group_str = "<a href='filial.php?id={$filials[0]['id']}&client_id_add={$_GET['id']}' class='b3'>Добавить в группу</a>";
                }else{
                    $filial_id = 0;
                    $filial_str = 'Не указан филиал';
                }

				echo '
					<div id="status">
						<header>
							<h2>Карточка ребёнка #'.$client[0]['id'];

                if (($clients['edit'] == 1) || $god_mode){
                    echo '
							<a href="client_edit.php?id='.$_GET['id'].'" class=""><img src="img/edit.png" title="Редактировать"></a>';
                }

                /*if (($clients['close'] == 1) || $god_mode){
                    echo '
							<a href="client_del.php?id='.$_GET['id'].'" class=""><img src="img/delete.png" title="Удалить"></a>';
                }*/

                echo '
				            </h2>
						</header>';

                if (($finance['add_new'] == 1) || $god_mode){
                    echo '
							<a href="add_order.php?client_id='.$_GET['id'].'&filial_id='.$filial_id.'" class="b3">Добавить ордер</a>';
                }

                if (($finance['see_all'] == 1) || $god_mode){
                    echo '
							<a href="client_balance.php?client_id='.$_GET['id'].'" class="b3">Баланс</a>';
                }

                if (($finance['add_new'] == 1) || $god_mode){
                    echo '
							<a href="add_finance.php?client='.$_GET['id'].'" class="b3" style="background-color: #CCC;">Добавить платёж (старое) <i class="fa fa-rub"></i></a>';
                }
                if (($finance['see_all'] == 1) || $god_mode){
                    echo '
							<a href="client_finance.php?client='.$_GET['id'].'" class="b3" style="background-color: #CCC;">История (старое) <i class="fa fa-rub"></i></a>';
                }

				if (($clients['see_all'] == 1) || $god_mode){
                    echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 101;">';

                    echo $block_fast_search_client;

                    echo '
					</div>';
				}
				echo '
						<div id="data">';


				echo '

							<div class="cellsBlock2">
								<div class="cellLeft">ФИО</div>
								<div class="cellRight">'.$client[0]['full_name'];
                echo '
				                </div>
							</div>';
							if (($client[0]['birthday'] == "-1577934000") || ($client[0]['birthday'] == 0)){
								$age = '';
							}else{
								$age = getyeardiff( $client[0]['birthday']).' лет';
							}
				echo '				
							<div class="cellsBlock2">
								<div class="cellLeft">Дата рождения</div>
								<div class="cellRight">', (($client[0]['birthday'] == '-1577934000') || ($client[0]['birthday'] == 0)) ? 'не указана' : date('d.m.Y', $client[0]['birthday']) ,' / <b>'.$age.'</b></div>
							</div>
								
							<div class="cellsBlock2">
								<div class="cellLeft">Пол</div>
								<div class="cellRight">';
				if ($client[0]['sex'] != 0){
					if ($client[0]['sex'] == 1){
						echo 'М';
					}
					if ($client[0]['sex'] == 2){
						echo 'Ж';
					}
				}else{
					echo 'не указан';
				}
				echo 
								'</div>
							</div>';
				if (($clients['see_all'] == 1) || $god_mode){
					echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">'.$client[0]['contacts'].'</div>
								</div>';
				}
				if (($clients['see_all'] == 1) || ($clients['see_own'] == 1) || $god_mode){
					echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Комментарий</div>
									<div class="cellRight">'.$client[0]['comments'].'</div>
								</div>';
				}
				echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Филиал</div>
									<div class="cellRight">';

                echo $filial_str;

				echo '
									</div>
								</div>';
                echo '
								<div class="cellsBlock2">
									<div class="cellLeft">Группа(ы)</div>
									<div class="cellRight">';

				//$groups = SelDataFromDB('journal_groups_clients', $_GET['id'], 'client');

                $groups_j = array();

                $query = "SELECT j_grcl.*, j_gr.name AS group_name, j_gr.color AS color, s_o.name AS office_name FROM `journal_groups_clients` j_grcl
                            LEFT JOIN `journal_groups` j_gr ON j_gr.id = j_grcl.group_id
                            LEFT JOIN `spr_office` s_o ON j_gr.filial = s_o.id
                            WHERE j_grcl.client='{$_GET['id']}';";
                //var_dump($query);

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($groups_j, $arr);
                    }
                }

				if (!empty($groups_j)){
					//var_dump ($groups_j);

					foreach($groups_j as $key => $value){
						//$group = SelDataFromDB('journal_groups', $value['group_id'], 'id');
                        //var_dump ($group);

						echo '
                            <div style="margin-bottom: 7px; border: 1px solid #CCC; padding: 2px; background-color: '.$value['color'].'">
                                <div style="display: inline; ">
                                    <a href="group.php?id='.$value['group_id'].'" class="ahref" style="padding: 0 4px;"><b>'.$value['group_name'].'</b> [<i>'.$value['office_name'].'</i>]</a>
                                </div>';
                        if (($finance['add_new'] == 1) || $god_mode){
                            echo ' 
                                <div style="display: inline;">
							        <a href="invoice_add.php?client_id='.$_GET['id'].'&group_id='.$value['group_id'].'" class="b3" style="font-size: 90%">Добавить счёт</a>
							    </div>';
                        }
                        echo '
                            </div>';
					}
				}else{
					echo 'Не в группе '.$add_group_str;
				}
				echo '
                        </div>
                    </div>';

				//Тарифы

                //$tarifs_j = array();

                //$query = "SELECT * FROM `logs` ORDER BY `date` DESC LIMIT {$limit_pos[0]}, {$limit_pos[1]};";

                //$res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                /*$number = mysqli_num_rows($res);
                if ($number != 0){
                    while ($arr = mysqli_fetch_assoc($res)){
                        array_push($tarifs_j, $arr);
                    }
                }*/

                /*echo '
                            <div style="width: 400px; border: 1px solid rgb(204, 204, 204); padding: 5px 10px; margin-top: 10px;">
                                <div style="color: darkred; border: 1px solid rgb(204, 204, 204); padding: 5px 10px; margin: -5px -10px; background-color: rgba(239, 253, 195, 0.39);">
                                    <div style="display: inline; margin-right: 20px;"><i>Тарифы</i></div>
                                    <div style="display: inline;">
                                        <a href="client_tarif_edit.php?client_id='.$_GET['id'].'" class="">
                                            <img src="img/table-edit-16x16.gif" title="Управление" style="vertical-align: middle;">
                                        </a>
                                    </div>
                                </div>
								<div style="margin-top: 20px;">';*/

				//if (!empty($tarifs_j)){
					//var_dump ($groups);
					/*foreach($groups as $key => $value){
						$group = SelDataFromDB('journal_groups', $value['group_id'], 'id');
						if ($group != 0){
							echo '<a href="group.php?id='.$value['group_id'].'" class="ahref" style="padding: 0 4px; background-color: '.$group[0]['color'].'">'.$group[0]['name'].'</a>';
						}else{
							echo 'ошибка группы';
						}
					}*/
				/*}else {
                    echo '<span style="color: red">не определены</span>';
                }*/
				/*echo '
                                </div>
                            </div>';*/

				echo '
					</div>';
					
					
				echo '
					<div style="margin-top: 20px; border: 1px dotted green; padding: 5px 10px; width: 400px; background-color: rgba(113, 226, 209, 0.2);">
						<div style="font-size: 75%; color: #999; margin-bottom: 5px;">Примечания</div>';

				//отобразить комментарии
				
				
				$comments = SelDataFromDB('comments', 'spr_clients'.':'.$_GET['id'], 'parrent');
				//var_dump ($comments);	
				
				if ($comments != 0){
					echo '
						<div style="max-height: 300px; overflow-y: scroll;" id="commentsLog">';
					foreach ($comments as $value){
						echo '
							<div style="border: 1px solid #CCC; border-radius: 5px; background-color: #EEE; padding: 10px; margin-bottom: 5px; width: 350px; position: relative;">
								<div style="font-size: 70%; border-bottom: 1px dotted #CCC; text-align: right;">
									<a href="user.php?id='.$value['create_person'].'" class="ahref">'.WriteSearchUser('spr_workers',$value['create_person'], 'user').'</a><br>
									<span style="font-size:80%;">'.date('d.m.y H:i', $value['create_time']).'</span>
								</div>
								<div style="margin-top: 5px;">'.nl2br($value['description']).'</div>';
						if ($god_mode) {
                            echo '
								<div class="msg_container_time" style="position: absolute; top: 2px; cursor: pointer;" onclick="deleteThisComment('.$value['id'].');">Удалить</div>';
                        }
                        echo '
							</div>';
					}
					echo '
						</div>';
				}				
					
				//оставить комментарий
				echo '
						<div style="margin-top: 5px;">
							<form>
								<div id="reqCom"></div>
								<div><textarea name="t_s_comment" id="t_s_comment" cols="40" rows="3"></textarea></div>
								<input type="button" class="b" value="Добавить" onclick="addComment()">
							</form>
						</div>
					</div>';


                CloseDB($msql_cnnct);
				
				echo '
					<script type="text/javascript">
						function addComment(){
							ajax({
								url:"add_comment_f.php",
								method:"POST",
								data:
								{
									id:'.$_GET['id'].',
									t_s_comment:encodeURIComponent(document.getElementById("t_s_comment").value)
								},
								success: function(request){
									document.getElementById("reqCom").innerHTML=request;
									setTimeout(function () {
										location.reload()
									}, 500);
								}
							});
						}
						//Прокручиваем лог в конец
						if ($("*").is("#commentsLog")){
						    document.querySelector("#commentsLog").scrollTop = document.querySelector("#commentsLog").scrollHeight;
						}
						    
						
					</script>';
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>