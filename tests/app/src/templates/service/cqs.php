<?php
/**
 * @var View $view
 * @var string[] $history
 */

use Stormmore\Framework\Mvc\View\View;

$view->setLayout('@templates/includes/layout.php');
?>

<h2>Success!</h2>
If you see this message it means command was successfully handled, otherwise you would see page with error.</br>
Enjoy small things.</br>
</br>

<table>
    <thead>
    <tr>
        <th style="text-align: left">Handled commands</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($history as $item): ?>
        <tr>
            <td><?php echo $item ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
