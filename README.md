# WGTOTW
___

This repository contains a project based on the "Anax-MVC" framework. The purpose of this project was to create a "questions and answers" site inspired by the model of stackoverflow.com

##Installation

You can either clone it

	git clone https://github.com/eddo81/WGTOTW.git 

Or just download the zip-file

Make sure you get the required dependencies listed in the composer.jason file by running
	
	composer install

##Manage the database

This project uses a SQLite database which can be found via this path:

	webroot/sqlite-db

Do make sure that you set the correct read/write permissions for the database.
The database comes pre-populated with some default data. The following route will reset the database if the user is logged in as admin:

	WGTOTW/webroot/setup



