<?php

//add_shed_group.php
//

	require_once 'header.php';

	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
				include_once 'DBWork.php';
				include_once 'functions.php';
				
				$spr_shed_templs_arr = array();
				
				$j_group = SelDataFromDB('journal_groups', $_GET['id'], 'group');
				
				if ($j_group != 0){
					echo '
						<div id="status">
							<header>
								<h2>График группы < <a href="group.php?id='.$_GET['id'].'" class="ahref">'.$j_group[0]['name'].'</a> ></h2>
							</header>';
					if ($j_group[0]['close'] == '1'){
						echo '<span style="color:#EF172F;font-weight:bold;">ЗАКРЫТА</span>';
					}
					if ($j_group[0]['close'] != '1'){
						echo '
						
								<div id="data">';
						echo '
									<form action="add_shed_templs_f.php">
										
										<div style="margin-bottom: 20px;">
											<div class="cellsBlock">
												<div class="cellTime" style="text-align: center; background-color:#CCC; min-width: 80px;">
													<!--<div id="addShedTimes" class="addShedTimes" style="text-align: center">
														<i class="fa fa-plus" style="color: green; cursor: pointer;"></i>
													</div>-->
												</div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>ПН</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>ВТ</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>СР</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>ЧТ</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>ПТ</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>СБ</b></div>
												<div class="cellTime" style="text-align: center; background-color:#FEFEFE; min-width: 80px;"><b>ВС</b></div>
											</div>';
										
										
						$spr_shed_times = SelDataFromDB('spr_shed_time', '', '');
						
						if ($spr_shed_times != 0){

							if (($scheduler['edit'] == 1) || $god_mode){
								$canEdit = true;
							}else{
								$canEdit = false;
							}
						
							$spr_shed_templs  = SelDataFromDB('spr_shed_templs', $_GET['id'], 'group');
							//var_dump($spr_shed_templs);
							
							//if ($spr_shed_templs != 0){
								$spr_shed_templs_arr = json_decode($spr_shed_templs[0]['template'], true);
								//var_dump($spr_shed_templs_arr);
								
								for ($i = 0; $i < count($spr_shed_times); $i++) {
									echo '
												<div class="cellsBlock cellsBlockHover">
													<div class="cellTime" style="text-align: center; min-width: 80px;">
														<b>'.$spr_shed_times[$i]['from_time'].' - '.$spr_shed_times[$i]['to_time'].'</b>
													</div>';
													
									for ($j = 1; $j <= 7; $j++) {
										
										$bg_color = '';
										$checked = '';
										
										if ($spr_shed_templs != 0){
											if ($spr_shed_templs_arr[$j]['time_id'] == $i+1){
												$bg_color = 'background: rgba(0, 255, 0, 0.5);';
												$checked = 'checked';
											}
										}
										echo '
													<div class="cellTime" style="text-align: center; '.$bg_color.' min-width: 80px;">';
										if ($canEdit){
											echo '
														<input type="checkbox" name="'.($i+1).'_'.$j.'" value="1" class="changeColor shedItem" '.$checked.'>';
										}
										echo '
													</div>';
									}
									
									echo '
												</div>';
								}
								
								if ($canEdit){
									echo '		
											</div>
											<div id="errror"></div>
											<input type="button" class="b" value="Сохранить изменения" onclick=Ajax_change_shed()>';
								}
							/*}else{
								echo '<h3>Нету графика для группы</h3>';
							}*/
						}else{
							echo '<h3>Не заполнен справочник графика времени.</h3>';
						}
							
						echo '
									</form>';	
							
						echo '
								</div>
							</div>';
							
						//Фунция JS
						
						echo '
							<script>
								$(document).ready(function(){
									$(".addShedTimes").on("click", function(data){
										
									})
									
									$(".changeColor").on("click", function(data){
										if (this.parentNode.style.background == ""){
											this.parentNode.style.background = "rgba(0, 255, 0, 0.5)";
										}else{
											this.parentNode.style.background = "";
										}
									})
								});
							</script>';			

						echo '
							<script>  
							
								function Ajax_change_shed() {
									
									var items = $(".shedItem");
									var resShedItems = 
										{
											"1": {
												"time_id": "0"
											},
											"2": {
												"time_id": "0"
											},
											"3": {
												"time_id": "0"
											},
											"4": {
												"time_id": "0"
											},
											"5": {
												"time_id": "0"
											},
											"6": {
												"time_id": "0"
											},
											"7": {
												"time_id": "0"
											}
										};

									$.each(items, function(){
										var arr = (this.name).split("_");
										if (this.checked){
											//console.log(arr[1]);
											//ыconsole.log(arr[0]);
											resShedItems[arr[1]]["time_id"] = arr[0];
										}else{
											//resShedItems[arr[1]]["time_id"] = 0;
										}
									});
									
									//console.log(resShedItems);
									
									ajax({
										url:"change_shed_f.php",
										statbox:"status",
										method:"POST",
										data:
										{
											group_id: '.$_GET['id'].',
											shedItems: JSON.stringify(resShedItems),
											session_id:'.$_SESSION['id'].',
										},
										success:function(data){
											document.getElementById("status").innerHTML=data;
										}
									});				
								};  
								  
							</script> 
						';	
					}
				}else{
					echo '<h1>Нет такой группы</h1><a href="groups.php">К группам</a>';
				}
			}else{
				echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
			}
		}else{
			echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>