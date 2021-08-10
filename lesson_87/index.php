<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body>
    <?php
        mb_internal_encoding('UTF-8');

        $text = 'Дан длинный текст я купила терминатора вчера';
        $words = explode(' ', $text);

        foreach ($words as &$word) {
            if ( mb_strlen($word) > 6 ) {
                $word = mb_substr($word, 0, 5) . '*';
            }
        }

        echo implode(' ', $words);
    ?>
</body>
</html>
