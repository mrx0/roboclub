<?php

// массив для хранения ошибок
$errorContainer = array();
// полученные данные
$arrayFields = array();

if (isset($_POST['client']))
    $arrayFields['client'] = $_POST['client'];
if (isset($_POST['worker']))
    $arrayFields['worker'] = $_POST['worker'];
if (isset($_POST['filial']))
    $arrayFields['filial'] = $_POST['filial'];
if (isset($_POST['name']))
    $arrayFields['name'] = $_POST['name'];
if (isset($_POST['age']))
    $arrayFields['age'] = $_POST['age'];
if (isset($_POST['summ']))
    $arrayFields['summ'] = $_POST['summ'];
if (isset($_POST['admSettings'])){
	//var_dump($_POST);
	foreach($_POST['admSettings'] as $key => $value){
		$arrayFields[$key] = $value;
	}
}

// проверка всех полей на пустоту
foreach($arrayFields as $fieldName => $oneField){
	
    if($oneField == '' || !isset($oneField) || ($oneField == '0')){
        $errorContainer[$fieldName] = '<span style="color: red">В этом поле ошибка</span>';
    }
	
	if (isset($_POST['summ'])){
		if (!is_numeric($oneField)) {
			$errorContainer[$fieldName] = '<span style="color: red">В этом поле ошибка</span>';
		}
		if ($oneField <= 0){
			$errorContainer[$fieldName] = '<span style="color: red">В этом поле ошибка</span>';
		}
	}
	
	if (isset($_POST['admSettings'])){
		if (!is_numeric($oneField)) {
			$errorContainer[$fieldName] = '<span style="color: red">В этом поле ошибка</span>';
		}
		if ($oneField <= 0){
			$errorContainer[$fieldName] = '<span style="color: red">В этом поле ошибка</span>';
		}
	}
}
 /*
// сравнение введенных паролей
if($arrayFields['password_user'] != $arrayFields['password_2_user'])
    $errorContainer['password_2_user'] = 'Пароли не совпадают';
 */
// делаем ответ для клиента
if(empty($errorContainer)){
    // если нет ошибок сообщаем об успехе
    echo json_encode(array('result' => 'success'));
}else{
    // если есть ошибки то отправляем
    echo json_encode(array('result' => 'error', 'text_error' => $errorContainer));
}

?>