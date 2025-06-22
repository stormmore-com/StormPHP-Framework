# StormPHP Framework &#9889;

Made with love, without unnecessary complexity, to deliver high-quality solutions.

- Easy to learn (the simplest app has four lines)
- Small footprint (100Kb, no dependencies)
- Blazing fast
- Built-in dependency injection container
- Built-in middleware support
- **Built-in CQS (command query separation)**
- Built-in event dispatcher
- Built-in multilingual support
- Built-in authentication and authorization system
- Built-in logger
- Built-in class autoloader with scanning source code (detects automatically controllers)
- Built-in path alias system ('@templates/homepage.php' refers to /your/project/dir/templates/homepage.php)
- Built-in mature view system using pure PHP, child views can control the content of the master layout (adding CSS/JSS scripts or changing the title)
- Built-in validation
- Built-in forms
- Build-in mailing (i18n, SMTP client)
- Support CLI tasks (define tasks and run them from the CLI)
- Support for running the controller from CLI
- Support e2e tests with PHP CLI (test automatically your entire backend stack from PHPUnit easily)
- Support error customization
- Support middleware (You can put code in the pipeline before and after the request is handled)
- Support Docker out of the box
- Support PHP8 and greater
- Works with StormPHP Queries

If you would like to join the project or share your ideas, please don't hesitate to contact me.

## Quick start

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

## TODO
- docs
- simple real-life application