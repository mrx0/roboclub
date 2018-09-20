<?php 

//add_client_f.php
//Функция для добавления клиента

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		if ($_POST){
			if (($_POST['fname'] == '')||($_POST['iname'] == '')||($_POST['oname'] == '')){

                echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то не заполнено. Если у ребёнка нет отчества, поставьте в поле "Отчество" символ *<br><a href="clients.php" class="b">К списку детей</a></div>'));

			}else{
				include_once 'DBWork.php';
				include_once 'functions.php';

				if ((preg_match( '/[a-zA-Z]/', $_POST['fname'] )) || (preg_match( '/[a-zA-Z]/', $_POST['iname'] )) || (preg_match( '/[a-zA-Z]/', $_POST['oname'] ))){

                    echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">В ФИО встречаются латинские буквы. Это недопустимо<br><a href="clients.php" class="b">К списку детей</a></div>'));

				}else{
					$full_name = CreateFullName(trim($_POST['fname']), trim($_POST['iname']), trim($_POST['oname']));
					//Проверяем есть ли такой клиент
					if (isSameFullName('spr_clients', $full_name)){

                        echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Такой ребёнок уже есть. Если тёзка, в конце поля "Отчество" поставьте символ *<br><a href="clients.php" class="b">К списку детей</a></div>'));

					}else{
						
						$name = CreateName(trim($_POST['fname']), trim($_POST['iname']), trim($_POST['oname']));

						$birthday = strtotime($_POST['sel_date'].'.'.$_POST['sel_month'].'.'.$_POST['sel_year']);
						
						$birth = date('Y-m-d', $birthday);
						if ($birthday == 0){
							$birth = '00-00-0000';
						}

						//Переменная для тестов
                        //$client_id = 778;

						$client_id = WriteClientToDB_Edit ($_SESSION['id'], $name, $full_name, $_POST['fname'], $_POST['iname'], $_POST['oname'], $_POST['contacts'],  $_POST['comment'], $birthday, $birth, $_POST['sex'], 0);

						$data = "";

			            $filials = SelDataFromDB('spr_office', '', '');

                        $data .= "
                            <header>
                                <h2>Ребёнок ".$full_name." добавлен</h2>
                                Теперь вы можете определить филиал и перейти к следующему шагу.
                            </header>";

			            $data .= "
                            <div id='data'>
                                <div class='cellsBlock2'>
                                    <div class='cellLeft'>Филиал</div>
                                    <div class='cellRight'>
                                        <select name='filial' id='filial'>
                                            <option value='0' selected>Выберите филиал</option>";
									if ($filials != 0){
										for ($i=0;$i<count($filials);$i++){
                                            $data .= "<option value='".$filials[$i]['id']."'>".$filials[$i]['name']."</option>";
										}
									}
                        $data .= "
									</select>
									<label id='filial_error' class='error'></label>
								</div>
							</div>";

                        $data .= "
                            <div id='errror'></div>
                            <input type='hidden' id='client_id' name='client_id' value='".$client_id."'>
							<br><input type='button' class='b' value='Перейти к следующему шагу' onclick='Ajax_update_filial_client(".$client_id.");'>";


                        echo json_encode(array('result' => 'success', 'data' => $data, 'client_id' => $client_id));

					}
				}
			}
		}
	}
?>