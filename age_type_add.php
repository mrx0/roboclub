<?php

//age_type_add.php
//Добавить возрастную группу

	require_once 'header.php';
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		require_once 'header_tags.php';
		include_once 'DBWork.php';
		
		echo '
			<div id="status">
				<header>
                    <div class="nav">
                        <a href="age_types.php" class="b">Возрастные группы</a>
                    </div>
					<h2>Добавить возрастную группу</h2>
					Заполните поля
				</header>';

		echo '
				<div id="data">';

		echo '
					<form action="add_filial_f.php">
				
						<div class="cellsBlock2">
							<div class="cellLeft">от</div>
							<div class="cellRight">
								<input type="text" name="from_age" id="from_age" value="">
							</div>
						</div>
						
						<div class="cellsBlock2">
							<div class="cellLeft">до</div>
							<div class="cellRight">
								<input type="text" name="to_age" id="to_age" value="">
							</div>
						</div>
						
						<div id="errror"></div>
						<input type="button" class="b" value="Добавить" onclick="Ajax_add_age_type();">
					</form>';
			
		echo '
				</div>
			</div>';
	}	
		
	require_once 'footer.php';

?>