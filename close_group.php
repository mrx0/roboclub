<?php 

	require_once 'header.php';
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		require_once 'header_tags.php';
		//var_dump ($_POST);
		if ($_GET){
			if (isset($_GET['id']) && (isset($_GET['close']))){
				include_once 'DBWork.php';
				
				$j_group = SelDataFromDB('journal_groups', $_GET['id'], 'id');
				if ($j_group !=0){			
					if ($j_group[0]['close'] == 1){
						$closed_status = 0;
						$closed_text = 'Открыта';
					}else{
						$closed_status = 1;
						$closed_text = 'Закрыта';
					}

					require 'config.php';
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					$time = time();
					$query = "UPDATE `journal_groups` SET `close`='{$closed_status}' WHERE `id`='{$j_group[0]['id']}'";
					mysql_query($query) or die(mysql_error());
					mysql_close();
					
					//логирование
					AddLog ('0', $_SESSION['id'], '', 'Статус группы ['.$j_group[0]['name'].'] изменён на ['.$closed_text.']');	
			
					echo '
						Статус группы ['.$j_group[0]['name'].'] изменён на '.$closed_text.'.
						<br /><br />
						<a href="index.php" class="b">На главную</a>
						';
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}	
		
	require_once 'footer.php';
	
?>