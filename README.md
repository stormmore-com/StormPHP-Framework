# PHP Storm Framework

Made with love without unnecessary complexity. 

- Easy to learn (simplest app have literally 4 lines)
- Small footprint (100Kb, no dependencies)
- Built-in dependency injection container
- Built-in multilanguage support
- Built-in authentication and authorization system
- Built-in class autoloader with scanning source code (detect automatically controllers)
- Built-in path alias system ('@templates/homepage.php' refers to /your/project/dir/templates/homepage.php)
- Built-in plain PHP views with mature system 
- Views support surrounding your view with layout from view level
- Views can controller content of layout (adding css/jss scripts or chaning title)
- Support forms 
- Support error customization
- Support Docker out of the box
- Works with StormQueries
- Support PHP8 and greater

To learn more visit https://keycode13.com/php-storm-framework \
\
If you want to join project or share with ideas fell free to contact me. 

## Projects using PHP Storm Framework
 keycode13

## Quick start

You can use `ready to develop` template with basic functionality. Download it here.

### Simplest application

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

