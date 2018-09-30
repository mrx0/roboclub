<?php

//finances2.php
//Новый общий финансовый отчет по филиалам


require_once 'header.php';

if ($enter_ok){
    require_once 'header_tags.php';
    //var_dump($_SESSION);

    if (($finance['see_all'] == 1) || $god_mode){
        include_once 'DBWork.php';
        include_once 'functions.php';
        include_once 'filter.php';
        include_once 'filter_f.php';
        include_once 'widget_calendar.php';

        include_once 'widget_calendar.php';


        $filials_j = getAllFilials(true, true);
        //var_dump($filials_j);

        $msql_cnnct = ConnectToDB ();


        echo '
                <header style="margin-bottom: 5px;">
                    <div class="nav">
                        <a href="invoice_report1.php" class="b">Незакрытые счета</a>
                        <a href="amort_report1.php" class="b">Амортизационные взносы</a>
                    </div>
                    <h1>Финансы</h1>
                    <a href="finances.php" class="b" style="background-color: #CCC;">Финансы (старое) <i class="fa fa-rub"></i></a>';
        echo '    
                </header>';
        echo '
                <div id="data">';


        $dop = '';

        if (isset($_GET['m']) && isset($_GET['y'])){
            $year = $_GET['y'];
            $month = $_GET['m'];
        }else{
            $year = date("Y");
            $month = date("m");
        }

        echo widget_calendar ($month, $year, 'finances2.php', $dop);

        //
        echo '
                    <div style="margin-top: 20px;">
                        <div style="display: inline-block; vertical-align: top;">
                            <b>Всего со всех филиалов:</b>
                        </div>
                        <div style="display: inline-block">
                            <div style="margin-bottom: 2px;"> 
                                <span class="calculateOrder allSumm">0</span> руб.
                            </div>
                            <div onclick="updateAllSumm ();">
                                <span  class="yellowLink">обновить</span>
                            </div>
                        </div>
                    </div>
                    <div id="tabs_w" style="margin-top: 20px; font-family: Verdana, Calibri, Arial, sans-serif; font-size: 100% !important;">';
        echo '
                        <ul>';
        foreach ($filials_j as $filials_item){
            echo '
                            <li>
                                <span style="position: absolute; height: 17px; padding: 4px 0; border: 1px solid #BFBCB5; vertical-align: middle; width: 4px; min-width: 4px; background-color: '.$filials_item['color'].';"></span>
                                <a href="#tabs-'.$filials_item['id'].'">'.$filials_item['name'].'</a>
                            </li>';
        }
        echo '
                        </ul>';

        foreach ($filials_j as $filials_item){
            echo '
                        <div id="tabs-'.$filials_item['id'].'" style="width: auto; float: none; margin-left: 180px; font-size: 12px; padding: 5px 30px; ">
                            <div style="margin-bottom: 20px;">
                                <div style="margin-bottom: 5px;">
                                    <b>Филиал</b>: <i>'.$filials_item['name'].'</i>
                                </div>
                                <div>
                                    <b>Всего</b>: <span class="calculateOrder summOrders">0</span> руб.    
                                </div>
                            </div>
                            <div class="ordersData" id="'.$filials_item['id'].'_'.$month.'_'.$year.'" style="border: 1px solid rgba(228, 228, 228, 0.72); padding: 5px 30px; ">
                            </div>

                            <div style="position: absolute; cursor: pointer; top: 1px; right: 5px; font-size: 110%; color: #0C0C0C;" onclick="refreshOnlyThisTab($(this), '.$filials_item['id'].', '.$month.', '.$year.');" title="Обновить эту вкладку">
                                <span style="font-size: 80%;">Обновить эту вкладку</span> <i class="fa fa-refresh" aria-hidden="true"></i>
                            </div>
                        </div>';
        }

        echo '
                    </div>
                </div>';



            echo '

				<script type="text/javascript">
				
				
				    $( "#tabs_w" ).tabs().addClass("ui-tabs-vertical ui-helper-clearfix");
				
                    $(document).ready(function() {
    
                        //changeTextColor(\'.colorizeText\');
                        
                        var ids = "0_0_0";
                        var ids_arr = {};
                        var filial = 0;
                        var month = 0;
                        var year = 0;
    
                        $(".ordersData").each(function() {
                            //console.log($(this).attr("id"));
                            
                            var thisObj = $(this);
    
                            ids = $(this).attr("id");
                            ids_arr = ids.split("_");
                            //console.log(ids_arr);
                             
                            filial = ids_arr[0];
                            month = ids_arr[1];
                            year = ids_arr[2];
                            
                            var certData = {
                                filial: filial,
                                month: month,
                                year: year
                            };
                            
                            
                            getOrdersDatafunc (thisObj, certData);
                        });
                    });

                    function iWantThisDate(){
                        var iWantThisMonth = $("#iWantThisMonth").val();
                        var iWantThisYear =  $("#iiWantThisYear").val();
                        
                        window.location.replace("finances2.php?m="+iWantThisMonth+"&y="+iWantThisYear);
                    }
                
				</script>';

    }else{
        echo '<h1>Не хватает прав доступа.</h1><a href="index.php">На главную</a>';
    }
}else{
    header("location: enter.php");
}

require_once 'footer.php';

?>