<?php

//get_contacts.php
//

	require_once 'header.php';
	
	if ($enter_ok){
		require_once 'header_tags.php';

		if ($god_mode){
	
			include_once 'DBWork.php';
			include_once 'functions.php';

            //require 'variables.php';
			
			require 'config.php';

            $msql_cnnct = ConnectToDB ();

            $offices_j = array(0 => array('name' => 'Филиал не указан', 'color' => 'red'));

            $query = "SELECT * FROM `spr_office`";

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){
                while ($arr = mysqli_fetch_assoc($res)){
                    $offices_j[$arr['id']]['name'] =  $arr['name'];
                    $offices_j[$arr['id']]['color'] =  $arr['color'];
                }

            }

            //var_dump($offices_j);

            $contacts_j = array();

            $query = "SELECT * FROM `spr_clients`;";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0) {

                while ($arr = mysqli_fetch_assoc($res)) {
                    if (!isset($contacts_j[$arr['filial']])) {
                        $contacts_j[$arr['filial']] = array();
                    }
                    array_push($contacts_j[$arr['filial']], $arr);

                }
            }

            //var_dump($contacts_j);

            echo '<table>';

            if (!empty($contacts_j)) {

                foreach ($contacts_j as $filial_id => $contacts) {

                    //echo '<tr><td>'.$arr['contacts'].'</td></tr>';

                    echo '
                        <tr style="margin-top: 20px; font-size: 150%;">
                            <td>
                                <b>'.$offices_j[$filial_id]['name'].'</b> 
                            </td>
                            <td style="background-color: '.$offices_j[$filial_id]['color'].';">
                                
                            </td>
                            <td style="background-color: '.$offices_j[$filial_id]['color'].';">
                                
                            </td>
                        </tr>';


                    foreach ($contacts as $item) {
                        echo '
                        <tr class="cellsBlockHover">
                            <td style="border: 1px solid #CCC; border-bottom: none; ">
                                <a href="client.php?id='.$item['id'].'" class="ahref">'.$item['full_name'].'</a>
                            </td>
                            <td style="border: 1px solid #CCC; border-bottom: none; border-left: none;">
                                ' . $item['contacts'] . '
                            </td>
                            <td style="border: 1px solid #CCC; border-bottom: none; border-left: none;">
                                ' . $item['comments'] . '
                            </td>
                        </tr>';
                    }
                }
            }

            echo '</table>';
            
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>