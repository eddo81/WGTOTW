<?php

namespace Anax\Comments;
 
/**
 * Model for Comments.
 *
 */
class Comment extends \Anax\MVC\CDatabaseModel
{
	public function getQuestionComments($questionId = null)
    {
        $sql = "SELECT * FROM comment WHERE question_id = ? AND answer_id IS NULL";
        $this->db->execute($sql, array($questionId));
        $res = $this->db->fetchAll();

        return $res;
    }

    public function getAnswerComments($answerId = null)
    {
        $sql = "SELECT * FROM comment WHERE answer_id = ?";
        $this->db->execute($sql, array($answerId));
        $res = $this->db->fetchAll();

        return $res;
    }

    public function getUserComments($userId = null)
    {
        $sql = "SELECT * FROM comment WHERE user_id = ?";
        $this->db->execute($sql, array($userId));
        $res = $this->db->fetchAll();

        return $res;
    }

    public function deleteByQuestion($questionId = null)
    {
        $sql = "DELETE FROM comment WHERE question_id = ?";
        $this->db->execute($sql, array($questionId));
    }

    public function deleteByAnswer($answerId)
    {
        $sql = "DELETE FROM comment WHERE answer_id = ?";
        $this->db->execute($sql, array($answerId));
    }

    /**
     * Update comment_autor.
     *
     * @param string new $name and $id of author to answer.
     *
     * @return void
     */
    public function setCommentAuthor($nameIn, $id)
    {
        $sql = "UPDATE comment SET comment_author = ? WHERE user_id = ?";
        $this->db->execute($sql, array($nameIn, $id));
    }

}