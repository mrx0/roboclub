<?php

//client_edit.php
//Редактирование карточки клиента

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($clients['edit'] == 1) || $god_mode){
			if ($_GET){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
			$client = SelDataFromDB('spr_clients', $_GET['id'], 'user');
			//var_dump($_SESSION);
			if ($client !=0){
				echo '
					<div id="status">
						<header>
							<h2>Редактировать карточку клиента</h2>
						</header>';

				echo '
						<div id="data">';

				echo '
							<form action="client_edit_f.php">
								<div class="cellsBlock2">
									<div class="cellLeft">
										ФИО';
				// !!!! Костыль для редактирования ФИО
				if ($god_mode || $clients['edit'] == 1){
					echo '    <a href="client_edit_fio.php?id='.$_GET['id'].'"><img src="img/change.png" title="Редактировать ФИО"></a>';
				}
				echo '
									</div>
									<div class="cellRight">'.$client[0]['full_name'].'</div>
								</div>
								<div class="cellsBlock2">
									<div class="cellLeft">Дата рождения</div>
									<div class="cellRight">';
				if ($client[0]['birthday'] != 0){
					//print_r  (getdate($client[0]['birthday']));
					$bdate = getdate($client[0]['birthday']);
				}else{
					$bdate = 0;
				}
				echo '<select name="sel_date" id="sel_date">';
				$i = 1;
				while ($i <= 31) {
					echo "<option value='" . $i . "'", $bdate['mday'] == $i ? ' selected':'' ,">$i</option>";
					$i++;
				}
				echo "</select>";
				// Месяц
				echo "<select name='sel_month' id='sel_month'>";
				$month = array(
					"Январь",
					"Февраль",
					"Март",
					"Апрель",
					"Май",
					"Июнь",
					"Июль",
					"Август",
					"Сентябрь",
					"Октябрь",
					"Ноябрь",
					"Декабрь"
				);
				foreach ($month as $m => $n) {
					echo "<option value='" . ($m + 1) . "'", ($bdate['mon'] == ($m + 1)) ? ' selected':'' ,">$n</option>";
				}
				echo '</select>';
				// Год
				echo "<select name='sel_year' id='sel_year'>";
				$j = 1920;
				while ($j <= 2020) {
					echo "<option value='" . $j . "'", $bdate['year'] == $j ? ' selected':'' ,">$j</option>";
					$j++;
				}
				echo '</select> <b>'.getyeardiff( $client[0]['birthday']).' лет</b>';

				echo '
									</div>
								</div>

								<div class="cellsBlock2">
									<div class="cellLeft">Пол</div>
									<div class="cellRight">
										<input id="sex" name="sex" value="1" ', $client[0]['sex'] == 1 ? 'checked': '',' type="radio"> М
										<input id="sex" name="sex" value="2" ', $client[0]['sex'] == 2 ? 'checked': '',' type="radio"> Ж
									</div>
								</div>';
								

				echo '								
								<div class="cellsBlock2">
									<div class="cellLeft">
										Контакты
									</div>
									<div class="cellRight">
										<textarea name="contacts" id="contacts" cols="35" rows="5">'.$client[0]['contacts'].'</textarea>
									</div>
								</div>';
								
				echo '				
								<div class="cellsBlock2">
									<div class="cellLeft">
										Комментарий
									</div>
									<div class="cellRight">
										<textarea name="comments" id="comments" cols="35" rows="5">'.$client[0]['comments'].'</textarea>
									</div>
								</div>';
								
				echo '					
											<input type="hidden" id="id" name="id" value="'.$_GET['id'].'">
											<input type=\'button\' class="b" value=\'Редактировать\' onclick=\'
												ajax({
													url:"client_edit_f.php",
													statbox:"status",
													method:"POST",
													data:
													{
														id:document.getElementById("id").value,
														contacts:document.getElementById("contacts").value,
														comments:document.getElementById("comments").value,
														sel_date:document.getElementById("sel_date").value,
														sel_month:document.getElementById("sel_month").value,
														sel_year:document.getElementById("sel_year").value,

														sex:sex_value,
														
														session_id:'.$_SESSION['id'].',
													},
													success:function(data){document.getElementById("status").innerHTML=data;}
												})\'
											>
										</form>';	
						echo '
						
								</div>
							</div>

										
				<script type="text/javascript">
					sex_value = '.$client[0]['sex'].';
					$("input[name=sex]").change(function() {
						sex_value = $("input[name=sex]:checked").val();
					});
				</script>';
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
				}
			}else{
				echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
			}
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>