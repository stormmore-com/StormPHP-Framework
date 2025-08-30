<?php
/**
 * @var View $view
 */

use Stormmore\Framework\Mvc\View\View;

$view->useLayout("@templates/mails/layout.php");
?>

<div style="background: burlywood">
    <?= t('email.test.content'); ?>
</div>

