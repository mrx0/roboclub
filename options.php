<?php

//options.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';
		if ($god_mode){

            echo '<a href="tarifs.php" class="b">Тарифы</a>';
            echo '<a href="tarif_types.php" class="b">Типы тарифов и сборов</a>';

		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}	
		
	require_once 'footer.php';

?>