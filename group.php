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
		
			echo '
				<div id="status">
					<header>
						<h2>Группа</h2>
					</header>';
			if ($j_group[0]['close'] == '1'){
				echo '<span style="color:#EF172F;font-weight:bold;">ЗАКРЫТА</span>';
			}

			echo '
					<div id="data">';

			echo '

							<div class="cellsBlock2">
								<div class="cellLeft">Название группы</div>
								<div class="cellRight" style="background-color: '.$j_group[0]['color'].';">'.$j_group[0]['name'].'</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Филиал</div>
								<div class="cellRight">';
			//Филиалы
			$j_filials = SelDataFromDB('spr_office', $j_group[0]['filial'], 'offices');
			
			if ($j_filials != 0){
				echo $j_filials[0]['name'];
			}else{
				echo 'unknown';
			}
			
			echo '				
								</div>
							</div>';

			echo '	
							
							<div class="cellsBlock2">
								<div class="cellLeft">Возраст</div>
								<div class="cellRight">';
			//Возрасты
			$ages = SelDataFromDB('spr_ages', $j_group[0]['age'], 'ages');
			if ($ages != 0){
				echo $ages[0]['from_age'].' - '.$ages[0]['to_age'];
			}else{
				echo 'unknown';
			}	
			echo '
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Тренер</div>
								<div class="cellRight">'.WriteSearchUser('spr_workers', $j_group[0]['worker'], 'user').'</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight">'.$j_group[0]['comment'].'</div>
							</div>
							<br /><br />';


			if (($groups['edit'] == 1) || $god_mode){
				echo '
								<a href="edit_group.php?id='.$_GET['id'].'" class="b">Редактировать</a>';
			}
				
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>