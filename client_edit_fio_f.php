<?php 

//client_edit_fio_f.php
//Изменение ФИО клиента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (($_POST['f'] == '')||($_POST['i'] == '')||($_POST['o'] == '')){
				echo '
					<div class="query_neok">
						Что-то не заполнено. Если у клиента нет отчества, поставьте в поле "Отчество" символ "*"<br /><br />
						<a href="clients.php" class="b">К списку клиентов</a>
					</div>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';

				if ((preg_match( '/[a-zA-Z]/', $_POST['f'] )) || (preg_match( '/[a-zA-Z]/', $_POST['i'] )) || (preg_match( '/[a-zA-Z]/', $_POST['o'] ))){
					echo '
						<div class="query_neok">
							В ФИО встречаются латинские буквы. Это недопустимо<br /><br />
							<a href="clients.php" class="b">К списку клиентов</a>
						</div>';
				}else{
					$full_name = CreateFullName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
					//Проверяем есть ли такой клиент
					if (isSameFullName('spr_clients', $full_name)){
						echo '
							<div class="query_neok">
								Такой клиент уже есть. Если тёзка, в конце поля "Отчество" поставьте символ "*"<br /><br />
								<a href="clients.php" class="b">К списку клиентов</a>
							</div>';
					}else{
						$name = CreateName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
						
						WriteFIOClientToDB_Update ($_SESSION['id'], $_POST['id'], $name, $full_name, $_POST['f'], $_POST['i'], $_POST['o']);
					
						echo '
							<div class="query_ok">
								<h3>ФИО клиента изменены</h3>
								ФИО: '.$full_name.'<br />
								<br /><br />
								<a href="client.php?id='.$_POST['id'].'" class="b">Вернуться в карточку</a>
								<a href="clients.php" class="b">К списку клиентов</a>
							</div>
							';
					}
				}
			}
		}
	}
?>