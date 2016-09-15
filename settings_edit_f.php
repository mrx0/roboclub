<?php 

//settings_edit_f.php
//Функция для редактирования

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		include_once 'DBWork.php';
		if ($_POST){
			if ((isset($_POST['admSettings'])) && (isset($_POST['admSettings2']))){
				$tempJSON = json_decode($_POST['admSettings'], true);
				$tempJSON2 = json_decode($_POST['admSettings2'], true);
				//var_dump ($tempJSON);
				
				$time = time();
				
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				
				foreach ($tempJSON as $key => $value){
					$query = "INSERT INTO `spr_settings` (`name`, `name_ru`, `value`, `time`) 
					VALUES 
					('$key', '{$tempJSON2[$key]}', '$value', '$time')";
					mysql_query($query) or die($query.' => '.mysql_error());
				}

				mysql_close();
				
				echo '<span style="font-size: 90%; color: green; font-weight: bold;">Сохранено. <a href="" class="ahref">обновите страницу</a></span>';	
			}
		}
	}
	
?>