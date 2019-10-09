<?php
	//проверяем поддерживает ли браузер webp
	function isBrowserWebpSupport(){
		if(strpos($_SERVER['HTTP_ACCEPT'], 'image/webp') === FALSE)
			return FALSE;
		else
			return TRUE;
	}

	//меняем изображения на webp
	function imagesToWebp(&$html_c){
		$html_c = str_replace('.jpg', '.webp', $html_c);
		$html_c = str_replace('.jpeg', '.webp', $html_c);
		$html_c = str_replace('.png', '.webp', $html_c);
	}
?>
