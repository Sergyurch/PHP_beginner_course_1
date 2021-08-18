<?php
    $error = false;
    
    if ( isset($_GET['submit']) ) {
        if ( empty($_GET['net-salary']) ) {
            $error = true;
        } else {
            // Получаем входные данные
            $net_salary_hour = +$_GET['net-salary'] / 20 / 4; // Чистая ЗП за 1 час
            $company_income_tax = ( empty($_GET['company-income-tax']) ) ? 0 : +$_GET['company-income-tax'];
            $ndfl = ( empty($_GET['ndfl']) ) ? 0 : +$_GET['ndfl'];
            $medicine_tax = ( empty($_GET['medicine-tax']) ) ? 0 : +$_GET['medicine-tax'];
            $retire_tax = ( empty($_GET['retire-tax']) ) ? 0 : +$_GET['retire-tax'];
            $life_insurance_tax = ( empty($_GET['life-insurance-tax']) ) ? 0 : +$_GET['life-insurance-tax'];
            $company_net_income = ( empty($_GET['company-net-income']) ) ? 0 : +$_GET['company-net-income'];
            $management_salary = ( empty($_GET['management-salary']) ) ? 0 : +$_GET['management-salary'];
            $amortisation = ( empty($_GET['amortisation']) ) ? 0 : +$_GET['amortisation'];
            $rent_expenses = ( empty($_GET['rent-expenses']) ) ? 0 : +$_GET['rent-expenses'];
            $other_expenses = ( empty($_GET['other-expenses']) ) ? 0 : +$_GET['other-expenses'];
            $marketing_expenses = ( empty($_GET['marketing-expenses']) ) ? 0 : +$_GET['marketing-expenses'];

            // Зарплата за 1час до налогообложения и соц. отчислений
            $salary_before_taxes = $net_salary_hour / ( 1 - ($ndfl + $medicine_tax + $retire_tax + $life_insurance_tax) / 100 );
            
            // Размер постоянных расходов в % от оборота
            $permanent_expenses = $management_salary + $amortisation + $rent_expenses + $other_expenses + $marketing_expenses;

            // Размер дохода компании в % от оборота до налогообложения
            $company_income_before_taxes = $company_net_income / (1 - $company_income_tax / 100);
        
            $result = $salary_before_taxes / ( 1 - ($permanent_expenses + $company_income_before_taxes)/100 );
            $result = ceil($result * 10) / 10;
        }
    }
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>Lesson 155</title>
        <style>
            div {
                margin-bottom: 15px;
            }

            input[type="number"] {
                width: 100px;
            }

            .error-msg {
                padding: 20px;
                background-color: red;
            }

            .result {
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <h1>Калькулятор расчета стоимости часа</h1>
        <form method="GET">
            <p>Заполните поля нужными данными. Пустые поля при расчетах игнорируются.</p>
            <p>Поле с желаемой ЗП обязательно к заполнению.</p>
            <?php if ($error): ?>
                <div class="error-msg">Вы не указали желаемую ЗП</div>
            <?php endif; ?>
            <div>
                <label>
                    <input type="number" name="net-salary" min="1" max="1000000" value="<?php echo ( isset($_GET['net-salary']) ) ? $_GET['net-salary'] : '50000'; ?>">
                    <span> руб. </span>
                    <span> Желаемая ЗП</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="company-income-tax" min="0" max="100" value="<?php echo ( isset($_GET['company-income-tax']) ) ? $_GET['company-income-tax'] : '6'; ?>">
                    <span> %. </span>
                    <span>Налог на прибыль юрлиц - УСН, ОСНО и др</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="ndfl" min="0" max="100" value="<?php if ( isset($_GET['ndfl']) ) echo $_GET['ndfl']; ?>">
                    <span> %. </span>
                    <span>Налог на прибыль физлиц - НДФЛ</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="medicine-tax" min="0" max="100" value="<?php if ( isset($_GET['medicine-tax']) ) echo $_GET['medicine-tax']; ?>">
                    <span> %. </span>
                    <span>Отчисление на бесплатную медицину</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="retire-tax" min="0" max="100" value="<?php if ( isset($_GET['retire-tax']) ) echo $_GET['retire-tax']; ?>">
                    <span> %. </span>
                    <span>Отчисление в ПФР</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="life-insurance-tax" min="0" max="100" value="<?php if ( isset($_GET['life-insurance-tax']) ) echo $_GET['life-insurance-tax']; ?>">
                    <span> %. </span>
                    <span>Отчисление на страхование жизни</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="company-net-income" min="0" max="100" value="<?php echo ( isset($_GET['company-net-income']) ) ? $_GET['company-net-income'] : '10'; ?>">
                    <span> %. </span>
                    <span>Прибыль компании</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="management-salary" min="0" max="100" value="<?php if ( isset($_GET['management-salary']) ) echo $_GET['management-salary']; ?>">
                    <span> %. </span>
                    <span>Зарплата руководства от оборота компании</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="amortisation" min="0" max="100" value="<?php if ( isset($_GET['amortisation']) ) echo $_GET['amortisation']; ?>">
                    <span> %. </span>
                    <span>Расходы на технику, амортизацию оборудования, канцелярию от оборота</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="rent-expenses" min="0" max="100" value="<?php if ( isset($_GET['rent-expenses']) ) echo $_GET['rent-expenses']; ?>">
                    <span> %. </span>
                    <span>Расходы на снятие офиса от оборота</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="other-expenses" min="0" max="100" value="<?php if ( isset($_GET['other-expenses']) ) echo $_GET['other-expenses']; ?>">
                    <span> %. </span>
                    <span>Расходы на бухгалтерию, и прочее</span>
                </label>
            </div>
            <div>
                <label>
                    <input type="number" name="marketing-expenses" min="0" max="100" value="<?php if ( isset($_GET['marketing-expenses']) ) echo $_GET['marketing-expenses']; ?>">
                    <span> %. </span>
                    <span>Расходы на маркетинг и продажи от выручки</span>
                </label>
            </div>
            <input type="submit" name="submit" value="Показать результат">
        </form>
        <?php if ( isset($result) ): ?>
            <div class="result">Стоимость 1 часа работы: <?= $result; ?> руб.</div>
        <?php endif; ?>
    </body>
</html>
