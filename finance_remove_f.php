<?php 

//finance_remove_f.php
//Функция для перераспределения платежа

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
			
			if ($arr['type'] == 2){
				echo '
					<div class="query_neok">
						Это амортизационный платёж.<br>
						Распределить средства не удасться<br>
						<a href="finance.php?id='.$_POST['id'].'" class="b">Вернуться в платёж</a>
					</div>
				';
			}else{
				if ($arr['summ'] <= $_POST['summ']){
					echo '
						<div class="query_neok">
							Сумма указано неверно.<br>
							Если хотите перенести всю сумму, то просто отредактируйте сам платёж<br>
							<a href="finance.php?id='.$_POST['id'].'" class="b">Вернуться в платёж</a>
						</div>
					';
				}else{
					
					//Редактируем сумму в старом
					$query = "UPDATE `journal_finance` SET 
					`summ`='".($arr['summ']-$_POST['summ'])."', `last_edit_person`='{$_SESSION['id']}', `last_edit_time`='{$time}' WHERE `id`='{$_POST['id']}'";
					mysql_query($query) or die(mysql_error());
					
					//Создаём новый
					$query = "INSERT INTO `journal_finance` (
							`create_time`, `client`, `month`, `year`, `summ`, `type`, `filial`, `create_person`, `comment`) 
							VALUES (
							'{$arr['create_time']}', '{$arr['client']}', '{$_POST['month']}', '{$_POST['year']}',
							'{$_POST['summ']}', '{$arr['type']}', '{$_POST['filial']}', '{$_SESSION['id']}', '{$_POST['comment']}') ";		

					mysql_query($query) or die(mysql_error());
					
					$mysql_insert_id = mysql_insert_id();
					
					mysql_close();
					
					//логирование
					AddLog ('0', $_SESSION['id'], $old, 'Перенесены средства из платежа ['.$arr['id'].'] (старая сумма '.$arr['summ'].', остаток '.($arr['summ']-$_POST['summ']).') в новый ['.$mysql_insert_id.'] (перенесённая сумма '.$_POST['summ'].').');
					
					echo '
						<div class="query_ok">
							<h3>Средства перенесены</h3>
							из платежа #'.$_POST['id'].'
							<br>
							в новый платёж #'.$mysql_insert_id.'<br>
							<a href="finance.php?id='.$_POST['id'].'" class="b">Старый платёж</a>
							<a href="finance.php?id='.$mysql_insert_id.'" class="b">Новый платёж</a>
						</div>
					';
				}
			}
		}

	}
	
?>