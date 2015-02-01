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

	    $this->questions = new \Anax\Questions\Question();
	    $this->questions->setDI($this->di);

	    $this->answers = new \Anax\Answers\Answer();
	    $this->answers->setDI($this->di);

	    $this->comments = new \Anax\Comments\Comment();
        $this->comments->setDI($this->di);
	}

	/**
	 * List all users.
	 *
	 * @return void
	 */
	public function listAction()
	{
	    $all = $this->users->query()
	        ->execute();

	    $this->theme->setTitle("Användare");
	    $this->views->add('users/list-all', [
	        'users' => $all,
	        'title' => "Användare",
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

		//Redirect if id is not valid
		if(!is_numeric($id) || $user === false)
		{
			$url = $this->url->create('users/list');
        	$this->response->redirect($url);
		}

	    $this->theme->setTitle('Profil');
	    $this->views->add('users/list-one', [
	        'user' => $user,
	        'title' => 'Profil',
	    ]);

	    $this->dispatcher->forward([
			'controller' => 'questions', 
        	'action'     => 'fetch', 
        	'params' => ['id' => $user->id],
		]);

		$this->dispatcher->forward([
			'controller' => 'answers', 
        	'action'     => 'fetch', 
        	'params' => ['id' => $user->id],
		]);

		$this->dispatcher->forward([
			'controller' => 'comments', 
        	'action'     => 'fetch', 
        	'params' => ['id' => $user->id],
		]);
	}

	/**
	 * Get most active users.
	 *
	 */
	public function activeAction()
	{
		$users = $this->users->getActive();

	        $this->views->add('users/active', [
            'title' => 'Aktivast användare',
            'users' => $users,
        ]);    
	}

	/**
	 * Add new user.
	 *
	 *
	 * @return void
	 */
	public function addAction()
	{
		//Prevent logged in users from access to this route
		if($this->session->get('user') != null)
		{
			$url = $this->url->create('');
        	$this->response->redirect($url);
		}

		$message = null;

        //Create form
        $form = $this->form->create(  
            [   
                'legend' => 'Nytt användarkonto',  
            ], 
            
            [   'name' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Namn:',  
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
                'value' 	=> 'Skapa konto',  
                'callback'  => function($form) 
                { 
                    return true;  
                }  
            ],  
        ]);  

        $status = $form->check();  

        if ($status === true) 
        {      
        	//Grab input from user
        	$nameIn = $form->value('name');
        	$emailIn = $form->value('email');
        	$gravatarIn = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($emailIn))) . '.jpg';
        	$passwordIn = password_hash($form->value('password'), PASSWORD_DEFAULT);

        	$now = date("Y-m-d H:i:s");

        	//Verify that the username is not taken
        	$usercheck = $this->users->query()
        		->where('name = ?')
	        	->execute(array($nameIn));

	        if(empty($usercheck))
	        {
	        	$this->users->save([
	        		'name' 	=> $nameIn,
	        		'email' => $emailIn,
	        		'web'	=>   null,
	        		'password' 	=> $passwordIn,
	        		'gravatar' =>  $gravatarIn,
	        		'created' 	=> $now,
	        		'activity' => 0
	        	]);

				$message['text'] = "Användarkonto skapat!";
				$message['class'] = "success";
	        }
	        else
	        {
	        	$message['text'] = "Det angivna användarnamnet är redan taget!";
	        	$message['class'] = "error";
	        }	

        }

        else if ($status === false) 
        {  	
			$message['text'] = "Ett fel inträffade! Försökt igen.";
			$message['class'] = "error";  
        }  

	    $this->theme->setTitle('Skapa konto');
	    $this->views->add('users/form', [
	        'content' => $form->getHTML(),
	        'message' => $message,
	    ]);
	}

	/**
	 * Edit user.
	 *
	 * @param int $id of user to edit.
	 *
	 * @return void
	 */
	public function editAction($id = null)
	{
		$userInSession = $this->session->get('user');

		//Prevent users not logged in from access to this route
		if($userInSession === null || $id != $userInSession->id)
		{
			$url = $this->url->create('');
        	$this->response->redirect($url);
		}

		//Fetch current userdetails
		$user = $this->users->find($id);

		$message = null;

        //Create form
        $form = $this->form->create(  
            [   
                'legend' => 'Uppdatera profil',  
            ], 
            
            [   'name' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Namn:',  
                'required'    => true,  
                'validation'  => ['not_empty'],
                'value'        => $user->name, 
            ],

            'email' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'E-post:',  
                'required'    => true,  
                'validation'  => ['not_empty', 'email_adress'],
                'value'        => $user->email,  
            ],

            'web' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Hemsida:',  
                'value'       => $user->web,  
            ], 
                          
            'submit' => [  
                'type'      => 'submit',
                'class' 	=> 'button',
                'value' 	=> 'Spara',  
                'callback'  => function($form) 
                { 
                    return true;  
                }  
            ],  
        ]);  

        $status = $form->check();  

        if ($status === true) 
        {      
        	//Grab input from user
        	$nameIn = $form->value('name');
        	$emailIn = $form->value('email');
        	$webIn = $form->value('web');
        	$gravatarIn = 'http://www.gravatar.com/avatar/' . md5(strtolower(trim($emailIn))) . '.jpg';

        	$now = date("Y-m-d H:i:s");

        	//Verify that the new username is not taken by anyone else
        	$usercheck = $this->users->query()
        		->where('name = ?')
	        	->execute(array($nameIn));

	        if(empty($usercheck) || $usercheck[0]->name === $user->name)
	        {
	        	$this->users->save([
	        		'name' 	=> $nameIn,
	        		'email' => $emailIn,
	        		'web'	=>   $webIn,
	        		'gravatar' =>  $gravatarIn,
	        		'created' 	=> $now,
	        	]);

	        	
	        	$this->questions->setQuestionAuthor($nameIn, $user->id);
	        	$this->answers->setAnswerAuthor($nameIn, $user->id);
	        	$this->comments->setCommentAuthor($nameIn, $user->id);

	        	$url = $this->url->create('users/id/' . $user->id);
        		$this->response->redirect($url);
	        }
	        else
	        {
	        	$message['text'] = "Det angivna användarnamnet är redan taget.";
	        	$message['class'] = "error";
	        }	

        }

        else if ($status === false) 
        {  	
			$message['text'] = "Ett fel inträffade. Försökt igen.";
			$message['class'] = "error";  
        }  

	    $this->theme->setTitle('Uppdatera profil');
	    $this->views->add('users/form', [
	        'content' => $form->getHTML(),
	        'message' => $message,
	    ]);
	}

	/**
	 * Login user.
	 */
	public function loginAction()
	{
		//Prevent logged in users to access this route
		if($this->session->get('user') != null)
		{
			$url = $this->url->create('');
        	$this->response->redirect($url);
		}

		$message = null;

		//Create form
		$form = $this->form->create(  
            [   
                'legend' => 'Inloggning',  
            ], 
            
            [   'name' => [  
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
                'value' 	=> 'Logga in',  
                'callback'  => function($form) 
                { 
                    return true;  
                }  
            ],  
        ]); 

        $status = $form->check();  

        if ($status === true) 
        {  
        	//Grab input from user
        	$nameIn = $form->value('name');
        	$passwordIn = $form->value('password');

        	//Verify that username exists and password is correct
        	$user = $this->users->query()
        			->where('name = ?')
	        		->execute(array($nameIn));
 			
	        if($user != null && password_verify($passwordIn, $user[0]->password) === true)
	        {
	        	$user = $user[0];
	        	$this->session->set('user', $user);
	        	$url = $this->url->create('');  
            	$this->response->redirect($url);
	        }
	        //If username is not found or password is inncorrect display error-message	    
	        else
	        {
	        	$message['text'] = "Felaktigt användarnamn eller lösenord!";
	        	$message['class'] = "error";
	       	}
 
        }

        else if ($status === false) 
        {  	
        	$message['text'] = "Ett fel inträffade. Försökt igen.";
        	$message['class'] = "error";  
        }  
	    
	    $this->theme->setTitle('Logga in');
	    $this->views->add('users/form', [
	        'content' => $form->getHTML(),
	        'message' => $message,
	    ]);
	}

	/**
	 * Login user.
	 */
	public function logoutAction()
	{
		if($this->session->get('user') != null)
		{
			$this->session->set('user', null);
		}
		
		$url = $this->url->create('users/login');
        $this->response->redirect($url);
	}


}