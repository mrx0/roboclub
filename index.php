<?php

//index.php
//Главная

	require_once 'header.php';
    require_once 'blocks_dom.php';

	if ($enter_ok){
		
		require_once 'header_tags.php';

		echo '
			<header style="margin-bottom: 5px;">
				<h1>Главная</h1>';
			echo '
			</header>
		
				<div id="data">';


        echo '
					<div class="cellsBlock2" style="width: 400px; position: absolute; top: 20px; right: 20px; z-index: 101;">';

        echo $block_fast_search_client;

        echo '
					</div>';
				
			echo 'Сверху выберите нужный вам раздел для начала работы.<br><br><br><br>';
			echo '<span style="font-size: 80%; color: #850;">Если что-то не работает<br> или работает не так, как должно работать, то<br> обновите страницу или почистите кэш браузера.</span><br><br>';
			echo '<span style="font-size: 60%; color: #CCC;">Живите долго и процветайте. <i class="fa fa-copyright"></i></span>';
			
			
			echo '		
				</div>';
		
	}else{
		header("location: enter.php");
	}
	
	require_once 'footer.php';

?>