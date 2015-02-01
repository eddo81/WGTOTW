<?php

namespace Anax\Tables;
 
/**
 * A controller for users and admin related events.
 *
 */
class TablesController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize()
	{
	    $this->tables = new \Anax\Tables\Table();
	    $this->tables->setDI($this->di);
	}

	/**
	 * Setup the database table.
	 *
	 * @return void
	 */
	public function setupAction()
	{
		//$this->db->setVerbose();

		$user = $this->session->get('user');

		if($user === null)
		{
			$url = $this->url->create('users/login');
        	$this->response->redirect($url);
		}
		elseif($user->id != 1)
		{
			$url = $this->url->create('');
        	$this->response->redirect($url);
		}
		else
		{
			$this->db->dropTableIfExists('user')->execute();
	 
	    $this->db->createTable(
	        'user',
	        [
	            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
	            'name' => ['varchar(80)', 'unique', 'not null'],
	            'email' => ['varchar(80)'],
	            'web' => ['varchar(80)'],
	            'password' => ['varchar(255)'],
	            'gravatar' => ['varchar(80)'],
	            'created' => ['datetime'],
	            'activity' => ['integer'],
	        ]
	    )->execute();

	    $this->db->insert(
	        'user',
	        ['name', 'email', 'web', 'password', 'gravatar', 'created', 'activity']
	    );
	 
	    $now = gmdate('Y-m-d H:i:s');
	 
	    $this->db->execute([
	        'admin',
	        'admin@dbwebb.se',
	        'http://dbwebb.se/',
	        password_hash('admin', PASSWORD_DEFAULT),
	        'http://www.gravatar.com/avatar/' . md5(strtolower(trim("admin@dbwebb.se"))) . '.jpg',
	        $now,
	        2
	    ]);
	 
	    $this->db->execute([
	        'ryukyu',
	        'ryukyu@dbwebb.se',
	        'http://dbwebb.se/',
	        password_hash('ryukyu', PASSWORD_DEFAULT),
	        'http://www.gravatar.com/avatar/' . md5(strtolower(trim("doe@dbwebb.se"))) . '.jpg',
	        $now,
	        2
	    ]);

	    $this->db->execute([
	        'serendib',
	        'serendib@dbwebb.se',
	        'http://dbwebb.se/',
	        password_hash('serendib', PASSWORD_DEFAULT),
	        'http://www.gravatar.com/avatar/' . md5(strtolower(trim("doe@dbwebb.se"))) . '.jpg',
	        $now,
	        0
	    ]);

	    $this->db->execute([
	        'sokoke',
	        'sokoke@dbwebb.se',
	        'http://dbwebb.se/',
	        password_hash('sokoke', PASSWORD_DEFAULT),
	        'http://www.gravatar.com/avatar/' . md5(strtolower(trim("doe@dbwebb.se"))) . '.jpg',
	        $now,
	        0
	    ]);

	     $this->db->execute([
	        'palawan',
	        'palawan@dbwebb.se',
	        'http://dbwebb.se/',
	        password_hash('palawan', PASSWORD_DEFAULT),
	        'http://www.gravatar.com/avatar/' . md5(strtolower(trim("doe@dbwebb.se"))) . '.jpg',
	        $now,
	        0
	    ]);

	    $this->db->execute([
	        'mindoro',
	        'mindoro@dbwebb.se',
	        'http://dbwebb.se/',
	        password_hash('mindoro', PASSWORD_DEFAULT),
	        'http://www.gravatar.com/avatar/' . md5(strtolower(trim("doe@dbwebb.se"))) . '.jpg',
	        $now,
	        0
	    ]);

	    $this->db->execute([
	        'luzon',
	        'luzon@dbwebb.se',
	        'http://dbwebb.se/',
	        password_hash('luzon', PASSWORD_DEFAULT),
	        'http://www.gravatar.com/avatar/' . md5(strtolower(trim("doe@dbwebb.se"))) . '.jpg',
	        $now,
	        0
	    ]);

	    $this->db->execute([
	        'enggano',
	        'enggano@dbwebb.se',
	        'http://dbwebb.se/',
	        password_hash('enggano', PASSWORD_DEFAULT),
	        'http://www.gravatar.com/avatar/' . md5(strtolower(trim("doe@dbwebb.se"))) . '.jpg',
	        $now,
	        0
	    ]);

	    //Create questions-table---------------------------------//

	    $this->db->dropTableIfExists('question')->execute();
	 
	    $this->db->createTable(
	        'question',
	        [
	            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
	            'user_id' => ['integer', 'not null'],
	            'question_title' => ['varchar(80)', 'not null'],
	            'question_content' => ['text', 'not null'],
	            'question_tags' => ['text'],
	            'question_slug' => ['text'],
	            'question_author' => ['varchar(80)', 'not null'],
	            'created' => ['datetime'],
	        ]
	    )->execute();


	    $this->db->insert(
	        'question',
	        ['user_id', 'question_title', 'question_content', 'question_tags', 'question_slug', 'question_author' , 'created']
	    );
	 
	    $this->db->execute([
	        1,
	        'Vilken art?',
	        'Jag har en uggla på tomten, hur tar man reda på vilken art av uggla det är? ',
	        'art ugglor övrigt',
	        'art ugglor vrigt',
	        'admin',
	        $now
	    ]);

	    $this->db->execute([
	        1,
	        'Ugglor i mossen?',
	        'Finns det ett fulare uttryck än just "ana ugglor i mossen"?',
	        'ugglor mossen övrigt',
	        'ugglor mossen vrigt',
	        'admin',
	        $now
	    ]);

	    $this->db->execute([
	        2,
	        'Riddarfalken ifrån malta?',
	        'På tal om rovfåglar, har någon av er sett den ovan nämnda filmen?',
	        'riddarfalk malta rovfågel',
	        'riddarfalk malta rovf-gel',
	        'ryukyu',
	        $now
	    ]);

	    $this->db->execute([
	        2,
	        'Owlbear, tuffaste fabeldjuret?',
	        'Uggla + björn måste vara höjden av coolhet, eller?',
	        'ugglor björnar blytugnt stentufft',
	        'ugglor bj-rnar blytugnt stentufft',
	        'ryukyu',
	        $now
	    ]);

	    //Create answers-table---------------------------------//

	    $this->db->dropTableIfExists('answer')->execute();
	 
	    $this->db->createTable(
	        'answer',
	        [
	            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
	            'question_id' => ['integer', 'not null'],
	            'user_id' => ['integer', 'not null'],
	            'answer_content' => ['text', 'not null'],
	            'answer_author' => ['varchar(80)', 'not null'],
	            'created' => ['datetime'],
	        ]
	    )->execute();

	    //Create comments-table---------------------------------//

	    $this->db->dropTableIfExists('comment')->execute();
	 
	    $this->db->createTable(
	        'comment',
	        [
	            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
	            'question_id' => ['integer', 'not null'],
	            'answer_id' => ['integer'],
	            'user_id' => ['integer', 'not null'],
	            'comment_content' => ['text', 'not null'],
	            'comment_author' => ['varchar(80)', 'not null'],
	            'created' => ['datetime'],
	        ]
	    )->execute();

	    $url = $this->url->create('users/list');
	    $this->response->redirect($url);
	    
		}
	 
	    
	}


}