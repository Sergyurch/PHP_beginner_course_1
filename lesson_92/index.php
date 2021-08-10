<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
<body>
    <?php
        mb_internal_encoding('UTF-8');
        $text = 'Дано «длинный текст», надо обрезать до 250 
        символов, причем не на середине слова, в конце поставить 
        многоточие.например, тут 
        http://phpfiddle.org/main/code/rxwh-vm6f 250й 
        символ попадает на слово "используя", то есть 
        нужно обрезать до этого слова, на середине (оставив 
        "испо") обрезать не надо.';

        $result = mb_substr($text, 0, 251);
        $last_space = mb_strripos($result, ' ');

        if ( $last_space != 250 ) {
            $result = mb_substr($text, 0, $last_space) . '...';
        } else {
            $result = mb_substr($text, 0, 250) . '...';
        }

        echo $result;
    ?>
</body>
</html>
