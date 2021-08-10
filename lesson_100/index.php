<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <?php
        mb_internal_encoding('UTF-8');
        $text = 'Голландский архитектор Барт Голдхоорн: в России плохой градостроительный опыт';

        $convert_list = [
            'а' => 'a',
            'б' => 'b',
            'в' => 'v',
            'г' => 'g',
            'д' => 'd',
            'е' => 'e',
            'ё' => 'e',
            'ж' => 'g',
            'з' => 'z',
            'и' => 'i',
            'й' => 'i',
            'к' => 'k',
            'л' => 'l',
            'м' => 'm',
            'н' => 'n',
            'о' => 'o',
            'п' => 'p',
            'р' => 'r',
            'с' => 's',
            'т' => 't',
            'у' => 'u',
            'ф' => 'f',
            'х' => 'h',
            'ц' => 'c',
            'ч' => 'ch',
            'ш' => 'sh',
            'щ' => 'sch',
            'ъ' => '',
            'ы' => 'i',
            'ь' => '',
            'э' => 'e',
            'ю' => 'ju',
            'я' => 'ja'
        ];

        echo 'Первый вариант:' .'<br>';
        echo make_english_letters_v1($text, $convert_list) . '<br><br>';
        echo 'Второй вариант:' .'<br>';
        echo make_english_letters_v2($text, $convert_list);

        function make_english_letters_v1($text, $convert_list) {
            $result = '';
            foreach(mb_str_split($text) as $symbol) {
                if ( array_key_exists(mb_strtolower($symbol), $convert_list) ) {
                    $result .= $convert_list[mb_strtolower($symbol)];
                } else {
                   $result .= $symbol; 
                }
            }

            return $result;
        }

        function make_english_letters_v2($text, $convert_list) {
            $result = '';
            foreach(mb_str_split($text) as $symbol) {
                if ( array_key_exists(mb_strtolower($symbol), $convert_list) ) {
                    $result .= $convert_list[mb_strtolower($symbol)];
                } elseif ($symbol == ' ') {
                   $result .= '_'; 
                } else {
                    continue;
                }
            }

            return $result;
        }

        
        
    ?>
</body>
</html>
