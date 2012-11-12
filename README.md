ZDynaUploadr
============

A dynamic text/csv data upload form MVC that does batch update for a table with configurable text fields & upload fields for [Yii Framework](http://www.yiiframework.com/) .

Usage
------------
Copy all of the files under models, controllers, views, components directories to the corresponding Yii webapp directory;

To start using it, you just need to create your own action function in 
	ZDynaUploadController.php

Using the following function as an example

	public function actionUserExample()
	{
		......
	}

To Do
------------
* Unit Test;
* Implement using 'LOAD DATA' directly for MySql for faster performance;
* Bundle files as a Yii extension;
* Better logging;
