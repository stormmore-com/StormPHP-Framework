<?php
/**
 * @var Stormmore\Framework\Mvc\View $view
 * @var string $name
 */

$view->setTitle("Storm App - Homepage");
$view->setLayout("@/templates/includes/layout.php");

?>
<h1>Working MVC model</h1>

<?php echo $name ?>