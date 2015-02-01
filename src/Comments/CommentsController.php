<?php

namespace Anax\Comments;

/**
 * To attach comments-flow to a page or some content.
 *
 */
class CommentsController implements \Anax\DI\IInjectionAware
{
    use \Anax\DI\TInjectable;

    /**
     * Initialize the controller and create the database table.
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
     * Fetch all comments from specific user.
     *
     *@return array
     */
    public function fetchAction($id = null)
    {
        $comments = $this->comments->getUserComments($id);

            foreach ($comments as $comment) {
                $comment->comment_content = $this->questions->truncate(preg_replace('/[^?!\p{L}\p{N}]/u', ' ', $comment->comment_content)); 
            }

            //var_dump($comments);

            $this->views->add('comment/comments', [
            'title' => 'Kommentarer',
            'comments' => $comments,
        ]);

    }

     public function qpostAction($questionId = null)
     {
        $userInSession = $this->session->get('user');
        $question = $this->questions->find($questionId);

        $message = null;

        //Prevent non users from access to this route
        if($userInSession === null)
        {
            $url = $this->url->create('users/login');
            $this->response->redirect($url);
        }

        //Redirect if id is not valid
        if(!is_numeric($questionId) || $question === false)
        {
            $url = $this->url->create('');
            $this->response->redirect($url);
        }

        //Fetch current userdetails
        $user = $this->users->find($userInSession->id);

        //Create form
        $form = $this->form->create(  
            [   
                'legend' => 'Kommentera fråga',  
            ], 
            
            [   'content' => [  
                'type'        => 'textarea',
                'class'       => 'textarea',  
                'label'       => 'Text:',  
                'required'    => true,  
                'validation'  => ['not_empty'], 
            ],
                           
            'submit' => [  
                'type'      => 'submit',
                'class'     => 'button',
                'value'     => 'Kommentera',  
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

            $this->comments->save([
                'question_id' => $question->id,
                'answer_id' => null,
                'user_id'   => $user->id,
                'comment_content'   =>   $contentIn,
                'comment_author' =>  $user->name,
                'created'   => $now
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

     public function apostAction($answerId = null)
     {
        $userInSession = $this->session->get('user');
        $answer = $this->answers->find($answerId);

        $message = null;

        //Prevent non users from access to this route
        if($userInSession === null)
        {
            $url = $this->url->create('users/login');
            $this->response->redirect($url);
        }

        //Redirect if id is not valid
        if(!is_numeric($answerId) || $answer === false)
        {
            $url = $this->url->create('');
            $this->response->redirect($url);
        }

        //Fetch current userdetails
        $user = $this->users->find($userInSession->id);

        //Create form
        $form = $this->form->create(  
            [   
                'legend' => 'Kommentera svar',  
            ], 
            
            [   'content' => [  
                'type'        => 'textarea',
                'class'       => 'textarea',  
                'label'       => 'Text:',  
                'required'    => true,  
                'validation'  => ['not_empty'], 
            ],
                           
            'submit' => [  
                'type'      => 'submit',
                'class'     => 'button',
                'value'     => 'Kommentera',  
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

            $this->comments->save([
                'question_id' => $answer->question_id,
                'answer_id' => $answer->id,
                'user_id'   => $user->id,
                'comment_content'   =>   $contentIn,
                'comment_author' =>  $user->name,
                'created'   => $now
            ]);

            $this->users->setActivity($user->id);

            $url = $this->url->create('questions/id/' . $answer->question_id);
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

}
