<?php
/** @var Stormmore\Framework\Mvc\View $view */

$view->setTitle(_('status.title'));
$view->setLayout('@/templates/includes/layout.php');
?>
<table>
    <tr>
        <td><?php echo _('status.environment') ?>:</td>
        <td><?php echo $view->configuration->environment ?></td>
    </tr>
    <tr>
        <td><?php echo _('status.project_directory') ?></td>
        <td><?php echo $view->configuration->projectDirectory ?></td>
    </tr>
    <tr>
        <td><?php echo _('status.source_directory') ?></td>
        <td><?php echo $view->configuration->sourceDirectory ?></td>
    </tr>
    <tr>
        <td><?php echo _('status.cache_directory') ?></td>
        <td><?php echo $view->configuration->cacheDirectory ?></td>
    </tr>
    <tr>
        <td><?php echo _('status.locale') ?>:</td>
        <td>
            <form action="/locale/change">
                <select name="tag">
                    <?php html_options($view->locales, $view->i18n->locale->tag) ?>
                </select>
                <button><?php echo _('status.change_locale') ?></button>
            </form>
        </td>
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





