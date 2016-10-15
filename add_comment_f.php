<?php 

//add_comment_f.php
//Функция для добавления комментария
	
	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if ($_POST['t_s_comment'] == ''){
				echo '<span style="font-size: 80%; color: red;">Ничего не написали.</span>';
			}else{
				require 'config.php';
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				$time = time();
				
				$query = "INSERT INTO `comments` (
						`dtable`, `description`, `create_time`, `create_person`, `parent`) 
						VALUES (
						'spr_clients', '{$_POST['t_s_comment']}', '{$time}', '{$_SESSION['id']}', '{$_POST['id']}')";				
				mysql_query($query) or die(mysql_error());
					
				$mysql_insert_id = mysql_insert_id();
					
				mysql_close();
					
				//логирование
				include_once 'DBWork.php';
				
				AddLog ('0', $_SESSION['id'], '', 'Добавлен комментарий #'.$mysql_insert_id.' к клиенту ['.$_POST['id'].']. Текст ['.$_POST['t_s_comment'].'].');	
				
			}
		}
	}
	
?>