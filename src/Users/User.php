<?php

namespace Anax\Users;
 
/**
 * Model for Users.
 *
 */
class User extends \Anax\MVC\CDatabaseModel
{
	public function setActivity($id = null)
	{
		$sql = "UPDATE user SET activity = activity + 1 WHERE id = ?";
		$this->db->execute($sql, array($id));
	}

	public function getActive()
	{
		$sql = "SELECT * FROM user WHERE activity > 0 ORDER BY activity DESC LIMIT 3";
		$this->db->execute($sql);
		$res = $this->db->fetchAll();

		return $res;
	}
}

