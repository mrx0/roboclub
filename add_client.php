<?php

//add_client.php
//Добавить клиента

require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';
    if (($clients['add_new'] == 1) || $god_mode){
        include_once 'DBWork.php';

        //$permissions = SelDataFromDB('spr_permissions', '', '');

        echo '
				<div id="status">
					<header>
						<h2>Добавить ребёнка</h2>
						Заполните поля
					</header>';

        echo '
					<div id="data">';
        echo '
						<div id="errrror"></div>';
        echo '
							<div class="cellsBlock2">
								<div class="cellLeft">Фамилия</div>
								<div class="cellRight">
									<input type="text" name="f" id="f" value="">
									<label id="fname_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Имя</div>
								<div class="cellRight">
									<input type="text" name="i" id="i" value="">
									<label id="iname_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Отчество</div>
								<div class="cellRight">
									<input type="text" name="o" id="o" value="">
									<label id="oname_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Дата рождения</div>
								<div class="cellRight">';
        echo '
									<select name="sel_date" id="sel_date">
										<option value="00">00</option>';
        $i = 1;
        while ($i <= 31) {
            echo '
										<option value="'.$i.'">'.$i.'</option>';
            $i++;
        }
        echo '
									</select>';
        // Месяц
        echo '
									<select name="sel_month" id="sel_month">
										<option value="00">---</option>';
        $month = array(
            "Январь",
            "Февраль",
            "Март",
            "Апрель",
            "Май",
            "Июнь",
            "Июль",
            "Август",
            "Сентябрь",
            "Октябрь",
            "Ноябрь",
            "Декабрь"
        );
        foreach ($month as $m => $n) {
            echo '
										<option value="'.($m + 1).'">'.$n.'</option>';
        }
        echo '
									</select>';
        // Год
        echo '
									<select name="sel_year" id="sel_year">
										<option value="0000">0000</option>';
        $j = 2000;
        while ($j <= 2020) {
            echo '
										<option value="'.$j.'">'.$j.'</option>';
            $j++;
        }
        echo '	
									</select>';

        echo '
									<label id="sel_date_error" class="error"></label>
									<label id="sel_month_error" class="error"></label>
									<label id="sel_year_error" class="error"></label>
								</div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Пол</div>
								<div class="cellRight">
									<input id="sex" name="sex" value="1" type="radio"> М
									<input id="sex" name="sex" value="2" type="radio"> Ж
									<label id="sex_error" class="error"></label>
								</div>
							</div>

							<div class="cellsBlock2">
								<div class="cellLeft">Контакты</div>
								<div class="cellRight"><textarea name="contacts" id="contacts" cols="35" rows="5"></textarea></div>
							</div>
							
							<div class="cellsBlock2">
								<div class="cellLeft">Комментарий</div>
								<div class="cellRight"><textarea name="comment" id="comment" cols="35" rows="5"></textarea></div>
							</div>';

        $filials = SelDataFromDB('spr_office', '', '');
        echo '
							<div class="cellsBlock2">
								<div class="cellLeft">Филиал</div>
								<div class="cellRight">
									<select name="filial" id="filial">
										<option value="0" selected>Выберите филиал</option>';
        if ($filials != 0){
            for ($i=0;$i<count($filials);$i++){
                echo "<option value='".$filials[$i]['id']."'>".$filials[$i]['name']."</option>";
            }
        }
        echo '
									</select>
									<label id="filial_error" class="error"></label>
								</div>
							</div>';
        echo '				
							<div id="errror"></div>
							<input type="button" class="b" value="Добавить" onclick="Ajax_add_client();">
						</form>';

        echo '
					</div>
				</div>
				
				<script type="text/javascript">
					sex_value = 0;
					$("input[name=sex]").change(function() {
						sex_value = $("input[name=sex]:checked").val();
					});
				</script>';


    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>