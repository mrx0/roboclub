<?php

//index.php
//Главная

	require_once 'header.php';

	if ($enter_ok){
		
		require_once 'header_tags.php';

		echo '
			<header style="margin-bottom: 5px;">
				<h1>Главная</h1>';
			echo '
			</header>
		
				<div id="data">';
				

			echo '		
				</div>';
		
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>