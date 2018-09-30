<?php

//journal_new.php
//Новый журнал после введения новой финансовой системы

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
				include_once 'DBWork.php';
				include_once 'functions.php';

                include_once 'widget_calendar.php';

                require 'variables.php';

				$j_group = SelDataFromDB('journal_groups', $_GET['id'], 'group');
				//var_dump ($j_group);
				
				//Определяем подмены
				$iReplace = FALSE;

                $dop = '';

                $msql_cnnct = ConnectToDB ();
				
				$query = "SELECT * FROM `journal_replacement` WHERE `group_id`='{$_GET['id']}' AND `user_id`='{$_SESSION['id']}'";

                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                $number = mysqli_num_rows($res);

				if ($number != 0){
					$iReplace = TRUE;
				}else{
				}

                foreach ($_GET as $key => $value){
                    if ($key == 'id'){
                        $dop .= '&'.$key.'='.$value;
                    }
                }

				if ($j_group != 0){
					if (($scheduler['see_all'] == 1) || (($scheduler['see_own'] == 1) && (($j_group[0]['worker'] == $_SESSION['id']) || ($iReplace))) || $god_mode){
						echo '
							<header style="margin-bottom: 5px;">
							     <div class="nav">
							        <a href="journal.php?id='.$_GET['id'].'" class="b3" style="background-color: #CCC;">Журнал посещений (старое)</a>
							     </div>
								<h1>Журнал посещений< <a href="group.php?id='.$_GET['id'].'" class="ahref">'.$j_group[0]['name'].'</a> ></h1>
							</header>';

								
						if ($j_group[0]['close'] == '1'){
							echo '<span style="color:#EF172F;font-weight:bold;">ГРУППА ЗАКРЫТА</span>';
						}else{
							
							require 'config.php';
							
							$weekDays = array();
							
							$spr_shed_times = SelDataFromDB('spr_shed_time', '', '');
							
							if ($spr_shed_times != 0){
								
								//Шаблон графика для группы
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
										if (($scheduler['see_all'] == 1) || ($god_mode)){
											echo '<span style="font-size: 80%; color: rgb(125, 125, 125);">Тренер: '.WriteSearchUser2('spr_workers', $j_group[0]['worker'], 'user', true).'</span><br>';
										}										
										//echo '<span style="font-size: 80%; color: rgb(125, 125, 125);">Сегодня: <a href="journal_new.php?id='.$_GET['id'].'" class="ahref">'.date("d").' '.$monthsName[date("m")].' '.date("Y").'</a></span>';

										echo '
											<div id="data">		
												<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">';

                                        echo widget_calendar ($month, $year, 'journal_new.php', $dop);

                                        echo '
                                            <br>
													<li class="cellsBlock" style="font-weight: bold; width: auto;">	
														<div class="cellPriority" style="text-align: center"></div>
														<div class="cellFullName" style="text-align: center">ФИО</div>
														<!--<div class="cellCosmAct" style="text-align: center"><i class="fa fa-rub"></i></div>-->';
										
										for ($i = 0; $i < count($weekDays); $i++) {
											$weekDaysArr = explode('.', $weekDays[$i]);
											if (($scheduler['see_all'] == 1) || $god_mode){
												echo '
													<a class="cellTime ahref cellsBlockHover" href="meets.php?id='.$_GET['id'].'&d='.$weekDaysArr[2].'&m='.$month.'&y='.$year.'" style="text-align: center; width: 70px; min-width: 70px;">'.$weekDaysArr[2].'</a>';
											}else{
												echo '<div class="cellTime" style="text-align: center; width: 70px; min-width: 70px;">'.$weekDaysArr[2].'</div>';
											}
										}										
					
										echo '				
													</li>';
													
										$uch_arr = SelDataFromDB('spr_clients', $_GET['id'], 'client_group');
										//var_dump($uch_arr);
										
										$journal_uch = array();										
										
										if ($uch_arr != 0){	

											$arr = array();
											
											$query = "SELECT `client_id`, `day`, `status` FROM `journal_user` WHERE `group_id` = '{$_GET['id']}' AND  `month` = '{$month}' AND  `year` = '{$year}'";

                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                            $number = mysqli_num_rows($res);

											if ($number != 0){
												while ($arr = mysqli_fetch_assoc($res)){
													$journal_uch[$arr['client_id']][$arr['day']] = $arr['status'];
												}
											}
											//var_dump($journal_uch);

											for ($i = 0; $i < count($uch_arr); $i++) {

												//Присутствовал
												$journal_was = 0;
												//Цена если был
												$need_cena = 0;
												//Общий долг
												$need_summ = 0;
												//Кол-во отсутствий
												$journal_x = 0;
												//Кол-во справок
												$journal_spr = 0;
												//Кол-во пробных
												$journal_try = 0;
												//
												$thisMonthRazn = 0;
												
												echo '
													<li class="cellsBlock cellsBlockHover" style="font-weight: bold; width: auto;">	
														<div class="cellPriority" style="text-align: center;"></div>
														<a href="client.php?id='.$uch_arr[$i]['id'].'" class="cellFullName ahref" id="4filter" style="position: relative;">'.$uch_arr[$i]['full_name'];
														
												$query = "SELECT * FROM `comments` WHERE `dtable`='spr_clients' AND `parent`='{$uch_arr[$i]['id']}'";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                                $number = mysqli_num_rows($res);

                                                if ($number != 0){
													echo '
															<div style="position: absolute; top: 0; right: 3px; color: rgb(247, 188, 50);">
																<i class="fa fa-commenting" title="Есть комментарии"></i>
															</div>';
												}else{
												}

												echo '
														</a>';
												
												$weekDaysArr = array();
												//var_dump($weekDays);
												
												//бегаем по дням
												for ($j = 0; $j < count($weekDays); $j++) {
													$weekDaysArr = explode('.', $weekDays[$j]);
													
													//$timeForPay = strtotime($weekDaysArr[2].'.'.$weekDaysArr[1].'.'.$weekDaysArr[0].' 23:59:59');
													
													if (isset($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]])){
														if ($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]] == 1){
															$backgroundColor = "background-color: rgba(0, 255, 0, 0.5)";
															$journal_ico = '<i class="fa fa-check"></i>';
															$journal_value = 1;
															

														}elseif($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]] == 2){
															$backgroundColor = "background-color: rgba(255, 0, 0, 0.5)";
															$journal_ico = '<i class="fa fa-times"></i>';
															$journal_value = 2;

														}elseif($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]] == 3){
															$backgroundColor = "background-color: rgba(255, 252, 0, 0.5)";
															$journal_ico = '<i class="fa fa-file-text-o"></i>';
															$journal_value = 3;
															

															
														}elseif($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]] == 4){
															$backgroundColor = "background-color: rgba(0, 201, 255, 0.5)";
															$journal_ico = '<i class="fa fa-check"></i>';
															$journal_value = 4;
								
															$journal_try++;
															

														}else{
															$backgroundColor = '';
															$journal_ico = '-';
															$journal_value = 0;
														}
														
														unset($journal_uch[$uch_arr[$i]['id']][$weekDaysArr[2]]);
														if (empty($journal_uch[$uch_arr[$i]['id']])){
															unset($journal_uch[$uch_arr[$i]['id']]);
														}
														
													}else{
														$backgroundColor = '';
														$journal_ico = '-';
														$journal_value = 0;
													}
													echo '<div id="'.$uch_arr[$i]['id'].'_'.$weekDays[$j].'" class="cellTime journalItem" style="text-align: center; width: 70px; min-width: 70px; '.$backgroundColor.'" onclick="JournalEdit('.$uch_arr[$i]['id'].', \''.$weekDays[$j].'\');">'.$journal_ico.'</div>';
													echo '<input type="hidden" id="'.$uch_arr[$i]['id'].'_'.$weekDays[$j].'_value" class="journalItemVal" value="'.$journal_value.'">';
												}	
												

												$journal_uch_all = array();
												$arr = array();
												
												$query = "SELECT * FROM `journal_user` WHERE `client_id` = '".$uch_arr[$i]['id']."' AND  `month` = '{$month}' AND  `year` = '{$year}' ORDER BY `day` ASC";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                                $number = mysqli_num_rows($res);

												if ($number != 0){
													while ($arr = mysqli_fetch_assoc($res)){
														//var_dump($arr);
														array_push($journal_uch_all, $arr);
													}
												}else{
													$journal_uch_all = 0;
												}
												//var_dump($journal_uch_all);
												
												if ($journal_uch_all != 0){
													foreach ($journal_uch_all as $key => $value) {

														$timeForPay = strtotime($value['day'].'.'.$value['month'].'.'.$value['year'].' 23:59:59');

														if ($value['status'] == 1){
															$journal_was++;

															/*foreach($settings['cena1'] as $key_time => $value_time_arr){
																$need_cena = 0;

																//если только одно значение
																if (count($settings['cena1']) == 1){
																	$need_cena = $settings['cena1'][$key_time]['value'];
																	$need_summ += $settings['cena1'][$key_time]['value'];
																}else{
																	//Если указанное в посещении время меньше чем текущее в цикле
																	if ($timeForPay < $key_time){
																		continue;
																	}else{
																		$need_cena = $settings['cena1'][$key_time]['value'];
																		$need_summ += $settings['cena1'][$key_time]['value'];
																		break;
																	}
																}
															}*/

														}elseif($value['status'] == 2){

															$journal_x++;

														}elseif($value['status'] == 3){

															$journal_spr++;

															//$need_cena = 0;

														}elseif($value['status'] == 4){

															$journal_try++;

														}else{
															$backgroundColor = '';
															$journal_ico = '-';
															$journal_value = 0;
														}

													}
												}else{
												}

												//Смотрим количество доступных занятий у ребёнка
                                                $journal_uch_lessons_balance = array();

                                                $lessons_summ = 0;
                                                $lessons_debt = 0;

                                                $query = "SELECT `summ`, `debt` FROM `journal_lessons_balance` WHERE `client_id` = '".$uch_arr[$i]['id']."' LIMIT 1";

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                                $number = mysqli_num_rows($res);

                                                if ($number != 0){
                                                    while ($arr = mysqli_fetch_assoc($res)){
                                                        //var_dump($arr);
                                                        //array_push($journal_uch_lessons_balance, $arr);
                                                        $lessons_summ = $arr['summ'];
                                                        $lessons_debt = $arr['debt'];
                                                    }
                                                }

												//Итог
												//Разница между потрачено и внесено уроков
												if (($lessons_summ - $lessons_debt) > 0){

													echo '
														<div id="" class="cellTime" style="text-align: center; width: 40px; min-width: 40px; background-color: rgb(143, 243, 0); color: rgb(62, 56, 56);">
												            '.($lessons_summ - $lessons_debt).' <i class="fa fa-thumbs-o-up"></i>
														</div>';

												}else{
													echo '
														<div id="" class="cellTime" style="text-align: center; width: 40px; min-width: 40px; background-color: rgb(210, 11, 11); color: #FFF;">
													        '.($lessons_summ - $lessons_debt).' <i class="fa fa-thumbs-down"></i>
														</div>';

												}

                                                //Смотрим баланс денежек
                                                if (($finance['see_all'] == 1) || $god_mode){
                                                    //Смотрим количество доступных занятий у ребёнка
                                                    $journal_balance = array();

                                                    $summ = 0;
                                                    $debited = 0;

                                                    /*$query = "SELECT `summ`, `debited` FROM `journal_balance` WHERE `client_id` = '".$uch_arr[$i]['id']."' LIMIT 1";

                                                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                                    $number = mysqli_num_rows($res);

                                                    if ($number != 0){
                                                        while ($arr = mysqli_fetch_assoc($res)){
                                                            //var_dump($arr);
                                                            //array_push($journal_uch_lessons_balance, $arr);
                                                            $summ = $arr['summ'];
                                                            $debited = $arr['debited'];
                                                        }
                                                    }*/

                                                    $client_debt = json_decode(calculateDebt ($uch_arr[$i]['id']), true);

                                                    //Итог
                                                    //Разница между потрачено и внесено денег
                                                    if (($client_debt['summ']) <= 0) {
                                                        $SummColor = '';
                                                    }else {
                                                        $SummColor = 'color: rgb(210, 11, 11);';
                                                    }

												    //Сумма долга
													echo '
														<div id="" class="cellTime" style="text-align: right; width: 70px; min-width: 70px; '.$SummColor.'">
															'.($client_debt['summ']).'
														</div>
														<a href="client_balance.php?client_id='.$uch_arr[$i]['id'].'" class="cellTime ahref" style="text-align: center; width: 20px; min-width: 20px;" title="Баланс"><i class="fa fa-rub"></i></a>';
												}

												echo '				
													</li>';
											}
											
											//Номера упражнений
											echo '
													<li class="cellsBlock" style="font-weight: bold; width: auto; margin-top: 20px;">	
														<div class="cellPriority" style="text-align: center;"></div>
														<div class="cellFullName" style="text-align: center; color: #FF9900; font-weight: normal;">Упражнения</div>';
											for ($i = 0; $i < count($weekDays); $i++) {
												//echo $weekDaysArr[2];
												
												$weekDaysArr = explode('.', $weekDays[$i]);

												$month = (int)$month;
												
												$query = "SELECT `descr` FROM `journal_exercize` WHERE `group_id` = '{$_GET['id']}' AND `day`= '{$weekDaysArr[2]}' AND  `month` = '{$month}' AND  `year` = '{$year}'";
												//var_dump($query);

                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

												$number = mysqli_num_rows($res);

												if ($number != 0){
													$arr = mysqli_fetch_assoc($res);
													if (($arr['descr'] == '') || ($arr['descr'] == null)){
														echo '<div id="'.$weekDaysArr[2].'" class="cellTime doExercize" style="text-align: center; width: 70px; min-width: 70px; color: #FF9900; cursor: pointer">';
														echo '<i class="fa fa-dot-circle-o"></i>';
													}else{
														echo '<div id="'.$weekDaysArr[2].'" class="cellTime doExercize" style="text-align: center; width: 70px; min-width: 70px; color: #FF9900; cursor: pointer">';													
														echo '<span style="font-weight: normal; color: #666; font-size: 80%;">'.$arr['descr'].'</span>';
													}
												}else{
													echo '<div id="'.$weekDaysArr[2].'" class="cellTime doExercize" style="text-align: center; width: 70px; min-width: 70px; color: #FF9900; cursor: pointer">';
													echo '<i class="fa fa-dot-circle-o"></i>';
												}

												echo '</div>';
											}	
											
											
										}else{
											echo '<h3>В этой группе нет участников</h3>';
											
											//Но если они когда-то были

											$query = "SELECT `client_id`, `day`, `status` FROM `journal_user` WHERE `group_id` = '{$_GET['id']}' AND  `month` = '{$month}' AND  `year` = '{$year}'";

                                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                            $number = mysqli_num_rows($res);

											if ($number != 0){
												while ($arr = mysqli_fetch_assoc($res)){
													$journal_uch[$arr['client_id']][$arr['day']] = $arr['status'];
												}
											}
											//var_dump($journal_uch);
											
										}
													
										echo '
												</ul>';
												

												
										if (count($journal_uch) > 0){
											//var_dump($journal_uch);
											
											echo '
												<span style="font-size: 80%; color: rgb(150, 150, 150);">Ниже перечислены участники, которые ранее были в этой группе и отмечались в журнале.<br>Сейчас этих участников в данной группе нет.</span>
												<br>
												<br>
												<ul class="live_filter" style="margin-left: 6px; margin-bottom: 20px;">
													<li class="cellsBlock cellsBlockHover" style="font-weight: bold; width: auto;">	
														<div class="cellPriority" style="text-align: center"></div>
														<div class="cellFullName" style="text-align: center">ФИО</div>
														';
											
											for ($i = 0; $i < count($weekDays); $i++) {
												$weekDaysArr = explode('.', $weekDays[$i]);
												echo '<div class="cellTime" style="text-align: center; width: 70px; min-width: 70px;">'.$weekDaysArr[2].'</div>';
											}										
						
											echo '				
													</li>';

											foreach ($journal_uch as $us_id => $value) {
												
												echo '
														<li class="cellsBlock" style="font-weight: bold; width: auto;">	
															<div class="cellPriority" style="text-align: center;"></div>
															<a href="client.php?id='.$us_id.'" class="cellFullName ahref" id="4filter" style="position: relative;">'.WriteSearchUser('spr_clients', $us_id, 'user_full');
															
												$query = "SELECT * FROM `comments` WHERE `dtable`='spr_clients' AND `parent`='{$us_id}'";
												//var_dump ($query);
                                                $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

												$number = mysqli_num_rows($res);

												if ($number != 0){
													echo '
																<div style="position: absolute; top: 0; right: 3px; color: rgb(247, 188, 50);">
																	<i class="fa fa-commenting" title="Есть комментарии"></i>
																</div>';
												}else{
												}
												echo '
															</a>';
													
												$weekDaysArr = array();
												//var_dump($weekDays);
													
												for ($j = 0; $j < count($weekDays); $j++) {
													$weekDaysArr = explode('.', $weekDays[$j]);
													if (isset($value[$weekDaysArr[2]])){
														if ($value[$weekDaysArr[2]] == 1){
															$backgroundColor = "background-color: rgba(0, 255, 0, 0.5)";
															$journal_ico = '<i class="fa fa-check"></i>';
															$journal_value = 1;
														}elseif($value[$weekDaysArr[2]] == 2){
															$backgroundColor = "background-color: rgba(255, 0, 0, 0.5)";
															$journal_ico = '<i class="fa fa-times"></i>';
															$journal_value = 2;
														}elseif($value[$weekDaysArr[2]] == 3){
															$backgroundColor = "background-color: rgba(255, 252, 0, 0.5)";
															$journal_ico = '<i class="fa fa-file-text-o"></i>';
															$journal_value = 3;
														}elseif($value[$weekDaysArr[2]] == 4){
															$backgroundColor = "background-color: rgba(0, 201, 255, 0.5)";
															$journal_ico = '<i class="fa fa-check"></i>';
															$journal_value = 4;
														}else{
															$backgroundColor = '';
															$journal_ico = '-';
															$journal_value = 0;
														}
														
														unset($journal_uch[$us_id]);
														
													}else{
														$backgroundColor = '';
														$journal_ico = '-';
														$journal_value = 0;
													}
													//echo '<div id="'.$us_id.'_'.$weekDays[$j].'" class="cellTime journalItem" style="text-align: center; width: 70px; min-width: 70px; '.$backgroundColor.'" onclick="JournalEdit('.$us_id.', \''.$weekDays[$j].'\');">'.$journal_ico.'</div>';
													echo '<div id="'.$us_id.'_'.$weekDays[$j].'" class="cellTime journalItem" style="text-align: center; width: 70px; min-width: 70px; '.$backgroundColor.'">'.$journal_ico.'</div>';
													echo '<input type="hidden" id="'.$us_id.'_'.$weekDays[$j].'_value" class="journalItemVal" value="'.$journal_value.'">';
												}									
												
												echo '				
														</li>';
											}
														
											echo '
													</ul>';
										}



										echo '
												</div>
												<br><br>
												<div id="errror"></div>
												<input type="button" class="b" value="Сохранить изменения" onclick=Ajax_change_journal('.$_GET['id'].')>';
										echo '		
												<br><br>
												<span style="font-size: 80%; color: rgb(150, 150, 150);">Если допустили ошибку, то, чтобы увидеть актуальный журнал, <a href="" class="ahref">обновите страницу</a></span>
												
											</div>';

										echo '	
											<script type="text/javascript">
												function JournalEdit(id, data){
													elem = document.getElementById(id + "_" + data);
													elem_val = document.getElementById(id + "_" + data + "_value");';
												
										if (($scheduler['see_all'] == 1) || $god_mode){
											echo '	
													if (elem_val.value == 0){
														elem.style.backgroundColor = "rgba(0, 255, 0, 0.5)";
														elem.innerHTML = "<i class=\"fa fa-check\"></i>";
														elem_val.value = 1;
													}else{
														if (elem_val.value == 1){
															elem.style.backgroundColor = "rgba(255, 0, 0, 0.5)";
															elem.innerHTML = "<i class=\"fa fa-times\"></i>";
															elem_val.value = 2;
														}else{
															if (elem_val.value == 2){
																elem.style.backgroundColor = "rgba(0, 201, 255, 0.5)";
																elem.innerHTML = "<i class=\"fa fa-check\"></i>";
																elem_val.value = 4;
															}else{
																if (elem_val.value == 4){
																	elem.style.backgroundColor = "rgba(255, 252, 0, 0.5)";
																	elem.innerHTML = "<i class=\"fa fa-file-text-o\"></i>";
																	elem_val.value = 3;
																}else{
																	if (elem_val.value == 3){
																		elem.style.backgroundColor = "";
																		elem.innerHTML = "-";	
																		elem_val.value = 0;
																	}
																}
															}
														}
													}';
										}else{
											if ($scheduler['see_own'] == 1){
												echo '	
													if (elem_val.value == 0){
														elem.style.backgroundColor = "rgba(0, 255, 0, 0.5)";
														elem.innerHTML = "<i class=\"fa fa-check\"></i>";
														elem_val.value = 1;
													}else{
														if (elem_val.value == 1){
															elem.style.backgroundColor = "rgba(255, 0, 0, 0.5)";
															elem.innerHTML = "<i class=\"fa fa-times\"></i>";
															elem_val.value = 2;
														}else{
															if (elem_val.value == 2){
																elem.style.backgroundColor = "rgba(0, 201, 255, 0.5)";
																elem.innerHTML = "<i class=\"fa fa-check\"></i>";
																elem_val.value = 4;
															}else{
																	if ((elem_val.value == 3) || (elem_val.value == 4)){
																		elem.style.backgroundColor = "";
																		elem.innerHTML = "-";	
																		elem_val.value = 0;
																	}
															}
														}
													}';
											}
										}
										echo '											
												document.getElementById("errror").innerHTML = "<span style=\"color: blue; font-size: 80%;\">Вы внесли изменения в журнал, не забудьте сохранить.<br>Или обновите страницу для сброса.</span>";
												}
												

												
												
												var elems = document.getElementsByClassName("doExercize"), newInput;
												for (var i=0; i<elems.length; i++) {
													var el = elems[i];
													el.addEventListener("click", function() {
														var thisID = this.id;
														
														var  inputs = this.getElementsByTagName("input");
														if (inputs.length > 0) return;
														if (!newInput) {
															newInput = document.createElement("input");
															newInput.type = "text";
															newInput.maxLength = 7;
															newInput.setAttribute("size", 20);
															newInput.style.width = "40px";
															newInput.addEventListener("blur", function() {
																if (newInput.value == ""){
																	newInput.parentNode.innerHTML = "<i class=\"fa fa-dot-circle-o\"></i>";
																}else{
																	newInput.parentNode.innerHTML = "<span style=\"font-weight: normal; color: #666; font-size: 80%;\">"+newInput.value+"</span>";
																}
																ajax({
																	url: "add_exercize_f.php",
																	method: "POST",
																	
																	data:
																	{
																		group_id: '.$_GET['id'].',
																		data: newInput.value,
																		day: thisID,
																		month: '.$month.',
																		year: '.$year.',
																		session_id: '.$_SESSION['id'].'
																	},
																	success: function(req){
																		//document.getElementById("errror").innerHTML = req;
																		//alert(req);
																		//document.getElementById("errror").innerHTML = "";
																		location.reload(true);
																	}
																});
															}, false)
														}

														newInput.value = this.firstChild.innerHTML;
														this.innerHTML = "";
														this.appendChild(newInput);
														newInput.focus();
														newInput.select()
													}.bind(el), false);
												};
												
											</script>
										';
										echo '
											<script type="text/javascript">
												function iWantThisDate(){
													var iWantThisMonth = document.getElementById("iWantThisMonth").value;
													var iWantThisYear = document.getElementById("iWantThisYear").value;
													
													window.location.replace("journal_new.php?id='.$_GET['id'].'&m="+iWantThisMonth+"&y="+iWantThisYear);
												}
											</script>';
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