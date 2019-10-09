<?php
	//01_пришел запрос на ту или иную страницу ИНФОРМАЦИОННЫХ статей
	if(isset($_POST['info_page'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//шаблон по заменам для текста для вывода в список статей поле текст вместе с тегами
		$search = array("<", ">");
		$replace = array("&lt", "&gt");
	
		$query = 'SELECT * FROM `info_pages`';
		$res = mysqli_query($db, $query);
		$unp_num = mysqli_num_rows($res);						//всего статей
		$first_page = (($_POST['info_page'] - 1) * 10 + 1);		//id первой выводимой статьи
		//id последней выводимой статьи
		if($unp_num > ($first_page + 10))
			$last_page = ($first_page + 9);
		else
			$last_page = $unp_num;
		
		$html = '
		<div class="small_title">Информационные страницы</div>
		<table class="info_table">
			<col width="3%">
			<col width="17%">
			<col width="30%">
			<col width="50%">
		
			<tr class="info_table_tr">
				<td class="info_table_title_td"></td>
				<td class="info_table_title_td">Название страницы</td>
				<td class="info_table_title_td">Название транслитом</td>
				<td class="info_table_title_td">Содержание страницы</td>
			</tr>';
			
		//
		$query = 'SELECT * FROM `info_pages` WHERE id_info>=' . $first_page;
		$res = mysqli_query($db, $query);	
			
		for($i = $first_page; $i <= $last_page; $i++){		
			$info = mysqli_fetch_assoc($res);
			
			$html .= '
			<tr>
				<td class="info_table_regular_td"><div class="at_div">' . $info['id_info'] . '</div></td>
				<td class="info_table_regular_td"><div class="at_div"><b>' . $info['title'] . '</b></div></td>
				<td class="info_table_regular_td"><div class="at_div">' . $info['title_translite'] . '</div></td>
				<td class="info_table_regular_td"><div class="at_div">' . str_replace($search, $replace, $info['text']) . '</div></td>
			</tr>';
		}

		$html .= '</table>';	//закрываем таблицу и #info_pages
		
		mysqli_close($db);
		unset($_POST['info_page']);
		
		echo $html;
	}
	
	
	//02_пришел запрос на добавление инфо-страниц
	if(isset($_POST['butt_in_add'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим название инфо-страницы в транслите
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
		$translit_title = translit_encode(mb_strtolower($_POST['info_header']));
		
		$query = 'INSERT INTO `info_pages`(title, title_translit, text)
			VALUES("' . $_POST['info_header'] . '", "' . $translit_title . '", "' . addslashes($_POST['info_text']) . '")';
		echo 'Инфо-страница была сохранена и доступна публично';

		mysqli_query($db, $query);
		
		mysqli_close($db);
		unset($_POST['butt_in_add']);
	}
	
	
	//03_пришел запрос на удаление инфо-страницы
	if(isset($_POST['info_del'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];					//статус пользователя в системе
		$user_name = $_COOKIE['adminpage_user'];		//имя пользователя
	
		//если пользователь Администратор
		if($user_status == 'Администратор'){
			$query = 'DELETE FROM `info_pages` WHERE id_info=' . $_POST['info_del'];
				mysqli_query($db, $query);
		
				//для всех статей идущих после удаленной уменьшим id
				$res = mysqli_query($db, 'SELECT * FROM `info_pages` WHERE id_info>' . $_POST['info_del']);
				$more_than_delete = mysqli_num_rows($res);
				$more_than_delete += $_POST['info_del'];
				for($j = ($_POST['info_del'] + 1); $j <= $more_than_delete; $j++){
					//для каждой статьи идущей после удаленной уменьшим id на 1
					mysqli_query($db, 'UPDATE `info_pages` SET `id_info`=' . ($j - 1) . ' WHERE `id_info`=' . $j);
				}
				//теперь уменьшим на 1 AUTO_INCREMENT
				mysqli_query($db, 'ALTER TABLE `info_pages` AUTO_INCREMENT=' . $more_than_delete);
				echo 'Инфо-страница была удалена';
			
		}
		//пользователь не автор и не Администратор
		else
			echo 'Чтобы удалить данную статью нужно иметь в системе статус Администратора';
		
		mysqli_close($db);
		unset($_POST['info_del']);
	}
	
	
	//04_пришел запрос на загрузку статьи в add_info
	if(isset($_POST['load_to_add'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];					//статус пользователя в системе
		$user_name = $_COOKIE['adminpage_user'];		//имя пользователя	
		
		//проверим является ли пользователь Администратором
		if($user_status == 'Администратор'){
			$query = 'SELECT * FROM `info_pages` WHERE id_info=' . $_COOKIE['info_to_add'];
			$res = mysqli_query($db, $query);
			$info_to_add = mysqli_fetch_assoc($res);
			$to_js = 'hilas' . $info_to_add['title'] . 'hilas' . $info_to_add['text'];
			echo $to_js;	
		}
		else
			echo 'Чтобы изменить эту инфо-страницу вы должны иметь в системе статус Администратора';
		
		//удалим cookie
		setcookie('info_to_add', '', time()-60, '/');
		
		mysqli_close($db);
		unset($_POST['load_to_add']);
	}
	
	
	//05_пришел запрос на сохранение изображения
	if(isset($_POST['img'])){
		//декодируем изображение из base64
		$img = base64_decode($_POST['img']);
		//сохраним в файл
		$file_name = 'includes/for_info/info_imgs/' . $_POST['img_name'];
		file_put_contents($file_name, $img);
		echo 'imgs_save';
		
		unset($_POST['img']);
	}
?>