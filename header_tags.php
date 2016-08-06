<?php

//header_tags.php
//Заголовок страниц сайта
	
	$god_mode = FALSE;
	
	echo'
		<!DOCTYPE html>
		<html>
		<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
			<meta name="description" content=""/>
			<meta name="keywords" content="" />
			<meta name="author" content="" />
			
			<title>:)</title>
			
			<link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon" />
			<link rel="stylesheet" href="css/style.css" type="text/css" />
			<!--<link rel="stylesheet" href="css/menu.css">-->
			<!--<link rel="stylesheet" type="text/css" href="css/default.css" />-->
			<link rel="stylesheet" type="text/css" href="css/component.css" />
			<link rel="stylesheet" type="text/css" href="css/ModalZakaz.css" />
			<link rel="stylesheet" type="text/css" href="css/jquery-ui.css">	
			
			
			<link rel="stylesheet" type="text/css" href="js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />

<link rel="stylesheet" href="css/calendar.css" type="text/css">

			
			
<script type="text/javascript" src="js/dict.js"></script>
<script type="text/javascript" src="js/common1.js"></script>




<script src="js/chart.js" type="text/javascript"></script>


			
			<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
			<script type="text/javascript" src="js/modernizr.custom.79639.js"></script> 
			
			<script type="text/javascript" src="js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
			<script type="text/javascript" src="js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
			<script type="text/javascript" src="jquery.liveFilter.js"></script>
			<script type="text/javascript" src="js/search.js"></script>
			<script type="text/javascript" src="js/search2.js"></script>
			
			
			<script type="text/javascript" src="js/search4.js"></script>
			
			<script type="text/javascript" src="js/search_fast_client.js"></script>
			



<script src="js/modernizr.custom.js"></script>
		

<script src="js/jquery.scrollUp.js?1.1"></script>

		
<script src="js/jszakaz.js"></script>
<script src="js/jquery-ui.min-1.8.js"></script>



			
			<script type="text/javascript">
				$(document).ready(function(){
					$("a.photo").fancybox({
						transitionIn: \'elastic\',
						transitionOut: \'elastic\',
						speedIn: 500,
						speedOut: 500,
						hideOnOverlayClick: false,
						titlePosition: \'over\'
					});
				});
			</script>
<script type="text/javascript">
	$(function(){
		$(\'#livefilter-list\').liveFilter(\'#livefilter-input\', \'li\', {
			filterChildSelector: \'#4filter\'
		});
	});
</script>
			
    <script>
		$(function () {
			$.scrollUp({
				animation: \'slide\',
				activeOverlay: false,
				scrollText: \'Наверх\',
			});
		});
    </script>
			

