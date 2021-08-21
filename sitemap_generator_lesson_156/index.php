<?php
    mb_internal_encoding('utf-8');
    set_time_limit(0);
    $error = NULL;
    $file_created = false;
    $file_path = 'sitemap.xml'; // Путь к будущему файлу
    $urls = []; // Здесь будут храниться собранные и обработанный ссылки
    $urls_tmp = []; // Здесь будут храниться ссылки, которые еще надо обработать
    $max_index = 200; // Максимальный размер карты сайта
    $extensions_allowed = ['php','aspx','htm','html','asp','cgi','pl'];// Разрешённые расширения файлов
    
    $streamContext = stream_context_create([
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false
        ]
    ]);

    // Если форма была отправлена
    if ( isset($_GET['submit']) ) {
        if ( empty($_GET['url']) ) { // Если не было заполнено поле
            $error = 'Не заполнено поле с адресом сайта';
        } else {
            // Определяем протокол и хост сайта, который надо индексировать
            $url_parts = parse_url($_GET['url']);
            $site_scheme = ( isset($url_parts['scheme']) ) ? $url_parts['scheme'] : NULL;
            $site_host = ( isset($url_parts['host']) ) ? $url_parts['host'] : NULL;
            $site_address = ( !empty($site_scheme) and !empty($site_host) ) ? "$site_scheme://$site_host" : NULL;
            
            if ( !$url_parts or ( $site_scheme and !$site_host ) ) {
                $error = 'Введите верный адрес сайта';
            } elseif ($site_scheme and $site_host) {
                if ( !$page_content = @file_get_contents("$site_scheme://$site_host", false, $streamContext) ) {
                    $error = 'Введите верный адрес сайта';
                }
            } else {
                if ( $page_content = @file_get_contents("http://{$url_parts['path']}", false, $streamContext) ) {
                    $url_parts = parse_url("http://{$url_parts['path']}");
                } elseif ( $page_content = @file_get_contents("https://{$url_parts['path']}", false, $streamContext) ) {
                    $url_parts = parse_url("https://{$url_parts['path']}");
                } else {
                    $error = 'Введите верный адрес сайта';
                }

                if (!$error) {
                    $site_scheme = $url_parts['scheme'];
                    $site_host = $url_parts['host'];
                    $site_address = "$site_scheme://$site_host";
                }

            }

            // Если ошибок нет, значит мы уже знаем протокол и хост сайта, а также получили его содержимое
            if (!$error) {
                // Проверяем нету ли мета-тега, который запрещает индексировать ссылки на странице с nofollow|noindex|none
                if ( preg_match('/<meta.*name=.?("|\'|).*robots.*?("|\'|).*?content=.*?("|\'|).*(nofollow|noindex|none).*?("|\'|).*>/i', $page_content) ) {
                    $error = 'На этом сайте указан мета-тег, который запрещает поисковым системам индексировать ссылки';
                } else {
                    $urls[] = $site_address . '/'; // Добавляем первую ссылку в наш каталог, это будет главная страница сайта
                    $urls_tmp = get_urls($page_content, $urls_tmp, $max_index, $site_scheme, $site_host, $site_address, $extensions_allowed, $streamContext);
                    
                    // Обрабатываем ссылки, которые еще не проходили проверку, а также добавляем новые
                    for ($i = 0; isset($urls_tmp[$i]); $i++) {
                        if ( count($urls) >= $max_index ) break;
                        $link_content = @file_get_contents("{$urls_tmp[$i]}", false, $streamContext);
                        
                        if ($link_content) {
                            $urls[] = $urls_tmp[$i];
                            $link_urls = get_urls($link_content, $urls_tmp, $max_index, $site_scheme, $site_host, $site_address, $extensions_allowed, $streamContext);
                            foreach ($link_urls as $link_url) {
                                $urls_tmp[] = $link_url;
                            }
                        }
                    }

                    // Создаем контент будущего файла sitemap.xml
                    $sitemapXML = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n";
                    $sitemapXML .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"';
                    $sitemapXML .= "\r\n\t" . 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"';
                    $sitemapXML .= "\r\n\t" . 'xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';

                    foreach ($urls as $key => $url) {
                        if ($key == 0) {
                            $sitemapXML .= "\r\n\t<url>\r\n\t\t<loc>{$url}</loc>\r\n\t\t<lastmod>" . date('c') . "</lastmod>\r\n\t\t<priority>1.00</priority>\r\n\t</url>";
                        } else {
                            $sitemapXML .= "\r\n\t<url>\r\n\t\t<loc>{$url}</loc>\r\n\t\t<lastmod>" . date('c') . "</lastmod>\r\n\t\t<priority>0.8</priority>\r\n\t</url>";
                        }
                        
                    }

                    $sitemapXML .= "\r\n</urlset>";
                    $sitemapXML = trim( strtr($sitemapXML, array('%2F'=>'/','%3A'=>':','%3F'=>'?','%3D'=>'=','%26'=>'&','%27'=>"'",'%22'=>'"','%3E'=>'>','%3C'=>'<','%23'=>'#','&'=>'&')) );

                    // Записываем данные в файл sitemap.xml
                    $fp = fopen($file_path,'w+');
                    if ( !fwrite($fp, $sitemapXML) ) {
                        $error = 'Ошибка записи файла!';
                        fclose($fp);
                    } else {
                        $file_created = true;
                        fclose($fp);
                    }
                    
                
                }
            }

        }

    }


    // Функция возвращает массив проиндексированных ссылок. Ссылки не повторяются и их нету в $urls_tmp 
    function get_urls(&$page_content, &$urls_tmp, $max_index, $site_scheme, $site_host, $site_address, $extensions_allowed, $streamContext) {
        $result = [];
             
        // Находим все ссылки на странице, с помощью регулярного выражения и сохраняем во временный массив
	    preg_match_all("/<a\s{1}[^>]*href[^=]*=[ '\"\s]*([^ \"'>\s#]+)[^>]*>/i", $page_content, $links_tmp);
        
        // Добавляем в массив links все ссылки не имеющие аттрибут nofollow
	    foreach ($links_tmp[0] as $key => $link) {
            if ( !preg_match('/<.*rel=.?("|\'|).*nofollow.*?("|\'|).*/i', $link) ) {
                $links[] = $links_tmp[1][$key];
            }
        }

        // Удаляем временный массив
	    unset($links_tmp);
        
        //Обрабатываем полученные ссылки из массива links
        if ( isset($links) ) {
            for ($i = 0; $i < count($links); $i++) {
                // Узнаём информацию о ссылке
                $link_info = parse_url($links[$i]);
                $link_scheme = ( isset($link_info['scheme']) ) ? $link_info['scheme'] : NULL;
                $link_host = ( isset($link_info['host']) ) ? $link_info['host'] : NULL;
                $link_path = ( isset($link_info['path']) ) ? $link_info['path'] : '/';
                if ( $link_path[0] != '/') $link_path = '/' . $link_path;
                
                //Если не установлена схема и хост ссылки, то подставляем наш хост
                if ( !$link_host ) {
                    $links[$i] = $site_address . $link_path;
                } else {
                    if ( $link_scheme != $site_scheme ) continue;
                    if( $link_host != $site_host ) {
                        if ( preg_match('/^www.*/i', $site_host) ) {
                            if ( mb_substr($site_host, 4) == $link_host ) {
                                $links[$i] = $site_address . $link_path;
                            } else {
                                continue;
                            }
                        } elseif ( preg_match('/^www.*/i', $link_host) ) {
                            if ( mb_substr($link_host, 4) == $site_host ) {
                                $links[$i] = $site_address . $link_path;
                            } else {
                                continue;
                            }
                        } else {
                            continue;
                        }
                    } else {
                        $links[$i] = $site_address . $link_path;
                    }
                }

                // Убираем якори у ссылок
                $links[$i] = preg_replace('/#.*/X', '', $links[$i]);

                // Если ссылка ведет на файл с расширением, то проверяем подходит оно нам или нет.
                // Если нет, то пропускаем ссылку
                if ( strpos($link_path, '.') ) {
                    $arr_tmp = explode('.', $link_path);
                    $extension = end($arr_tmp);
                    if ( !in_array($extension, $extensions_allowed) ) continue;
                }

                // Если ссылка уже есть в списке на обработку или в массиве результатов функции, пропускаем ее
                if ( in_array($links[$i], $urls_tmp) or in_array($links[$i], $result) ) continue;
                // Если хост не наш, пропускаем ссылку
                if ( $link_host and ($link_host != $site_host) ) continue;
                // Если путь указан на главную страницу, пропускаем ссылку
                if ( $link_path == '/' ) continue;
                // Если ссылка на почту или телефон, пропускаем ссылку
                if ( strpos($link_path,'@') or strpos($link_path,'tel:') ) continue;

                $result[] = $links[$i];
            
            }
        }

        return $result;
    }

