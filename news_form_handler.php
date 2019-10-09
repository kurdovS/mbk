<?php
	//01_пришел запрос на ту или иную страницу НЕОПУБЛИКОВАННЫХ или ОПУБЛИКОВАННЫХ новостей
	if(isset($_POST['news_page'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//шаблон по заменам для текста для вывода в список новостей поле текст вместе с тегами
		$search = array("<", ">");
		$replace = array("&lt", "&gt");
	
		//определим страница ОПУБЛИКОВАННЫХ или НЕОПУБЛИКОВАННЫХ новостей запрашивается
		if($_POST['type_news'] == 'unpublished'){
			$query = 'SELECT * FROM `unpublished_news`';
			$small_title = 'Новости ожидающие публикации';	
			$create_or_release = 'создания';
			$public_or_unpublic = 'Опубликовать';
			$release_or_unrelease = 'unrelease';
		}
		else
		{
			$query = 'SELECT * FROM `published_news`';
			$small_title = 'Опубликованные новости';
			$create_or_release = 'публикации';
			$public_or_unpublic = 'Снять с публикации';
			$release_or_unrelease = 'release';
		}
		
		$res = mysqli_query($db, $query);
		$unp_num = mysqli_num_rows($res);						//всего новостей
		$first_page = (($_POST['news_page'] - 1) * 10 + 1);	//id первой выводимой новости
		//id последней выводимой новости
		if($unp_num > ($first_page + 10))
			$last_page = ($first_page + 9);
		else
			$last_page = $unp_num;
		
		$html = '
		<div class="small_title">' . $small_title . '</div>
		<table class="news_table">
			<col width="3%">
			<col width="20%">
			<col width="42%">
			<col width="8%">
			<col width="7%">
			<col width="10%">
			<col width="10%">
		
			<tr class="news_table_tr">
				<td class="news_table_title_td"></td>
				<td class="news_table_title_td">Заголовок новости</td>
				<td class="news_table_title_td">Текст новости</td>
				<td class="news_table_title_td">Путь к изображению</td>
				<td class="news_table_title_td">Автор</td>
				<td class="news_table_title_td">Дата ' . $create_or_release . '</td>
				<td class="news_table_title_td">Статус</td>
			</tr>';
			
		//определим страница ОПУБЛИКОВАННЫХ или НЕОПУБЛИКОВАННЫХ новостей запрашивается
		if($_POST['type_news'] == 'unpublished')
			$query = 'SELECT * FROM `unpublished_news` WHERE id_new>=' . $first_page;
		else
			$query = 'SELECT * FROM `published_news` WHERE id_new>=' . $first_page;
		$res = mysqli_query($db, $query);	
			
		for($i = $first_page; $i <= $last_page; $i++){		
			$news = mysqli_fetch_assoc($res);
			
			$html .= '
			<tr>
				<td class="news_table_regular_td"><div class="at_div">' . $news['id_new'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div"><b>' . $news['title'] . '</b></div></td>
				<td class="news_table_regular_td"><div class="at_div">' . str_replace($search, $replace, $news['text']) . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $news['img'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $news['author'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $news['date_time'] . '</div></td>
				<td class="news_table_regular_td"><div class="at_div">' . $news['status'] . '</div></td>';
				
				//если для del то кнопку удалить
				if(isset($_POST['for_del']))
					$html .= '<td class="news_table_regular_td_l"><div class="del_new" onclick="del_butt_push(this)">Удалить новость</div></td>';
				else if(isset($_POST['for_edit']))
					$html .= '<td class="news_table_regular_td_l"><div class="edit_new" onclick="edit_butt_push(this)">Изменить новость</div></td>';
				else
					$html .= '<td class="news_table_regular_td_l"><div class="' . $release_or_unrelease . '" onclick="release_butt_push(this)">' . $public_or_unpublic . '</div></td>';
				$html .= '</tr>';
		}

		$html .= '</table>';	//закрываем таблицу и #unpublished
		
		mysqli_close($db);
		unset($_POST['news_page']);
		
		echo $html;
	}


	//02_пришел запрос на перенос новости из unpublished в published или наоборот
	if(isset($_POST['new_transfer'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];					//статус пользователя в системе
		$user_name = $_COOKIE['adminpage_user'];		//имя пользователя
		
		//определим направление перемещения
		if($_POST['new_transfer'] == 'Опубликовать'){
			//переносим новость из unpublished в published
			//берем всю информацию о новости из unpublished
			$query = 'SELECT * FROM `unpublished_news` WHERE id_new=' . $_POST['new_id'];
			$res = mysqli_query($db, $query);
			$new = mysqli_fetch_assoc($res);
			
			//получим заголовок статьи в транслите
			require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
			$translit_title = translit_encode(mb_strtolower($new['title']));
			
			//если пользователь не автор новости и не Администратор
			if($new['author'] != $user_name && $user_status != 'Администратор')
				echo 'Вы не можете опубликовать данную новость, т.к. вы не являетесь ее автором';
			else {
				//добавляем новость в таблицу published
				$query = 'INSERT INTO `published_news`(title, title_translit, text, img, author, date_time, status) 
						VALUES("' . $new['title'] . '", "' . $translit_title . '", "' . addslashes($new['text']) . '", "' . $new['img'] . '", "' . $new['author'] . '", "' .
						date("d.m.Y H:i") . '", "Опубликованно")';
				$res = mysqli_query($db, $query);
				//удаляем эту новость из unpublished
				$query = 'DELETE FROM `unpublished_news` WHERE id_new=' . $_POST['new_id'];
				mysqli_query($db, $query);
				//для всех новостей идущих после удаленной уменьшим id
				$res = mysqli_query($db, 'SELECT * FROM `unpublished_news` WHERE id_new>' . $_POST['new_id']);
				$more_than_delete = mysqli_num_rows($res);
				$more_than_delete += $_POST['new_id'];
				for($j = ($_POST['new_id'] + 1); $j <= $more_than_delete; $j++){
					//для каждой новости идущей после удаленной уменьшим id на 1
					mysqli_query($db, 'UPDATE `unpublished_news` SET `id_new`="' . ($j - 1) . '" WHERE `id_new`=' . $j);
				}
				//теперь уменьшим на 1 AUTO_INCREMENT
				mysqli_query($db, 'ALTER TABLE `unpublished_news` AUTO_INCREMENT=' . $more_than_delete);

				//запишем информацию о новости в sitemap.xml
				$file_sitemap = fopen($_SERVER['DOCUMENT_ROOT'] . '/sitemap.xml', 'r+');
				fseek($file_sitemap, -10, SEEK_END);
				//формируем текст который надо вставить в sitemap.xml
				$sitemap_txt = '<url>' . PHP_EOL;
				$sitemap_txt .= ' <loc>https://mbk-delivery.ru/mcdonalds/news/' . $translit_title . '</loc>' . PHP_EOL;
				$sitemap_txt .= ' <lastmod>' . date(DATE_ATOM) . '</lastmod>' . PHP_EOL;
				$sitemap_txt .= ' <priority>0.64</priority>' . PHP_EOL . '</url>' . PHP_EOL;
				$sitemap_txt .= PHP_EOL . '</urlset>';
				//запишем текст в файл sitemap.xml
				fwrite($file_sitemap, $sitemap_txt);
				fclose($file_sitemap); 

				echo 'Вы опубликовали данную новость';
			}
		}	
		else {
			//переносим новость из published в unpublished
			//берем всю информацию о новости из published
			$query = 'SELECT * FROM `published_news` WHERE id_new=' . $_POST['new_id'];
			$res = mysqli_query($db, $query);
			$new = mysqli_fetch_assoc($res);
			
			//получим заголовок новости в транслите
			require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
			$translit_title = translit_encode(mb_strtolower($new['title']));
			
			//если пользователь не автор новости и не Администратор
			if($new['author'] != $user_name && $user_status != 'Администратор')
				echo 'Вы не сможете снять данную новость с публикации, т.к. вы не являетесь ее автором';
			else {
				//добавляем новость в таблицу unpublished
				$query = 'INSERT INTO `unpublished_news`(title, title_translit, text, img, author, date_time, status) 
						VALUES("' . $new['title'] . '", "' . $translit_title . '", "' . addslashes($new['text']) . '", "' . $new['img'] . '", "' . $new['author'] . '", "' .
						date("d.m.Y H:i") . '", "' . $_POST['cause'] . '")';
				$res = mysqli_query($db, $query);
				//удаляем эту новость из published
				$query = 'DELETE FROM `published_news` WHERE id_new=' . $_POST['new_id'];
				mysqli_query($db, $query);
				//для всех новостей идущих после удаленной уменьшим id
				$res = mysqli_query($db, 'SELECT * FROM `published_news` WHERE id_new>' . $_POST['new_id']);
				$more_than_delete = mysqli_num_rows($res);
				$more_than_delete += $_POST['new_id'];
				for($j = ($_POST['new_id'] + 1); $j <= $more_than_delete; $j++){
					//для каждой новости идущей после удаленной уменьшим id на 1
					mysqli_query($db, 'UPDATE `published_news` SET `id_new`=' . ($j - 1) . ' WHERE `id_new`=' . $j);
				}
				//теперь уменьшим на 1 AUTO_INCREMENT
				mysqli_query($db, 'ALTER TABLE `published_news` AUTO_INCREMENT=' . $more_than_delete);
				echo 'Вы сняли данную новость с публикации';
			}
		}
		
		mysqli_close($db);
		unset($_POST['new_transfer']);
	}
	
	
	//03_пришел запрос на добавление новости в unpublished или в published
	if(isset($_POST['butt_in_add'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим заголовок новости в транслите
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
		$translit_title = translit_encode(mb_strtolower($_POST['new_header']));
		
		//выясним куда сохранять новость в НЕОПУБЛИКОВАННЫЕ или уже ОПУБЛИКОВАННЫЕ новости
		if($_POST['butt_in_add'] == 'save'){		//сохранить новость в unpublished
			$query = 'INSERT INTO `unpublished_news`(title, title_translit, text, img, author, date_time, status)
					VALUES("' . $_POST['new_header'] . '", "' . $translit_title . '", "' . addslashes($_POST['new_text']) . '", "' . $_POST['title_img'] . '", "' . $_COOKIE['adminpage_user'] .
					'", "' . date("d.m.Y H:i") . '", "Ожидает редактирования")';
			echo 'Ваша новость была сохранена';
		}
		else {
			$query = 'INSERT INTO `published_news`(title, title_translit, text, img, author, date_time, status)
					VALUES("' . $_POST['new_header'] . '", "' . $translit_title . '", "' . addslashes($_POST['new_text']) . '", "' . $_POST['title_img'] . '", "' . $_COOKIE['adminpage_user'] .
					'", "' . date("d.m.Y H:i") . '", "Опубликованно")';

			//запишем информацию о новости в sitemap.xml
			$file_sitemap = fopen($_SERVER['DOCUMENT_ROOT'] . '/sitemap.xml', 'r+');
			fseek($file_sitemap, -10, SEEK_END);
			//формируем текст который надо вставить в sitemap.xml
			$sitemap_txt = '<url>' . PHP_EOL;
			$sitemap_txt .= ' <loc>https://mbk-delivery.ru/mcdonalds/news/' . $translit_title . '</loc>' . PHP_EOL;
			$sitemap_txt .= ' <lastmod>' . date(DATE_ATOM) . '</lastmod>' . PHP_EOL;
			$sitemap_txt .= ' <priority>0.64</priority>' . PHP_EOL . '</url>' . PHP_EOL;
			$sitemap_txt .= PHP_EOL . '</urlset>';
			//запишем текст в файл sitemap.xml
			fwrite($file_sitemap, $sitemap_txt);
			fclose($file_sitemap);

			echo 'Ваша новость была сохранена и доступна публично';
		}
		mysqli_query($db, $query);
		
		mysqli_close($db);
		unset($_POST['butt_in_add']);
	}
	
	
	//04_пришел запрос на удаление новости
	if(isset($_POST['new_del'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];					//статус пользователя в системе
		$user_name = $_COOKIE['adminpage_user'];		//имя пользователя
		
		//определим автора удаляемой новости
		$author = mysqli_query($db, 'SELECT `author` FROM `published_news` WHERE id_new=' . $_POST['new_del']);
		$author = mysqli_fetch_row($author);
		$author = $author[0];						//автор удаляемой новости
	
		//если пользователь автор новости или Администратор
		if($author == $user_name || $user_status == 'Администратор'){
			//определяем из какой таблицы удалять новость
			if($_POST['type'] == 'published'){
				$query = 'DELETE FROM `published_news` WHERE id_new=' . $_POST['new_del'];
				mysqli_query($db, $query);
		
				//для всех новостей идущих после удаленной уменьшим id
				$res = mysqli_query($db, 'SELECT * FROM `published_news` WHERE id_new>' . $_POST['new_del']);
				$more_than_delete = mysqli_num_rows($res);
				$more_than_delete += $_POST['new_del'];
				for($j = ($_POST['new_del'] + 1); $j <= $more_than_delete; $j++){
					//для каждой новости идущей после удаленной уменьшим id на 1
					mysqli_query($db, 'UPDATE `published_news` SET `id_new`=' . ($j - 1) . ' WHERE `id_new`=' . $j);
				}
				//теперь уменьшим на 1 AUTO_INCREMENT
				mysqli_query($db, 'ALTER TABLE `published_news` AUTO_INCREMENT=' . $more_than_delete);
				echo 'Новость была удалена';
			}
			else {
				$query = 'DELETE FROM `unpublished_news` WHERE id_new=' . $_POST['new_del'];
				mysqli_query($db, $query);
		
				//для всех новостей идущих после удаленной уменьшим id
				$res = mysqli_query($db, 'SELECT * FROM `unpublished_news` WHERE id_new>' . $_POST['new_del']);
				$more_than_delete = mysqli_num_rows($res);
				$more_than_delete += $_POST['new_del'];
				for($j = ($_POST['new_del'] + 1); $j <= $more_than_delete; $j++){
					//для каждой новости идущей после удаленной уменьшим id на 1
					mysqli_query($db, 'UPDATE `unpublished_news` SET `id_new`=' . ($j - 1) . ' WHERE `id_new`=' . $j);
				}
				//теперь уменьшим на 1 AUTO_INCREMENT
				mysqli_query($db, 'ALTER TABLE `unpublished_news` AUTO_INCREMENT=' . $more_than_delete);
				echo 'Новость была удалена';
			}
		}
		//пользователь не автор и не Администратор
		else
			echo 'Чтобы удалить данную новость нужно быть ее автором или иметь в системе статус Администратора';
		
		mysqli_close($db);
		unset($_POST['new_del']);
		unset($_POST['type']);
	}
	
	
	//05_пришел запрос на загрузку новости в add_news
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
		
		//определим автора удаляемой новости
		$author = mysqli_query($db, 'SELECT `author` FROM `' . $_COOKIE['new_from'] . '_news` WHERE id_new=' . $_COOKIE['new_to_add']);
		$author = mysqli_fetch_row($author);
		$author = $author[0];	
		
		//проверим является ли пользователь автором новости или Администратором
		if($author == $user_name || $user_status == 'Администратор'){
			$query = 'SELECT * FROM `' . $_COOKIE['new_from'] . '_news` WHERE id_new=' . $_COOKIE['new_to_add'];
			$res = mysqli_query($db, $query);
			$new_to_add = mysqli_fetch_assoc($res);
			$to_js = 'hilas' . $new_to_add['title'] . 'hilas' . $new_to_add['text'] . 'hilas' . $new_to_add['img'];
			echo $to_js;	
		}
		else
			echo 'Чтобы изменить эту новость вы должны быть ее автором или иметь в системе статус Администратора';
		
		//удалим cookie
		setcookie('new_to_add', '', time()-60, '/');
		setcookie('new_from', '', time()-60, '/');
		
		mysqli_close($db);
		unset($_POST['load_to_add']);
	}
	
	
	//06_пришел запрос на сохранение изображения
	if(isset($_POST['img'])){
		//декодируем изображение из base64
		$img = base64_decode($_POST['img']);
		//сохраним в файл
		$file_name = 'includes/for_news/news_imgs/' . $_POST['img_name'];
		file_put_contents($file_name, $img);
		if($_POST['img_or_imgs'] == 'img')
			echo 'img_save';
		else
			echo 'imgs_save';
		
		unset($_POST['img']);
	}
?>
