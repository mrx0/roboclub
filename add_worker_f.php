<?php 

//+++add_worker_f.php
//

	session_start();
	
	$god_mode = FALSE;
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		include_once 'DBWork.php';
		//var_dump($_SESSION);
		//!!!! Костыль с правами
		if ($_SESSION['permissions'] == '777'){
			$god_mode = TRUE;
		}else{
			//Получили список прав
			$permissions = SelDataFromDB('spr_permissions', $_SESSION['permissions'], 'id');	
			//var_dump($permissions);
		}
		if (!$god_mode){
			if ($permissions != 0){
				$clients = json_decode($permissions[0]['clients'], true);
				$workers = json_decode($permissions[0]['workers'], true);
				$offices = json_decode($permissions[0]['offices'], true);
				$finance = json_decode($permissions[0]['finance'], true);
				$scheduler = json_decode($permissions[0]['scheduler'], true);
				//var_dump($offices);
			}
		}else{
			//Видимость
			$clients['see_all'] = 0;
			$clients['see_own'] = 0;
			$workers['see_all'] = 0;
			$workers['see_own'] = 0;
			$offices['see_all'] = 0;
			$offices['see_own'] = 0;
			$zapis['see_all'] = 0;
			$zapis['see_own'] = 0;
			$scheduler['see_all'] = 0;
			$scheduler['see_own'] = 0;
			//
			$clients['add_new'] = 0;
			$clients['add_own'] = 0;
			$workers['add_new'] = 0;
			$workers['add_own'] = 0;
			$offices['add_new'] = 0;
			$offices['add_own'] = 0;
			$zapis['add_new'] = 0;
			$zapis['add_own'] = 0;
			$scheduler['add_new'] = 0;
			$scheduler['add_own'] = 0;
			//
			$clients['edit'] = 0;
			$workers['edit'] = 0;
			$offices['edit'] = 0;
			$zapis['edit'] = 0;
			$scheduler['edit'] = 0;
			//
			$clients['close'] = 0;
			$workers['close'] = 0;
			$offices['close'] = 0;
			$zapis['close'] = 0;
			$scheduler['close'] = 0;
			//
			$clients['reopen'] = 0;
			$workers['reopen'] = 0;
			$offices['reopen'] = 0;
			$zapis['reopen'] = 0;
			$scheduler['reopen'] = 0;
			//
			$clients['add_worker'] = 0;
			$workers['add_worker'] = 0;
			$offices['add_worker'] = 0;
			$zapis['add_worker'] = 0;
			$scheduler['add_worker'] = 0;
		
		}
		if ($_POST){
			if (($_POST['f'] == '')||($_POST['i'] == '')||($_POST['o'] == '')){
				echo 'Что-то не заполнено. Давайте еще разок =)<br /><br />
					<a href="add_worker.php" class="b">Добавить</a>
					<a href="index.php" class="b">На главную</a>';
			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				//Генератор пароля
				$password = PassGen();
				
				$full_name = CreateFullName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
				//Проверяем есть ли такой пользователь
				if (isSameFullName('spr_workers', $full_name)){
					echo 'Такой пользователь уже есть.<br /><br />
						<a href="add_worker.php" class="b">Добавить</a>
						<a href="index.php" class="b">На главную</a>';
				}else{
					$name = CreateName(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
					$login = CreateLogin(trim($_POST['f']), trim($_POST['i']), trim($_POST['o']));
					//Если такой логин уже есть, добавляем символ 2 в конце или 3 или 4 ..
					$login = isSameLogin ($login);
					WriteWorkerToDB_Edit ($_POST['session_id'], $login, $name, $full_name, $password, $_POST['contacts'], $_POST['permissions']);
				
					echo '
						<h1>Пользователь добавлен в базу.</h1>
						ФИО: '.$full_name.'<br />
						Логин: '.$login.'<br />';
					if ($god_mode){
						echo '	
							Пароль: '.$password.'';
					}else{
						echo '	
							Пароль: уточнить у системного администратора.<br />
							Изменить права доступа может только адмиинистратор.';
					}
					echo '
						<br /><br />
						<a href="add_worker.php" class="b">Добавить ещё</a>
						<a href="index.php" class="b">На главную</a>
						';
				}
			}
		}
	}
?>