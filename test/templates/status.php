<?php
/** @var Stormmore\Framework\Mvc\View $view */
$view->setTitle(_('status.title'));
$view->setLayout('@/templates/includes/layout.php');
?>
<table>
    <tr>
        <td><?php echo _('status.environment') ?>:</td>
        <td><?php echo $view->env ?></td>
    </tr>
    <tr>
        <td><?php echo _('status.locale') ?>:</td>
        <td><?php echo $view->i18n->locale->tag ?></td>
    </tr>
    <tr>
        <td><?php echo _('status.currency') ?></td>
        <td><?php echo $view->i18n->culture->currency ?></td>
    </tr>
    <tr>
        <td><?php echo _('status.date_format') ?></td>
        <td><?php echo $view->i18n->culture->dateFormat ?></td>
    </tr>
    <tr>
        <td><?php echo _('status.date_time_format') ?></td>
        <td><?php echo $view->i18n->culture->dateTimeFormat ?></td>
    </tr>
</table>

<form action="/locale/change">
    <select name="tag">
        <?php html_options($view->locales, $view->i18n->locale->tag) ?>
    </select>
    <button><?php echo _('status.change_locale') ?></button>
</form>



