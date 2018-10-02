<?php


//tarif_type_edit.php
//Редактирование типа тарифа

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';
    if (($offices['edit'] == 1) || $god_mode){
        if ($_GET){
            include_once 'DBWork.php';
            include_once 'functions.php';

            $tarif_type_j = SelDataFromDB('spr_tarif_types', $_GET['id'], 'id');
            //var_dump($filial);
            echo '
					<div id="status">
                        <header>
                            <div class="nav">
                                <a href="tarif_types.php" class="b">Типы тарифов и сборов</a>
                            </div>
							<h2>Редактировать тип тарифов</h2>
						</header>';
            echo '
						<div id="data">';

            if ($tarif_type_j !=0){
                if ($tarif_type_j[0]['status'] == 9){
                    $closed = TRUE;
                    echo '<div style="margin-bottom: 10px;"><span style= "background: rgba(255,39,119,0.7);">в архиве</span></div>';
                }else{
                    $closed = FALSE;
                }
                echo '
							<form action="edit_filial_f.php">
						
								<div class="cellsBlock2">
									<div class="cellLeft">Название</div>
									<div class="cellRight">';
                if (!$closed){
                    echo '
                                        <input type="text" name="name" id="name" value="'.$tarif_type_j[0]['name'].'">
                                        <label id="name_error" class="error"></label>';
                }else{
                    echo $tarif_type_j[0]['name'];
                }
                echo '
									</div>
								</div>
								
								<div class="cellsBlock2">
									<div class="cellLeft">Описание</div>
									<div class="cellRight">';

                if (!$closed){
                    echo '
										<textarea name="descr" id="descr" cols="35" rows="5">'.$tarif_type_j[0]['descr'].'</textarea>
                                        <label id="descr_error" class="error"></label>';
                }else{
                    echo $tarif_type_j[0]['descr'];
                }
                echo '
									</div>
								</div>';

                if (!$closed){
                    echo '
						        <div id="errror"></div>
								<input type="button" class="b" value="Редактировать" onclick="Ajax_tarif_type_edit('.$tarif_type_j[0]['id'].');">';
                }
                echo '
							</form>';

            }else{
                echo '<h1>Какая-то ошибка</h1>';
            }
            echo '
						</div>
					</div>';
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