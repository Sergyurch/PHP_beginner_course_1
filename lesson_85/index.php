<?php
    $length = 12;
    $a1 = range(0,9);
    $a2 = range('a','z');
    $a3 = [',', '.', '<', '>', '!', '@', '#', '$', '%', '^', '&', '*', '?', '/', ';', ':'];
    $array = [$a1, $a2, $a3];
    $result = '';
    
    for ($i = 1; $i <= $length; $i++) {
        $sub_array = $array[rand(0,2)];
        $sub_array_len = count($sub_array);
        $result .= $sub_array[rand(0, $sub_array_len - 1)];
    }
    
    echo $result;
?>