<script src="js/DrawTeethMapMenu.js"></script>
			
			<script type="text/javascript">
				function XmlHttp()
				{
				var xmlhttp;
				try{xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");}
				catch(e)
				{
				 try {xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");} 
				 catch (E) {xmlhttp = false;}
				}
				if (!xmlhttp && typeof XMLHttpRequest!=\'undefined\')
				{
				 xmlhttp = new XMLHttpRequest();
				}
				  return xmlhttp;
				}
				 
				function ajax(param)
				{
								if (window.XMLHttpRequest) req = new XmlHttp();
								method=(!param.method ? "POST" : param.method.toUpperCase());
				 
								if(method=="GET")
								{
											   send=null;
											   param.url=param.url+"&ajax=true";
								}
								else
								{
											   send="";
											   for (var i in param.data) send+= i+"="+param.data[i]+"&";
											   send=send+"ajax=true";
								}
				 
								req.open(method, param.url, true);
								if(param.statbox)document.getElementById(param.statbox).innerHTML = \'<img src="img/wait.gif"> Обработка...\';
								req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
								req.send(send);
								req.onreadystatechange = function()
								{
											   if (req.readyState == 4 && req.status == 200) //если ответ положительный
											   {
															   if(param.success)param.success(req.responseText);
											   }
								}
				}
			</script>			

			
<!--для скрытых блоков старое-->				
			<script type="text/javascript">
				function switchDisplay(id){
					var el = document.getElementById(id);
					if (!!el)
						el.style.display = (el.style.display=="none") ? "" : "none";
					return false;
				}
			</script>
			
			
			<script type="text/javascript">
				$( document ).ready(typeres())
				function typeres() {
					$(\'.tabs\').hide();
					var etabs = document.getElementById("tabSelector");
					if (etabs != null){
						if (etabs.options[etabs.selectedIndex].value.indexOf("tabs-") != -1) {
							var tab = \'#tabs-\'+etabs.options[etabs.selectedIndex].value.substring(5);
							$(tab).fadeIn();
						}
					}
				}
			</script>
			
	<!--для скрытых блоков-->				
    <script>
		$(document).ready(function () {
			$(\'#showDiv1\').click(function () {
				$(\'#div1\').stop(true, true).slideToggle(\'slow\');
				$(\'#div2\').slideUp(\'slow\');
			});
			$(\'#showDiv2\').click(function () {
				$(\'#div2\').stop(true, true).slideToggle(\'slow\');
				$(\'#div1\').slideUp(\'slow\');
			});
		});
    </script>
	
	<!--для печати-->	
	<style type="text/css" media="print">
		div.no_print {display: none; }
	</style> 


	
			
		</head>
		<body>

		<div class="no_print"> 
		<header class="h">
			<nav>
				<ul>';
	//Если в системе
	if ($enter_ok){
		include_once 'DBWork.php';
		if ($_SESSION['permissions'] == '777'){
			$god_mode = TRUE;
		}else{
			//Получили список прав
			$permissions = SelDataFromDB('spr_permissions', $_SESSION['permissions'], 'id');	
			//var_dump($permissions);
		}
		if (!$god_mode){
			if ($permissions != 0){
				$it = json_decode($permissions[0]['it'], true);
				//var_dump($it);
				$cosm = json_decode($permissions[0]['cosm'], true);
				$stom = json_decode($permissions[0]['stom'], true);
				$clients = json_decode($permissions[0]['clients'], true);
				$workers = json_decode($permissions[0]['workers'], true);
				$offices = json_decode($permissions[0]['offices'], true);
				$soft = json_decode($permissions[0]['soft'], true);
				$scheduler = json_decode($permissions[0]['scheduler'], true);
				$zapis = json_decode($permissions[0]['zapis'], true);
				//var_dump($offices);
			}
		}else{
			//Видимость
			$it['see_all'] = 0;
			$it['see_own'] = 0;
			$cosm['see_all'] = 0;
			$cosm['see_own'] = 0;
			$stom['see_all'] = 0;
			$stom['see_own'] = 0;
			$workers['see_all'] = 0;
			$workers['see_own'] = 0;
			$clients['see_all'] = 0;
			$clients['see_own'] = 0;
			$offices['see_all'] = 0;
			$offices['see_own'] = 0;
			$soft['see_all'] = 0;
			$soft['see_own'] = 0;
			$scheduler['see_all'] = 0;
			$scheduler['see_own'] = 0;
			$zapis['see_all'] = 0;
			$zapis['see_own'] = 0;
			//
			$it['add_new'] = 0;
			$it['add_own'] = 0;
			$cosm['add_new'] = 0;
			$cosm['add_own'] = 0;
			$stom['add_new'] = 0;
			$stom['add_own'] = 0;
			$workers['add_new'] = 0;
			$workers['add_own'] = 0;
			$clients['add_new'] = 0;
			$clients['add_own'] = 0;
			$offices['add_new'] = 0;
			$offices['add_own'] = 0;
			$soft['add_new'] = 0;
			$soft['add_own'] = 0;
			$scheduler['add_new'] = 0;
			$scheduler['add_own'] = 0;
			$zapis['add_new'] = 0;
			$zapis['add_own'] = 0;
			//
			$it['edit'] = 0;
			$cosm['edit'] = 0;
			$stom['edit'] = 0;
			$workers['edit'] = 0;
			$clients['edit'] = 0;
			$offices['edit'] = 0;
			$soft['edit'] = 0;
			$scheduler['edit'] = 0;
			$zapis['edit'] = 0;
			//
			$it['close'] = 0;
			$cosm['close'] = 0;
			$stom['close'] = 0;
			$workers['close'] = 0;
			$clients['close'] = 0;
			$offices['close'] = 0;
			$soft['close'] = 0;
			$scheduler['close'] = 0;
			$zapis['close'] = 0;
			//
			$it['reopen'] = 0;
			$cosm['reopen'] = 0;
			$stom['reopen'] = 0;
			$workers['reopen'] = 0;
			$clients['reopen'] = 0;
			$offices['reopen'] = 0;
			$soft['reopen'] = 0;
			$scheduler['reopen'] = 0;
			$zapis['reopen'] = 0;
			//
			$it['add_worker'] = 0;
			$cosm['add_worker'] = 0;
			$stom['add_worker'] = 0;
			$workers['add_worker'] = 0;
			$clients['add_worker'] = 0;
			$offices['add_worker'] = 0;
			$soft['add_worker'] = 0;
			$scheduler['add_worker'] = 0;
			$zapis['add_worker'] = 0;
			//
			
		}
		echo '<a href="index.php">Главная</a>';

		/*if (($scheduler['see_all'] == 1) || ($scheduler['see_own'] == 1) || $god_mode){
			echo '<li><a href="scheduler.php">График</a></li>';
		}*/
		if (($clients['see_all'] == 1) || ($clients['see_own'] == 1) || $god_mode){
			echo '<li><a href="clients.php">Клиенты</a></li>';
		}
		if (($offices['see_all'] == 1) || ($offices['see_own'] == 1) || $god_mode){
			echo '<li><a href="filials.php">Филиалы</a></li>';
		}
		if (($workers['see_all'] == 1) || ($workers['see_own'] == 1) || $god_mode){
			echo '<li><a href="contacts.php">Сотрудники</a></li>';
		}
		if ($god_mode){
			echo '<li><a href="admin.php">Adm</a></li>';
		}
	}

	
	echo
				'</ul>
				<ul style="float:right;">';
	if (!$enter_ok){
		echo '
					<li><a href="enter.php">Вход</a></li>';
	}else{
		
		$alarm = 0;
		$warning = 0;
		$pre_warning = 0;
		$if_notes = '';
		$if_removes = '';
		
	
		echo '
					<li><a href="user.php?id='.$_SESSION['id'].'" class="href_profile">['.$_SESSION['name'].']<br /></a><a href="exit.php" class="href_exit">Выход</a></li>';
		
	}
	echo '
				</ul>
			</nav>
		</header>
		</div> 
		<section id="main">
';

?>