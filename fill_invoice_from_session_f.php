<?php 

//fill_invoice_from_session_f.php
//

	session_start();
	
	if (empty($_SESSION['login']) || empty($_SESSION['id'])){
		header("location: enter.php");
	}else{
		//var_dump ($_POST);
		
		$request = '
						<div class="cellsBlock">
							<div class="cellCosmAct" style="font-size: 80%; text-align: center;">
								<i><b>№</b></i>
							</div>
							<div class="cellText2" style="font-size: 100%; text-align: center;">
								<i><b>Наименование</b></i>
							</div>
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
								<i><b>Цена, руб.</b></i>
							</div>
							<!--<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
								<i><b>Коэфф.</b></i>
							</div>-->
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
								<i><b>Кол-во</b></i>
							</div>
							<!--<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px;">
								<i><b>Скидка</b></i>
							</div>-->
							<!--<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 30px; min-width: 30px; max-width: 30px;">
								<i><b>Гар.<br>Под.</b></i>
							</div>-->
							<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;">
								<i><b>Всего, руб.</b></i>
							</div>
							<div class="cellCosmAct" style="font-size: 70%; text-align: center;">
								<i><b>-</b></i>
							</div>
						</div>';
		
		if ($_POST){
			if (!isset($_POST['client_id']) || !isset($_POST['group_id'])){
				echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так1</div>'));
			}else{
				include_once 'DBWork.php';

				$client = $_POST['client_id'];
				$group = $_POST['group_id'];

                $price['price'] = 0;
                $price['start_price'] = 0;
				
				if (!isset($_SESSION['invoice_data'][$client][$group]['data'])){
					echo json_encode(array('result' => 'error', 'data' => '<div class="query_neok">Что-то пошло не так2</div>'));
				}else{
                    //$_SESSION['invoice_data'][$client][$group]['data'] = array_values($_SESSION['invoice_data'][$client][$group]['data']);
					//берем из сесии данные
					$data = $_SESSION['invoice_data'][$client][$group]['data'];
                    //$discount = $_SESSION['invoice_data'][$client][$group]['discount'];

					//ksort($data);

					$t_number_active = $_SESSION['invoice_data'][$client][$group]['t_number_active'];
					//$mkb_data = $_SESSION['invoice_data'][$client][$zapis_id]['mkb'];

                    $msql_cnnct = ConnectToDB ();

					foreach ($data as $ind => $items){

						$request .= '
							<div class="cellsBlock">
								<div class="cellCosmAct toothInInvoice" style="">
									'.($ind+1).'
								</div>';

						//часть прайса
						if (!empty($items)){

                            $request .= '
                                <div class="cellsBlock" style="font-size: 100%; height: 45px; min-height: 45px;"" >

                                    <div class="cellText2" style=""><div style="text-overflow: ellipsis; overflow: hidden; white-space: inherit;">';

                            //Хочу имя позиции в прайсе
                            $rez = array();

                            $query = "SELECT * FROM `spr_tarifs` WHERE `id` = '{$items['id']}'";

                            $res = mysqli_query($msql_cnnct, $query) or die(mysqli_error($msql_cnnct).' -> '.$query);

							$number = mysqli_num_rows($res);
							if ($number != 0){
								while ($arr = mysqli_fetch_assoc($res)){
									array_push($rez, $arr);
								}
							}

							if (!empty($rez)){

							    $request .= $rez[0]['name'];

                                //Узнать цену
                                //переменная для цены
                                $price['price'] = $rez[0]['cost'];
                                $price['start_price'] = $rez[0]['cost'];

                            }else{
                                $request .= '?';
                            }


                            $request .= '
								    </div>
							    </div>
								<div class="cellCosmAct invoiceItemPrice settings_text" ind="'.$ind.'" key="0" price="'.$price['price'].'" start_price="'.$price['start_price'].'" style="font-size: 100%; text-align: center; width: 60px; min-width: 60px; max-width: 60px;  position: relative;">
									<div start_price="'.$price['start_price'].'" onclick="contextMenuShow('.$ind.', 0, event, \'priceItem\');">'.$price['price'].'</div>
                                    <!--<div class="invPriceUpOne" style="top: 0;" onclick="invPriceUpDownOne('.$ind.', 0, '.$price['price'].', '.$price['start_price'].', \'up\');">
                                        <i class="fa fa-caret-up" aria-hidden="true"></i>
                                    </div>
                                    <div class="invPriceDownOne" style="bottom: 0;" onclick="invPriceUpDownOne('.$ind.', 0, '.$price['price'].', '.$price['start_price'].', \'down\');">
                                        <i class="fa fa-caret-down" aria-hidden="true"></i>
                                    </div>-->
								</div>
								<!--<div class="cellCosmAct spec_koeffInvoice settings_text"  speckoeff="'. 0 .'" style="font-size: 90%; text-align: center;  width: 40px; min-width: 40px; max-width: 40px;" onclick="contextMenuShow('.$ind.', '.$ind.', event, \'spec_koeffItem\');">
									'. 0 .'
								</div>-->
								<div class="cellCosmAct" style="font-size: 80%; text-align: center; width: 40px; min-width: 40px; max-width: 40px; ">
									<input type="number" size="2" name="quantity" id="quantity" min="1" max="99" value="'.$items['quantity'].'" class="mod" onchange="changeQuantityInvoice('.$ind.', this);">
								</div>
								<!--<div class="cellCosmAct settings_text"  discount="'.$items['discount'].'" style="font-size: 90%; text-align: center;  width: 40px; min-width: 40px; max-width: 40px;" onclick="contextMenuShow('.$ind.', '.$ind.', event, \'discountItem\');">
									'.$items['discount'].'
								</div>-->
								
								<!--<div class="cellCosmAct settings_text" guarantee="'. 0 .'" gift="'.$items['gift'].'" style="font-size: 80%; text-align: center; width: 30px; min-width: 30px; max-width: 30px; " onclick="contextMenuShow('.$ind.', '.$ind.', event, \'guaranteeGiftItem\');">';
								/*if ($items['guarantee'] != 0){
									$request .= '
										<i class="fa fa-check" aria-hidden="true" style="color: red; font-size: 150%;"></i>';
								}else*/if ($items['gift'] != 0){
                                    $request .= '
                                        <i class="fa fa-gift" aria-hidden="true" style="color: blue; font-size: 150%;"></i>';
								}else{
                                    $request .= '-';
                                }
								$request .= '
								</div>-->
								
								<div class="cellCosmAct invoiceItemPriceItog" manual_itog_price="'.$items['manual_itog_price'].'" style="font-size: 105%; font-weight: bold; text-align: center;  width: 60px; min-width: 60px; max-width: 60px; cursor: pointer;" onclick="contextMenuShow('.$ind.', 0, event, \'priceItemItog\');">';


                                //if (isset($items['manual_itog_price'])){
                                    if (isset($items['itog_price'])){
                                        if ($items['itog_price'] > 0){
                                            $request .= $items['itog_price'];
                                        }else{
                                            $request .= '0';
                                        }
                                    }else{
                                        $request .= '0';
                                    }
                                //}else{
                                    //$request .= '0';
                                //}


                                $request .= '
								</div>
								<div class="cellCosmAct info" style="font-size: 100%; text-align: center; " onclick="deleteInvoiceItem('.$ind.', this);">
									<i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
								</div>
							</div>';
							//}*/
						}else{
							$request .= '
							<div class="cellsBlock" style="font-size: 100%;" >
								<div class="cellText2" style="text-align: center;  border: 1px dotted #DDD;">
									<span style="color: rgba(255, 0, 0, 0.62);">не заполнено</span>
								</div>
								<div class="cellCosmAct info" style="font-size: 100%; text-align: center; " onclick="deleteInvoiceItem('.$ind.', this);">
									<i class="fa fa-times" aria-hidden="true" style="cursor: pointer;"  title="Удалить"></i>
								</div>
							</div>';
						}
							$request .= '
							</div>';
					}

					//CloseDB ($msql_cnnct);
					
					echo json_encode(array('result' => 'success', 'data' => $request));
				}
			}
		}
	}
?>