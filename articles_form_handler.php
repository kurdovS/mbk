<?php
	//01_пришел запрос на ту или иную страницу НЕОПУБЛИКОВАННЫХ или ОПУБЛИКОВАННЫХ статей
	if(isset($_POST['articles_page'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//шаблон по заменам для текста для вывода в список статей поле текст вместе с тегами
		$search = array("<", ">");
		$replace = array("&lt", "&gt");
	
		//определим страница ОПУБЛИКОВАННЫХ или НЕОПУБЛИКОВАННЫХ статей запрашивается
		if($_POST['type_articles'] == 'unpublished'){
			$query = 'SELECT * FROM `unpublished_articles`';
			$small_title = 'Статьи ожидающие публикации';	
			$create_or_release = 'создания';
			$public_or_unpublic = 'Опубликовать';
			$release_or_unrelease = 'unrelease';
		}
		else
		{
			$query = 'SELECT * FROM `published_articles`';
			$small_title = 'Опубликованные статьи';
			$create_or_release = 'публикации';
			$public_or_unpublic = 'Снять с публикации';
			$release_or_unrelease = 'release';
		}
		
		$res = mysqli_query($db, $query);
		$unp_num = mysqli_num_rows($res);						//всего статей
		$first_page = (($_POST['articles_page'] - 1) * 10 + 1);	//id первой выводимой статьи
		//id последней выводимой статьи
		if($unp_num > ($first_page + 10))
			$last_page = ($first_page + 9);
		else
			$last_page = $unp_num;
		
		$html = '
		<div class="small_title">' . $small_title . '</div>
		<table class="articles_table">
			<col width="3%">
			<col width="20%">
			<col width="42%">
			<col width="8%">
			<col width="7%">
			<col width="10%">
			<col width="10%">
		
			<tr class="articles_table_tr">
				<td class="articles_table_title_td"></td>
				<td class="articles_table_title_td">Заголовок статьи</td>
				<td class="articles_table_title_td">Текст статьи</td>
				<td class="articles_table_title_td">Путь к изображению</td>
				<td class="articles_table_title_td">Автор</td>
				<td class="articles_table_title_td">Дата ' . $create_or_release . '</td>
				<td class="articles_table_title_td">Статус</td>
			</tr>';
			
		//определим страница ОПУБЛИКОВАННЫХ или НЕОПУБЛИКОВАННЫХ статей запрашивается
		if($_POST['type_articles'] == 'unpublished')
			$query = 'SELECT * FROM `unpublished_articles` WHERE id_article>=' . $first_page;
		else
			$query = 'SELECT * FROM `published_articles` WHERE id_article>=' . $first_page;
		$res = mysqli_query($db, $query);	
			
		for($i = $first_page; $i <= $last_page; $i++){		
			$articles = mysqli_fetch_assoc($res);
			
			$html .= '
			<tr>
				<td class="articles_table_regular_td"><div class="at_div">' . $articles['id_article'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div"><b>' . $articles['title'] . '</b></div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . str_replace($search, $replace, $articles['text']) . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $articles['img'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $articles['author'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $articles['date_time'] . '</div></td>
				<td class="articles_table_regular_td"><div class="at_div">' . $articles['status'] . '</div></td>';
				
				//если для del то кнопку удалить
				if(isset($_POST['for_del']))
					$html .= '<td class="articles_table_regular_td_l"><div class="del_article" onclick="del_butt_push(this)">Удалить статью</div></td>';
				else if(isset($_POST['for_edit']))
					$html .= '<td class="articles_table_regular_td_l"><div class="edit_article" onclick="edit_butt_push(this)">Изменить статью</div></td>';
				else
					$html .= '<td class="articles_table_regular_td_l"><div class="' . $release_or_unrelease . '" onclick="release_butt_push(this)">' . $public_or_unpublic . '</div></td>';
				$html .= '</tr>';
		}

		$html .= '</table>';	//закрываем таблицу и #unpublished
		
		mysqli_close($db);
		unset($_POST['articles_page']);
		
		echo $html;
	}
	
	
	//02_пришел запрос на перенос статьи из unpublished в published или наоборот
	if(isset($_POST['article_transfer'])){
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
		if($_POST['article_transfer'] == 'Опубликовать'){
			//переносим статью из unpublished в published
			//берем всю информацию о статье из unpublished
			$query = 'SELECT * FROM `unpublished_articles` WHERE id_article=' . $_POST['article_id'];
			$res = mysqli_query($db, $query);
			$article = mysqli_fetch_assoc($res);
			
			//получим заголовок статьи в транслите
			require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
			$translit_title = translit_encode(mb_strtolower($article['title']));
			
			//если пользователь не автор статьи и не Администратор
			if($article['author'] != $user_name && $user_status != 'Администратор')
				echo 'Вы не можете опубликовать данную статью, т.к. вы не являетесь ее автором';
			else {
				//добавляем статью в таблицу published
				$query = 'INSERT INTO `published_articles`(title, title_translit, text, img, author, date_time, status) 
						VALUES("' . $article['title'] . '", "' . $translit_title . '", "' . addslashes($article['text']) . '", "' . $article['img'] . '", "' . $article['author'] . '", "' .
						date("d.m.Y H:i") . '", "Опубликованно")';
				$res = mysqli_query($db, $query);
				//удаляем эту статью из unpublished
				$query = 'DELETE FROM `unpublished_articles` WHERE id_article=' . $_POST['article_id'];
				mysqli_query($db, $query);
				//для всех статей идущих после удаленной уменьшим id
				$res = mysqli_query($db, 'SELECT * FROM `unpublished_articles` WHERE id_article>' . $_POST['article_id']);
				$more_than_delete = mysqli_num_rows($res);
				$more_than_delete += $_POST['article_id'];
				for($j = ($_POST['article_id'] + 1); $j <= $more_than_delete; $j++){
					//для каждой статьи идущей после удаленной уменьшим id на 1
					mysqli_query($db, 'UPDATE `unpublished_articles` SET `id_article`="' . ($j - 1) . '" WHERE `id_article`=' . $j);
				}
				//теперь уменьшим на 1 AUTO_INCREMENT
				mysqli_query($db, 'ALTER TABLE `unpublished_articles` AUTO_INCREMENT=' . $more_than_delete);

				//запишем информацию о статье в sitemap.xml
				$file_sitemap = fopen($_SERVER['DOCUMENT_ROOT'] . '/sitemap.xml', 'r+');
				fseek($file_sitemap, -10, SEEK_END);
				//формируем текст который надо вставить в sitemap.xml
				$sitemap_txt = '<url>' . PHP_EOL;
				$sitemap_txt .= ' <loc>https://mbk-delivery.ru/mcdonalds/articles/' . $translit_title . '</loc>' . PHP_EOL;
				$sitemap_txt .= ' <lastmod>' . date(DATE_ATOM) . '</lastmod>' . PHP_EOL;
				$sitemap_txt .= ' <priority>0.64</priority>' . PHP_EOL . '</url>' . PHP_EOL;
				$sitemap_txt .= PHP_EOL . '</urlset>';
				//запишем текст в файл sitemap.xml
				fwrite($file_sitemap, $sitemap_txt);
				fclose($file_sitemap);

				echo 'Вы опубликовали данную статью';
			}
		}	
		else {
			//переносим статью из published в unpublished
			//берем всю информацию о статье из published
			$query = 'SELECT * FROM `published_articles` WHERE id_article=' . $_POST['article_id'];
			$res = mysqli_query($db, $query);
			$article = mysqli_fetch_assoc($res);
			
			//получим заголовок статьи в транслите
			require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
			$translit_title = translit_encode(mb_strtolower($article['title']));
			
			//если пользователь не автор статьи и не Администратор
			if($article['author'] != $user_name && $user_status != 'Администратор')
				echo 'Вы не сможете снять данную статью с публикации, т.к. вы не являетесь ее автором';
			else {
				//добавляем статью в таблицу unpublished
				$query = 'INSERT INTO `unpublished_articles`(title, title_translit, text, img, author, date_time, status) 
						VALUES("' . $article['title'] . '", "' . $translit_title . '", "' . addslashes($article['text']) . '", "' . $article['img'] . '", "' . $article['author'] . '", "' .
						date("d.m.Y H:i") . '", "' . $_POST['cause'] . '")';
				$res = mysqli_query($db, $query);
				//удаляем эту статью из published
				$query = 'DELETE FROM `published_articles` WHERE id_article=' . $_POST['article_id'];
				mysqli_query($db, $query);
				//для всех статей идущих после удаленной уменьшим id
				$res = mysqli_query($db, 'SELECT * FROM `published_articles` WHERE id_article>' . $_POST['article_id']);
				$more_than_delete = mysqli_num_rows($res);
				$more_than_delete += $_POST['article_id'];
				for($j = ($_POST['article_id'] + 1); $j <= $more_than_delete; $j++){
					//для каждой статьи идущей после удаленной уменьшим id на 1
					mysqli_query($db, 'UPDATE `published_articles` SET `id_article`=' . ($j - 1) . ' WHERE `id_article`=' . $j);
				}
				//теперь уменьшим на 1 AUTO_INCREMENT
				mysqli_query($db, 'ALTER TABLE `published_articles` AUTO_INCREMENT=' . $more_than_delete);
				echo 'Вы сняли данную статью с публикации';
			}
		}
		
		mysqli_close($db);
		unset($_POST['article_transfer']);
	}
	

	//03_пришел запрос на добавление статьи в unpublished или в published
	if(isset($_POST['butt_in_add'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим заголовок статьи в транслите
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/translit.inc');
		$translit_title = translit_encode(mb_strtolower($_POST['article_header']));
		
		//выясним куда сохранять статью в НЕОПУБЛИКОВАННЫЕ или уже ОПУБЛИКОВАННЫЕ статьи
		if($_POST['butt_in_add'] == 'save'){		//сохранить статью в unpublished
			$query = 'INSERT INTO `unpublished_articles`(title, title_translit, text, img, author, date_time, status)
					VALUES("' . $_POST['article_header'] . '", "' . $translit_title . '", "' . addslashes($_POST['article_text']) . '", "' . $_POST['title_img'] . '", "' . $_COOKIE['adminpage_user'] .
					'", "' . date("d.m.Y H:i") . '", "Ожидает редактирования")';
			echo 'Ваша статья была сохранена';
		}
		else {
			$query = 'INSERT INTO `published_articles`(title, title_translit, text, img, author, date_time, status)
					VALUES("' . $_POST['article_header'] . '", "' . $translit_title . '", "' . addslashes($_POST['article_text']) . '", "' . $_POST['title_img'] . '", "' . $_COOKIE['adminpage_user'] .
					'", "' . date("d.m.Y H:i") . '", "Опубликованно")';

			//запишем информацию о статье в sitemap.xml
			$file_sitemap = fopen($_SERVER['DOCUMENT_ROOT'] . '/sitemap.xml', 'r+');
			fseek($file_sitemap, -10, SEEK_END);
			//формируем текст который надо вставить в sitemap.xml
			$sitemap_txt = '<url>' . PHP_EOL;
			$sitemap_txt .= ' <loc>https://mbk-delivery.ru/mcdonalds/articles/' . $translit_title . '</loc>' . PHP_EOL;
			$sitemap_txt .= ' <lastmod>' . date(DATE_ATOM) . '</lastmod>' . PHP_EOL;
			$sitemap_txt .= ' <priority>0.64</priority>' . PHP_EOL . '</url>' . PHP_EOL;
			$sitemap_txt .= PHP_EOL . '</urlset>';
			//запишем текст в файл sitemap.xml
			fwrite($file_sitemap, $sitemap_txt);
			fclose($file_sitemap);

			echo 'Ваша статья была сохранена и доступна публично';
		}
		mysqli_query($db, $query);
		
		mysqli_close($db);
		unset($_POST['butt_in_add']);
	}
	
	
	//04_пришел запрос на удаление статьи
	if(isset($_POST['article_del'])){
		//подключение к БД
		require_once($_SERVER['DOCUMENT_ROOT'] . '/includes/for_all/db_connection.inc');
		$db = connectionDB();
		
		//получим статус пользователя в системе
		$query = 'SELECT status FROM adminpage_system_accounts WHERE name="' . $_COOKIE['adminpage_user'] . '"';
		$user_status = mysqli_query($db, $query);
		$user_status = mysqli_fetch_row($user_status);
		$user_status = $user_status[0];					//статус пользователя в системе
		$user_name = $_COOKIE['adminpage_user'];		//имя пользователя
		
		//определим автора удаляемой статьи
		$author = mysqli_query($db, 'SELECT `author` FROM `published_articles` WHERE id_article=' . $_POST['article_del']);
		$author = mysqli_fetch_row($author);
		$author = $author[0];						//автор удаляемой статьи
	
		//если пользователь автор статьи или Администратор
		if($author == $user_name || $user_status == 'Администратор'){
			//определяем из какой таблицы удалять статью
			if($_POST['type'] == 'published'){
				$query = 'DELETE FROM `published_articles` WHERE id_article=' . $_POST['article_del'];
				mysqli_query($db, $query);
		
				//для всех статей идущих после удаленной уменьшим id
				$res = mysqli_query($db, 'SELECT * FROM `published_articles` WHERE id_article>' . $_POST['article_del']);
				$more_than_delete = mysqli_num_rows($res);
				$more_than_delete += $_POST['article_del'];
				for($j = ($_POST['article_del'] + 1); $j <= $more_than_delete; $j++){
					//для каждой статьи идущей после удаленной уменьшим id на 1
					mysqli_query($db, 'UPDATE `published_articles` SET `id_article`=' . ($j - 1) . ' WHERE `id_article`=' . $j);
				}
				//теперь уменьшим на 1 AUTO_INCREMENT
				mysqli_query($db, 'ALTER TABLE `published_articles` AUTO_INCREMENT=' . $more_than_delete);
				echo 'Статья была удалена';
			}
			else {
				$query = 'DELETE FROM `unpublished_articles` WHERE id_article=' . $_POST['article_del'];
				mysqli_query($db, $query);
		
				//для всех статей идущих после удаленной уменьшим id
				$res = mysqli_query($db, 'SELECT * FROM `unpublished_articles` WHERE id_article>' . $_POST['article_del']);
				$more_than_delete = mysqli_num_rows($res);
				$more_than_delete += $_POST['article_del'];
				for($j = ($_POST['article_del'] + 1); $j <= $more_than_delete; $j++){
					//для каждой статьи идущей после удаленной уменьшим id на 1
					mysqli_query($db, 'UPDATE `unpublished_articles` SET `id_article`=' . ($j - 1) . ' WHERE `id_article`=' . $j);
				}
				//теперь уменьшим на 1 AUTO_INCREMENT
				mysqli_query($db, 'ALTER TABLE `unpublished_articles` AUTO_INCREMENT=' . $more_than_delete);
				echo 'Статья была удалена';
			}
		}
		//пользователь не автор и не Администратор
		else
			echo 'Чтобы удалить данную статью нужно быть ее автором или иметь в системе статус Администратора';
		
		mysqli_close($db);
		unset($_POST['article_del']);
		unset($_POST['type']);
	}
	
	
	//05_пришел запрос на загрузку статьи в add_articles
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
		
		//определим автора статьи
		$author = mysqli_query($db, 'SELECT `author` FROM `' . $_COOKIE['article_from'] . '_articles` WHERE id_article=' . $_COOKIE['article_to_add']);
		$author = mysqli_fetch_row($author);
		$author = $author[0];	
		
		//проверим является ли пользователь автором статьи или Администратором
		if($author == $user_name || $user_status == 'Администратор'){
			$query = 'SELECT * FROM `' . $_COOKIE['article_from'] . '_articles` WHERE id_article=' . $_COOKIE['article_to_add'];
			$res = mysqli_query($db, $query);
			$article_to_add = mysqli_fetch_assoc($res);
			$to_js = 'hilas' . $article_to_add['img'] . 'hilas' . $article_to_add['title'] . 'hilas' .$article_to_add['text'] . 'hilas';
			echo $to_js;
		}
		else
			echo 'Чтобы изменить эту статью вы должны быть ее автором или иметь в системе статус Администратора';
		
		//удалим cookie
		setcookie('article_to_add', '', time()-60, '/');
		setcookie('article_from', '', time()-60, '/');
		
		mysqli_close($db);
		unset($_POST['load_to_add']);
	}
	
	
	//06_пришел запрос на сохранение изображения
	if(isset($_POST['img'])){
		//декодируем изображение из base64
		$img = base64_decode($_POST['img']);
		//сохраним в файл
		$file_name = 'includes/for_articles/articles_imgs/' . $_POST['img_name'];
		file_put_contents($file_name, $img);
		if($_POST['img_or_imgs'] == 'img')
			echo 'img_save';
		else
			echo 'imgs_save';
		
		unset($_POST['img']);
	}
?>
