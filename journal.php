<?php

//journal.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
				include_once 'DBWork.php';
				include_once 'functions.php';
				include_once 'filter.php';
				include_once 'filter_f.php';
				
				//Массив с месяцами
				$monthsName = array(
				'01' => 'Январь',
				'02' => 'Февраль',
				'03' => 'Март',
				'04' => 'Апрель',
				'05' => 'Май',
				'06' => 'Июнь',
				'07'=> 'Июль',
				'08' => 'Август',
				'09' => 'Сентябрь',
				'10' => 'Октябрь',
				'11' => 'Ноябрь',
				'12' => 'Декабрь'
				);
				
				
				$j_group = SelDataFromDB('journal_groups', $_GET['id'], 'group');
				//var_dump ($j_group);
				
				if ($j_group != 0){
					if (($scheduler['see_all'] == 1) || (($scheduler['see_own'] == 1) && ($j_group[0]['worker'] == $_SESSION['id'])) || $god_mode){
						echo '
							<header style="margin-bottom: 5px;">
								<h1>Журнал посещений< <a href="group.php?id='.$_GET['id'].'" class="ahref">'.$j_group[0]['name'].'</a> ></h1>
							</header>';
								
						if ($j_group[0]['close'] == '1'){
							echo '<span style="color:#EF172F;font-weight:bold;">ГРУППА ЗАКРЫТА</span>';
						}else{
							
							require 'config.php';
							
							$weekDays = array();
							
							$spr_shed_times = SelDataFromDB('spr_shed_time', '', '');
							
							if ($spr_shed_times != 0){
								
								$spr_shed_templs  = SelDataFromDB('spr_shed_templs', $_GET['id'], 'group');
								//var_dump($spr_shed_templs);
								$spr_shed_templs_arr = json_decode($spr_shed_templs[0]['template'], true);
								//var_dump($spr_shed_templs_arr);
								
								if ($spr_shed_templs != 0){
									
									if (isset($_GET['m']) && isset($_GET['y'])){
										$year = $_GET['y'];
										$month = $_GET['m'];
									}else{
										$year = date("Y");
										$month = date("m");
									}
									
									echo '
										<!--<p style="margin: 5px 0; padding: 1px; font-size:80%;">
											Быстрый поиск: 
											<input type="text" class="filter" name="livefilter" id="livefilter-input" value="" placeholder="Поиск"/>
										</p>-->';
									
									for ($i = 1; $i <= count($spr_shed_templs_arr); $i++) {
										//var_dump($spr_shed_templs_arr[$i]['time_id']);
										if ($spr_shed_templs_arr[$i]['time_id'] != 0){
											//var_dump($i);
											//var_dump (getWeekDays(strval($i)));
											if(!isset($weekDays)){
												$weekDays = array_merge(getWeekDays(strval($i), $month, $year));
											}else{
												$weekDays = array_merge($weekDays, getWeekDays(strval($i), $month, $year));
											}
											
										}
									}
									
									//var_dump(!empty($weekDays));
									
									if (!empty($weekDays)){
										
										//var_dump($weekDays);
										sort($weekDays);
										//var_dump($weekDays);
										$weekDaysArr = array();

										if ((int)$month - 1 < 1){
											$prev = '12';
											$pYear = $year - 1;
										}else{
											$pYear = $year;
											if ((int)$month - 1 < 10){
												$prev = '0'.strval((int)$month-1);
											}else{
												$prev = strval((int)$month-1);
											}
										}
										
										if ((int)$month + 1 > 12){
											$next = '01';
											$nYear = $year + 1;
										}else{
											$nYear = $year;
											if ((int)$month + 1 < 10){
												$next = '0'.strval((int)$month+1);
											}else{
												$next = strval((int)$month+1);
											}
										}
										
										echo '<span style="font-size: 80%; color: #CCC;">Сегодня: <a href="journal.php?id='.$_GET['id'].'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>';	
										echo '
											<div id="data">		
												<ul class="live_filter" style="margin-left: 6px;">
													<li class="cellsBlock" style="font-weight: bold; width: auto; text-align: right;">
														<a href="journal.php?id='.$_GET['id'].'&m='.$prev.'&y='.$pYear.'" class="cellTime ahref" style="text-align: center;">
															<span style="font-weight: normal; font-size: 70%;"><< '.$monthsName[$prev].'</span>
														</a>
														<div class="cellTime" style="text-align: center;">
															<span style="color: #2EB703">'.$monthsName[$month].'</span>
														</div>
														<a href="journal.php?id='.$_GET['id'].'&m='.$next.'&y='.$nYear.'" class="cellTime ahref" style="text-align: center;">
															<span style="font-weight: normal; font-size: 70%;">'.$monthsName[$next].' >></span>
														</a>
													</li>
													<br>
													<li class="cellsBlock" style="font-weight: bold; width: auto;">	
														<div class="cellPriority" style="text-align: center"></div>
														<div class="cellFullName" style="text-align: center">ФИО</div>';
										
										for ($i = 0; $i < count($weekDays); $i++) {
											$weekDaysArr = explode('.', $weekDays[$i]);
											echo '<div class="cellTime" style="text-align: center; width: 70px; min-width: 70px;">'.$weekDaysArr[2].'</div>';
										}										
					
										echo '				
													</li>';
													
										$uch_arr = SelDataFromDB('spr_clients', $_GET['id'], 'client_group');
										//var_dump(count($uch_arr));
										
										if ($uch_arr != 0){	

											for ($i = 0; $i < count($uch_arr); $i++) {
												$arr = array();
												$journal_uch = array();
												
												mysql_connect($hostname,$username,$db_pass) OR DIE("Не возможно создать соединение");
												mysql_select_db($dbName) or die(mysql_error()); 
												mysql_query("SET NAMES 'utf8'");
												$query = "SELECT `day`, `status` FROM `journal_user` WHERE `group_id` = '{$_GET['id']}' AND `user_id` = '{$uch_arr[$i]['id']}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
												$res = mysql_query($query) or die(mysql_error());
												$number = mysql_num_rows($res);
												if ($number != 0){
													while ($arr = mysql_fetch_assoc($res)){
														$journal_uch[$arr['day']] = $arr['status'];
													}
												}
												//var_dump($journal_uch);
												
												echo '
													<li class="cellsBlock" style="font-weight: bold; width: auto;">	
														<div class="cellPriority" style="text-align: center"></div>
														<a href="client.php?id='.$uch_arr[$i]['id'].'" class="cellFullName ahref" id="4filter">'.$uch_arr[$i]['full_name'].'</a>';
												
												$weekDaysArr = array();
												//var_dump($weekDays);
												
												for ($j = 0; $j < count($weekDays); $j++) {
													$weekDaysArr = explode('.', $weekDays[$j]);
													if (isset($journal_uch[$weekDaysArr[2]])){
														if ($journal_uch[$weekDaysArr[2]] == 1){
															$backgroundColor = "background-color: rgba(0, 255, 0, 0.5)";
															$journal_ico = '<i class="fa fa-check"></i>';
														}elseif($journal_uch[$weekDaysArr[2]] == 2){
															$backgroundColor = "background-color: rgba(255, 0, 0, 0.5)";
															$journal_ico = '<i class="fa fa-times"></i>';
														}elseif($journal_uch[$weekDaysArr[2]] == 3){
															$backgroundColor = "background-color: rgba(255, 252, 0, 0.5)";
															$journal_ico = '<i class="fa fa-file-text-o"></i>';
														}else{
															$backgroundColor = '';
															$journal_ico = '-';
														}
													}else{
														$backgroundColor = '';
														$journal_ico = '-';
													}
													echo '<div id="'.$uch_arr[$i]['id'].'_'.$weekDays[$j].'" class="cellTime journalItem" style="text-align: center; width: 70px; min-width: 70px; '.$backgroundColor.'" onclick="JournalEdit('.$uch_arr[$i]['id'].', \''.$weekDays[$j].'\');">'.$journal_ico.'</div>';
												}									
					
												echo '				
													</li>';
											}
										}else{
											echo '<h3>В этой группе нет участников</h3>';
										}
													
										echo '
												</ul>

												</div>
												<br><br>
												<div id="errror"></div>
												<input type="button" class="b" value="Сохранить изменения" onclick=Ajax_change_journal()>
												<br><br>
												<span style="font-size: 80%; color: #AAA;">Если допустили ошибку, то, чтобы увидеть актуальный журнал, <a href="" class="ahref">обновите страницу</a></span>
												
											</div>';

										echo '	
											<script type="text/javascript">
												function JournalEdit(id, data){
													elem = document.getElementById(id + "_" + data);
													if (elem.style.backgroundColor === ""){
														elem.style.backgroundColor = "rgba(0, 255, 0, 0.5)";
														elem.innerHTML = "<i class=\"fa fa-check\"></i>";
													}else{
														if ((elem.style.backgroundColor === "rgba(0, 255, 0, 0.5)") || ((elem.style.backgroundColor).indexOf("rgba(0, 255, 0,")) != -1){
															elem.style.backgroundColor = "rgba(255, 0, 0, 0.5)";
															elem.innerHTML = "<i class=\"fa fa-times\"></i>";	
														}else{
															if ((elem.style.backgroundColor === "rgba(255, 0, 0, 0.5)") || ((elem.style.backgroundColor).indexOf("rgba(255, 0, 0,")) != -1){
																elem.style.backgroundColor = "rgba(0, 201, 255, 0.5)";
																elem.innerHTML = "<i class=\"fa fa-check\"></i>";
															}else{
																if ((elem.style.backgroundColor === "rgba(0, 201, 255, 0.5)") || ((elem.style.backgroundColor).indexOf("rgba(0, 201, 255,")) != -1){
																	elem.style.backgroundColor = "rgba(255, 252, 0, 0.5)";
																	elem.innerHTML = "<i class=\"fa fa-file-text-o\"></i>";
																}else{
																	if ((elem.style.backgroundColor === "rgba(255, 252, 0, 0.5)") || ((elem.style.backgroundColor).indexOf("rgba(255, 252, 0,")) != -1){
																		elem.style.backgroundColor = "";
																		elem.innerHTML = "-";	
																	}
																}
															}
														}
													}
													document.getElementById("errror").innerHTML = "<span style=\"color: blue;\">Вы внесли изменения в журнал, не забудьте сохранить.</span>";
												}
												
												function Ajax_change_journal() {
													
													var items = $(".journalItem");
													var resJournalItems = {};
													
													$.each(items, function(){
														//var arr = (this.id).split("_");
														if (this.style.backgroundColor === ""){
															resJournalItems[this.id] = "0";
														}
														if ((this.style.backgroundColor === "rgba(0, 255, 0, 0.5)") || ((this.style.backgroundColor).indexOf("rgba(0, 255, 0,")) != -1){
															resJournalItems[this.id] = "1";
														}
														if ((this.style.backgroundColor === "rgba(255, 0, 0, 0.5)") || ((this.style.backgroundColor).indexOf("rgba(255, 0, 0,")) != -1){
															resJournalItems[this.id] = "2";
														}
														if ((this.style.backgroundColor === "rgba(255, 252, 0, 0.5)") || ((this.style.backgroundColor).indexOf("rgba(255, 252, 0,")) != -1){
															resJournalItems[this.id] = "3";
														}
													});
													//console.log(resJournalItems);
													ajax({
														url: "add_meet_journal_f.php",
														method: "POST",
														
														data:
														{
															group_id: '.$_GET['id'].',
															journalItems: JSON.stringify(resJournalItems),
															session_id: '.$_SESSION['id'].'
														},
														success: function(req){
															//document.getElementById("errror").innerHTML = req;
															alert(req);
															document.getElementById("errror").innerHTML = "";
															//location.reload(true);
														}
													});
												}
												
											</script>
										';
									}
								}else{
									echo '<h3>Для группы не заполнено расписание</h3>';
								}
							}else{
								echo '<h3>Не заполнен справочник графика времени.</h3>';
							}
						}	
					}else{
						echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
					}
				}else{
					echo '<h1>Не найдена такая группа</h1><a href="index.php">Вернуться на главную</a>';
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