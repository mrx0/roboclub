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
				//var_dump ($fast_search);
				for ($i = 0; $i < count($fast_search); $i++){
					if (isset($_POST['path']) && ($_POST['path'] == 'group_client.php')){
						echo "
						<div style='border-bottom: 1px #ccc solid; width: 350px;'>
							<div style='display: block; height: 100%;'>
								<span style='font-size: 80%; font-weight: bold;'>".$fast_search[$i]["full_name"]."</span>";
						if (isset($_POST['path']) && ($_POST['path'] == 'group_client.php')){
							echo "<span clientid='".$fast_search[$i]["id"]."' class='addClientInGroup' style='float: right;'
							onclick='
										var id = $(this).attr(\"clientid\");
										ajax({
											url: \"add_ClientInGroup_f.php\",
											method: \"POST\",
											
											data:
											{
												id: id,
												group: group,
												session_id: session_id
											},
											success: function(req){
												alert(req);
												location.reload(true);
											}
										})
							'
							><i class='fa fa-plus' style='color: green; cursor: pointer;'></i></span>";
						}
						echo "
								<br />
								<span style='font-size: 70%'>Дата рождения: ", $fast_search[$i]['birthday'] == "-1577934000" ? "не указана" : date("d.m.Y", $fast_search[$i]["birthday"]) ," / <b>".getyeardiff($fast_search[$i]["birthday"])." лет</b></span><br />
								<span style='font-size: 70%'>Контакты: ".$fast_search[$i]['contacts']."</span>
							</div>
						</div>";
					}else{
						echo "
						<div style='border-bottom: 1px #ccc solid; width: 350px;'>
							<a href='client.php?id=".$fast_search[$i]["id"]."' class='ahref' style='display: block; height: 100%;'>
								<span style='font-size: 80%; font-weight: bold;'>".$fast_search[$i]["full_name"]."</span>
								<br />
								<span style='font-size: 70%'>Дата рождения: ", $fast_search[$i]['birthday'] == "-1577934000" ? "не указана" : date("d.m.Y", $fast_search[$i]["birthday"]) ," / <b>".getyeardiff($fast_search[$i]["birthday"])." лет</b></span><br />
								<span style='font-size: 70%'>Контакты: ".$fast_search[$i]['contacts']."</span>
							</a>
						</div>";
					}
				}
			}
			
		}
	}

?>