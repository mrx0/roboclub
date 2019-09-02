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


            $query = "SELECT `contacts` FROM `spr_clients`;";
            //var_dump($query);

            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

            $number = mysqli_num_rows($res);
            if ($number != 0){

                echo '<table>';

                while ($arr = mysqli_fetch_assoc($res)){
                    //echo '<tr><td>'.$arr['contacts'].'</td></tr>';
                    echo '
                        <tr>
                            <td>
                                '. preg_replace('/[^0-9-() \n]/', '', $arr['contacts']).'
                            </td>
                            <td>
                                '. $arr['contacts'].'
                            </td>
                        </tr>';
                }

                echo '</table>';
            }
		}else{
			echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
		}
	}else{
		header("location: enter.php");
	}
		
	require_once 'footer.php';

?>