<?php 

//functions.php
//Различные функции

	include_once 'DBWork.php';
	
	//Создаём Полное ФИО
	function CreateFullName($f, $i, $o){
		$full_name =$f.' '.$i.' '.$o;
		
		return $full_name;
	}	
	
	//Создаём Краткое ФИО
	function CreateName($f, $i, $o){
		$name = $f.' '.mb_substr($i, 0, 1, "UTF-8").'.'.mb_substr($o, 0, 1, "UTF-8").'.';
		
		return $name;
	}	
	
	//Создаём логин
	function CreateLogin($f, $i, $o){
		$replace = array(
			"А"=>"a","а"=>"a",
			"Б"=>"b","б"=>"b",
			"В"=>"v","в"=>"v",
			"Г"=>"g","г"=>"g",
			"Д"=>"d","д"=>"d",
			"Е"=>"e","е"=>"e",
			"Ё"=>"e","ё"=>"e",
			"Ж"=>"z","ж"=>"z",
			"З"=>"z","з"=>"z",
			"И"=>"i","и"=>"i",
			"Й"=>"i","й"=>"i",
			"К"=>"k","к"=>"k",
			"Л"=>"l","л"=>"l",
			"М"=>"m","м"=>"m",
			"Н"=>"n","н"=>"n",
			"О"=>"o","о"=>"o",
			"П"=>"p","п"=>"p",
			"Р"=>"r","р"=>"r",
			"С"=>"s","с"=>"s",
			"Т"=>"t","т"=>"t",
			"У"=>"u","у"=>"u",
			"Ф"=>"f","ф"=>"f",
			"Х"=>"h","х"=>"h",
			"Ц"=>"c","ц"=>"c",
			"Ч"=>"ch","ч"=>"ch",
			"Ш"=>"sh","ш"=>"sh",
			"Щ"=>"sh","щ"=>"sh",
			"Ы"=>"y","ы"=>"y",
			"Э"=>"e","э"=>"e",
			"Ю"=>"u","ю"=>"u",
			"Я"=>"y","я"=>"y"
		);
		$login = iconv("UTF-8","UTF-8//IGNORE",strtr(mb_substr($i, 0, 1, "UTF-8"),$replace)).iconv("UTF-8","UTF-8//IGNORE",strtr(mb_substr($o, 0, 1, "UTF-8"),$replace)).iconv("UTF-8","UTF-8//IGNORE",strtr(mb_substr($f, 0, 1, "UTF-8"),$replace));
		
		return $login;
	}

	//Проверка на существование пользователя такими фио
	function isSameFullName($datatable, $name){
		$rezult = array();
		$rezult = SelDataFromDB($datatable, $name, 'full_name');
		//var_dump ($rezult);
		
		if ($rezult != 0){
			return TRUE;
		}else{
			return FALSE;
		}
	}	
	
	//Проверка на существование логина
	function isSameLogin($login){
		$rezult = array();
		$isSame = TRUE;
		$rez_login = $login;
		$dop = 2;
		while ($isSame){
			$rezult = SelDataFromDB('spr_workers', $rez_login, 'login');
			if ($rezult != 0){
				$rez_login = $login.$dop;
				$dop++;
			}else{
				$isSame = FALSE;
			}
		}
		
		return $rez_login;
	}	
	
	//PassGen
	function PassGen(){
		// Символы, которые будут использоваться в пароле.
		$chars = "1234567890";
		// Количество символов в пароле.
		$max = 4;
		// Определяем количество символов в $chars
		$size = StrLen($chars)-1;
		// Определяем пустую переменную, в которую и будем записывать символы.
		$password = null;
		// Создаём пароль.
		while($max--){
			$password .= $chars[rand(0,$size)];
		}
		
		return $password;
	}
	
	//Поиск в многомерном массиве
	function SearchInArray($array, $data, $search){
		$rez = 0;
		foreach ($array as $key => $value){
			if (array_search ($data, $value)){
				$rez = $value[$search];
			}				
		}
		return $rez;
	}
	
	//
	function WriteSearchUser($datatable, $sw, $type){
		if ($type == 'user_full'){
			$search = 'user';
		}else{
			$search = $type;
		}
		$user = SelDataFromDB($datatable, $sw, $search);
		if ($user != 0){
			if ($type == 'user_full'){
				return $user[0]['full_name'];
			}else{
				return $user[0]['name'];
			}
		}else{
			return 'unknown';
		}
	}
	
	//Сложение двух массивов
	function ArraySum($array1, $array2){
		if (count($array1) > count($array2)){
			$temp_arr1 = $array1;
			$temp_arr2 = $array2;
		}else{
			$temp_arr1 = $array2;
			$temp_arr2 = $array1;
		}
		foreach ($temp_arr2 as $key => $value) {
			if (!isset($temp_arr1[$key])){
				$temp_arr1[$key] = 0;
			}
		}
		foreach ($temp_arr1 as $key => $value) {
			if (isset($temp_arr2[$key])){
				$temp_arr1[$key] = $temp_arr1[$key] + $temp_arr2[$key];
			}
		}
		return $temp_arr1;
	}
	
	function isFired($id){
		$user = SelDataFromDB('spr_workers', $id, 'user');
		if ($user != 0){
			if ($user[0]['fired'] == 1){
				return TRUE;
			}else{
				return FALSE;
			}
		}else{
			return TRUE;
		}
	}

	//Полных лет / Возраст
	function getyeardiff($bday){
		$today = time();
		$arr1 = getdate($bday);
		$arr2 = getdate($today);
		if((int)date('md', $today) >= (int)date('md', $bday) ) { 
			$t = 1;
		} else {
			$t = 0;
		}
		return ($arr2['year'] - $arr1['year'] - 1) + $t;
	}	
	
	function clear_dir($path) {
		//var_dump($path);
		if (file_exists(''.$path.'/')){
			foreach (glob(''.$path.'/*') as $file){
				//var_dump($file);
				unlink($file);
			}
		}
	}
	
	function getWeekDays ($weekDay, $month, $year){
		
		$rezult = array();

		$date = new DateTime("01.{$month}.{$year}");
		//var_dump($date);
		$flag = false;
		$m = $date -> format("m");
		while (($date -> format("Y") == $year) && ($date -> format("m") == $month)){
			if ($flag === false){
				if ($date -> format("N") === $weekDay){
					$flag = true;
				}else{
					$date -> add(new DateInterval("P1D"));
				}
			}
			if ($flag === true){
				//var_dump($date);
				//var_dump ($date->format("Y.m.d"));
				array_push($rezult, $date -> format("Y.m.d"));
				$date -> add(new DateInterval("P1W"));
			}
			/*if ($m != $date->format("m")){
				$m = $date->format("m");
				echo "\r\n\r\n";
			}*/
		}
		
		return $rezult;
	}

?>