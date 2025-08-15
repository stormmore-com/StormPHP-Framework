# PHP Storm Framework &#9889;

Made with love without unnecessary complexity. 

- Easy to learn (simplest app have literally 4 lines)
- Small footprint (100Kb, no dependencies)
- Blazing fast
- Built-in dependency injection container
- Built-in middleware support
- **Built-in CQS (command query separation)**
- Built-in event dispatcher 
- Built-in multilanguage support
- Built-in authentication and authorization system
- Built-in logger
- Built-in class autoloader with scanning source code (detect automatically controllers)
- Built-in path alias system ('@templates/homepage.php' refers to /your/project/dir/templates/homepage.php)
- Built-in mature view system using pure PHP, views can control content of layout (adding css/jss scripts or changing title
- Build-in validation
- Build-in forms
- Build-in mailing (i18n, smtp client)
- Support cli tasks (define tasks and run it from cli)
- Support for running controller from cli
- Support e2e tests with php cli (test automatically your entire backend stack from phpunit easilly)
- Support error customization
- Support middleware (put your code to pipeline before and after request is handled)
- Support Docker out of the box
- Support PHP8 and greater
- Works with StormQueries

To learn more visit https://keycode13.com/php-storm-framework \
\
If you want to join project or share with ideas fell free to contact me. 

### keycode13.com

Framework designed to deliver. 

## Quick start

You can use `ready to develop` template with basic functionality. Download it here.

### Simplest application
Install by typing
```
composer require stormmore/framework
```
index.php
```php
require '../vendor/autoload.php';

$app = App::create();
$app->addRoute('/', function() { return "hello world";});
$app->run();
```

### Run demonstration

#### Use docker

```php
docker composer up
```

You  can change source code and watch changes on http://localhost:91

#### Use build-in web server
Go to `test/server`directory and run `php -S localhost:90` command.

## Tutorial
Designed to be intuitive as scratching your nose but there is some things you should know.

### Views

Thaks to alias system you can easilly change templates

