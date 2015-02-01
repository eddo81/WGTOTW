<?php

namespace Anax\Questions;
 
/**
 * A controller for users and admin related events.
 *
 */
class QuestionsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

	/**
	 * Initialize the controller.
	 *
	 * @return void
	 */
	public function initialize()
	{
		$this->comments = new \Anax\Comments\Comment();
        $this->comments->setDI($this->di);

	    $this->questions = new \Anax\Questions\Question();
	    $this->questions->setDI($this->di);

	    $this->answers = new \Anax\Answers\Answer();
	    $this->answers->setDI($this->di);

	    $this->users = new \Anax\Users\User();
	    $this->users->setDI($this->di);
	}

	/**
	 * List all questions.
	 *
	 * @return void
	 */
	public function listAction()
	{
	    $all = $this->questions->query()
	        ->execute();

	    foreach ($all as $question) {
	       	if($question->question_tags != null)
	       		{
	       			$question->question_tags = $this->questions->tagsFromQuestion($question->id);
	       		}
	     }

	    $this->theme->setTitle("Frågor");
	    $this->views->add('question/list-all', [
	        'questions' => $all,
	        'title' => 'Frågor',
	    ]);
	}

	/**
	 * Fetch questions from specific user.
	 *
	 *@return array
	 */
	public function fetchAction($id = null)
	{
        $questions = $this->questions->query()
            ->where('user_id = ?')
            ->execute(array($id));

            foreach ($questions as $question) {
            	$question->question_title = $this->questions->truncate($question->question_title);
            }

	        $this->views->add('question/questions', [
            'title' => 'Frågor',
            'questions' => $questions,
        ]);

	}

	/**
	 * List question with id.
	 *
	 * @param int $id of question to display
	 *
	 * @return void
	 */
	public function idAction($id = null)
	{

		$question = $this->questions->find($id);
		$comments = $this->comments->getQuestionComments($id);

		//Redirect if id is not valid
		if(!is_numeric($id) || $question === false)
		{
			$url = $this->url->create('questions/list');
        	$this->response->redirect($url);
		}

		$question->question_content = $this->textFilter->doFilter($question->question_content, 'shortcode, markdown');
		
		if($question->question_tags != null)
	    {
	       	$question->question_tags = $this->questions->tagsFromQuestion($question->id);
	    }

	    if(!empty($comments))
	    {
	    	foreach ($comments as $comment) {
	    		$comment->comment_content = $this->textFilter->doFilter($comment->comment_content, 'shortcode, markdown');
	    	}
	    }	    

	    $this->theme->setTitle('Fråga: ' . $question->id);
	    $this->views->add('question/single-question', [
	        'question' => $question,
	        'comments' => $comments,
	    ]);

	    $this->dispatcher->forward([ 
        'controller' => 'answers', 
        'action'     => 'list', 
        'params' => [$question->id],
    ]);

	}

	/**
	 * Add new question.
	 *
	 * 
	 * @return void
	 */
	 public function postAction()
	 {
		$userInSession = $this->session->get('user');

		$message = null;

		//Prevent non users from access to this route
		if($userInSession === null)
		{
			$url = $this->url->create('users/login');
        	$this->response->redirect($url);
		}

		//Fetch current userdetails
		$user = $this->users->find($userInSession->id);

		//Create form
        $form = $this->form->create(  
            [   
                'legend' => 'Ny fråga',  
            ], 
            
            [   'title' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Rubrik:',  
                'required'    => true,  
                'validation'  => ['not_empty'], 
            ],

            'tags' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Taggar:',  
                'required'    => false,   
            ],

           'content' => [  
                'type'        => 'textarea',
                'class'		  => 'textarea',  
                'label'       => 'Text:',  
                'required'    => true,  
                'validation'  => ['not_empty'], 
            ],
                           
            'submit' => [  
                'type'      => 'submit',
                'class' 	=> 'button',
                'value' 	=> 'Skicka fråga',  
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
        	$titleIn = $form->value('title');
        	$contentIn = $form->value('content');
        	$tagsIn = is_null($form->value('tags')) ? null : str_replace(array(' ', ','), ' ', trim(strtolower($form->value('tags'))));
        	
        	$now = date("Y-m-d H:i:s");

	        $this->questions->save([
	        	'user_id' 	=> $user->id,
	        	'question_title' => $titleIn,
	        	'question_content'	=>   $contentIn,
	        	'question_tags' 	=> $tagsIn,
	        	'question_slug' 	=> slugify($tagsIn),
	        	'question_author' =>  $user->name,
	        	'created' 	=> $now
	        ]);

	        $this->users->setActivity($user->id);

			$url = $this->url->create('questions/list');
        	$this->response->redirect($url);

        }

        else if ($status === false) 
        {  	
			$message['text'] = "Ett fel inträffade! Försökt igen.";
			$message['class'] = "error";  
        }

       	$this->theme->setTitle('Ny fråga');
	    $this->views->add('users/form', [
	        'content' => $form->getHTML(),
	        'message' => $message,
	    ]);  
	 }

	 /**
	 * Edit question.
	 *
	 * 
	 * @return void
	 */
	 public function editAction($id = null)
	 {
		$userInSession = $this->session->get('user');
		$question = $this->questions->find($id);

		$message = null;

		//Prevent users not logged in from access to this route
		if($userInSession === null)
		{
			$url = $this->url->create('users/login');
        	$this->response->redirect($url);
		}
		elseif($question->user_id != $userInSession->id)
		{
			$url = $this->url->create('');
        	$this->response->redirect($url);
		}

		//Fetch current userdetails
		$user = $this->users->find($userInSession->id);

		//Create form
        $form = $this->form->create(  
            [   
                'legend' => 'Redigera fråga',  
            ], 
            
            [   'title' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Rubrik:',  
                'required'    => true,  
                'validation'  => ['not_empty'],
                'value'        => $question->question_title, 
            ],

            'tags' => [  
                'type'        => 'text',
                'class'		  => 'input',  
                'label'       => 'Taggar:',  
                'required'    => false,
                'value'        => $question->question_tags,   
            ],

           'content' => [  
                'type'        => 'textarea',
                'class'		  => 'textarea',  
                'label'       => 'Text:',  
                'required'    => true,  
                'validation'  => ['not_empty'],
                'value'       => $question->question_content, 
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
        	$titleIn = $form->value('title');
        	$contentIn = $form->value('content');
        	$tagsIn = is_null($form->value('tags')) ? null : str_replace(array(' ', ','), ' ', trim(strtolower($form->value('tags'))));
        	
        	$now = date("Y-m-d H:i:s");

	        $this->questions->save([
	        	'user_id' 	=> $user->id,
	        	'question_title' => $titleIn,
	        	'question_content'	=>   $contentIn,
	        	'question_tags' 	=> $tagsIn,
	        	'question_slug' 	=> slugify($tagsIn),
	        	'question_author' =>  $user->name,
	        	'created' 	=> $now
	        ]);


			$url = $this->url->create('questions/id/' . $question->id);
        	$this->response->redirect($url);

        }

        else if ($status === false) 
        {  	
			$message['text'] = "Ett fel inträffade! Försökt igen.";
			$message['class'] = "error";  
        }

       	$this->theme->setTitle('Redigera fråga');
	    $this->views->add('users/form', [
	        'content' => $form->getHTML(),
	        'message' => $message,
	    ]);  
	 }

	 /**
	 * Delete question.
	 *
	 * @param int $id of question to delete
	 *
	 * @return void
	 */
	public function deleteAction($questionId = null)
	{
		$question = $this->questions->find($questionId);
		$user = $this->session->get('user');

		if($question === false || $user === null)
		{
			$url = $this->url->create('users/login');
        	$this->response->redirect($url);
		}
		elseif($question->user_id != $user->id)
		{
			$url = $this->url->create('');
        	$this->response->redirect($url);
		}
		else
		{
			$this->questions->delete($questionId);
			$this->answers->deleteAnswers($questionId);
			$this->comments->deleteByQuestion($questionId);

			$url = $this->url->create('users/id/' . $user->id);
        	$this->response->redirect($url);
		}		
	}

	/**
	 * Get latest questions.
	 *
	 */
	public function latestAction()
	{
		$questions = $this->questions->getLatest();

		foreach ($questions as $question) {
            	$question->question_title = $this->questions->truncate($question->question_title);
            }
        
	        $this->views->add('question/questions', [
            'title' => 'Senaste frågor',
            'questions' => $questions,
        ]);    
	}

	/**
	 * Get popular tags.
	 *
	 */
	public function popularAction()
	{
		$populartags = $this->questions->getPopular();
        
	        $this->views->add('question/populartags', [
            'title' => 'Populära taggar',
            'populartags' => $populartags,
        ]);    
	}

	/**
	 * List all tags.
	 *
	 * @param int $id of question to display
	 *
	 * @return void
	 */
	public function tagsAction()
	{
		$tags = $this->questions->getTags();

		$this->theme->setTitle('Taggar');
		$this->views->add('tags/all-tags', [
	        'tags' => $tags,
	        'title' => 'Sök taggar',
	    ]);
	}


	/**
	 * List all questions that matches tag.
	 *
	 * @param int $id of question to display
	 *
	 * @return void
	 */
	public function tagAction($tag = null)
	{

		if($tag === null)
		{
			$url = $this->url->create('questions/tags');
       	 	$this->response->redirect($url);
		}

		$questions = $this->questions->fetchByTag($tag);

	    foreach ($questions as $question) {
	       	if($question->question_tags != null)
	       		{
	       			$question->question_tags = $this->questions->tagsFromQuestion($question->id);
	       		}
	     }

		$this->theme->setTitle('Sökrestultat');
		$this->views->add('question/matching-tags', [
	        'questions' => $questions,
	        'title' => 'Sökrestultat',
	    ]);
	}

}