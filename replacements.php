<?php
//user.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		include_once 'DBWork.php';
		include_once 'functions.php';
		
		$j_replacements = SelDataFromDB('journal_replacement', '', '');
		//var_dump($j_replacements);
		
			if ($j_replacements != 0){
				echo '
					<div id="status">
						<div id="request"></div>
						<header>
							<h2>Назначенные подмены</h2>
						</header>';
				
				echo '
						<div id="data">
							<ul class="live_filter" id="livefilter-list" style="margin-left:6px;">
								<li class="cellsBlock cellsBlockHover" style="width: auto;">
									<div class="cellName"><b>Группа</b></div>
									<div class="cellFullName"><b>Тренер</b></div>
									<div class="cellFullName"><b>Подмена</b></div>
									<div class="cellCosmAct" style="text-align: center;">-</div>
								</li>';
				for ($i = 0; $i < count($j_replacements); $i++) { 
					$j_group = SelDataFromDB('journal_groups', $j_replacements[$i]['group_id'], 'group');
					//var_dump($j_group);
						echo '
								<li class="cellsBlock cellsBlockHover" style="width: auto;">';
					if ($j_group != 0){
						echo '
									<a href="group.php?id='.$j_group[0]['id'].'" class="cellName ahref" id="4filter">'.$j_group[0]['name'].'</a>';

						echo '
									<a href="user.php?id='.$j_replacements[$i]['user_id'].'" class="cellFullName ahref">'.WriteSearchUser('spr_workers', $j_group[0]['worker'], 'user_full').'</a>';
						echo '
									<a href="user.php?id='.$j_replacements[$i]['user_id'].'" class="cellFullName ahref">'.WriteSearchUser('spr_workers', $j_replacements[$i]['user_id'], 'user_full').'</a>';

					}else{
						echo '
									<div class="cellName" id="4filter">ошибка группы</div>
									<div class="cellFullName">-</div>
									<div class="cellFullName">-</div>';
					}
					
					if (($groups['edit'] == 1) || $god_mode){
						echo '
									<div id="delReplacementFromGroup" replacementid="'.$j_replacements[$i]['id'].'" class="cellCosmAct delReplacementFromGroup" style="text-align: center">
										<i class="fa fa-minus" style="color: red; cursor: pointer;"></i>
									</div>';
					}
					echo '	
								</li>';
				}
				echo '
							</ul>';
					
				if (($groups['edit'] == 1) || $god_mode){
					echo '
						<br>
						<span class="delAllReplacementsFromGroup" style="border-bottom: 1px dashed #000080; text-decoration: none; font-size: 70%; color: red; background-color: rgba(252, 252, 0, 0.3); cursor: pointer;">Удалить все подмены</span>';
				}

				echo '
					<script type="text/javascript">
						$(document).ready(function(){
							
							$(\'.delReplacementFromGroup\').on(\'click\', function(data){
								var rys = confirm("Вы уверены?");
								if (rys){
									var id = $(this).attr(\'replacementid\');
									ajax({
										url: "del_ReplacementFromGroup_f.php",
										method: "POST",
										
										data:
										{
											id: id,
											session_id: '.$_SESSION['id'].'
										},
										success: function(req){
											//document.getElementById("request").innerHTML = req;
											alert(req);
											location.reload(true);
										}
									})
								}
							})
							$(\'.delAllReplacementsFromGroup\').on(\'click\', function(data){
								var rys = confirm("Это удалит все подмены\n\nВы уверены?");
								if (rys){
									ajax({
										url: "del_ALLReplacementsFromGroup_f.php",
										method: "POST",
										
										data:
										{
											session_id: '.$_SESSION['id'].'
										},
										success: function(req){
											//document.getElementById("request").innerHTML = req;
											alert(req);
											location.reload(true);
										}
									})
								}
							})
						});
					</script>
				';			
									
			}else{
				echo '<h1>Нечего показывать.</h1><a href="index.php">На главную</a>';
			}

	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>