<?php
    mb_internal_encoding('utf-8');
    
    $questions = [
        ['question_text' => 'В каком году Колумб открыл Америку?',
         'answers' => [
            ['answer_text' => '1492', 'is_correct' => true],
            ['answer_text' => '1450', 'is_correct' => false],
            ['answer_text' => '1492', 'is_correct' => true],
            ['answer_text' => '1510', 'is_correct' => false]
        ]],
        ['question_text' => 'Без труда не вытащить и рыбку из...',
         'answers' => [
            ['answer_text' => 'Холодильника', 'is_correct' => false],
            ['answer_text' => 'Кармана', 'is_correct' => false],
            ['answer_text' => 'Пруда', 'is_correct' => true],
            ['answer_text' => 'Ведра', 'is_correct' => false]
        ]],
        ['question_text' => 'Сколько сторон у куба?',
         'answers' => [
            ['answer_text' => '4', 'is_correct' => false],
            ['answer_text' => '6', 'is_correct' => true],
            ['answer_text' => '4 + 2', 'is_correct' => true],
            ['answer_text' => '10', 'is_correct' => false]
        ]]
    ];

    $all_correct_answers = 0; // Максимально возможное количество правильных ответов
    $correct_answers = 0;
    $error = false;

    // Определяем в каком порядке выводить вопросы
    if ( isset($_GET['submit']) ) {
        $question_numbers = explode('_', $_GET['questions-order']);

        foreach ($question_numbers as $question_number) {
            if ( !isset($_GET[$question_number]) ) {
                $error = true;
                break;
            }
        }
    } else {
        $question_numbers = range(0, count($questions) - 1);
        shuffle($question_numbers);
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 152</title>
        <style>
            input[type="submit"] {
                margin-top: 20px;
            }

            .error-msg {
                padding: 20px;
                background-color: red;
            }

            .correct-answer {
                background-color: green;
            }

            .incorrect-answer {
                background-color: red;
            }
        </style>
    </head>
    <body>
        <h1>Вопросы</h1>
        <form method="GET">
            <!-- Скрытое поле с порядком вопросов -->
            <input type="hidden" name="questions-order" value="<?= implode('_', $question_numbers); ?>">

            <!-- Выводим вопросы -->
            <?php foreach ($question_numbers as $question_number): ?>
                <div>
                    <!-- Выводим текст вопроса -->
                    <h3><?= $questions[$question_number]['question_text']; ?></h3>

                    <!-- Если не был выбран вариант ответа -->
                    <?php if ($error and !isset($_GET[$question_number]) ): ?>
                        <div class="error-msg">Вы не выбрали вариант ответа на этот вопрос</div>
                    <?php endif; ?>

                    <!-- Определяем в какой порядке выводить ответы -->
                    <?php
                        if ( !isset($_GET['submit']) ) {
                            $answer_numbers = range(0, count($questions[$question_number]['answers']) - 1);
                            shuffle($answer_numbers);
                        } else {
                            $answer_numbers = explode('_', $_GET['answers-order-q' . $question_number]);
                        }
                    ?>

                    <!-- Выводим варианты ответов -->
                    <?php foreach ($answer_numbers as $answer_number): ?>
                        <!-- Скрытое поле с порядком ответов -->
                        <input type="hidden" name="<?= 'answers-order-q' . $question_number; ?>" value="<?= implode('_',$answer_numbers); ?>">
                        
                        <!-- Считаем максимально возможное кол-во правильных ответов-->
                        <?php 
                            if ( $questions[$question_number]['answers'][$answer_number]['is_correct'] ) $all_correct_answers++; 
                        ?>
                        
                        <!-- Если форма была отправлена, ошибок нету и это ответ, который был выбран -->
                        <?php if ( isset($_GET['submit']) and !$error and in_array($answer_number, $_GET[$question_number])  ): ?>
                            <!-- Проверяем ответ и отмечаем его нужным цветом -->
                            <?php if ( $questions[$question_number]['answers'][$answer_number]['is_correct'] ): ?>
                                <label class="correct-answer">
                                <?php $correct_answers++; ?>
                            <?php else: ?>
                                <label class="incorrect-answer">
                            <?php endif; ?>    
                        <?php else: ?>
                            <label>
                        <?php endif; ?>

                            <!-- Отмечаем ответ, который ранее был выбран -->
                            <?php if ( isset($_GET[$question_number]) and in_array($answer_number, $_GET[$question_number]) ): ?>
                                <input type="checkbox" name="<?= $question_number; ?>[]" value="<?= $answer_number; ?>" checked>
                            <?php else: ?>
                                <input type="checkbox" name="<?= $question_number; ?>[]" value="<?= $answer_number; ?>">
                            <?php endif; ?>
                            <span><?= $questions[$question_number]['answers'][$answer_number]['answer_text']; ?></span>
                        </label><br>
                    <?php endforeach; ?>

                </div>
            <?php endforeach; ?>

            <!-- Показываем результат или кнопку отправки формы -->
            <?php if ( isset($_GET['submit']) and !$error ): ?>
                <p>Количество правильных ответов: <?= $correct_answers . ' из ' . $all_correct_answers; ?></p>
                <a href="<?= basename(__FILE__); ?>">Пройти тест заново</a>
            <?php else: ?>
                <input type="submit" name="submit" value="Отправить на проверку">
            <?php endif; ?>
        </form>
    </body>
</html>
