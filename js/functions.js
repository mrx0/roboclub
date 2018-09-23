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


    //Удаление коментария из карточки ребенка
    function deleteThisComment(comment_id){

        var rys = confirm("Вы хотите удалить комментарий. \nЕго невозможно будет восстановить. \n\nВы уверены?");
        if (rys){

            link = "del_Comment_f.php";

            reqData = {
                comment_id: comment_id
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
                        $("#status").html(res.data);
                        setTimeout(function () {
                            location.reload()
                        }, 1000);
                    }
                    if(res.result == "error"){
                        console.log(res);
                    }
                }
            })

        }
    }

    function Ajax_add_client() {

        hideAllErrors();

        var link = "ajax_test.php";

        var reqData = {
            fname: $("#f").val(),
            iname: $("#i").val(),

            sel_date: $("#sel_date").val(),
            sel_month: $("#sel_month").val(),
            sel_year: $("#sel_year").val(),

            sex: sex_value
        };
        //console.log($("#filial").val());

        if ($("#filial").val() > 0) {
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

                        link = "add_client_f.php";

                        //Дополняем объект
                        reqData.oname = $("#o").val();
                        reqData.contacts = $("#contacts").val();
                        reqData.comment = $("#comment").val();
                        reqData.filial = $("#filial").val();

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
                                //$("#errror").html(res.data);

                                if (res.result == "success") {

                                    $("#status").html(res.data);
                                    /*setTimeout(function () {
                                     window.location.replace('client.php?id='+res.client_id);
                                     //console.log('client.php?id='+id);
                                     }, 1000);*/

                                }
                                if (res.result == "error") {
                                    $("#errror").html(res.data);
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
        }else{
            // выводим текст ошибок
            $('#filial_error').html('Выберите филиал');
            // показываем текст ошибок
            $('#filial_error').show();

            $("#errror").html('<span style="color: red; font-weight: bold;">Ошибка, что-то заполнено не так.</span>');
        }
    }

    function Ajax_del_client(session_id) {
        var id =  $("#id").val();

        ajax({
            url:"client_del_f.php",
            statbox:"errrror",
            method:"POST",
            data:
                {
                    id: id,
                    session_id: session_id,
                },
            success:function(data){
                $("#errrror").html(data);
                setTimeout(function () {
                    window.location.replace('client.php?id='+id);
                    //console.log('client.php?id='+id);
                }, 100);
            }
        })
    };


    //Добавляем/редактируем в базу платёж
    function Ajax_order_add(mode){
        //console.log(mode);

        //var invoice_id = 0;
        var link = "ajax_test.php";

        var paymentStr = '';

        var Summ = $("#summ").val();
        var SummType =  $("#summ_type").val();
        //var SummType = document.querySelector('input[name="summ_type"]:checked').value;
        var filial_id = $("#filial").val();

        if (filial_id == 0){
            $("#errror").html('<span style="color: red; font-weight: bold;">Филиал должен быть обязательно выбран.</span>');
        }else {

            var client_id = $("#client_id").val();
            var invoice_id = $("#invoice_id").val();
            //console.log(invoice_id);
            var date_in = $("#date_in").val();
            //console.log(date_in);

            var comment = $("#comment").val();
            //console.log(comment);

            if (invoice_id != 0) {
                paymentStr = '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">' +
                    '<a href= "payment_add.php?invoice_id=' + invoice_id + '" class="b">Оплатить наряд #' + invoice_id + '</a>' +
                    '</li>';
            }

            //проверка данных на валидность
            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data: {
                    summ: Summ,
                    summ_type: SummType
                },
                cache: false,
                beforeSend: function () {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                success: function (data) {
                    if (data.result == 'success') {

                        link = "order_add_f.php";

                        $.ajax({
                            url: link,
                            global: false,
                            type: "POST",
                            dataType: "JSON",
                            data: {
                                client_id: client_id,
                                filial_id: filial_id,
                                summ: Summ,
                                summtype: SummType,
                                date_in: date_in,
                                comment: comment
                            },
                            cache: false,
                            beforeSend: function () {
                                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                            },
                            // действие, при ответе с сервера
                            success: function (res) {
                                //console.log(res);
                                //$('#errror').html(res);

                                if (res.result == "success") {
                                    //$('#data').hide();
                                    $('#data').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">' +
                                        '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Добавлен платёж</li>' +
                                        '<li class="cellsBlock" style="width: auto;">' +
                                        '<a href="order.php?id=' + res.data + '" class="cellName ahref">' +
                                        '<b>Платёж #' + res.data + '</b><br>' +
                                        '</a>' +
                                        '<div class="cellName">' +
                                        '<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">' +
                                        'Сумма:<br>' +
                                        '<span class="calculateInvoice" style="font-size: 13px">' + Summ + '</span> руб.' +
                                        '</div>' +
                                        '</div>' +
                                        '</li>' +
                                        paymentStr +
                                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">' +
                                        '<a href="finance_account.php?client_id=' + client_id + '" class="b">Управление счётом</a>' +
                                        '</li>' +
                                        '</ul>');

                                        //Блокируем выбор даты
                                        $("#date_in").prop( "disabled", true );

                                } else {
                                    $('#errror').html(res.data);
                                }
                            }
                        });
                        // в случае ошибок в форме
                    } else {
                        // перебираем массив с ошибками
                        for (var errorField in data.text_error) {
                            // выводим текст ошибок
                            $('#' + errorField + '_error').html(data.text_error[errorField]);
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
    }

    //Добавим ребёнка в эту группу
    function addClientInGroup(client_id, group_id) {

        var link = "add_ClientInGroup_f.php";

        var reqData = {
            id: client_id,
            group: group_id
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
            },
            success: function (res) {
                //console.log (res);

                alert(res);
                location.reload(true);

                /*if (res.result == "success") {
                    alert(res);
                    location.reload(true);
                }*/
            }
        })
    }
