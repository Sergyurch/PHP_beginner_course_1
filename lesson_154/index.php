<?php
    mb_internal_encoding('utf-8');
    $m = ['а','б','в'];
    $len = 3; // Длина каждой комбинации
    $result = [];
    $current_array = []; // Здесь будут символы текущей комбинации
    $last_combination = '';
    $letter_pointer = 0; // Указатель позиции символа
    $last_letter = $m[count($m) - 1]; // Последний символ в массиве

    for ($i = 1; $i <= $len; $i++) {
        $current_array[] = $m[0]; // Массив символов первой комбинации
        $last_combination .= $m[count($m) - 1]; // Последняя комбинация
    }

    $result[] = implode('', $current_array);
    
    // Выполняем цикл, пока не достигнем последней комбинации
    while ( implode('', $current_array) != $last_combination ) {
        if ( $current_array[$letter_pointer] != $last_letter ) {
            $current_array[$letter_pointer] = get_next_letter($current_array[$letter_pointer], $m);
            $result[] = implode('', $current_array);
        } else {
            do {
                $letter_pointer++;
            } while ( $current_array[$letter_pointer] == $last_letter );
            
            $current_array[$letter_pointer] = get_next_letter($current_array[$letter_pointer], $m);
            
            for ($i = 0; $i <= $letter_pointer - 1; $i++) {
                $current_array[$i] = $m[0];
            }
            
            $result[] = implode('', $current_array);
            $letter_pointer = 0;
        }
    }

    // Функция возвращает следующий символ из массива или с индексом 1
    function get_next_letter($current_letter, $array) {
        foreach ($array as $key => $letter) {
            if ($letter == $current_letter) $position = $key;
        }

        return ( isset($array[$position + 1]) ) ? $array[$position + 1] : $array[1];
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 154</title>
    </head>
    <body>
        <?php foreach ($result as $word): ?>
            <div><?= $word; ?></div>
        <?php endforeach; ?>
    </body>
</html>
