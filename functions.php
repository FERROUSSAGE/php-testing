<?
    include 'settings/connect.php';

    function print_arr($arr){
        echo '<pre>' . 
        print_r($arr, true) .
        '</pre>';
    }

    function getListTest(){
        global $pdo;
        $stmt = $pdo->query('select * from test where enable = "1"');
        $data = array();
        while($row = $stmt->fetch()){
            $data[] = $row;
        }
        return $data;
    }

    function getTestData($id_test){
        if( $id_test == false) return;
        global $pdo;
        $stmt = $pdo->prepare("select q.*, a.* from
         questions as q left join answers as a on q.id_questions = a.id_question
         left join test on test.id_test = q.id_test where q.id_test = ?
         and test.enable = '1'");
        $stmt->execute(array($id_test));
        $data = null;
        while($row = $stmt->fetch()){
            if( !$row['id_questions']) return false;
            $data[$row['id_questions']][0] = $row['question_name'];
            $data[$row['id_questions']][$row['id_answers']] = $row['answers_name'];
        }
        return $data;
    }

    function getCorrectAnswers($test){
        if( !$test ) return false;
        global $pdo;
        $stmt = $pdo->prepare("select q.id_questions as question_id, a.id_answers
        as answer_id from questions as q left join answers as a 
        on q.id_questions = a.id_question left join test on test.id_test = q.id_test where q.id_test = ? and a.correct_answer = '1' and test.enable = '1';");
        $stmt->execute(array($test));
        $data = null;
        while($row = $stmt->fetch()){
            $data[$row['question_id']] = $row['answer_id'];
        }
        return $data;

    }

    function getTestDataResult($test_alldata, $result){
        //наполняем массив тестоллдата корректными ответами и неотвеченныи вопросами
        foreach($result as $question => $answer){
            $test_alldata[$question]['correct_answer'] = $answer;
            if(!isset($_POST[$question])){
                $test_alldata[$question]['incorrect_answer'] = 0;
            }
        }
        //наполняем неверными ответами
        foreach($_POST as $question => $answer){
            //удаляем несуществующие вопросы от пользователя
            if(!isset($test_alldata[$question])){
                unset($_POST[$question]);
                continue;
            }
            //удаляем несуществующие ответы от пользователя
            if(!isset($test_alldata[$question][$answer])){
                $test_alldata[$question]['incorrect_answer'] = 0;
                //считаем такие вопросы не отвеченными
                continue;
            }
            if($test_alldata[$question]['correct_answer'] != $answer){
                $test_alldata[$question]['incorrect_answer'] = $answer;
            }
        }
        return $test_alldata;
    }

    function getResultTest($test_alldata_result){
        $count_questions = count($test_alldata_result); //общие кол-во вопросов
        $count_correctAnswer = 0; // общее кол-во верных ответов
        $count_incorrectAnswer = 0; // общее кол-во неверных ответов
        $percent_correctAnswer = 0; //процент верных ответов

        foreach($test_alldata_result as $value){
            if( isset($value['incorrect_answer']))
                $count_incorrectAnswer++;
        }
        $count_correctAnswer = $count_questions - $count_incorrectAnswer;
        $percent_correctAnswer = ceil($count_correctAnswer / $count_questions * 100);
        
        $print_result = '<div class="content-output">';
            $print_result .= '<div class="count_result">';
                $print_result .= "<p>Всего вопросов: <b>{$count_questions}</b></p>";
                $print_result .= "<p>Из них верных: <b>{$count_correctAnswer}</b></p>";
                $print_result .= "<p>Из них неверных: <b>{$count_incorrectAnswer}</b></p>";
                $print_result .= "<p>% верных ответов: <b>{$percent_correctAnswer}</b></p>";
            $print_result .= '</div>';
            //печать теста
            foreach($test_alldata_result as $id_question => $value){//вопрос/ответ
                $correct_answer = $value['correct_answer'];
                $incorrect_answer = null;
                if(isset($value['incorrect_answer'])){
                    $incorrect_answer = $value['incorrect_answer'];
                    $class = 'question-result error';
                }else{
                    $class = 'question-result ok';
                }
                $print_result .= "<div class='$class'>";
                foreach($value as $id_answer => $answer){
                    if($id_answer === 0){
                        $print_result .= '<p class="question">'.$answer.'</p>';//вопрос
                    }
                    elseif(is_numeric($id_answer)){
                        if($id_answer == $correct_answer){
                            $class = 'answer ok-answer';
                        }
                        elseif($id_answer == $incorrect_answer){
                            $class = 'answer error-answer';
                        }
                        else{
                            $class = 'answer';
                        }
                        $print_result .= "<p class='$class'>$answer</p>";
                    }
                }
                $print_result .= '</div>';

            }
        $print_result .= '</div>';

        return $print_result;
    }

    function pagination($count_questions, $test_data){
        $keys = array_keys($test_data);
        $pagination = '<div class="pagination">';
        for($i = 1; $i <= $count_questions; $i++){
            $key = array_shift($keys);
            if( $i == 1 ){
                $pagination .= '<a class="nav-active" href="#question-'.$key.'">'.$i.'</a>';
            }
            else{
                $pagination .= '<a href="#question-'.$key.'">'.$i.'</a>';
            }
        }
        $pagination .= '</div>';
        return $pagination;
    }
