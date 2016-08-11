<?php

//contacts.php
//Список сотрудников

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if (($workers['see_all'] == 1) || $god_mode){
			include_once 'DBWork.php';
			include_once 'functions.php';

			
			echo '
				<header style="margin-bottom: 5px;">
					<h1>Список сотрудников</h1>';

			$contacts = SelDataFromDB('spr_workers', '', '');
			//var_dump ($contacts);
			$arr_permissions = SelDataFromDB('spr_permissions', '', '');
			//var_dump ($arr_permissions);
			
			if (($workers['add_new'] == 1) || $god_mode){
				echo '
					<a href="add_worker.php" class="b">Добавить</a>
				</header>';			
			}
			if ($contacts != 0){

				echo '
					<p style="margin: 5px 0; padding: 2px;">
						Фильтр: 
						<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
					</p>
					<div id="data">
						<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
							<li class="cellsBlock" style="font-weight:bold;">	
								<div class="cellFullName" style="text-align: center">Полное имя</div>
								<div class="cellOffice" style="text-align: center">Должность</div>
								<div class="cellText" style="text-align: center">Контакты</div>
								<div class="cellName" style="text-align: center">Логин</div>
								<div class="cellName" style="text-align: center">Пароль</div>
							</li>';

				for ($i = 0; $i < count($contacts); $i++) { 
					if ($contacts[$i]['permissions'] != '777'){
						$permissions = SearchInArray($arr_permissions, $contacts[$i]['permissions'], 'name');
						//var_dump($permissions);
						
						if ($contacts[$i]['fired'] == '1'){
							$cl_color = ' background-color: rgba(161,161,161,1);';
						}else{
							$cl_color = '';
						}
						
						echo '
								<li class="cellsBlock cellsBlockHover">
									<a href="user.php?id='.$contacts[$i]['id'].'" class="cellFullName ahref" id="4filter" style="'.$cl_color.'">'.$contacts[$i]['full_name'].'</a>
									<div class="cellOffice" style="'.$cl_color.'">', $permissions != '0' ? $permissions : '-' ,'</div>
									<div class="cellText" style="'.$cl_color.'">'.$contacts[$i]['contacts'].'</div>
									<div class="cellName" style="text-align: center; '.$cl_color.'">'.$contacts[$i]['login'].'</div>';
						if ($god_mode){			
							echo '
									<div class="cellName" style="text-align: center; '.$cl_color.'">'.$contacts[$i]['password'].'</div>';
						}else{
							echo '<div class="cellName" style="text-align: center; '.$cl_color.'">****</div>';
						}
						echo '
								</li>';
					}
				}
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
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