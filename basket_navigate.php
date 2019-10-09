<?php
	function basket_navigate(){
		$basket_navigate = 
'<div id="basket_navigate">
	<div id="bn_basket" class="bn">&#10003; Корзина</div>
	<div id="bn_order" class="bn">&#10003; Адрес доставки</div>
	<div id="bn_payment" class="bn">&#10003; Оплата</div>
	<div id="bn_thanks" class="bn">&#10003; Заказ оформлен</div>
</div>';
				
		return $basket_navigate;
	}
?>