<?php
/**
 * Config-file for navbar_me.
 *
 */
return [

    // Use for styling the menu
    'class' => 'navbar',
 
    // Here comes the menu strcture
    'items' => [

        // This is a menu item
        'Hem'  => [
            'text'  => 'Hem',
            'url'   => $this->di->get('url')->create(''),
            'title' => ''
        ],

        // This is a menu item
        'Frågor' => [
            'text'  =>'Frågor',
            'url'   => $this->di->get('url')->create('questions/list'),
            'title' => ''
        ],

        // This is a menu item
        'Taggar' => [
            'text'  =>'Taggar',
            'url'   => $this->di->get('url')->create('questions/tags'),
            'title' => ''
        ],

        // This is a menu item
        'Användare' => [
            'text'  =>'Användare',
            'url'   => $this->di->get('url')->create('users/list'),
            'title' => ''
        ],

       // This is a menu item
        'Om oss' => [
            'text'  =>'Om oss',
            'url'   => $this->di->get('url')->create('about'),
            'title' => ''
        ],
  
    ],
 
    // Callback tracing the current selected menu item base on scriptname
    'callback' => function ($url) {
        if ($url == $this->di->get('request')->getRoute()) {
                return true;
        }
    },

    // Callback to create the urls
    'create_url' => function ($url) {
        return $this->di->get('url')->create($url);
    },
];
