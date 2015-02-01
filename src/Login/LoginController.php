<?php

namespace Anax\Users;
 
/**
 * A controller for users and admin related events.
 *
 */
class UsersController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize()
	{
	    $this->users = new \Anax\Users\User();
	    $this->users->setDI($this->di);
	}

	/**
	 * Setup the database table.
	 *
	 * @return void
	 */
	public function setupAction()
	{
		//$this->db->setVerbose();
	 
	    $this->db->dropTableIfExists('user')->execute();
	 
	    $this->db->createTable(
	        'user',
	        [
	            'id' => ['integer', 'primary key', 'not null', 'auto_increment'],
	            'acronym' => ['varchar(20)', 'unique', 'not null'],
	            'email' => ['varchar(80)'],
	            'name' => ['varchar(80)'],
	            'password' => ['varchar(255)'],
	            'created' => ['datetime'],
	            'updated' => ['datetime'],
	            'deleted' => ['datetime'],
	            'active' => ['datetime'],
	        ]
	    )->execute();

	    $this->db->insert(
	        'user',
	        ['acronym', 'email', 'name', 'password', 'created', 'active']
	    );
	 
	    $now = gmdate('Y-m-d H:i:s');
	 
	    $this->db->execute([
	        'admin',
	        'admin@dbwebb.se',
	        'Administrator',
	        password_hash('admin', PASSWORD_DEFAULT),
	        $now,
	        $now
	    ]);
	 
	    $this->db->execute([
	        'doe',
	        'doe@dbwebb.se',
	        'John/Jane Doe',
	        password_hash('doe', PASSWORD_DEFAULT),
	        $now,
	        $now
	    ]);

	    $url = $this->url->create('users/list');
	    $this->response->redirect($url);
	}


	/**
	 * List all users.
	 *
	 * @return void
	 */
	public function listAction()
	{
	    $all = $this->users->findAll();
	    $disabled = null;

	    foreach ($all as $key => $user) {
	    	if ($user->deleted !== null || $user->active === null) {
	    		$disabled[$key] = "disabled";
	    	}
	    	else
	    	{
	    		$disabled[$key] = null;
	    	}
	    } 

	    $this->theme->setTitle("Alla användare");
	    $this->navbar->configure(ANAX_APP_PATH . 'config/navbar_users.php');
	    $this->views->add('users/list-all', [
	        'users' => $all,
	        'title' => "Alla användare",
	        'disabled' => $disabled,
	    ]);
	}

	/**
	 * List all active and not deleted users.
	 *
	 * @return void
	 */
	public function listActiveAction()
	{
	    $all = $this->users->query()
	        ->where('active IS NOT NULL')
	        ->andWhere('deleted is NULL')
	        ->execute();
	 	$this->navbar->configure(ANAX_APP_PATH . 'config/navbar_users.php');
	    $this->theme->setTitle("Aktiva användare");
	    $this->views->add('users/list-active', [
	        'users' => $all,
	        'title' => "Aktiva användare",
	    ]);
	}

	/**
	 * List all inactive and not deleted users.
	 *
	 * @return void
	 */
	public function listInactiveAction()
	{
	    $all = $this->users->query()
	        ->where('active is NULL')
	        ->andWhere('deleted is NULL')
	        ->execute();
	 	$this->navbar->configure(ANAX_APP_PATH . 'config/navbar_users.php');
	    $this->theme->setTitle("Inaktiva användare");
	    $this->views->add('users/list-inactive', [
	        'users' => $all,
	        'title' => "Inaktiva användare",
	    ]);
	}

	/**
	 * List all deleted users.
	 *
	 * @return void
	 */
	public function listDeletedAction()
	{
	    $all = $this->users->query()
	        ->where('deleted IS NOT NULL')
	        ->execute();
	 	$this->navbar->configure(ANAX_APP_PATH . 'config/navbar_users.php');
	    $this->theme->setTitle("Papperskorgen");
	    $this->views->add('users/list-deleted', [
	        'users' => $all,
	        'title' => "Papperskorgen",
	    ]);
	}

	/**
	 * List user with id.
	 *
	 * @param int $id of user to display
	 *
	 * @return void
	 */
	public function idAction($id = null)
	{
	    $user = $this->users->find($id);
	 	$this->navbar->configure(ANAX_APP_PATH . 'config/navbar_users.php');
	    $this->theme->setTitle("Visa användare efter id");
	    $this->views->add('users/list-one', [
	        'user' => $user,
	        'title' => $user->name,
	    ]);
	}

	/**
	 * Add new user.
	 *
	 * @param string $acronym of user to add.
	 *
	 * @return void
	 */
	public function addAction($acronym = null)
	{
		$this->navbar->configure(ANAX_APP_PATH . 'config/navbar_users.php');
        $this->theme->setTitle('Lägg till användare');  

        $session = $this->session();  

        $form = $this->form->create(  
            [   
                'legend' => 'Ny användare',  
            ], 
            
            [   'acronym' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Acronym:',  
                'required'    => true,  
                'validation'  => ['not_empty'], 
            ],
            
            'email' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'E-post:',  
                'required'    => true,  
                'validation'  => ['not_empty', 'email_adress'], 
            ],  
            
            'name' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Namn:',  
                'required'    => true,  
                'validation'  => ['not_empty'], 
            ],    
            
            'password' => [  
                'type'        => 'password',
                'class'		  => 'input',  
                'label'       => 'Lösenord:',  
                'required'    => true,  
                'validation'  => ['not_empty'],  
            ],    
            
            'submit' => [  
                'type'      => 'submit',
                'class' 	=> 'button',
                'value' 	=> 'Spara',  
                'callback'  => function($form) 
                {

                	$now = date("Y-m-d H:i:s");  
           
		            $this->users->save([  
		                'acronym' 	=> $form->Value('acronym'),  
		                'email' 	=> $form->Value('email'),  
		                'name' 		=> $form->Value('name'),  
		                'password' 	=> password_hash($form->Value('password'), PASSWORD_DEFAULT),  
		                'created' 	=> $now,  
		                'active' 	=> $now,  
		            ]); 

                    return true;  
                }  
            ],  
        ]); 

        $status = $form->check();  

        if ($status === true) 
        {  
            $url = $this->url->create('users/list');  
            $this->response->redirect($url);  
        }

        else if ($status === false) 
        {  	
        	$url = $this->url->create('users/add');
            $this->response->redirect($url);
            $form->AddOutput("<p>Ett fel inträffade. Försökt igen.</p>");  
        }  

        $this->views->add('users/form', [    
            'content' => $form->getHTML(),    
        ]);
	}

	/**
	 * Edit user.
	 *
	 * @param string $acronym of user to add.
	 *
	 * @return void
	 */
	public function editAction($id = null)
	{
		if (!isset($id) || !is_numeric($id)) {
	        die("Missing or faulty id");
	    }

		$user = $this->users->find($id); 

		$this->navbar->configure(ANAX_APP_PATH . 'config/navbar_users.php');
        $this->theme->setTitle('Uppdatera användare');  

        $session = $this->session();  

        $form = $this->form->create(  
            [   
                'legend' => 'Uppdatera användare',  
            ], 
            
            [   'acronym' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Acronym:',  
                'required'    => true,  
                'validation'  => ['not_empty'],
                'value'        => $user->acronym, 
            ],
            
            'email' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'E-post:',  
                'required'    => true,  
                'validation'  => ['not_empty', 'email_adress'],
                'value'        => $user->email, 
            ],  
            
            'name' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Namn:',  
                'required'    => true,  
                'validation'  => ['not_empty'],
                'value'        => $user->name, 
            ],    
            
            'submit' => [  
                'type'      => 'submit',
                'class' 	=> 'button',
                'value' 	=> 'Spara',  
                'callback'  => function($form) 
                {

                	$now = date("Y-m-d H:i:s");  
           
		            $this->users->save([  
		                'acronym' 	=> $form->Value('acronym'),  
		                'email' 	=> $form->Value('email'),  
		                'name' 		=> $form->Value('name'),    
		                'created' 	=> $now,  
		                'active' 	=> $now,  
		            ]); 

                    return true;  
                }  
            ],  
        ]); 

        $status = $form->check();  

        if ($status === true) 
        {  
            $url = $this->url->create('users/list');  
            $this->response->redirect($url);  
        }

        else if ($status === false) 
        {  	
        	$url = $this->url->create('users/edit');
            $this->response->redirect($url);
            $form->AddOutput("<p>Ett fel inträffade. Försökt igen.</p>");  
        }  

        $this->views->add('users/form', [    
            'content' => $form->getHTML(),    
        ]);
	}

	/**
	 * Inactivate user.
	 *
	 * @param integer $id of user to inactivate.
	 *
	 * @return void
	 */
	public function inactivateAction($id = null)
	{
	    if (!isset($id)) {
	        die("Missing id");
	    }

	    $user = $this->users->find($id);
	 
	    $user->active = null;
	    $user->save();
	 
	    $url = $this->url->create('users/list');
	    $this->response->redirect($url);
	}

	/**
	 * Activate user.
	 *
	 * @param integer $id of user to inactivate.
	 *
	 * @return void
	 */
	public function activateAction($id = null)
	{
	    if (!isset($id)) {
	        die("Missing id");
	    }

	    $now = gmdate('Y-m-d H:i:s');
	 
	    $user = $this->users->find($id);
	 
	    $user->active = $now;
	    $user->save();
	 
	    $url = $this->url->create('users/list');
	    $this->response->redirect($url);
	}

	/**
	 * Delete user.
	 *
	 * @param integer $id of user to delete.
	 *
	 * @return void
	 */
	public function deleteAction($id = null)
	{
	    if (!isset($id)) {
	        die("Missing id");
	    }
	 
	    $res = $this->users->delete($id);
	 
	    $url = $this->url->create('users/list');
	    $this->response->redirect($url);
	}

	/**
	 * Delete (soft) user.
	 *
	 * @param integer $id of user to delete.
	 *
	 * @return void
	 */
	public function softDeleteAction($id = null)
	{
	    if (!isset($id)) {
	        die("Missing id");
	    }
	 
	    $now = gmdate('Y-m-d H:i:s');
	 
	    $user = $this->users->find($id);
	 
	    $user->deleted = $now;
	    $user->save();
	 
	    $url = $this->url->create('users/list');
	    $this->response->redirect($url);
	}

	public function undoDeleteAction($id = null)
	{
	    if (!isset($id)) {
	        die("Missing id");
	    }
	 
	    $user = $this->users->find($id);
	 
	    $user->deleted = null;
	    $user->save();
	 
	    $url = $this->url->create('users/list');
	    $this->response->redirect($url);
	}

}