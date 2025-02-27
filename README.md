# PHP Storm Framework &#9889;

Made with love without unnecessary complexity. 

- Easy to learn (simplest app have literally 4 lines)
- Small footprint (100Kb, no dependencies)
- Built-in dependency injection container
- Built-in multilanguage support
- Built-in authentication and authorization system
- Built-in class autoloader with scanning source code (detect automatically controllers)
- Built-in path alias system ('@templates/homepage.php' refers to /your/project/dir/templates/homepage.php)
- Built-in mature view system using pure PHP
- Views can controller content of layout (adding css/jss scripts or changing title)
- Support forms 
- Support error customization
- Support middleware (put your code to pipeline before and after request is handled)
- Support Docker out of the box
- Works with StormQueries
- Support PHP8 and greater

To learn more visit https://keycode13.com/php-storm-framework \
\
If you want to join project or share with ideas fell free to contact me. 

### keycode13.com

Framework designed to build it. 

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

