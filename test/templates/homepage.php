<?php
/** @var Stormmore\Framework\Mvc\View $view */

$view->setTitle("Storm App - Homepage");
$view->setLayout("@/templates/includes/layout.php");

?>
<h1>Working MVC model</h1>

<?php echo $view->name ?>