<?php

	/**
	*** Fichier de gestion des questions
	**/

	//Connexion à la base de données
	include "../includes/DB.php";

	if ($_POST) {
		if (isset($_POST['nt_theme'])) {
			// Ajout d'un nouveau thème
			$theme = $_POST['nt_theme'];
			$query = $bdd->query("INSERT INTO theme (`thm_id`, `thm_name`) 
											VALUES (NULL, '".$theme."')");
			header('Location: ../pages/adminView.php');
		}
		elseif (isset($_POST['nq_question'])) {
			var_dump($_POST);
			// Ajout d'un nouveau thème
			$question = $_POST['nq_question'];
			$theme = $_POST['nq_theme'];
			$answers = array();
			$answers['A'] = array('ans_text' => $_POST['nq_answerA'], 'ans_correct' => 0);
			$answers['B'] = array('ans_text' => $_POST['nq_answerB'], 'ans_correct' => 0);
			$answers['C'] = array('ans_text' => $_POST['nq_answerC'], 'ans_correct' => 0);
			$answers['D'] = array('ans_text' => $_POST['nq_answerD'], 'ans_correct' => 0);
			 
			$correct = array('correctA', 'correctB', 'correctC', 'correctD');
			foreach($correct as $el) {
				if (isset($_POST['nq_'.$el])) {
					$ident = str_replace('correct', '', $el);
					$answers[$ident]['ans_correct'] = 1;
				}
			}

			$query = $bdd->query("	INSERT INTO question (`qst_id`, `qst_text`, `qst_theme_id`) 
									VALUES (NULL, '".$question."', '".$theme."')");
			
			$idQuestion = $bdd->lastInsertId();

			foreach ($answers as $answer) {
				$query = $bdd->query("	INSERT INTO answer (`ans_id`, `ans_text`, `ans_question_id`, `ans_correct`) 
										VALUES (NULL, '".$answer['ans_text']."', '".$idQuestion."', '".$answer['ans_correct']."')");
			}
			
			header('Location: ../pages/adminView.php');
		}
	}

	if(session_id() == null){
		session_start();
	}	

	//Selection des thèmes
	$query = $bdd->query("SELECT thm_id, thm_name
						  FROM theme");
	$themes = $query->fetchAll();

	//Selection des questions
	$query = $bdd->query("	SELECT qst_id, qst_text, thm_name 
							FROM question
							INNER JOIN theme ON thm_id = qst_theme_id");
	$allQuestions = $query->fetchAll();
	$questions = array();
	foreach ($allQuestions as $question) {
		$query = $bdd->query("	SELECT ans_id, ans_text, ans_correct 
								FROM answer
								WHERE ans_question_id = ".$question['qst_id']);
		$answers = $query->fetchAll();
		$questions[$question['qst_id']] = array('text' => $question['qst_text'], 'answers' => $answers, 'theme' => $question['thm_name']);
	}
?>