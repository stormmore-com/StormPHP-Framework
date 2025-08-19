<?php
/**
 * @var View $view
 * @var Stormmore\Framework\Internationalization\Locale[] $locales
 * @var Stormmore\Framework\AppConfiguration $configuration
 * @var Settings $settings
 */

use src\Infrastructure\Settings;
use Stormmore\Framework\Mvc\View\View;

$view->setTitle(t('status.title'));
$view->setLayout('@templates/includes/layout.php');
?>
<h1>Application configuration</h1>
<table>
    <tr>
        <td><?php echo t('status.app_name') ?></td>
        <td></td>
    </tr>
    <tr>
        <td><?php echo t('status.app_url') ?></td>
        <td></td>
    </tr>
    <tr>
        <td><?php echo t('status.environment') ?>:</td>
        <td><?php echo $configuration->getEnvironment() ?></td>
    </tr>
    <tr>
        <td><?php echo t('status.project_directory') ?></td>
        <td><?php echo $configuration->projectDirectory ?></td>
    </tr>
    <tr>
        <td><?php echo t('status.source_directory') ?></td>
        <td><?php echo $configuration->sourceDirectory ?></td>
    </tr>
    <tr>
        <td><?php echo t('status.cache_directory') ?></td>
        <td><?php echo $configuration->cacheDirectory ?></td>
    </tr>
    <tr>
        <td><?php echo t('status.url') ?></td>
        <td>
            <form action="/change-url" method="post">
                <input type="text" name="url" value="<?php echo $settings->url ?>" />
                <button><?php echo t('status.change') ?></button>
            </form>
        </td>
    </tr>
    <tr>
        <td><?php echo t('status.locale') ?>:</td>
        <td>
            <form action="/locale/change">
                <select name="tag">
                    <?php $view->html->options($locales, $view->i18n->locale->tag) ?>
                </select>
                <button><?php echo t('status.change') ?></button>
            </form>
        </td>
    </tr>
    <tr>
        <td><?php echo t('status.currency') ?></td>
        <td><?php echo $view->i18n->culture->currency ?></td>
    </tr>
    <tr>
        <td><?php echo t('status.date_format') ?></td>
        <td><?php echo $view->i18n->culture->dateFormat ?></td>
    </tr>
    <tr>
        <td><?php echo t('status.date_time_format') ?></td>
        <td><?php echo $view->i18n->culture->dateTimeFormat ?></td>
    </tr>
</table>





