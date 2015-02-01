<?php

namespace Anax\Answers;
 
/**
 * A controller for users and admin related events.
 *
 */
class AnswersController implements \Anax\DI\IInjectionAware
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
		
	    $this->answers = new \Anax\Answers\Answer();
	    $this->answers->setDI($this->di);

	    $this->questions = new \Anax\Questions\Question();
	    $this->questions->setDI($this->di);

	    $this->users = new \Anax\Users\User();
	    $this->users->setDI($this->di);
	}

	/**
	 * Add new answer.
	 *
	 * @param string $id of question to answer.
	 *
	 * @return void
	 */
	 public function postAction($id = null)
	 {
		$userInSession = $this->session->get('user');

		$question = $this->questions->find($id);

		$message = null;

		//Prevent non users from access to this route
		if($userInSession === null)
		{
			$url = $this->url->create('users/login');
        	$this->response->redirect($url);
		}

		//Redirect if id is not valid
		if(!is_numeric($id) || $question === false)
		{
			$url = $this->url->create('users/list');
        	$this->response->redirect($url);
		}

		$user = $this->users->query()
        		->where('id = ?')
	        	->execute(array($userInSession->id));

	    $user = $user[0];

		//Create form
        $form = $this->form->create(  
            [   
                'legend' => 'Nytt svar',  
            ], 
            
            [   'content' => [  
                'type'        => 'textarea',
                'class'		  => 'textarea',  
                'label'       => 'Text:',  
                'required'    => true,  
                'validation'  => ['not_empty'], 
            ],
                           
            'submit' => [  
                'type'      => 'submit',
                'class' 	=> 'button',
                'value' 	=> 'Skicka svar',  
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
        	$contentIn = $form->value('content');
        	
        	$now = date("Y-m-d H:i:s");

	        $this->answers->save([
	        	'question_id' => $question->id,
	        	'user_id' 	=> $user->id,
	        	'answer_content'	=>   $contentIn,
	        	'answer_author' =>  $user->name,
	        	'created' 	=> $now
	        ]);

	        $this->users->setActivity($user->id);

			$url = $this->url->create('questions/id/' . $question->id);
        	$this->response->redirect($url);

        }

        else if ($status === false) 
        {  	
			$message['text'] = "Ett fel inträffade! Försökt igen.";
			$message['class'] = "error";  
        }

       	$this->theme->setTitle('Nytt svar');
	    $this->views->add('users/form', [
	        'content' => $form->getHTML(),
	        'message' => $message,
	    ]);  
	 }

	 /**
	 * Fetch all answers from specific user.
	 *
	 *@return array
	 */
	public function fetchAction($id = null)
	{
        $answers = $this->answers->query()
            ->where('user_id = ?')
            ->execute(array($id));

            foreach ($answers as $answer) {
            	$answer->answer_content = $this->questions->truncate(preg_replace('/[^?!\p{L}\p{N}]/u', ' ', $answer->answer_content));
            }

	        $this->views->add('answer/answers', [
            'title' => 'Svar',
            'answers' => $answers,
        ]);

	}

	/**
	 * Edit answer.
	 *
	 * @param string $id of answer to edit.
	 *
	 * @return void
	 */
	 public function editAction($id = null)
	 {
		$userInSession = $this->session->get('user');
		$answer = $this->answers->find($id);

		$message = null;

		//Prevent non users from access to this route
		if($userInSession === null)
		{
			$url = $this->url->create('users/login');
        	$this->response->redirect($url);
		}
		
		elseif($answer->user_id != $userInSession->id)
		{
			$url = $this->url->create('');
        	$this->response->redirect($url);
		}

		$user = $this->users->find($userInSession->id);

		//Create form
        $form = $this->form->create(  
            [   
                'legend' => 'Redigera svar',  
            ], 
            
            [   'content' => [  
                'type'        => 'textarea',
                'class'		  => 'textarea',  
                'label'       => 'Text:',  
                'required'    => true,  
                'validation'  => ['not_empty'],
                'value'       => $answer->answer_content, 
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
        	$contentIn = $form->value('content');
        	
        	$now = date("Y-m-d H:i:s");

	        $this->answers->save([
	        	'question_id' => $answer->question_id,
	        	'user_id' 	=> $user->id,
	        	'answer_content'	=>   $contentIn,
	        	'answer_author' =>  $user->name,
	        	'created' 	=> $now
	        ]);

			$url = $this->url->create('questions/id/' . $answer->question_id);
        	$this->response->redirect($url);

        }

        else if ($status === false) 
        {  	
			$message['text'] = "Ett fel inträffade! Försökt igen.";
			$message['class'] = "error";  
        }

       	$this->theme->setTitle('Redigera svar');
	    $this->views->add('users/form', [
	        'content' => $form->getHTML(),
	        'message' => $message,
	    ]);  
	 }

	/**
	 * List all answers for specific question.
	 *
	 *
	 */
	public function listAction($id = null)
	{	
			$answers = $this->answers->getAnswersQuestion($id);
			$nr = null;

			foreach ($answers as $answer) {
				$answer->answer_content = $this->textFilter->doFilter($answer->answer_content, 'shortcode, markdown');
				$answer->comments = $this->comments->getAnswerComments($answer->id);

				foreach ($answer->comments as $comment) {
					$comment->comment_content = $this->textFilter->doFilter($comment->comment_content, 'shortcode, markdown');
				}

				$nr += 1;
			}
    
	        $this->views->add('answer/all-answers', [
            'title' => $nr . ' svar',
            'answers' => $answers,
        ]);

	}

	/**
	 * Delete answer.
	 *
	 * @param int $id of answer to delete
	 *
	 * @return void
	 */
	public function deleteAction($answerId = null)
	{
		$answer = $this->answers->find($answerId);
		$user = $this->session->get('user');

		if($answer === false)
		{
			$url = $this->url->create('users/login');
        	$this->response->redirect($url);
		}
		elseif($answer->user_id != $user->id)
		{
			$url = $this->url->create('');
        	$this->response->redirect($url);
		}
		else
		{	
			$this->answers->delete($answerId);
			$this->comments->deleteByAnswer($answerId);

			$url = $this->url->create('questions/id/' . $answer->question_id);
        	$this->response->redirect($url);
		}		
	}

}