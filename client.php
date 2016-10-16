<?php

//user.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			include_once 'DBWork.php';
			include_once 'functions.php';

			$client = SelDataFromDB('spr_clients', $_GET['id'], 'user');
			//var_dump($user);
			
			if ($client != 0){
				echo '
					<div id="status">
						<header>
							<h2>Карточка клиента #'.$client[0]['id'].'</h2>
						</header>';
				if (($clients['see_all'] == 1) || $god_mode){
					echo '
						<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px;">
							<div class="cellRight">
								<span style="font-size: 70%;">Быстрый поиск клиента</span><br />
								<input type="text" size="50" name="searchdata_fc" id="search_client" placeholder="Введите первые три буквы для поиска" value="" class="who_fc"  autocomplete="off">
								<div id="search_result_fc2"></div>
							</div>
						</div>';
				}
				echo '
						<div id="data">';


				echo '

							<div class="cellsBlock2">
								<div class="cellLeft">ФИО</div>
								<div class="cellRight">'.$client[0]['full_name'].'</div>
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
				echo '					
								<div class="cellsBlock2">
									<div class="cellLeft">Контакты</div>
									<div class="cellRight">'.$client[0]['contacts'].'</div>
								</div>';
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
				$filials = SelDataFromDB('spr_office', $client[0]['filial'], 'offices');
				if ($filials != 0){
					echo '<a href="filial.php?id='.$filials[0]['id'].'" class="ahref">'.$filials[0]['name'].'</a>';	
				}else{
					echo 'Не указан филиал';
				}
				echo '
									</div>
								</div>

								<div class="cellsBlock2">
									<div class="cellLeft">Группа</div>
									<div class="cellRight">';
				$groups = SelDataFromDB('journal_groups_clients', $_GET['id'], 'client');
				if ($groups != 0){
					//var_dump ($groups);
					foreach($groups as $key => $value){
						$group = SelDataFromDB('journal_groups', $value['group_id'], 'id');
						if ($group != 0){
							echo '<a href="group.php?id='.$value['group_id'].'" class="ahref" style="padding: 0 4px; background-color: '.$group[0]['color'].'">'.$group[0]['name'].'</a>';	
						}else{
							echo 'ошибка группы';
						}
					}
				}else{
					echo 'Не в группе';
				}
				echo '
									</div>
								</div>
								<br>';


									
				if (($finance['add_new'] == 1) || $god_mode){
					echo '
							<a href="add_finance.php?client='.$_GET['id'].'" class="b">Добавить платёж <i class="fa fa-rub"></i></a>';
				}
				if (($finance['see_all'] == 1) || $god_mode){
					echo '
							<a href="client_finance.php?client='.$_GET['id'].'" class="b">История <i class="fa fa-rub"></i></a>';
				}
				
				echo '<br><br>';
				
				if (($clients['edit'] == 1) || $god_mode){
					echo '
							<a href="client_edit.php?id='.$_GET['id'].'" class="b">Редактировать</a>';
				}
				echo '
					</div>';
					
					
				echo '
					<div style="margin-top: 20px; border: 1px dotted green; padding: 10px; width: 400px; background-color: rgba(113, 226, 209, 0.2);">
						<div style="font-size: 90%; color: #999; margin-bottom: 10px;">Примечания</div>';

				//отобразить комментарии
				
				
				$comments = SelDataFromDB('comments', 'spr_clients'.':'.$_GET['id'], 'parrent');
				//var_dump ($comments);	
				
				if ($comments != 0){
					echo '
						<div style="max-height: 300px; overflow-y: scroll;" id="commentsLog">';
					foreach ($comments as $value){
						echo '
							<div style="border: 1px solid #CCC; border-radius: 5px; background-color: #EEE; padding: 10px; margin-bottom: 5px; width: 350px;">
								<div style="font-size: 70%; border-bottom: 1px dotted #CCC; text-align: right;">
									<a href="user.php?id='.$value['create_person'].'" class="ahref">'.WriteSearchUser('spr_workers',$value['create_person'], 'user').'</a><br>
									<span style="font-size:80%;">'.date('d.m.y H:i', $value['create_time']).'</span>
								</div>
								<div style="margin-top: 5px;">'.nl2br($value['description']).'</div>
							</div>';
					}
					echo '
						</div>';
				}				
					
				//оставить комментарий
				echo '
						<div style="margin-top: 20px;">
							<form>
								<div id="reqCom"></div>
								<div><textarea name="t_s_comment" id="t_s_comment" cols="40" rows="3"></textarea></div>
								<input type="button" class="b" value="Добавить" onclick="addComment()">
							</form>
						</div>
					</div>';
				
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
						document.querySelector("#commentsLog").scrollTop = document.querySelector("#commentsLog").scrollHeight;
						
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