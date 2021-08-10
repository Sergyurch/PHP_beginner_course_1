<?php
    error_reporting(E_ALL);
    $mail_to = 'sergyurch@gmail.com';
    $subject = 'Заявка с сайта www.домен.ру';
    $errors = [];

    $required_field_names = [
        'name' => 'Ваше имя',
        'email' => 'Ваш e-mail',
        'text' => 'Сообщение',
        'question' => 'Антибот проверка'
    ];
    
    $captcha_questions = [
        ['question' => 'Третья планета от Солнца', 'answer' => 'Земля'],
        ['question' => 'На дворе трава, на траве...', 'answer' => 'дрова'],
        ['question' => 'Сколько дней в высокосном году?', 'answer' => '366'],
        ['question' => 'Наша Таня громко плачет, уронила в речку ...', 'answer' => 'мячик'],
        ['question' => 'Как называется столица Великобритании?', 'answer' => 'Лондон']
    ];

    $question_number = rand(0, count($captcha_questions) - 1); // Выбираем случайный вопрос
    
    // Если форма была отправлена
    if ( isset($_POST['submit']) ) {
        // Проверяем поля
        foreach($_POST as $field_name => $value) {
            if ( !in_array($field_name, ['phone', 'submit', 'question-number']) ) {
                if ( empty($value) ) {
                    $errors[] = "Не заполнено поле \"$required_field_names[$field_name]\"";
                } elseif ( $field_name == 'question' && $value != $captcha_questions[$_POST['question-number']]['answer'] ) {
                    $errors[] = "Некорректно заполнено поле \"$required_field_names[$field_name]\"";
                }
            }
        }

        // Если поля были заполнены, отправляем письмо
        if ( empty($errors) ) {
            $name = $_POST['name'];
            $email = $_POST['email'];
            $phone = $_POST['phone'];
            $text = $_POST['text'];
            $mail_text = "Имя: $name\r\nПочта: $email\r\nТелефон: $phone\r\n$text";
            
            header("Location: index.php?success=1&name=$name");
            if ( mail($mail_to, $subject, $mail_text) ) {
                header("Location: index.php?success=1&name=$name");
            }
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 133</title>
        <style>
            .success-msg {
                padding: 20px;
                background-color: green;
                font-size: 20px;
            }

            .error-msg {
                padding: 20px;
                background-color: red;
                font-size: 20px;
            }

            input[type="submit"] {
                margin-top: 20px;
            }
        </style>
    <body>
        <!-- Если сообщение отправлено -->
        <?php if ( isset($_GET['success']) && isset($_GET['name']) ): ?>
            <div class="success-msg"><?= $_GET['name']; ?>, ваше сообщение успешно отправлено.</div>
            <a href="index.php">Отправить еще раз</a>
        <?php else: ?>
            <!-- Если есть ошибки -->
            <?php if ( !empty($errors) ): ?>
                <?php foreach ($errors as $error): ?>
                    <div class="error-msg">"<?= $error; ?>"</div>
                <?php endforeach; ?>
            <?php endif; ?>
            <form method="POST">
                <div>
                    <label>
                        <span>Ваше имя</span><br>
                        <input type="text" name="name" value="<?php if ( isset($_POST['name']) ) echo $_POST['name']; ?>">
                    </label>
                </div>
                <div>
                    <label>
                        <span>Ваше e-mail</span><br>
                        <input type="email" name="email" value="<?php if ( isset($_POST['email']) ) echo $_POST['email']; ?>">
                    </label>
                </div>
                <div>
                    <label>
                        <span>Ваше телефон</span><br>
                        <input type="tel" name="phone">
                    </label>
                </div>
                <div>
                    <label>
                        <span>Сообщение</span><br>
                        <textarea name="text" rows="5" cols="35"><?php if ( isset($_POST['text']) ) echo $_POST['text']; ?></textarea>
                    </label>
                </div>
                <div>
                    <p>Антибот проверка:</p>
                    <label>
                        <span><?= $captcha_questions[$question_number]['question'] ?></span><br>
                        <input type="hidden" name="question-number" value="<?= $question_number; ?>">
                        <input type="text" name="question">
                    </label>
                </div>
                <input type="submit" name="submit" value="Отправить">
            </form>
        <?php endif; ?>
    </body>
</html>
