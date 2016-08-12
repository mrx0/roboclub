<?php 

//add_group_f.php
//Функция для добавления группы

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump ($_POST);
		
		if ($_POST){
			if (!isset($_POST['worker'])){
				$_POST['worker'] = 0;
			}
								
			$group_id = WriteToDB_EditGroup ($_POST['name'], $_POST['filial'], $_POST['age'], $_POST['worker'], $_POST['comment'], $_POST['session_id'], '#'.$_POST['color']);
					
			echo '
				Добавлена группа.
				<br />
				<a href="group.php?id='.$group_id.'" class="b">'.$_POST['name'].'</a><br /><br />';
			if ($_POST['worker'] == 0){
				echo 'Группе не назначен тренер. Вы можете сделать это позже в настройках группы.';
			}else{
				echo 'Группе назначен <a href="user.php?id='.$_POST['worker'].'">тренер</a>';
			}

		}
	}
?>