?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <title>Lesson 156</title>
        <style>
            h1, form, .text-center {
                text-align: center;
            }

            input[type="submit"] {
                margin-top: 20px;
            }

            .msg {
                display: inline-block;
                padding: 15px;
                margin: 15px 0;
                border-radius: 5px;
                font-weight: bold;
                color: white;
            }

            .error-msg {
                background-color: red;
            }

            .success-msg {
                background-color: green;
            }

            a {
                margin: 15px;
            }

            #loader {
                margin-top: 20px;
                display: none;
            }
        </style>
    </head>
    <body>
        <h1>Сайтмап генератор</h1>
        <form method="GET" id="form" onsubmit="showLoader(event)">
            <?php if ( !empty($error) ): ?>
                <div class="text-center">
                    <div class="msg error-msg"><?= $error; ?></div>
                </div>
            <?php endif; ?>
            <div>
                <label>
                    <span>Введите адрес сайта </span><br>
                    <input type="text" name="url" value="<?php if ( isset($_GET['url']) ) echo $_GET['url']; ?>">
                </label>
            </div>
            <div id="loader">
                <p>Ожидайте. Идет индексация.</p>
                <img src="images/spinner.gif" alt="Лоадер">
            </div>
            <input type="submit" id="submit" value="Начать индексирование">
        </form>
        <?php if ($file_created) : ?>
            <div id="success" class="text-center">
                <div class="msg success-msg">Карта успешно сгенерирована. Найдено <?= count($urls); ?> страниц.</div>
                <div>
                    <a href="<?= $file_path; ?>" download>Скачать карту</a>
                    <a href="<?= $file_path; ?>" target="_blank">Просмотреть карту</a>
                </div>
            </div>
        <?php endif; ?>
        <script>
            function showLoader(event) {
                let form = event.target;
                let loader = document.getElementById("loader");
                let submitBtn = document.getElementById("submit");
                let divSuccess = document.getElementById("success");
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'submit';
                input.value = '1';
                form.appendChild(input);
                submitBtn.disabled = true;
                loader.style.display = 'block';
                divSuccess.style.display = 'none';
            }
        </script>
    </body>
</html>
