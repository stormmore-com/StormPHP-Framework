<?php
/**
 * @var \Stormmore\Framework\Mvc\View\View $view
 * @var \Stormmore\Framework\Mvc\View\ViewBag $bag
 **/

$view->setLayout("@templates/mails/layout.php");
?>

<?= t('email.boilerplate'); ?>
</br>
Content: <?= $bag->content ?>
