<?php

namespace Anax\Questions;
 
/**
 * Model for Questions.
 *
 */
class Question extends \Anax\MVC\CDatabaseModel
{
	public function truncate($stringIn, $max = 30)
	{
		$stringOut = (strlen($stringIn) > $max) ? substr($stringIn, 0, $max) . '...' : $stringIn;
		return $stringOut;
	}

	/**
	 * Update question_autor.
	 *
	 * @param string new $name and $id of author to question.
	 *
	 * @return void
	 */
	public function setQuestionAuthor($nameIn, $id)
	{
		$sql = "UPDATE question SET question_author = ? WHERE user_id = ?";
		$this->db->execute($sql, array($nameIn, $id));
	}

	public function getTags()
	{
		$string = null;
		$html = null;

		$sql = "SELECT question_tags FROM question";
		$this->db->execute($sql);
		$res = $this->db->fetchAll();

		if(!empty($res))
		{
			foreach ($res as $tag) {
		    	
		    	$string .= ' ' . $tag->question_tags;
		    }

		    $string = trim($string);

		    $tags = explode(' ', $string);
		    $tags = array_unique($tags);
		    
		    $html = "<div class='overflow tagButtonWrapper'>";
				
				foreach ($tags as $tag)
				{	
					if($tag != null)
					{
						$html .= "<a class='button grey marginRightBottom left' href='" . $this->url->create('questions/tag/' . slugify($tag)) . "'> &#35;" . $this->truncate($tag, 12) . "</a>";
					}	
				}
		
			$html .= "</div>";
		}

	    return $html;  
	}

	public function fetchByTag($tag)
	{
		$tag = slugify($tag);

		$sql = "SELECT * FROM question WHERE question_slug like ?";
		$this->db->execute($sql, array('%'.$tag.'%'));
		$res = $this->db->fetchAll();

		return $res;
	}

	public function getQuestion($id)
	{
		
		$sql = "SELECT * FROM question WHERE id = ?";
		$this->db->execute($sql, array($id));
		$res = $this->db->fetchAll();

		return $res;
	}

	public function tagsFromQuestion($questionId)
	{
		$html = null;

		$sql = "SELECT question_tags FROM question WHERE id = ?";
		$this->db->execute($sql, array($questionId));
		$res = $this->db->fetchAll();
		
		if(!empty($res))
		{
			$string = trim($res[0]->question_tags);

		    $tags = explode(' ', $string);
		    $tags = array_unique($tags);

		    $html = "<div class='overflow tagButtonWrapper'>";
				
				foreach ($tags as $tag)
				{	
					if($tag != null)
					{	
						$html .= "<a class='button grey small marginRight left' href='" . $this->url->create('questions/tag/' . slugify($tag)) . "'> &#35;" . $this->truncate($tag, 12) . "</a>";
					}
				}
		
			$html .= "</div>";
		}

	    return $html;
	}

	public function getLatest()
	{
		$sql = "SELECT * FROM question ORDER BY created DESC LIMIT 5";
		$this->db->execute($sql);
		$res = $this->db->fetchAll();

		return $res;
	}

	public function getPopular()
	{
		$string = null;
		$html = null;

		$sql = "SELECT question_tags FROM question";
		$this->db->execute($sql);
		$res = $this->db->fetchAll();

		if(!empty($res))
		{
		    foreach ($res as $tag) {
		    	
		    	$string .= ' ' . $tag->question_tags;
		    }

		    $string = trim($string);

		    $tags = explode(' ', $string);

		    $arr = array_count_values($tags);

			arsort($arr);
		    
		    if(sizeof($arr) > 5)
		    {
		    	$arr = array_slice($arr, 0, 5);
		    }

		    $html = "<div class='overflow tagButtonWrapper'>";

		    foreach ($arr as $key => $value) 
		    {
		    	if($value != null)
				{
		   			$html .= "<a class='button grey small margrinFour left' href='" . $this->url->create('questions/tag/' . slugify($key)) . "'> &#35;" . $this->truncate($key, 12) . " ("  . $value . ")</a>";
		   		}
		    }

		    $html .= "</div>";
		}
		
	    return $html;
	}
}