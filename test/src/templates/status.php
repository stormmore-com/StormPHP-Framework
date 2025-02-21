<?php
/** @var Stormmore\Framework\Mvc\View $view */
?>
<table>
    <tr>
        <td>Environment:</td>
        <td><?php echo $view->env ?></td>
    </tr>
    <tr>
        <td>Locale:</td>
        <td><?php echo $view->getLocale()->tag ?></td>
    </tr>
</table>

