<?php
/**
 * @var View $view
 * @var ViewBag $bag
 **/

use Stormmore\Framework\Mvc\View\View;
use Stormmore\Framework\Mvc\View\ViewBag;

$view->setLayout("@templates/mails/layout.php");
?>

<?= t('email.boilerplate'); ?>
</br>
Content: <?= $bag->content ?>
