<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <?php
        mb_internal_encoding('UTF-8');
        $x = '{Пожалуйста,|Просто|Если сможете,} сделайте так, 
        чтобы это {удивительное|крутое|простое|важное|бесполезное} 
        тестовое предложение изменялось {быстро|мгновенно|
        оперативно|правильно} случайным образом каждый раз.';
                
        while ( mb_stripos($x, '{') !== false ) {
            $start = mb_stripos($x, '{');
            $finish = mb_stripos($x, '}', $start);
            $sub_text = mb_substr($x, $start + 1, $finish - $start - 1);
            $words_array = explode('|', $sub_text);
            $word = $words_array[array_rand($words_array)];
            $x = mb_substr($x, 0, $start) . $word . mb_substr($x, $finish + 1);
        }
        
        echo $x;
    ?>
</body>
</html>
