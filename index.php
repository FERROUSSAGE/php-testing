<?
    require_once 'settings/config.php';
    require_once 'functions.php';
    require_once 'settings/connect.php';

    if( isset($_POST['test']) ){
        $test = intval($_POST['test']);
        $result = getCorrectAnswers($test);
        unset($_POST['test']);
        if(!is_array($result)) exit("Тест не закончен, не является массивом!");
        $test_alldata = getTestData($test);
        $test_alldata_result = getTestDataResult($test_alldata, $result, $_POST);
        echo getResultTest($test_alldata_result);
        die();
    }

    /**Получение тестов */
    $tests = getListTest();

    if( isset($_GET['test'])){
        $id_test = intval($_GET['test']);
        $test_data = getTestData($id_test);
        if( is_array($test_data)){
            $count_questions = count($test_data);
            $pagination = pagination($count_questions, $test_data);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/css/main.css">
    <title>testPage</title>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <? if($tests): ?>
                <span>Доступные тесты:</span>
                <? foreach($tests as $value): ?>
                    <h3><a href="?test=<?=$value['id_test'] ?>"><?=$value["test_name"]?></a></h3>
                <? endforeach; ?>
            <? else:?>
                <span>Доступных тестов нет</span>
            <? endif; ?>
        </div>

        <div class="content">
            <? if( isset($test_data) ) :?>
                <? if( is_array($test_data) ): ?>
                <div class="content-title">
                    <span>Общее количество вопросов - <?=$count_questions?></span>
                    <?=$pagination;?>
                    <span class="none" id="id_test"><?=$id_test?></span>
                </div>
                <div class="content-output">
                    <? foreach($test_data as $id_question => $value): //Перебор вопросов и ответов?>
                        <div class="output-question" data-id="<?=$id_question?>" id="question-<?=$id_question?>">
                            <? foreach($value as $id_answer => $answer): ?>
                                <? if( !$id_answer ): ?>
                                    <p class="q"><?=$answer?></p>
                                <? else: ?>
                                    <p class="a">
                                        <input type="radio" name="question-<?=$id_question?>" id="answer-<?=$id_answer?>" value="<?=$id_answer?>">
                                        <label for="answer-<?=$id_answer?>"><?=$answer?></label>
                                    </p>
                                <? endif; ?>
                            <? endforeach; ?>
                        </div>
                    <? endforeach;?>
                </div>
                <div class="buttons">
                    <button class="btn" id="btn-close">Закончить тест</button>
                </div>
                <? endif;?>
            <? else: ?>
                Выберите тест из вышедоступных!
            <? endif?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>