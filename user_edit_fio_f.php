<?php 

//user_edit_fio_f.php
//Изменение ФИО пользователя

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (($_POST['f'] == '')||($_POST['i'] == '')||($_POST['o'] == '')){
				echo 'Что-то не заполнено. Если у пользователя нет отчества, поставьте в поле "Отчество" символ "*"<br /><br />
					<a href="user_edit_fio.php?id='.$_POST['id'].'" class="b">Назад</a>
					<a href="contacts.php" class="b">К списку пользователей</a>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				if ((preg_match( '/[a-zA-Z]/', $_POST['f'] )) || (preg_match( '/[a-zA-Z]/', $_POST['i'] )) || (preg_match( '/[a-zA-Z]/', $_POST['o'] ))){
					echo 'В ФИО встречаются латинские буквы. Это недопустимо<br /><br />
						<a href="user_edit_fio.php?id='.$_POST['id'].'" class="b">Назад</a>
						<a href="contacts.php" class="b">К списку пользователей</a>';
				}else{
					$full_name = CreateFullName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
					//Проверяем есть ли такой пользователь
					if (isSameFullName('spr_workers', $full_name)){
						echo 'Такой пользователь уже есть. Если тёзка, в конце поля "Отчество" поставьте символ "*"<br /><br />
							<a href="user_edit_fio.php?id='.$_POST['id'].'" class="b">Назад</a>
							<a href="contacts.php" class="b">К списку пользователей</a>';
					}else{
						$name = CreateName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
						
						WriteFIOUserToDB_Update ($_SESSION['id'], $_POST['id'], $name, $full_name);
					
						echo '
							<h1>ФИО пользователя изменены</h1>
							ФИО: '.$full_name.'<br />
							<br /><br />
							<a href="user.php?id='.$_POST['id'].'" class="b">Вернуться в карточку</a>
							<a href="contacts.php" class="b">К списку пользователей</a>
							';
					}
				}
			}
		}
	}
?>