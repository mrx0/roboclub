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
                    '<a href= "payment_add.php?invoice_id=' + invoice_id + '" class="b">Оплатить счёт #' + invoice_id + '</a>' +
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
                                        '<a href="client_balance.php?client_id=' + client_id + '" class="b">Баланс</a>' +
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

    //Подсчёт суммы для счёта
    function calculateInvoice (changeItogPrice){
        //console.log("calculateInvoice");

        var Summ = 0;

        var link = 'add_price_price_id_in_item_invoice_f.php';

        //console.log(link);

        $("#calculateInvoice").html(Summ);

        $(".invoiceItemPrice").each(function() {

            var invoiceItemPriceItog = 0;

            //получаем значение гарантии
            //var guarantee = $(this).next().next().next().next().attr('guarantee');

            //получаем значение подарка
            //var gift = $(this).next().next().next().next().attr('gift');
            //console.log(gift);

            //Цена
            var cost = Number($(this).attr('price'));

            var ind = $(this).attr('ind');
            var key = $(this).attr('key');

            //обновляем цену в сессии как можем
            $.ajax({
                url: link,
                global: false,
                type: "POST",
                dataType: "JSON",
                data:
                    {
                        client_id: $("#client_id").val(),
                        group_id: $("#group_id").val(),

                        ind: ind,
                        key: key,

                        price: cost
                    },
                cache: false,
                beforeSend: function() {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function(res){

                }
            });

            //коэффициент

            //скидка акция
            //var discount = $(this).next().next().next().attr('discount');
            //console.log(discount);

            //взяли количество
            var quantity = Number($(this).parent().find('[type=number]').val());

            //вычисляем стоимость
            //var stoim = quantity * (cost +  cost * spec_koeff / 100);
            var stoim = quantity * cost	;

            //с учетом скидки акции, но если не страховая
            /*if (insure == 0) {
                stoim = stoim - (stoim * discount / 100);*/

                //Убрали округление 2017.08.09
                //stoim = Math.round(stoim / 10) * 10;
                //Изменили округление 2017.08.10
            /*    stoim = Math.round(stoim);
            }*/

            if (!changeItogPrice) {
                stoim = Number($(this).parent().find('.invoiceItemPriceItog').html());
            }

            //суммируем сумму в итоги
            /*if ((guarantee == 0) && (gift == 0)) {
                if (insure != 0){
                    if (insureapprove != 0){
                        SummIns += stoim;
                    }
                }else{*/
                    Summ += stoim;
            /*    }
            }*/

            var invoiceItemPriceItog = stoim;
            var ishod_price = Number($(this).parent().find('.invoiceItemPriceItog').html());

            if (ishod_price == 0) {
                //2018.03.13 попытка разобраться с гарантийной ценой для зарплаты
                //if (guarantee != 1) {
                $(this).parent().find('.invoiceItemPriceItog').html(stoim);
                //}
            }

            if (changeItogPrice) {
                //прописываем стоимость этой позиции
                /*if ((guarantee == 0) && (gift == 0)) {*/

                    $(this).parent().find('.invoiceItemPriceItog').html(stoim);
                /*}*/
            }
            //console.log("calculateInvoice --> changeItogPrice ---->");
            //console.log(invoiceItemPriceItog);

            if (changeItogPrice) {
                //console.log(changeItogPrice);

                var link2 = "add_manual_itog_price_id_in_item_invoice_f.php";

/*                if (invoice_type == 88){
                    link2 = 'add_manual_itog_price_id_in_item_invoice_free_f.php';
                }*/

                $.ajax({
                    url: link2,
                    global: false,
                    type: "POST",
                    dataType: "JSON",
                    data: {
                        ind: ind,
                        key: key,
                        price: invoiceItemPriceItog,
                        manual_itog_price: stoim,

                        client_id: $("#client_id").val(),
                        group_id: $("#zapis_id").val(),

                    },
                    cache: false,
                    beforeSend: function () {
                        //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                    },
                    // действие, при ответе с сервера
                    success: function (res) {
                        //console.log(res);
                    }
                });

            }
        });

        //Summ = Math.round(Summ - (Summ * discount / 100));
        //Убрали округление 2017.08.09
        //Summ = Math.round(Summ/10) * 10;
        //Изменили округление 2017.08.10
        Summ = Math.round(Summ);

        //SummIns = Math.round(SummIns - (SummIns * discount / 100));
        //страховые не округляем
        //SummIns = Math.round(SummIns/10) * 10;

        $("#calculateInvoice").html(Summ);

    }

    //Подсчёт суммы для расчёта
    /*function calculateCalculate (){

        var Summ = 0;

        $(".invoiceItemPriceItog").each(function() {

            Summ += Number($(this).html());
            //console.log(Summ);
        });

        $("#calculateSumm").html(Summ);

    };*/

    //Подсчёт суммы для счёта с учетом сертификата
    /*function calculatePaymentCert (){

        var SummCert = 0;
        var rezSumm = 0;

        var leftToPay = Number($("#leftToPay").html());

        $(".cert_pay").each(function() {
            SummCert += Number($(this).html());
        });

        if (SummCert > leftToPay){
            rezSumm = leftToPay;
        }else{
            rezSumm = SummCert;
        }

        $("#summ").html(rezSumm);

    }*/

    //Смена исполнителя для расчета
    /*function changeWorkerInCalculate (){

        var link = "search_user_f.php";

        var reqData = {
            workerFIO: $("#search_client2").val(),
        };

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data: reqData,
            cache: false,
            beforeSend: function () {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function (res) {
                //console.log(res);

                $("#worker").val(res.data.id);
            }
        });
    }*/

    //Окрасить кнопки с зубами
    /*function colorizeTButton (t_number_active){
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
    }*/

    //Функция заполняет результат счета из сессии
    function fillInvoiseRez(changeItogPrice){

        //var invoice_type =  $("#invoice_type").val();
        //console.log(invoice_type);

        var link = "fill_invoice_from_session_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    client_id: $("#client_id").val(),
                    group_id: $("#group_id").val()
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log("fillInvoiseRez---------->");
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res.data2);
                    $('#invoice_rezult').html(res.data);

                    // !!!
                    calculateInvoice(changeItogPrice);

                }else{
                    //console.log('error');
                    $('#errror').html(res.data);
                }

                // !!! скролл надо замутить сюда $('#invoice_rezult').scrollTop();
            }
        });
    }

    //Функция заполняет результат расчета из сессии
    /*function fillCalculateRez(){

        var invoice_type = $("#invoice_type").val();
        //console.log(invoice_type);

        var link = "fill_calculate_stom_from_session_f.php";
        if (invoice_type == 6){
            link = "fill_calculate_cosm_from_session_f.php";
        }
        if (invoice_type == 88){
            link = "fill_calculate_free_from_session_f.php";
        }
        //console.log(link);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id2").val(),
                    filial: $("#filial2").val(),
                    worker: $("#worker").val(),
                    invoice_type: invoice_type

                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                if(res.result == "success"){
                    //console.log(res.data);
                    $('#calculate_rezult').html(res.data);
                    //$('#calculate_rezult').append(res.data);

                    // !!!
                    calculateCalculate();

                }else{
                    //console.log(res.data);
                    $('#errror').html(res.data);
                }
                // !!! скролл надо замутить сюда $('#invoice_rezult').scrollTop();
            }
        });
        //$('#errror').html('Результат');
        //calculateInvoice();
    }*/

    // что-то как-то я хз, типа добавляем в сессию новый зуб (наряд)
    /*function addInvoiceInSession(t_number){

        colorizeTButton(t_number);

        //Отправляем в сессию
        $.ajax({
            url:"add_invoice_in_session_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    t_number: t_number,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val()
                },
            cache: false,
            beforeSend: function() {
                //$(\'#errrror\').html("<div style=\'width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);\'><img src=\'img/wait.gif\' style=\'float:left;\'><span style=\'float: right;  font-size: 90%;\'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){

                fillInvoiseRez(true);

                if(res.result == "success"){
                    //$(\'#errror\').html(rez.data);


                }else{
                    $('#errror').html(res.data);
                }

            }
        })
    }*/

    //меняет кол-во позиции
    function changeQuantityInvoice(ind, dataObj){
        //console.log(dataObj.val());
        //console.log(this);

        //var invoice_type = $("#invoice_type").val();

        var link = "add_quantity_price_id_in_invoice_f.php";

/*        if (invoice_type == 88){
            link = "add_quantity_price_id_in_invoice_free_f.php";
        }*/
        //console.log(invoice_type);

        //количество
        var quantity = dataObj.value;
        //console.log(quantity);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    ind: ind,

                    client_id: $("#client_id").val(),
                    group_id: $("#group_id").val(),

                    quantity: quantity
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                fillInvoiseRez(true);

            }
        });
    }

    //Для измения цены +1
    /*function invPriceUpDownOne(ind, itemId, price, start_price, up_down){
        //console.log(dataObj.value);
        //console.log(this);

        var invoice_type = $("#invoice_type").val();

        var link = 'add_price_up_down_one_price_id_in_invoice_f.php';

        if (invoice_type == 88){
            link = 'add_price_up_down_one_price_id_in_invoice_free_f.php';
        }

        if (up_down == 'up'){
            price = Number(price) + 1;
        }
        if (up_down == 'down'){
            price = Number(price) - 1;
        }

        if (isNaN(price)) price = start_price;
        if (price <= start_price) price = start_price;

        //console.log(price);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    key: itemId,
                    ind: ind,

                    price: price,
                    start_price: start_price,

                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type: invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){
                //console.log(data);

                fillInvoiseRez(true);

            }
        });
    }*/

    //Удалить текущую позицию
    function deleteInvoiceItem(ind, dataObj){
        //console.log(dataObj.getAttribute("invoiceitemid"));

        var link = "delete_invoice_item_from_session_f.php";

        //номер позиции
        //var itemId = dataObj.getAttribute("invoiceitemid");
        var target = 'ind';

        //if ((itemId == 0) || (itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
/*        if ((itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
            target = 'ind';
        }*/
        //console.log(zub);
        //console.log(target);

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    /*key: itemId,*/
                    ind: ind,

                    client_id: $("#client_id").val(),
                    group_id: $("#group_id").val(),

                    target: target
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){

                fillInvoiseRez(true);

                //$('#errror').html(data);
                if(res.result == "success"){
                    //console.log(111);

                    //colorizeTButton (res.t_number_active);

                }
            }
        });
    }

    //Удалить текущую позицию в расчете
    /*function deleteCalculateItem(ind, dataObj){
        //console.log($(dataObj).parent().remove());
        //$(dataObj).parent().remove();

        //номер позиции
        var itemId = dataObj.getAttribute("invoiceitemid");
        var target = 'item';

        //if ((itemId == 0) || (itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
        if ((itemId == null) || (itemId == 'null') || (typeof itemId == "undefined")){
            target = 'ind';
        }

        $.ajax({
            url:"fl_delete_calculate_item_from_session_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    key: itemId,
                    ind: ind,

                    client: $("#client").val(),
                    zapis_id: $("#zapis_id2").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    target: target
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                fillCalculateRez();

                //$('#errror').html(data);
                if(data.result == "success"){

                }else{
                    //console.log('error');
                    $('#errror').html(data.data);
                }


            }
        });
    }*/

    //Изменить коэффициент специалиста у всех
    /*function spec_koeffInvoice(spec_koeff){
        //console.log(spec_koeff);

        var invoice_type = $("#invoice_type").val();

        var link = "add_spec_koeff_price_id_in_invoice_f.php";
        if (invoice_type == 88){
            link = "add_spec_koeff_price_id_in_invoice_free_f.php";
        }

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            //dataType: "JSON",
            data:
                {
                    spec_koeff: spec_koeff,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type: invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){
                //$('#errror').html(data);

                fillInvoiseRez(true);

            }
        });

    }*/

    //Изменить гарантию у всех
    /*function guaranteeInvoice(guarantee){

        var invoice_type =  $("#invoice_type").val();

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"add_guarantee_in_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    guarantee: guarantee,
                    client:  $("#client").val(),
                    zapis_id:  $("#zapis_id").val(),
                    filial:  $("#filial").val(),
                    worker:  $("#worker").val(),

                    invoice_type: invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                fillInvoiseRez(true);

            }
        });

    }*/

    //Изменить гарантию или подарок у всех
    /*function giftOrGiftInvoice(guaranteeOrGift){
        //console.log(guaranteeOrGift);

        var invoice_type = $("#invoice_type").val();

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"add_guarantee_gift_in_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    guaranteeOrGift: guaranteeOrGift,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type: invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить согласование у всех
    /*function insureApproveInvoice(approve){

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"add_insure_approve_in_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    approve: approve,
                    client:  $("#client").val(),
                    zapis_id:  $("#zapis_id").val(),
                    filial:  $("#filial").val(),
                    worker:  $("#worker").val()
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить скидку у всех
    /*function discountInvoice(discount){
        //console.log(discount);

        var invoice_type = $("#invoice_type").val();

        var link = "add_discount_price_id_in_invoice_f.php";
        if (invoice_type == 88){
            link = "add_discount_price_id_in_invoice_free_f.php";
        }

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    discount: discount,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type: invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                // $("#discountValue").html(Number(discount));

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить страховую у всех
    /*function insureInvoice(insure){

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"add_insure_price_id_in_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    insure: insure,
                    client:  $("#client").val(),
                    zapis_id:  $("#zapis_id").val(),
                    filial:  $("#filial").val(),
                    worker:  $("#worker").val(),
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить страховую у этого зуба
    /*function insureItemInvoice(zub, key, insure){

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"add_insure_price_id_in_item_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    zub: zub,
                    key: key,
                    insure: insure,
                    client:  $("#client").val(),
                    zapis_id:  $("#zapis_id").val(),
                    filial:  $("#filial").val(),
                    worker:  $("#worker").val(),
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить согласование у этого зуба
    /*function insureApproveItemInvoice(zub, key, approve){

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"add_insure_approve_price_id_in_item_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    zub: zub,
                    key: key,
                    approve: approve,
                    client:  $("#client").val(),
                    zapis_id:  $("#zapis_id").val(),
                    filial:  $("#filial").val(),
                    worker:  $("#worker").val(),
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить гарантию у этого зуба
    /*function guaranteeItemInvoice(zub, key, guarantee){

        var invoice_type =  $("#invoice_type").val();

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"add_guarantee_price_id_in_item_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    zub: zub,
                    key: key,
                    guarantee: guarantee,
                    client:  $("#client").val(),
                    zapis_id:  $("#zapis_id").val(),
                    filial:  $("#filial").val(),
                    worker:  $("#worker").val(),

                    invoice_type: invoice_type,
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить гарантию и подарок у этого зуба
    /*function guaranteeGiftItemInvoice(zub, key, guaranteeOrGift){

        var invoice_type = $("#invoice_type").val();

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"add_guarantee_gift_price_id_in_item_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    zub: zub,
                    key: key,
                    guaranteeOrGift: guaranteeOrGift,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type: invoice_type,
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить категорию процентов
    /*function fl_changeItemPercentCat(ind, key, percent_cat){

        var invoice_type = $("#invoice_type").val();

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        //$('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        //$('.context-menu').remove();

        $.ajax({
            url:"fl_add_percent_cat_in_item_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    ind: ind,
                    key: key,
                    percent_cat: percent_cat,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id2").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type: invoice_type,
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){
                //console.log(data);

                fillCalculateRez();

            }
        });
    }*/


    //Изменить Коэффициент у этого зуба
    /*function spec_koeffItemInvoice(ind, key, spec_koeff){

        var invoice_type = $("#invoice_type").val();

        var link = "add_spec_koeff_price_id_in_item_invoice_f.php";

        if (invoice_type == 88){
            link = "add_spec_koeff_price_id_in_item_invoice_free_f.php";
        }
        //console.log(link);

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    ind: ind,
                    key: key,
                    spec_koeff: spec_koeff,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type: invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить скидка акция у этого зуба
    /*function discountItemInvoice(ind, key, discount){

        var invoice_type = $("#invoice_type").val();

        var link = 'add_discount_price_id_in_item_invoice_f.php';

        if (invoice_type == 88){
            link = "add_discount_price_id_in_item_invoice_free_f.php";
        }

        //console.log(discount);
        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    ind: ind,
                    key: key,
                    discount: discount,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type: invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить цену у этого зуба
    /*function priceItemInvoice(ind, key, price, start_price){

        var invoice_type =  $("#invoice_type").val();

        if (isNaN(price)) price = start_price;
        if (price <= start_price) price = start_price;

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url:"add_manual_price_id_in_item_invoice_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    ind: ind,
                    key: key,
                    price: price,

                    start_price: start_price,

                    client:  $("#client").val(),
                    zapis_id:  $("#zapis_id").val(),
                    filial:  $("#filial").val(),
                    worker:  $("#worker").val(),

                    invoice_type: invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res);

                fillInvoiseRez(true);

            }
        });
    }*/

    //Изменить итоговую цену у этой позиции
    /*function priceItemItogInvoice(ind, key, price, manual_itog_price){

        var invoice_type = $("#invoice_type").val();

        var link = "add_manual_itog_price_id_in_item_invoice_f.php";

        if (invoice_type == 88){
            link = "add_manual_itog_price_id_in_item_invoice_free_f.php";
        }

        /!*console.log(ind);
         console.log(key);*!/

        var min_price = manual_itog_price - 10;
        var max_price = manual_itog_price + 2;

        if (min_price < 0) min_price = 0;

        if (isNaN(price)) price = max_price;
        if (price < min_price) price = min_price;
        if (price > max_price) price = max_price;

        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    ind: ind,
                    key: key,
                    price: price,
                    manual_itog_price: manual_itog_price,

                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val(),

                    invoice_type: invoice_type
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){

                fillInvoiseRez(false);

            }
        });


    }*/

    //Выбор позиции из таблички в наряде
    /*function toothInInvoice(t_number){

        var invoice_type = $("#invoice_type").val();

        var link = "add_invoice_in_session_f.php";
        if (invoice_type == 88){
            link = "add_invoice_free_in_session_f.php";
        }

        //console.log (t_number);
        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    t_number: t_number,
                    client: $("#client").val(),
                    zapis_id: $("#zapis_id").val(),
                    filial: $("#filial").val(),
                    worker: $("#worker").val()
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(data){

                fillInvoiseRez(true);

                if(data.result == "success"){
                    //$('#errror').html(data.data);

                }else{
                    $('#errror').html(data.data);
                }
            }
        })

        colorizeTButton(t_number);
    }*/

    //Добавить позицию из прайса в счет
    function checkPriceItem(price_id){
        //console.log(100);

        var link = "add_price_id_in_invoice_f.php";

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    price_id: price_id,
                    client_id: $("#client_id").val(),
                    group_id: $("#group_id").val()
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res.data);

                fillInvoiseRez(true);

            }
        });

    };

    //Полностью чистим счёт
    /*function clearInvoice(){

        var rys = false;

        rys = confirm("Очистить?");

        if (rys){
            $.ajax({
                url:"invoice_clear_f.php",
                global: false,
                type: "POST",
                dataType: "JSON",
                data:
                    {
                        client: $("#client").val(),
                        zapis_id: $("#zapis_id").val()
                    },
                cache: false,
                beforeSend: function() {
                    //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
                },
                // действие, при ответе с сервера
                success: function(data){

                    fillInvoiseRez(true);

                    colorizeTButton();
                }
            });

        }
    };*/


    //Добавляем/редактируем в базу наряд из сессии
    function Ajax_invoice_add(mode){

        var link = "invoice_add_f.php";

        var Summ = $("#calculateInvoice").html();

        var client_id = $("#client_id").val();

        $.ajax({
            url: link,
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    client_id: client_id,
                    group_id: $("#group_id").val(),

                    summ: Summ

                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                console.log(res);
                $('.center_block').remove();
                $('#overlay').hide();

                if(res.result == "success"){
                    $('#data').hide();
                    $('#invoices').html('<ul style="margin-left: 6px; margin-bottom: 10px; display: inline-block; vertical-align: middle;">'+
                        '<li style="font-size: 90%; font-weight: bold; color: green; margin-bottom: 5px;">Cчёт добавлен</li>'+
                        '<li class="cellsBlock" style="width: auto;">'+
                        '<a href="invoice.php?id='+res.data+'" class="cellName ahref">'+
                        '<b>Счёт #'+res.data+'</b><br>'+
                        '</a>'+
                        '<div class="cellName">'+
                        '<div style="border: 1px dotted #AAA; margin: 1px 0; padding: 1px 3px;">'+
                        'Сумма:<br>'+
                        '<span class="calculateInvoice" style="font-size: 13px">'+Summ+'</span> руб.'+
                        '</div>'+
                        '</div>'+
                        '</li>'+
                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                        '<a href="payment_add.php?invoice_id='+res.data+'" class="b">Оплатить</a>'+
                        '</li>'+
                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                        '<a href="add_order.php?client_id='+client_id+'&invoice_id='+res.data+'" class="b">Добавить платёж</a>'+
                        '</li>'+
                        '<li style="font-size: 85%; color: #7D7D7D; margin-bottom: 5px;">'+
                        '<a href="client_balance.php?client_id='+client_id+'" class="b">Баланс</a>'+
                        '</li>'+
                        '</ul>');
                }else{
                    $('#errror').html(res.data);
                }
            }
        });
    }


    //Показываем блок с суммами и кнопками Для наряда
    function showInvoiceAdd(){
        //console.log(mode);
        $('#overlay').show();

        var Summ = $("#calculateInvoice").html();

        var buttonsStr = '<input type="button" class="b" value="Сохранить" onclick="Ajax_invoice_add()">';

        // Создаем меню:
        var menu = $('<div/>', {
            class: 'center_block' // Присваиваем блоку наш css класс контекстного меню:
        })
            .appendTo('#overlay')
            .append(
                $('<div/>')
                    .css({
                        "height": "100%",
                        "border": "1px solid #AAA",
                        "position": "relative",
                    })
                    .append('<span style="margin: 5px;"><i>Проверьте сумму и нажмите сохранить</i></span>')
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "width": "100%",
                                "margin": "auto",
                                "top": "-10px",
                                "left": "0",
                                "bottom": "0",
                                "right": "0",
                                "height": "50%",
                            })
                            .append('<div style="margin: 10px;">К оплате: <span class="calculateInvoice">'+Summ+'</span> руб.</div>')
                    )
                    .append(
                        $('<div/>')
                            .css({
                                "position": "absolute",
                                "bottom": "2px",
                                "width": "100%",
                            })
                            .append(buttonsStr+
                                '<input type="button" class="b" value="Отмена" onclick="$(\'#overlay\').hide(); $(\'.center_block\').remove()">'
                            )
                    )
            );

        menu.show(); // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню

    }

    //попытка показать контекстное меню
    function contextMenuShow(ind, key, event, mark){
        return;
        //console.log(mark);

/*        // Убираем css класс selected-html-element у абсолютно всех элементов на странице с помощью селектора "*":
        $('*').removeClass('selected-html-element');
        // Удаляем предыдущие вызванное контекстное меню:
        $('.context-menu').remove();

        // Получаем элемент на котором был совершен клик:
        var target = $(event.target);

        //console.log(target.attr('start_price'));

        // Добавляем класс selected-html-element что бы наглядно показать на чем именно мы кликнули (исключительно для тестирования):
        target.addClass('selected-html-element');

        $.ajax({
            url:"context_menu_show_f.php",
            global: false,
            type: "POST",
            dataType: "JSON",
            data:
                {
                    mark: mark,
                    ind: ind,
                    key: key
                },
            cache: false,
            beforeSend: function() {
                //$('#errrror').html("<div style='width: 120px; height: 32px; padding: 10px; text-align: center; vertical-align: middle; border: 1px dotted rgb(255, 179, 0); background-color: rgba(255, 236, 24, 0.5);'><img src='img/wait.gif' style='float:left;'><span style='float: right;  font-size: 90%;'> обработка...</span></div>");
            },
            // действие, при ответе с сервера
            success: function(res){
                //console.log(res.data);

                //для записи
                if (mark == 'zapis_options'){
                    res.data = $('#zapis_options'+ind+'').html();
                }
                //Регуляция цены
                if (mark == 'priceItem'){

                    var start_price = Number(target.attr('start_price'));

                    res.data =
                        '<li style="font-size: 10px;">'+
                        'Введите новую цену (не менее '+start_price+')'+
                        '</li>'+
                        '<li>'+
                        //'<input type="number" name="changePriceItem" id="changePriceItem" class="form-control" size="2" min="'+start_price+'" value="'+Number(target.html())+'" class="mod" onchange="priceItemInvoice('+ind+', '+key+', $(this).val(), '+start_price+');">'+
                        '<input type="number" name="changePriceItem" id="changePriceItem" class="form-control" size="2" min="'+start_price+'" value="'+Number(target.html())+'" class="mod">'+
                        //'<input type="text" name="changePriceItem" id="changePriceItem" class="form-control" value="'+Number(target.html())+'" onkeyup="changePriceItem(this.val(), '+start_price+');">'+
                        '<div style="display: inline;" onclick="priceItemInvoice('+ind+', '+key+', $(\'#changePriceItem\').val(), '+start_price+')">Ok</div>'+
                        '</li>';

                }
                //Регуляция конечной цены
                if (mark == 'priceItemItog'){

                    var itog_price = Number(target.html());
                    var manual_itog_price = Number(target.attr("manual_itog_price"));

                    manual_itog_price = itog_price;

                    var min_itog_price = manual_itog_price - 10;
                    var max_itog_price = manual_itog_price + 2;

                    if (min_itog_price < 1) min_itog_price = 1;


                    res.data =
                        '<li style="font-size: 10px;">'+
                        'Введите цену (от '+min_itog_price+' до '+max_itog_price+')'+
                        '</li>'+
                        '<li>'+
                        '<input type="number" name="changePriceItogItem" id="changePriceItogItem" class="form-control" size="3" min="'+min_itog_price+'"  max="'+max_itog_price+'" value="'+itog_price+'" class="mod">'+
                        '<div style="display: inline;" onclick="priceItemItogInvoice('+ind+', '+key+', $(\'#changePriceItogItem\').val(), '+manual_itog_price+')">Ok</div>'+
                        '</li>';

                }
                //для молочных
                if (mark == 'teeth_moloch'){
                    res.data = $('#teeth_moloch_options').html();
                }

                // Создаем меню:
                var menu = $('<div/>', {
                    class: 'context-menu' // Присваиваем блоку наш css класс контекстного меню:
                })
                    .css({
                        left: event.pageX+'px', // Задаем позицию меню на X
                        top: event.pageY+'px' // Задаем позицию меню по Y
                    })
                    .appendTo('body') // Присоединяем наше меню к body документа:
                    .append( // Добавляем пункты меню:
                        $('<ul/>').append(res.data)
                    );



                if ((mark == 'insure') || (mark == 'insureItem')){
                    menu.css({
                        'height': '300px',
                        'overflow-y': 'scroll',
                    });
                }
                // Показываем меню с небольшим стандартным эффектом jQuery. Как раз очень хорошо подходит для меню
                menu.show();

            }
        });*/
    }