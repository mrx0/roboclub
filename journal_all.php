<?php

//journal_all.php
//Журнал всех почещений ребёнка за всю жизнь

	require_once 'header.php';
    require_once 'blocks_dom.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($_GET){
			if (($finance['see_all'] == 1) || $god_mode){

                include_once 'DBWork.php';
                include_once 'functions.php';
                include_once 'filter.php';
                include_once 'filter_f.php';

                require 'variables.php';

				$client = SelDataFromDB('spr_clients', $_GET['client_id'], 'user');
				
				if ($client != 0){
					echo '
						<header style="margin-bottom: 5px;">
							<h1>Посещенные занятия <a href="client.php?id='.$client[0]['id'].'" class="ahref">'.$client[0]['full_name'].'</a></h1>
						</header>';
						

                    echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 101;">';

                    //echo $block_fast_search_client;

                    echo '
					</div>';


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
					
					require 'config.php';	
					
					//Смотрим посещения
					$journal_uch = array();

                    $msql_cnnct = ConnectToDB ();

					//$query = "SELECT * FROM `journal_user` WHERE `client_id` = '".$client[0]['id']."' AND  `month` = '{$month}' AND  `year` = '{$year}' ORDER BY `day` ASC";
					$query = "SELECT * FROM `journal_user` WHERE `client_id` = '".$client[0]['id']."' ORDER BY `year` DESC, `month` DESC, `day` ASC";

                    $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

					$number = mysqli_num_rows($res);

					if ($number != 0){
						while ($arr = mysqli_fetch_assoc($res)){
							array_push($journal_uch, $arr);
						}
					}

					if (!empty($journal_uch)){
                        //var_dump($journal_uch);

                        $journal_arr = array();

                        foreach ($journal_uch as $journal_item) {
                            if (!isset($journal_arr[$journal_item['year']])){
                                $journal_arr[$journal_item['year']] = array();
                            }
                            if (!isset($journal_arr[$journal_item['year']][$journal_item['month']])){
                                $journal_arr[$journal_item['year']][$journal_item['month']] = array();
                            }
                            if (!isset($journal_arr[$journal_item['year']][$journal_item['month']][$journal_item['day']])){
                                $journal_arr[$journal_item['year']][$journal_item['month']][$journal_item['day']] = array();
                            }

                            array_push( $journal_arr[$journal_item['year']][$journal_item['month']][$journal_item['day']], $journal_item);
                        }
                        //var_dump($journal_arr);


						
						foreach ($journal_arr as $year => $year_arr) {
                            //var_dump($year_arr);

                            echo '
						        <ul style="margin-left: 6px; margin-bottom: 20px; border: 1px solid #CCC; box-shadow: 2px 1px 1px 0px rgba(101, 101, 101, 1); width: auto;">
                                    <li style="font-size: 100%; border-bottom: 1px dotted #CCC; background-color: rgba(109, 176, 255, 0.2); color: rgb(78, 78, 78); padding: 10px;">
                                        <b>'.$year.' год</b>
                                    </li>';

                            foreach ($year_arr as $month => $month_arr) {
                                //var_dump($month_arr);

                                echo '
                                    <li style="font-size: 90%; border-bottom: 1px solid #CCC; border-right: 1px solid #CCC; box-shadow: -1px -1px 0px 0px rgba(101, 101, 101, 1); background-color: rgba(227, 255, 109, 0.2); color: rgb(78, 78, 78); margin: 5px; padding: 5px;">
                                        <i>'.$monthsName[$month].'</i>
                                    </li>';

                                echo '
                                    <li style="font-size: 90%; color: rgb(78, 78, 78); margin: 5px; padding: 0px 5px 5px 15px;">';

                                foreach ($month_arr as $day => $day_arr) {
                                    //var_dump($value);

                                    foreach ($day_arr as $value) {


                                        if ($value['status'] == 1) {
                                            $backgroundColor = "background-color: rgba(0, 255, 0, 0.5)";
                                            $journal_ico = '<i class="fa fa-check"></i>';
                                            //$journal_value = 1;

                                            $journal_was++;

                                        } elseif ($value['status'] == 2) {
                                            $backgroundColor = "background-color: rgba(255, 0, 0, 0.5)";
                                            $journal_ico = '<i class="fa fa-times"></i>';
                                            //$journal_value = 2;

                                            $journal_x++;

                                        } elseif ($value['status'] == 3) {
                                            $backgroundColor = "background-color: rgba(255, 252, 0, 0.5)";
                                            $journal_ico = '<i class="fa fa-file-text-o"></i>';
                                            //$journal_value = 3;

                                            $journal_spr++;

                                        } elseif ($value['status'] == 4) {
                                            $backgroundColor = "background-color: rgba(0, 201, 255, 0.5)";
                                            $journal_ico = '<i class="fa fa-check"></i>';
                                            //$journal_value = 4;

                                            $journal_try++;

                                        } else {
                                            $backgroundColor = '';
                                            $journal_ico = '-';
                                            //$journal_value = 0;
                                        }

                                        //Группы
                                        $group_j = array();

                                        $query = "SELECT j_gr.name AS group_name, j_gr.color AS color, s_o.name AS office_name FROM `journal_groups` j_gr
                                                    LEFT JOIN `spr_office` s_o ON j_gr.filial = s_o.id
                                                    WHERE j_gr.id='{$value['group_id']}'
                                                    LIMIT 1;";
                                        //var_dump($query);

                                        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

                                        $number = mysqli_num_rows($res);
                                        if ($number != 0){
                                            while ($arr = mysqli_fetch_assoc($res)){
                                                $group_j['group_name'] = $arr['group_name'];
                                                $group_j['color'] = $arr['color'];
                                                $group_j['office_name'] = $arr['office_name'];
                                            }
                                        }
                                        //var_dump($group_j);

                                        echo '
                                            <a href="journal_new.php?id='.$value['group_id'].'&m='.$value['month'].'&y='.$value['year'].'" class="cellName ahref" style="text-align: center; width: 100px; '.$backgroundColor.'">
                                                <b>'.$value['day'].'.'.$value['month'].'.'.$value['year'].'</b>
                                                <div style="font-size: 70%; margin-top: 10px;">';
                                        if (!empty($group_j)){
                                            echo '<b>'.$group_j['group_name'].'</b><br>[<i>'.$group_j['office_name'].'</i>]';
                                        }else{
                                            echo 'ошибка группы';
                                        }
                                        echo '
                                                </div>
                                                <div style="font-size: 150%;">
                                                    '.$journal_ico.'
                                                </div>
                                                <div style="font-size: 70%; margin-bottom: 20px;"></div>
                                            </a>';

                                    }
                                }
                                echo '
                                    </li>';
                            }

                            echo '
                                </ul>';
                        }


					}else{
						//echo '<h1>В этом месяце посещений не отмечено.</h1>';
					}
					



					echo '
						</ul>';



					echo '	
						<ul style="font-size: 90%; margin-left: 6px; margin-bottom: 20px; border: 1px solid #CCC; width: 500px; padding: 7px;">';

					echo '
						<li class="cellsBlock" style="width: auto; font-size: 90%; color: #777; margin-bottom: 0px; border-bottom: 1px solid #CCC;  ">
							<b>За всё время</b><br>
							<!--<span style="font-size: 80%;">Если общая разница не сходится с <a href="client_finance.php?client='.$client[0]['id'].'" class="ahref">текущим месяцем ()</a>, перераспределите деньги за прошедшие месяцы.</span>-->
						</li>';




						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
							Был на занятиях: <span style="font-weight: bold; font-size: 110%; color: rgba(9, 198, 31, 0.92);">'.$journal_was.'</span>
						</li>';

					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
							Пропустил: <span style="font-weight: bold; font-size: 110%; color: rgba(255, 0, 0, 0.86);">'.$journal_x.'</span>
						</li>';
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 0px;">
							Справка: <span style="font-weight: bold; font-size: 110%; color: rgb(249, 151, 5);">'.$journal_spr.'</span>
						</li>';
						
					echo '
						<li class="cellsBlock" style="width: auto; text-align: right; font-size: 90%; color: #777; margin-bottom: 10px;">
							Пробные: <span style="font-weight: bold; font-size: 110%; color: rgba(0, 201, 255, 0.5);">'.$journal_try.'</span>
						</li>';	
						

					echo '
						</ul>';

					
					echo '		
						</div>';
						
					/*echo '
						<script type="text/javascript">
							function iWantThisDate(){
								var iWantThisMonth = document.getElementById("iWantThisMonth").value;
								var iWantThisYear = document.getElementById("iWantThisYear").value;
								
								window.location.replace("client_finance.php?client='.$client[0]['id'].'&m="+iWantThisMonth+"&y="+iWantThisYear);
							}
						</script>';*/
				
				}else{
					echo '<h1>Что-то пошло не так</h1><a href="index.php">Вернуться на главную</a>';
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