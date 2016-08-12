<?php 

	var_dump ($_POST);
	if ($_POST){
		if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['filial']) && isset($_POST['age'])){
			include_once 'DBWork.php';
			
			$j_group = SelDataFromDB('journal_groups', $_POST['id'], 'group');
			if ($j_group !=0){	
			
				if (!isset($_POST['worker'])){
					$_POST['worker'] = 0;
				}
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				$query = "UPDATE `journal_groups` SET `name`='{$_POST['name']}', `filial`='{$_POST['filial']}', `age`='{$_POST['age']}', `worker`='{$_POST['worker']}', `color`='".'#'.$_POST['color']."', `comment`='{$_POST['comment']}'  WHERE `id`='{$_POST['id']}'";
				mysql_query($query) or die(mysql_error());
				mysql_close();
				
				//логирование
				AddLog ('0', $_POST['session_id'], 'Название группы ['.$j_group[0]['name'].']. Филиал ['.$j_group[0]['filial'].']. Возраст ['.$j_group[0]['age'].']. Тренер ['.$j_group[0]['worker'].']. Цвет ['.'#'.$j_group[0]['color'].']. Коммент ['.$j_group[0]['comment'].'].', 'Название группы ['.$_POST['name'].']. Филиал ['.$_POST['filial'].']. Возраст ['.$_POST['age'].']. Тренер ['.$_POST['worker'].']. Цвет ['.'#'.$_POST['color'].']. Коммент ['.$_POST['comment'].'].');	
			
				echo '
					Группа отредактирована.
					<br /><br />
					<a href="index.php" class="b">На главную</a>
					';
			}else{
				echo '<h1>Не найдена такая группа</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}
	
?>