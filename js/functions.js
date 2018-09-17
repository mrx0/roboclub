	//Размер объекта
	Object.size = function(obj) {
		var size = 0, key;
		for (key in obj) {
			if (obj.hasOwnProperty(key)) size++;
		}
		return size;
	};

	function hideAllErrors (){
        // убираем класс ошибок с инпутов
        $('input').each(function(){
            $(this).removeClass('error_input');
        });

        // прячем текст ошибок
        $('.error').hide();
        $('#errror').html('');
	}

    //Блок с прогрессом ожидания
    function blockWhileWaiting (show){
    	if (show){
            $('#overlay').show();

            $('#overlay').append( "<div id='waiting' style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.7);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>" );
            //$('#waiting').html("");
		}else {
            $('#overlay').html('');
            $('#overlay').hide();
        }
	}

    //для сбора чекбоксов в массив
    function itemExistsChecker(cboxArray, cboxValue) {

        var len = cboxArray.length;
        if (len > 0) {
            for (var i = 0; i < len; i++) {
                if (cboxArray[i] == cboxValue) {
                    return true;
                }
            }
        }

        cboxArray.push(cboxValue);

        return (cboxArray);
    }

    function checkedItems2 (){

        var cboxArray = [];

        $('input[type="checkbox"]').each(function() {
            var cboxValue = $(this).val();
            //console.log($(this).attr("id"));

            if ($(this).attr("id") != 'fired') {
                if ($(this).prop("checked")) {
                    cboxArray = itemExistsChecker2(cboxArray, cboxValue);
                }
            }
        });

        return cboxArray;
    }

	// Return an array of the selected opion values
	// select is an HTML select element
	function getSelectValues(select) {
		var result = [];
		var options = select && select.options;
		var opt;

		for (var i=0, iLen=options.length; i<iLen; i++) {
			opt = options[i];

			//if (opt.selected) {
				result.push(opt.val() || opt.text);
			//}
		}
		return result;
	}


    //Окрасить кнопки с зубами
    function colorizeTButton (t_number_active){
        $(".sel_tooth").each(function() {
            this.style.background = '';
        });
        $(".sel_toothp").css({'background': ""});

        if (t_number_active == 99){
            $(".sel_toothp").css({'background': "#83DB53"});
        }else{
            $(".sel_tooth").each(function() {
                if (Number(this.innerHTML) == t_number_active){
                    this.style.background = '#83DB53';
                }
            });
        }
    }

    //Добавить тип тарифов
    function Ajax_add_tarif_type() {

        hideAllErrors();

        var link = "ajax_test.php";

        var reqData = {
            name: $("#name").val()
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
            },
            success: function (res) {
                //console.log (res);

                if (res.result == "success") {
                    //console.log (res.data);

                    link = "add_tarif_type_f.php";

                    reqData = {
                        name: $("#name").val(),
                        descr: $("#descr").val()
                    };

                    $.ajax({
                        url: link,
                        global: false,
                        type: "POST",
                        dataType: "JSON",
                        data: reqData,
                        cache: false,
                        beforeSend: function () {
                        },
                        success: function (res) {
                            //console.log (res);

                            if(res.result == "success") {
                                $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">' +
                                    '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Новый тип тарифов добавлен</li>' +
                                    '</ul>');
                                setTimeout(function () {
                                    window.location.replace('tarif_types.php');
                                }, 1000);
                            }
                            if(res.result == "error"){
                                console.log(res);
                            }
                        }
                    })
                } else {
                    // перебираем массив с ошибками
                    for (var errorField in res.text_error) {
                        // выводим текст ошибок
                        $('#' + errorField + '_error').html(res.text_error[errorField]);
                        // показываем текст ошибок
                        $('#' + errorField + '_error').show();
                        // обводим инпуты красным цветом
                        // $('#'+errorField).addClass('error_input');
                    }
                    $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');

                }
            }
        })
    }

    //Добавить тариф
    function Ajax_add_tarif() {

        var type = document.querySelector('input[name="type"]:checked').value;
        //console.log(type);

        hideAllErrors();

        var link = "ajax_test.php";

        var reqData = {
            name: $("#name").val(),
            cost: $("#cost").val()
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
            },
            success: function (res) {
                //console.log (res);
                console.log (reqData);

                if (res.result == "success") {
                    //console.log (res.data);

                    link = "add_tarif_f.php";

                    reqData = {
                        name: $("#name").val(),
                        descr: $("#descr").val(),
                        cost: $("#cost").val(),
                        type: type
                    };

                    $.ajax({
                        url: link,
                        global: false,
                        type: "POST",
                        dataType: "JSON",
                        data: reqData,
                        cache: false,
                        beforeSend: function () {
                        },
                        success: function (res) {
                            //console.log (res);

                            if(res.result == "success") {
                                $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">' +
                                    '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Новый тариф добавлен</li>' +
                                    '</ul>');
                                setTimeout(function () {
                                    window.location.replace('tarifs.php');
                                }, 1000);
                            }
                            if(res.result == "error"){
                                console.log(res);
                            }
                        }
                    })
                } else {
                    // перебираем массив с ошибками
                    for (var errorField in res.text_error) {
                        // выводим текст ошибок
                        $('#' + errorField + '_error').html(res.text_error[errorField]);
                        // показываем текст ошибок
                        $('#' + errorField + '_error').show();
                        // обводим инпуты красным цветом
                        // $('#'+errorField).addClass('error_input');
                    }
                    $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');

                }
            }
        })
    }

