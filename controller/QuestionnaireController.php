<?php

require './model/Questionario.php';
require './Components/ApiComponent.php';

class QuestionnaireController {

    protected function _criarEstruturaQuestao(array $postData, $update = true) {
        $questionario = new Questionario();

        $requiredFields = array(
            'id_usuario',
            'id_curso',
        );

        $questionStructure = array();

        foreach ($requiredFields as $requiredField) {
            if (!isset($postData[$requiredField]) || empty($postData[$requiredField])) {
                echo 'não pode ser vazio';
                return;
            }

            $data[$requiredField] = $_POST[$requiredField];
        }

        //$curseStatus = $questionario->makeQuery("SELECT * FROM curso_usuario WHERE usuario_id = " . $data['id_usuario'] ." AND curso_id = " . $data['id_curso'] ."");
        //$data['id_usuario'] = $curseStatus[0]['questao_atual'];
   
        $answeredQuestions = $questionario->makeQuery("SELECT * FROM answers WHERE usuario_id = " . $data['id_usuario'] ." AND curso_id = " .$data['id_curso'] . " ORDER BY id DESC  LIMIT 1");
        $currentQuestion = array();

        if (!empty($answeredQuestions)) {
            $child_of = $answeredQuestions[0]['question_id'];
            $if_answer = $answeredQuestions[0]['answer_reference_id'];

            $currentQuestion = $questionario->makeQuery(
                "SELECT q.id AS question_id, q.question, q.teacher_text, asr.id AS resposta_id, asr.text AS resposta, q.if_answer
                FROM questions AS q INNER JOIN
                answer_references AS asr
                ON q.id = asr.mensagem_id
                WHERE q.curso_id = " . $data['id_curso'] .
                " AND q.child_of = " . $child_of . ""
            );
            
            $checkQuestion = array();

            foreach ($currentQuestion as $question) {
                if (strpos($question['if_answer'], ',')) {
                    $explodeIfAnswer = explode(',', $question['if_answer']);
                    if (in_array($if_answer, $explodeIfAnswer)) {
                        $checkQuestion[] = $question;
                    }
                } else {
                    if ($question['if_answer'] == $if_answer) {
                        $checkQuestion[] = $question;
                    }
                }
            }

            $currentQuestion = array();
            $currentQuestion = $checkQuestion;

        } else {
            $currentQuestion = $questionario->makeQuery(
                "SELECT q.id AS question_id, q.question, q.teacher_text, asr.id AS resposta_id, asr.text AS resposta FROM questions AS q INNER JOIN
                answer_references AS asr
                ON q.id = asr.mensagem_id
                WHERE curso_id = " .$data['id_curso'] .
                " AND child_of = 0"
            );
        }

        foreach ($currentQuestion as $question) {
            $questionStructure['Questao'] = array(
                'id_questao' => $question['question_id'],
                'texto_professor' => $question['teacher_text'],
                'texto_questao' => $question['question'],
            );

            $questionStructure['Resposta'][] = array(
                'id_resposta' => $question['resposta_id'],
                'texto_resposta' => $question['resposta'],
            );
        }

        return ApiComponent::jsonResponse($questionStructure, 200);
    }

    public function retornarQuestaoAtual() {
        $this->_criarEstruturaQuestao($_POST);
    }

    public function responderQuestao() {
        $questionario = new Questionario();

        $saveAnswer = array(
            'usuario_id' => $_POST['id_usuario'],
            'question_id' => $_POST['id_questao'],
            'answer_reference_id' => $_POST['id_resposta'],
            'curso_id' => $_POST['id_curso'],
        );

        $updateCourse = array(
            'id' => 1,
            'usuario_id' => (int)$_POST['id_usuario'],
            'curso_id' => (int)$_POST['id_curso'],
            'questao_atual' => 1,
        );

        $questionario->save('answers', $saveAnswer);

        $questionario->update('curso_usuario', $updateCourse);

        $postData = array(
            'id_usuario' => $_POST['id_usuario'],
            'id_curso' => $_POST['id_curso'],
        );

        $this->_criarEstruturaQuestao($postData);
    }
}