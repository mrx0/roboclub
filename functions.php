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


    //Собираем все филиалы
    function getAllFilials($sort){
        $filials_j = array();

        $msql_cnnct = ConnectToDB ();

        $query = "SELECT * FROM `spr_office`";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                $filials_j[$arr['id']] = $arr;
            }
        }

        if ($sort){
            if (!empty($filials_j)) {
                $filials_j_names = array();

                //Определяющий массив из названий для сортировки
                foreach ($filials_j as $key => $arr) {
                    /*if ($short_name){
                        array_push($filials_j_names, $arr['name2']);
                    }else {*/
                        array_push($filials_j_names, $arr['name']);
                    //}
                }

                array_multisort($filials_j_names, SORT_LOCALE_STRING, $filials_j);
            }
        }

        return $filials_j;
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
			return 'не найдено';
		}
	}

    //Пишем ФИО человека
    function WriteSearchUser2($datatable, $sw, $type, $link){
        if ($type == 'user_full'){
            $search = 'user';
        }else{
            $search = $type;
        }

        if ($datatable == 'spr_clients'){
            $uri = 'client.php';
        }
        if ($datatable == 'spr_workers'){
            $uri = 'user.php';
        }

        if ($sw != ''){
            $user = SelDataFromDB($datatable, $sw, $search);
            //var_dump ($user);
            //var_dump ($search);

            if ($user != 0){
                if ($type == 'user_full'){
                    if ($link){
                        return '<a href="'.$uri.'?id='.$sw.'" class="ahref">'.$user[0]['full_name'].'</a>';
                    }else{
                        return $user[0]['full_name'];
                    }
                }else{
                    if ($link){
                        return '<a href="'.$uri.'?id='.$sw.'" class="ahref">'.$user[0]['name'].'</a>';
                    }else{
                        return $user[0]['name'];
                    }
                }
            }else{
                return 'не указан';
            }
        }else{
            return 'не указан';
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


    //Пагинатор
    function paginationCreate ($count_on_page, $page_number, $db, $file_name, $msql_cnnct, $dop){
        $paginator_str = '';
        $pages = 0;

        $rezult_str = '';
        $rezult = array();

        //Хочу получить общее количество
        $query = "SELECT COUNT(*) AS total_ids FROM `$db` $dop;";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            $arr = mysqli_fetch_assoc($res);
            $total_ids = $arr['total_ids'];
        }else{
            $total_ids = 0;
        }

        if ($total_ids != 0) {

            $pages = (int)ceil($total_ids/$count_on_page);
            //var_dump($pages);

            if ($pages > 10){
                $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';

                //next
                if ($page_number != 1) {
                    $paginator_str .= '<a href="' . $file_name . '?page=' . ($page_number - 1) . '" class="paginator_btn" style=""><i class="fa fa-caret-left" aria-hidden="true"></i></a> ';
                }

                if (($page_number == 1) || ($page_number == 2) || ($page_number == $pages) || ($page_number == $pages-1)){
                    //1я
                    $paginator_str .= '<a href="'.$file_name.'?page=1" class="paginator_btn" style="';

                    if ($page_number == 1){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">1</a> ';

                    //2я
                    $paginator_str .= '<a href="'.$file_name.'?page=2" class="paginator_btn" style="';

                    if ($page_number == 2){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">2</a> ';

                    //3я
                    $paginator_str .= '<a href="'.$file_name.'?page=3" class="paginator_btn" style="';

                    if ($page_number == 3){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">3</a> ... ';

                    //Препредпоследняя
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($pages-2) . '" class="paginator_btn" style="';

                    if ($page_number == $pages-2){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">' . ($pages-2) . '</a>';
                    $paginator_str .= '</a> ';

                    //Предпоследняя
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($pages-1) . '" class="paginator_btn" style="';

                    if ($page_number == $pages-1){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">' . ($pages-1) . '</a>';
                    $paginator_str .= '</a> ';

                    //Последняя
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($pages) . '" class="paginator_btn" style="';

                    if ($page_number == $pages){
                        $paginator_str .= $pg_btn_bgcolor;
                    }

                    $paginator_str .= '">' . ($pages) . '</a>';
                    $paginator_str .= '</a> ';
                }else {

                    //1я
                    $paginator_str .= '<a href="' . $file_name . '?page=1" class="paginator_btn" style="';
                    $paginator_str .= '">1</a> ';

                    if ($page_number - 1 != 2){
                        $paginator_str .= '... ';
                    }

                    //
                    $paginator_str .= '<a href="' . $file_name . '?page=' . ($page_number - 1) . '" class="paginator_btn" style="';
                    $paginator_str .= '">' . ($page_number - 1) . '</a> ';

                    //
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($page_number) . '" class="paginator_btn" style="';
                    $paginator_str .= $pg_btn_bgcolor;
                    $paginator_str .= '">' . ($page_number) . '</a> ';

                    //
                    $paginator_str .= '<a href="' . $file_name . '?page=' . ($page_number + 1) . '" class="paginator_btn" style="';
                    $paginator_str .= '">' . ($page_number + 1) . '</a> ';

                    if ($page_number+1 != $pages-1){
                        $paginator_str .= '... ';
                    }

                    //Последняя
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($pages) . '" class="paginator_btn" style="';
                    $paginator_str .= '">' . ($pages) . '</a> ';

                }
                //next
                if ($page_number != $pages) {
                    $paginator_str .= '<a href="' . $file_name . '?page=' . ($page_number + 1) . '" class="paginator_btn" style=""><i class="fa fa-caret-right" aria-hidden="true"></i></a> ';
                }

            }else {
                for ($i = 1; $i <= $pages; $i++) {
                    $pg_btn_bgcolor = '';
                    if (isset($_GET)) {
                        if (isset($page_number)) {
                            if ($page_number == $i) {
                                $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';
                            }
                        } else {
                            if ($i == 1) {
                                $pg_btn_bgcolor = 'background: rgb(249, 255, 1); color: red;';
                            }
                        }
                    }
                    $paginator_str .= '<a href="'.$file_name.'?page=' . ($i) . '" class="paginator_btn" style="' . $pg_btn_bgcolor . '">' . ($i) . '</a> ';
                }
            }
        }

        if ($pages > 1) {
            $rezult_str = '<div style="margin: 2px 6px 3px;">
                                    <span style="font-size: 80%; color: rgb(0, 172, 237);">Перейти на страницу: </span>' . $paginator_str . '
                               </div>';
        }

        return $rezult_str;

    }

    //добавляем клиенту новую запись с балансом
    function addClientBalanceNew ($client_id, $balance){

        $msql_cnnct = ConnectToDB ();

        $time = date('Y-m-d H:i:s', time());

        //Вставим новую запись баланса ребёнка
        $query = "INSERT INTO `journal_balance` (
                            `client_id`, `summ`, `create_time`, `create_person`)
                            VALUES (
                                '{$client_id}', '{$balance}', '{$time}', '{$_SESSION['id']}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    }

    //добавляем клиенту новую запись с долгом
    function addClientDebtNew ($client_id, $balance){

        $msql_cnnct = ConnectToDB ();

        $time = date('Y-m-d H:i:s', time());

        //Вставим новую запись баланса ребёнка
        $query = "INSERT INTO `journal_debt` (
                            `client_id`, `summ`, `create_time`, `create_person`)
                            VALUES (
                                '{$client_id}', '{$balance}', '{$time}', '{$_SESSION['id']}')";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    }

    //Обновим баланс контрагента
    function updateBalance ($id, $client_id, $Summ, $debited){

        $msql_cnnct = ConnectToDB ();

        $query = "UPDATE `journal_balance` SET `summ`='$Summ', `debited`='$debited'  WHERE `id`='$id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
    }

    //Обновим долг контрагента
    function updateDebt ($id, $client_id, $Summ){

        $msql_cnnct = ConnectToDB ();

        $query = "UPDATE `journal_debt` SET `summ`='$Summ'  WHERE `id`='$id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
    }

    //Смотрим баланс
    function watchBalance ($client_id, $Summ){

        $msql_cnnct = ConnectToDB ();

        $clientBalance = array();

        //Посмотрим баланс, если он есть. Если нет, то сделаем INSERT
        $query = "SELECT * FROM `journal_balance` WHERE `client_id`='$client_id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($clientBalance, $arr);
            }
        }else{
            addClientBalanceNew ($client_id, $Summ);
        }

        return($clientBalance);
    }

    //считаем по нарядам, сколько потрачено и обновляем
    function calculatePayment ($client_id){

        $rezult = array();

        $msql_cnnct = ConnectToDB ();

        $clientPayments = array();
        $arr = array();

        //Соберем все оплаты
        $query = "SELECT * FROM `journal_payment` WHERE `client_id`='$client_id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($clientPayments, $arr);
            }
        }

        //Переменная для суммы
        $Summ = 0;

        //Если были там какие-то оплаты
        if (!empty($clientPayments)) {
            //Посчитаем сумму
            foreach ($clientPayments as $payments) {
                if ($payments['type'] != 1) {
                    $Summ += $payments['summ'];
                }
            }
        }

        $rezult['summ'] = $Summ;

        //return (json_encode($rezult, true));

        return ($Summ);
    }

    //Смотрим долг
    function watchDebt ($client_id, $Summ){

        $msql_cnnct = ConnectToDB ();

        $clientDebt = array();
        $arr = array();

        //Посмотрим баланс, если он есть. Если нет, то сделаем INSERT
        $query = "SELECT * FROM `journal_debt` WHERE `client_id`='$client_id'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($clientDebt, $arr);
            }
        }else{
            addClientDebtNew ($client_id, $Summ);
        }

        return($clientDebt);
    }


    //считаем по платежам, сколько внесено и обновляем
    function calculateBalance ($client_id){

        $rezult = array();

        $msql_cnnct = ConnectToDB ();

        $clientOrders = array();

        //Соберем все (неудаленные) платежи
        $query = "SELECT * FROM `journal_order` WHERE `client_id`='$client_id' AND `status` <> '9'";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);
        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($clientOrders, $arr);
            }
        }

        //Переменная для суммы
        $Summ = 0;

        //Если были там какие-то платежи
        if (!empty($clientOrders)) {
            //Посчитаем сумму
            foreach ($clientOrders as $orders) {
                $Summ += $orders['summ'];
            }
        }

        //Смотрим есть ли баланс вообще
        $clientBalance = watchBalance ($client_id, $Summ);
        //var_dump($clientBalance);

        //Если че та там есть с балансом
        if (!empty($clientBalance)){
            $rezult['summ'] = $Summ;
            $rezult['debited'] = calculatePayment($client_id);

            //Обновим баланс контрагента
            updateBalance ($clientBalance[0]['id'], $client_id, $Summ, $rezult['debited']);
        }else {
            $rezult['summ'] = $Summ;
            $rezult['debited'] = 0;
        }

        return (json_encode($rezult, true));

        //return ($Summ);
    }

    //считаем по нарядам, сколько выставлено и обновляем
    function calculateDebt ($client_id){

        $rezult = array();

        $msql_cnnct = ConnectToDB ();

        $clientInvoices = array();
        $arr = array();

        //Соберем все (неудаленные) наряды, где общая сумма не равна оплаченной
        $query = "SELECT * FROM `journal_invoice` WHERE `client_id`='$client_id' AND `status` <> '9' AND `summ` <> `paid`";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                array_push($clientInvoices, $arr);
            }
        }
        //return ($clientInvoices);

        //Переменная для суммы
        $Summ = 0;

        //Если были там какие-то наряды
        if (!empty($clientInvoices)) {
            //Посчитаем сумму
            foreach ($clientInvoices as $invoices) {
                $Summ += $invoices['summ'] - $invoices['paid'];
            }
        }

        //Смотрим есть ли долг в базе вообще
        $clientDebt = watchDebt ($client_id, $Summ);
        //var_dump($clientBalance);

        //Если че та там есть с долгом
        if (!empty($clientDebt)){
            $rezult['summ'] = $Summ;

            //Обновим баланс контрагента
            updateDebt ($clientDebt[0]['id'], $client_id, $Summ);
        }else {
            $rezult['summ'] = $Summ;
        }

        return (json_encode($rezult, true));

        //return ($Summ);
    }




    //Добавляем клиенту новую запись с общим или по группе балансом по урокам
    function addClientLessonsBalanceNew ($client_id, $Summ, $debt, $is_group, $group_id){

        $msql_cnnct = ConnectToDB ();

        //$time = date('Y-m-d H:i:s', time());

        //Вставим новую запись баланса
        if ($is_group && ($group_id != 0)){
            $query = "INSERT INTO `journal_lessons_balance_group` (
                                `client_id`, `group_id`, `summ`)
                                VALUES (
                                    '{$client_id}', '{$group_id}', '{$Summ}', '{$debt}')";
        }else{
            $query = "INSERT INTO `journal_lessons_balance` (
                                `client_id`, `summ`, `debt`)
                                VALUES (
                                    '{$client_id}', '{$Summ}', '{$debt}')";
        }

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    }

    //Обновляем клиенту запись с общим или по группе балансом по урокам
    function  updateClientLessonsBalance ($balance_id, $Summ, $debt, $is_group){

        $msql_cnnct = ConnectToDB ();

        //$time = date('Y-m-d H:i:s', time());

        //Вставим новую запись баланса
        if ($is_group){
            $query = "UPDATE `journal_lessons_balance_group` 
                      SET `summ`='$Summ', `debt`= '$debt'
                      WHERE `id`='$balance_id'";
        }else{
            $query = "UPDATE `journal_lessons_balance`
                      SET `summ`='$Summ', `debt`= '$debt'
                      WHERE `id`='$balance_id'";
        }

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

    }

    //Смотрим есть ли общий баланс занятий или конкретной группы
    function watchLessonsBalance ($client_id, $Summ, $debt, $is_group, $group_id){

        $msql_cnnct = ConnectToDB ();

        $clientLBalance = array();

        //Посмотрим баланс, если он есть. Если нет, то сделаем INSERT
        if ($is_group && ($group_id != 0)){
            $query = "SELECT `id` FROM `journal_lessons_balance_group` WHERE `client_id`='$client_id' AND `group_id`='$group_id' LIMIT 1";
        }else {
            $query = "SELECT `id` FROM `journal_lessons_balance` WHERE `client_id`='$client_id' LIMIT 1";
        }

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //array_push($clientLBalance, $arr);
                $balance_id = $arr['id'];
            }

            updateClientLessonsBalance($balance_id, $Summ, $debt, $is_group);
        }else {
            addClientLessonsBalanceNew($client_id, $Summ, $debt, $is_group, $group_id);
        }

        return($clientLBalance);
    }



    //Пересчёт количества занятий в конкретной группе, к которым допущен ребёнок
    function calculateUpdateLessonsBalance($client_id){
        $lessons_summ = 0;
        $invoice_ex_j = array();

        $msql_cnnct = ConnectToDB ();

        //Соберем
        //Общая сумма количеств занятий выписанных ребенку для всех групп
        /*$query = "SELECT jiex.*, ji.client_id AS client_id, ji.group_id AS group_id FROM `journal_invoice_ex` jiex
              LEFT JOIN `journal_invoice` ji ON ji.id = jiex.invoice_id
              LEFT JOIN `spr_tarifs` st ON st.id = jiex.tarif_id AND jiex.tarif_id IN (
              SELECT id FROM `spr_tarifs` WHERE `type` = '3'
              )
              WHERE ji.client_id='$client_id';";*/

        //для отдельной группы
        /*$query = "SELECT jiex.*, ji.client_id AS client_id, jg.name AS group_name  FROM `journal_invoice_ex` jiex
              INNER JOIN `journal_invoice` ji ON ji.id = jiex.invoice_id
              INNER JOIN `journal_groups` jg ON jg.id = ji.group_id
              INNER JOIN `spr_tarifs` st ON st.id = jiex.tarif_id AND jiex.tarif_id IN (
              SELECT id FROM `spr_tarif_types` WHERE `period_type` = '3'
              )
              WHERE ji.client_id='$client_id';";*/

        //Получили все позиции всех нарядов из всех филиалов, из которых пойдет дальше количество занятий оформленных
        $query = "SELECT jiex.*, ji.group_id AS group_id, jg.name AS group_name, so.name AS office_name FROM `journal_invoice_ex` jiex
              INNER JOIN `spr_tarifs` st ON st.id = jiex.tarif_id 
              INNER JOIN `spr_tarif_types` stt ON stt.id = st.type AND stt.period_type = '3'
              INNER JOIN `journal_invoice` ji ON ji.id = jiex.invoice_id AND ji.client_id='$client_id'
              LEFT JOIN `journal_groups` jg ON jg.id = ji.group_id
              LEFT JOIN `spr_office` so ON so.id = jg.filial;";

        //var_dump($query);

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);
        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){
                //array_push($rezult, $arr);

                if (!isset($invoice_ex_j[$arr['group_id']])){
                    $invoice_ex_j[$arr['group_id']] = array();
                }

                array_push($invoice_ex_j[$arr['group_id']], $arr);
            }
        }

        $Summ = 0;
        $group_summ = array();

        //Если что-то есть в нарядах
        if (!empty($invoice_ex_j)){
            //Соберем суммы всех филиалов в отдельный массив и посчитаем общую сумму
            foreach ($invoice_ex_j as $group_id => $group_arr){
                foreach ($group_arr as $group_item) {
                    //var_dump($group_item);

                    $Summ += $group_item['quantity'];

                    if (!isset($group_summ[$group_id])) {
                        $group_summ[$group_id] = $group_item['quantity'];
                    } else {
                        $group_summ[$group_id] += $group_item['quantity'];
                    }
                }
            }
            //var_dump($Summ);


        }

        //Расчет посещенных занятий

        //Присутствовал
        $journal_was = 0;
        //Кол-во отсутствий
        $journal_x = 0;
        //Кол-во справок
        $journal_spr = 0;
        //Кол-во пробных
        $journal_try = 0;

        //Смотрим посещения
        $journal_uch = array();

        $query = "SELECT * FROM `journal_user` 
                  WHERE `client_id` = '".$client_id."' 
                  AND  ((`year` = '2018' AND `month` > '08')  
                  OR  (`year` > '2018'))";

        $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

        $number = mysqli_num_rows($res);

        if ($number != 0){
            while ($arr = mysqli_fetch_assoc($res)){

                array_push($journal_uch, $arr);
            }
        }

        if (!empty($journal_uch)){
            //var_dump($journal_uch);

            foreach ($journal_uch as $journal_item){
                if ($journal_item['status'] == 1){
                    $journal_was++;
                }
                if ($journal_item['status'] == 2){
                    $journal_x++;
                }
                if ($journal_item['status'] == 3){
                    $journal_spr++;
                }
                if ($journal_item['status'] == 4){
                    $journal_try++;
                }
            }
        }

        $debt = $journal_was + $journal_x + $journal_try;

        //Херачим общий баланс
        watchLessonsBalance ($client_id, $Summ, $debt, false, 0);

        //Херачим балансы по каждой из групп
        // !!!!! отключил подсчет по группам отдельно, так как им это пока не надо
        /*foreach ($group_summ as $group_id => $group_item_summ){
            watchLessonsBalance ($client_id, $group_item_summ, true, $group_id);
        }*/

        $rezult['group_summ'] = $group_summ;
        $rezult['summ'] = $Summ;
        $rezult['debt'] = $debt;

        //return ($group_summ);
        return (json_encode($rezult, true));

    }

?>