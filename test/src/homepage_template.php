<?php
/** @var Stormmore\Framework\Mvc\View $view */

$view->setLayout("@src/layout.php");
$view->setTitle("Storm App - Homepage");
$view->addJsScript("/1.js");
$view->addJsScript(["/2.js", "/3.js"]);
$view->addCssScript("/1.css");
$view->addCssScript(["/2.css", "/3.css"]);
?>
<h1>Working MVC model</h1>

<?php echo $view->name ?>