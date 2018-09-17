<?php

//add_tarif.php
//Добавить тип тарифов и сборов

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if (($finance['add_new'] == 1) || $god_mode){
			include_once 'DBWork.php';

			echo '
				<div id="status">
					<header>
                        <div class="nav">
                            <a href="tarif_types.php" class="b">Все типы тарифов и сборов</a>
                        </div>
						<h2>Добавить тип тарифа</h2>
						Заполните поля
					</header>';

			echo '
					<div id="data">';
			echo '
						<div id="errrror"></div>';
			echo '
			
                        <div class="cellsBlock2">
                            <div class="cellLeft">Название</div>
                            <div class="cellRight">
                                <input type="text" name="name" id="name" value="">
                                <label id="name_error" class="error"></label>
                            </div>
                        </div>
                        
                        <div class="cellsBlock2">
                            <div class="cellLeft">Описание</div>
                            <div class="cellRight">
                                <textarea name="descr" id="descr" cols="35" rows="5"></textarea>
                                <label id="descr_error" class="error"></label>
                            </div>
                        </div>';
                        

			echo '				
                        <div id="errror"></div>
                        <input type="button" class="b" value="Добавить" onclick="Ajax_add_tarif_type()">';
				
			echo '
					</div>
				</div>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>