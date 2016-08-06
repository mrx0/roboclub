<?php 

//+++add_client_f.php
//Функция для добавления клиента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (($_POST['f'] == '')||($_POST['i'] == '')||($_POST['o'] == '')){
				echo 'Что-то не заполнено. Если у клиента нет отчества, поставьте в поле "Отчество" символ "*"<br /><br />
					<a href="add_client.php" class="b">Добавить</a>
					<a href="clients.php" class="b">К списку клиентов</a>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';
				$echo_therapist = '';
				$echo_therapist2 = '';
				if ((preg_match( '/[a-zA-Z]/', $_POST['f'] )) || (preg_match( '/[a-zA-Z]/', $_POST['i'] )) || (preg_match( '/[a-zA-Z]/', $_POST['o'] ))){
					echo 'В ФИО встречаются латинские буквы. Это недопустимо<br /><br />
						<a href="add_client.php" class="b">Добавить</a>
						<a href="clients.php" class="b">К списку клиентов</a>';
				}else{
					$full_name = CreateFullName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
					//Проверяем есть ли такой клиент
					if (isSameFullName('spr_clients', $full_name)){
						echo 'Такой клиент уже есть. Если тёзка, в конце поля "Отчество" поставьте символ "*"<br /><br />
							<a href="add_client.php" class="b">Добавить ещё</a>
							<a href="clients.php" class="b">К списку клиентов</a>';
					}else{
						
						$name = CreateName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
						//echo
						$birthday = strtotime($_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year']);
						
						WriteClientToDB_Edit ($_POST['session_id'], $name, $full_name, $_POST['f'], $_POST['i'], $_POST['o'], $_POST['contacts'],  $_POST['comment'], $birthday, $_POST['sex']);
					
						echo '
							<h1>Добавлен в базу.</h1>
							ФИО: '.$full_name.'
							<br /><br />
							<a href="add_client.php" class="b">Добавить ещё</a>
							<a href="clients.php" class="b">К списку клиентов</a>
							';
					}
				}
			}
		}
	}
?>