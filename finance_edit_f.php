<?php 

//finance_edit_f.php
//Функция для редактирования платежа

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		include_once 'DBWork.php';
		if ($_POST){

			$old = '';
			require 'config.php';
			mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
			mysql_select_db($dbName) or die(mysql_error()); 
			mysql_query("SET NAMES 'utf8'");
			//Для лога соберем сначала то, что было в записи.
			$query = "SELECT * FROM `journal_finance` WHERE `id`='{$_POST['id']}'";
			$res = mysql_query($query) or die(mysql_error());
			$number = mysql_num_rows($res);
			if ($number != 0){
				$arr = mysql_fetch_assoc($res);
				$old = 'Клиент ['.$arr['client'].']. Сумма ['.$arr['summ'].']. Месяц ['.$arr['month'].']. Год ['.$arr['year'].']. Тип ['.$arr['type'].']. . Филиал ['.$arr['filial'].']. Комментарий ['.$arr['comment'].'].';
			}else{
				$old = 'Не нашли старую запись.';
			}
			$time = time();
			$query = "UPDATE `journal_finance` SET 
			`month`='{$_POST['month']}', `year`='{$_POST['year']}', `summ`='{$_POST['summ']}', `type`='{$_POST['type']}', `filial`='{$_POST['filial']}', `comment`='{$_POST['comment']}', `last_edit_person`='{$_SESSION['id']}', `last_edit_time`='{$time}' WHERE `id`='{$_POST['id']}'";
			mysql_query($query) or die(mysql_error());
			mysql_close();
			
			//логирование
			AddLog ('0', $_SESSION['id'], $old, 'Отредактирован платёж ['.$_POST['id'].']. ['.date('d.m.y H:i', $time).']. Сумма ['.$_POST['summ'].']. Месяц ['.$_POST['month'].']. Год ['.$_POST['year'].']. Тип ['.$_POST['type'].']. Филиал ['.$_POST['filial'].']. Комментарий ['.$_POST['comment'].'].');
			
			echo '
				<h1>Платёж отредактирован.</h1>
				<a href="finance.php?id='.$_POST['id'].'" class="b">Вернуться в платёж</a>
			';			
		}

	}
	
?>