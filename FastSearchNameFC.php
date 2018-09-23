<?php
	
//FastSearchNameFC.php
//Поиск по имени

	//var_dump ($_POST);
	if ($_POST){
		if(($_POST['searchdata'] == '') || (strlen($_POST['searchdata']) < 3)){
			//--
		}else{
			include_once 'DBWork.php';
			include_once 'functions.php';
			
			$fast_search = SelForFastSearchFullName ('spr_clients', $_POST['searchdata']);
			if ($fast_search != 0){
				//var_dump ($_POST['path']);
				for ($i = 0; $i < count($fast_search); $i++){
					if (isset($_POST['path']) && ($_POST['path'] == 'group_client.php')){
						echo "
						<div style='border-bottom: 1px #ccc solid; width: 350px;'>
							<div style='display: block; height: 100%;'>
								<span style='font-size: 90%; font-weight: bold;'>".$fast_search[$i]["full_name"]."</span>";
						if (isset($_POST['path']) && ($_POST['path'] == 'group_client.php')){
							echo "<span clientid='".$fast_search[$i]["id"]."' class='addClientInGroup' style='float: right;'
							onclick='addClientInGroup(".$fast_search[$i]["id"].", group)'>
							<i class='fa fa-plus' style='color: green; border: 1px solid #CCC; padding: 3px; cursor: pointer; '></i></span>";
						}
						
						if (($fast_search[$i]['birthday'] == "-1577934000") || ($fast_search[$i]['birthday'] == 0)){
							$age = '';
						}else{
							$age = getyeardiff($fast_search[$i]['birthday']).' лет';
						}
						
						echo "
								<br />
								<span style='font-size: 80%'>Дата рождения: ", (($fast_search[$i]['birthday'] == "-1577934000") || ($fast_search[$i]['birthday'] == 0)) ? "не указана" : date("d.m.Y", $fast_search[$i]["birthday"]) ," / <b>".$age."</b></span><br />
								<span style='font-size: 80%'>Контакты: ".$fast_search[$i]['contacts']."</span>
							</div>
						</div>";
					}elseif (isset($_POST['path']) && ($_POST['path'] == 'finances.php')){
						echo "
						<div style='border-bottom: 1px #ccc solid; width: 350px;'>
							<div style='display: block; height: 100%;'>
								<span style='font-size: 90%; font-weight: bold;'>".$fast_search[$i]["full_name"]."</span>";
						if (isset($_POST['path']) && ($_POST['path'] == 'finances.php')){
							echo "<span clientid='".$fast_search[$i]["id"]."' class='addClientInGroup' style='float: right;'>
							<a href='add_finance.php?client=".$fast_search[$i]["id"]."' class='ahref' style='color: green; border: 1px solid #CCC; padding: 3px;'>
							<i class='fa fa-rub' style='font-size: 110%;'></i><i class='fa fa-plus' style='font-size: 80%;'></i>
							</a></span>";
						}
						
						if (($fast_search[$i]['birthday'] == "-1577934000") || ($fast_search[$i]['birthday'] == 0)){
							$age = '';
						}else{
							$age = getyeardiff($fast_search[$i]['birthday']).' лет';
						}
						
						echo "
								<br />
								<span style='font-size: 80%'>Дата рождения: ", (($fast_search[$i]['birthday'] == "-1577934000") || ($fast_search[$i]['birthday'] == 0)) ? "не указана" : date("d.m.Y", $fast_search[$i]["birthday"]) ," / <b>".$age."</b></span><br />
								<span style='font-size: 80%'>Контакты: ".$fast_search[$i]['contacts']."</span>
							</div>
						</div>";					
					}else{
						
						if (($fast_search[$i]['birthday'] == "-1577934000") || ($fast_search[$i]['birthday'] == 0)){
							$age = '';
						}else{
							$age = getyeardiff($fast_search[$i]['birthday']).' лет';
						}
							
						echo "
						<div style='border-bottom: 1px #ccc solid; width: 350px;'>
							<a href='client.php?id=".$fast_search[$i]["id"]."' class='ahref' style='display: block; height: 100%;'>
								<span style='font-size: 90%; font-weight: bold;'>".$fast_search[$i]["full_name"]."</span>
								<br />
								<span style='font-size: 80%'>Дата рождения: ", (($fast_search[$i]['birthday'] == "-1577934000") || ($fast_search[$i]['birthday'] == 0)) ? "не указана" : date("d.m.Y", $fast_search[$i]["birthday"]) ," / <b>".$age."</b></span><br />
								<span style='font-size: 80%'>Контакты: ".$fast_search[$i]['contacts']."</span>
							</a>
						</div>";
					}
				}
			}
			
		}
	}

?>