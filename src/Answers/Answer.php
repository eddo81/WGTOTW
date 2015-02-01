<?php

namespace Anax\Answers;
 
/**
 * Model for Answers.
 *
 */
class Answer extends \Anax\MVC\CDatabaseModel
{
	public function getAnswersQuestion($questionId)
	{
		$sql = "SELECT * FROM answer WHERE question_id = ?";
		$this->db->execute($sql, array($questionId));
		$res = $this->db->fetchAll();

		return $res;
	}

	/**
	 * Update answer_autor.
	 *
	 * @param string new $name and $id of author to answer.
	 *
	 * @return void
	 */
	public function setAnswerAuthor($nameIn, $id)
	{
		$sql = "UPDATE answer SET answer_author = ? WHERE user_id = ?";
		$this->db->execute($sql, array($nameIn, $id));
	}

	public function deleteAnswers($questionId)
	{
		$sql = "DELETE FROM answer WHERE question_id = ?";
		$this->db->execute($sql, array($questionId));
	}

}