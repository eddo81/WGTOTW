<?php 
/**
 * This is a Anax pagecontroller.
 *
 */
// Include the essential settings.
require __DIR__.'/config_with_app.php'; 

// Create services and inject into the app. 
$di->setShared('db', function() {
    $db = new \Mos\Database\CDatabaseBasic();
    $db->setOptions(require ANAX_APP_PATH . 'config/database_sqlite.php');
    $db->connect();
    return $db;
});

$di->set('form', '\Mos\HTMLForm\CForm');

$di->set('TablesController', function() use ($di) {
    $controller = new \Anax\Tables\TablesController();
    $controller->setDI($di);
    return $controller;
});

$di->set('UsersController', function() use ($di) {
    $controller = new \Anax\Users\UsersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('QuestionsController', function() use ($di) {
    $controller = new \Anax\Questions\QuestionsController();
    $controller->setDI($di);
    return $controller;
});

$di->set('AnswersController', function() use ($di) {
    $controller = new \Anax\Answers\AnswersController();
    $controller->setDI($di);
    return $controller;
});

$di->set('CommentsController', function() use ($di) {
    $controller = new \Anax\Comments\CommentsController();
    $controller->setDI($di);
    return $controller;
});


// Setup 'pretty URLs'. 
$app->url->setUrlType(\Anax\Url\CUrl::URL_CLEAN);

//Setup session.
$app->session();

// Setup theme.
$app->theme->setVariable('title', "Me");
$app->theme->configure(ANAX_APP_PATH . 'config/theme_owl.php');

//Setup navbar.
$app->navbar->configure(ANAX_APP_PATH . 'config/navbar_owl.php');

// Set Routes.
$app->router->add('', function() use ($app) {
    
$app->theme->setTitle("Hem");


    $content = $app->fileContent->get('home.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown');

    $app->views->add('owl/home', [
        'title' => 'Välkommen!',
        'content' => $content,
    ]);

    $app->dispatcher->forward([ 
        'controller' => 'questions', 
        'action'     => 'latest', 
    ]);

    $app->dispatcher->forward([ 
        'controller' => 'users', 
        'action'     => 'active', 
    ]);

    $app->dispatcher->forward([ 
        'controller' => 'questions', 
        'action'     => 'popular', 
    ]);

});

$app->router->add('about', function() use ($app) {
	$app->theme->setTitle("Om Oss");

	$content = $app->fileContent->get('about.md');
    $content = $app->textFilter->doFilter($content, 'shortcode, markdown'); 

    $app->views->add('owl/about', [
        'content' => $content
    ]);

});


$app->router->add('setup', function() use ($app) {
    
    $app->dispatcher->forward([ 
        'controller' => 'tables', 
        'action'     => 'setup',
    ]);

});


$app->router->handle();
$app->theme->render();
