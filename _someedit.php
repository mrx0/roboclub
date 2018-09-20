<?php 

				require 'config.php';
				
				$arr = array();
				$journal = array();
				
				mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
				mysql_select_db($dbName) or die(mysql_error()); 
				mysql_query("SET NAMES 'utf8'");
				
				$query = "SELECT * FROM `spr_clients`";
				
				$res = mysql_query($query) or die(mysql_error());
				$number = mysql_num_rows($res);
				if ($number != 0){
					while ($arr = mysql_fetch_assoc($res)){
						array_push($journal, $arr);
					}
				}else{
					$journal = 0;
				}
				var_dump($journal);
				
				if ($journal != 0){
					
					require 'config.php';
					
					mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
					mysql_select_db($dbName) or die(mysql_error()); 
					mysql_query("SET NAMES 'utf8'");
					
					foreach ($journal as $value){
						echo 'Ребёнок: '.$value['name'].'<br>';
						
						$birth = date('Y-m-d', $value['birthday']);
					
						//if ($value['birth'] == '0000-00-00'){
							//var_dump($birth);
							$query = "UPDATE `spr_clients` SET `birth`='{$birth}'  WHERE `id` = '{$value['id']}'";
							mysql_query($query) or die('3->'.mysql_error().'->'.$query);
							
						//}
					
					
						
						/*
						$query = "SELECT `filial` FROM `spr_clients` WHERE `id` = '{$value['client']}'";
						$res = mysql_query($query) or die(mysql_error());
						$number = mysql_num_rows($res);
						if ($number != 0){
							while ($arr = mysql_fetch_assoc($res)){
								echo 'У него филиал: '.$arr['filial'].'<br>';
								$query = "UPDATE `journal_finance` SET `filial`='{$arr['filial']}'  WHERE `id` = '{$value['id']}'";
								$rez = mysql_query($query) or die(mysql_error());
								echo 'Обновлено ОК<br><br>';
							}
						}else{
							echo 'Ошибка<br><br>';
						}*/
					}
				}
				
				mysql_close();	
?>