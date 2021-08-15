<?php
    mb_internal_encoding('utf-8');
    
    $questions = [
        ['question_text' => 'В каком году Колумб открыл Америку?',
         'answers' => [
            ['answer_text' => '1492', 'is_correct' => true],
            ['answer_text' => '1450', 'is_correct' => false],
            ['answer_text' => '1592', 'is_correct' => false],
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
            ['answer_text' => '8', 'is_correct' => false],
            ['answer_text' => '10', 'is_correct' => false]
        ]]
    ];

    $questions_amount = count($questions); // Количество вопросов
    $correct_answers = 0;
    $error = false;

    // Если ответов меньше чем вопросов
    if ( isset($_GET['submit']) ) {
        if ( (count($_GET) - 1) < $questions_amount ) $error = true;
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
            <?php foreach ($questions as $question_number => $question_block): ?>
                <div>
                    <!-- Выводим вопрос -->
                    <h3><?= $question_block['question_text']; ?></h3>

                    <!-- Если не был выбран вариант ответа -->
                    <?php if ($error and !isset($_GET[$question_number]) ): ?>
                        <div class="error-msg">Вы не выбрали вариант ответа на этот вопрос</div>
                    <?php endif; ?>

                    <!-- Выводим варианты ответов -->
                    <?php foreach ($question_block['answers'] as $answer_number => $answer): ?>
                        <!-- Если форма была отправлена, ошибок нету и это ответ, который был выбран -->
                        <?php if ( isset($_GET['submit']) and !$error and $_GET[$question_number] == $answer_number ): ?>
                            <!-- Проверяем ответ и отмечаем его нужным цветом -->
                            <?php if ( $question_block['answers'][$answer_number]['is_correct'] ): ?>
                                <label class="correct-answer">
                                <?php $correct_answers++; ?>
                            <?php else: ?>
                                <label class="incorrect-answer">
                            <?php endif; ?>    
                        <?php else: ?>
                            <label>
                        <?php endif; ?>
                            <!-- Отмечаем ответ, который ранее был выбран -->
                            <?php if ( isset($_GET[$question_number]) and $_GET[$question_number] == $answer_number ): ?>
                                <input type="radio" name="<?= $question_number; ?>" value="<?= $answer_number; ?>" checked>
                            <?php else: ?>
                                <input type="radio" name="<?= $question_number; ?>" value="<?= $answer_number; ?>">
                            <?php endif; ?>
                            <span><?= $answer['answer_text']; ?></span>
                        </label><br>
                    <?php endforeach; ?>

                </div>
            <?php endforeach; ?>

            <!-- Показываем результат или кнопку отправки формы -->
            <?php if ( isset($_GET['submit']) and !$error ): ?>
                <p>Количество правильных ответов: <?= $correct_answers . ' из ' . $questions_amount; ?></p>
                <a href="<?= basename(__FILE__); ?>">Пройти тест заново</a>
            <?php else: ?>
                <input type="submit" name="submit" value="Отправить на проверку">
            <?php endif; ?>
        </form>
    </body>
</html>
