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
					$uch_count = SelDataFromDB('spr_clients', $_GET['id'], 'group');
					$uch_count = 0;
					echo '
								<div style="font-size: 90%; margin-bottom: 20px;">Всего в группе уже: 
									<span style="color: rgb(36,36,36); font-weight: bold;">'.$uch_count.'</span>
								</div>';

								
					if (($groups['edit'] == 1) || $god_mode){
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