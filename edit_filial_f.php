<?php 

	//var_dump ($_POST);
	if ($_POST){
		if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['address']) && isset($_POST['contacts'])){
			include_once 'DBWork.php';
			
			$filial = SelDataFromDB('spr_office', $_POST['id'], 'id');
			if ($filial !=0){	
			
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				$query = "UPDATE `spr_office` SET `name`='{$_POST['name']}', `address`='{$_POST['address']}', `contacts`='{$_POST['contacts']}'  WHERE `id`='{$_POST['id']}'";
				mysql_query($query) or die(mysql_error());
				mysql_close();
				
				//логирование
				AddLog ('0', $_POST['session_id'], 'Название филиала ['.$filial[0]['name'].']. Адрес филиала ['.$filial[0]['address'].']. Контакты филиала ['.$filial[0]['contacts'].'].', 'Название филиала ['.$_POST['name'].']. Адрес филиала ['.$_POST['address'].']. Контакты филиала ['.$_POST['contacts'].'].');	
			
				echo '
					Филиал отредактирован.
					<br /><br />
					<a href="index.php" class="b">На главную</a>
					';
			}else{
				echo '<h1>Не найден такой филиал</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}
	
?>