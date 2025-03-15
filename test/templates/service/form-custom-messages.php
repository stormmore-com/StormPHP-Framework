<?php
/**
 * @var array $days
 * @var Stormmore\Framework\Mvc\View $view
 * @var \Configuration\CustomMessagesForm $form
 */

$view->setLayout('@templates/includes/layout.php');
?>

<?php if (!$form->isValid()): ?>
    <div>Formularz ma błedy</div>
<?php endif ?>

<form action="/form-custom-messages" enctype="multipart/form-data" method="post">
    <table>
        <!-- Alpha -->
        <tr>
            <td><label for="alpha">Alpha:</label></td>
            <td><input id="alpha" type="text" name="alpha" value="<?php echo $form->alpha ?>" /></td>
            <td>alpha</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->alpha): ?>
                    <div><?php echo $form->errors->alpha ?></div>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td colspan="3"><button>Send</button></td>
        </tr>
    </table>
</form>
