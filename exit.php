<?php

//exit.php
//Закрытие сессии на сайту

	//Запускаем сессию для работы с куками
	session_start();
	
	include_once 'DBWork.php';
	//логирование
	AddLog ('0', $_SESSION['id'], '', 'Пользователь вышел из системы');
	//Так как пользователь хотел выйти,
	//удаляем ему логин и id из кукисов
	unset($_SESSION['login']);
	unset($_SESSION['id']);
	unset($_SESSION['permissions']);
	unset($_SESSION['filial']);
	 
	//Переадресовываем на главную
	header("location: index.php");

?>