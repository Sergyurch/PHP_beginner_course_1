<!-- Необходимо определить Браузер пользователя и его версию.
     И вывести их на экран. А также если у него Internet Explorer 9
     версии и ниже - то выводить сообщение "У вас слишком старая
     версия. Обновитесь или установите Google Chrome" -->

<?php
    // Получаем название и версию браузера
    list($browser, $version) = get_user_browser($_SERVER['HTTP_USER_AGENT']);

    // Функция возвращает название и версию браузера 
    function get_user_browser($agent) {
        if ( strpos($agent, 'Chrome') && strpos($agent, 'Edg') ) {
            preg_match("/(Edg)(?:\/| )([0-9.]+)/", $agent, $browser_info);
        } else {
            preg_match("/(MSIE|Edg|Opera|Firefox|Chrome|Version)(?:\/| )([0-9.]+)/", $agent, $browser_info);
        }
        
        list(,$browser,$version) = $browser_info;
        if ($browser == 'Opera' && $version == '9.80') return ['Opera', substr($agent,-5)];
        if ($browser == 'Version') return ['Safari', $version];
        if ($browser == 'MSIE') return ['Internet Explorer', $version];
        if (!$browser && strpos($agent, 'Gecko')) return ['Browser based on Gecko', ''];
        if (!$browser) return ['Браузер неизвестен.', ''];
        return [$browser, $version];
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 141</title>
    </head>
    <body>
        <p>Ваш браузер: <?php echo $browser .' '. $version; ?></p>
        <?php if ( ($browser == 'Internet Explorer') and (substr($version, 0, strpos($version, '.')) <= 9) ): ?>
            <p>У вас слишком старая версия. Обновитесь или установите Google Chrome.</p>
        <?php endif; ?>
    </body>
</html>
