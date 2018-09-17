<?php
	
//auth.php
//	
	
    session_start();

    unset($_SESSION['login']);
    unset($_SESSION['id']);
    unset($_SESSION['permissions']);
    unset($_SESSION['filial']);
	
	//вся процедура работает на сессиях. Именно в ней хранятся данные  пользователя, пока он находится на сайте. Очень важно запустить их в  самом начале странички!!!
	if (isset($_POST['login'])){
		$login = $_POST['login']; 
		if ($login == ''){
			unset($login);
		}
	} 
	
	//заносим введенный пользователем логин в переменную $login, если он пустой, то уничтожаем переменную
    if (isset($_POST['password'])){
		$password = $_POST['password']; 
		if ($password == ''){
			unset($password);
		}
	}
	
    //заносим введенный пользователем пароль в переменную $password, если он пустой, то уничтожаем переменную
	if (empty($login) or empty($password)){ 
		//если пользователь не ввел логин или пароль, то выдаем ошибку и останавливаем скрипт
		exit (json_encode(array('result' => 'error', 'data' => 'Не ввели логин и пароль')));
    }
    //если логин и пароль введены,то обрабатываем их, чтобы теги и скрипты не работали, мало ли что люди могут ввести
    $login = stripslashes($login);
    $login = htmlspecialchars($login);
	$password = stripslashes($password);
    $password = htmlspecialchars($password);
	//удаляем лишние пробелы
    $login = trim($login);
    $password = trim($password);

	include_once 'DBWork.php';

	$rezult = SelDataFromDB('spr_workers', $login, 'login');
	//извлекаем из базы все данные о пользователе с введенным логином
	//var_dump ($rezult);
    if ($rezult !=0){
		if (empty($rezult[0]['password'])){
			//если пользователя с введенным логином не существует
			exit (json_encode(array('result' => 'error', 'data' => 'Что-то пошло не так')));
		}else{
			//Если уволен - не пускать
			if ($rezult[0]['fired'] != '1'){
				//если существует, то сверяем пароли
				if ($rezult[0]['password'] == $password){
					//если пароли совпадают, то запускаем пользователю сессию! Можете его поздравить, он вошел!
                    $_SESSION['login']=$rezult[0]['login'];
                    $_SESSION['id']=$rezult[0]['id'];
                    $_SESSION['name']=$rezult[0]['name'];
                    $_SESSION['permissions']=$rezult[0]['permissions'];
					//эти данные очень часто используются, вот их и будет "носить с собой" вошедший пользователь

                    //логирование
                    AddLog ('0', $_SESSION['id'], '', 'Пользователь вошёл в систему');

                    exit (json_encode(array('result' => 'success', 'data' => 'Вы успешно вошли в систему<br>и будете перенаправлены на <a href="index.php">главную</a>')));
					
				}else{
					//если пароли не сошлись
					exit (json_encode(array('result' => 'error', 'data' => 'Что-то пошло не так')));
				}
			}else{
				//если звёзды не сошлись
				exit (json_encode(array('result' => 'error', 'data' => 'Нельзя пользоваться программой, если вас уволили')));
			}
		}
	}else{
			exit (json_encode(array('result' => 'error', 'data' => 'Что-то пошло не так')));
	}
	
?>