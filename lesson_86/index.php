<?php
    $length = 12;
    $a1 = range(0,9);
    $a2 = range('a','z');
    $a3 = [',', '.', '<', '>', '!', '@', '#', '$', '%', '^', '&', '*', '?', '/', ';', ':'];
    $array = [$a1, $a2, $a3];
    $result = '';
    
    #Добавляем в результат по одному символу из каждого массива
    foreach ($array as $sub_array) {
        $result .= get_rand_symbol($sub_array);
    }
    
    #Добавляем остачу случайных символов
    for ($i = 1; $i <= $length - 3; $i++) {
        $sub_array = $array[rand(0,2)];
        $result .= get_rand_symbol($sub_array);
    }
    
    echo str_shuffle($result);
    
    function get_rand_symbol($array) {
        $array_len = count($array);
        return $array[rand(0, $array_len - 1)];
    }
?>

