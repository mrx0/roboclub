<?php
	
//enter.php
//Форма входа на сайт
	

		echo '
			<h1>Вы вошли как гость. <br />Войдите в систему, используя логин и пароль.</h1>
			<form action="testreg.php" method="post">
				<div class="cellsBlock2">
					<div class="cellLeft">Логин:</div>
					<div class="cellRight"><input name="login" type="text" size="15" maxlength="15"></div>
				</div>
				<div class="cellsBlock2">
					<div class="cellLeft">Пароль:</div>
					<div class="cellRight"><input name="password" type="password" size="15" maxlength="15"></div>
				</div>
				<input type="submit" name="submit" value="Войти">
			</form>';

	
?>