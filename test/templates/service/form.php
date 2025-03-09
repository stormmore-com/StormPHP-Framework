<?php
/**
 * @var Stormmore\Framework\Mvc\View $view
 * @var \Configuration\BasicForm $form
 */

$view->setLayout('@templates/includes/layout.php');
?>

<form action="/form" method="post">
    <input type="text" name="text" value="<?php echo '' ?>" />
    <select>
        <option>Opcja 123</option>
        <option>Opcja 312</option>
        <option>Opcja 777</option>
    </select>
    <button>Send</button>
</form>

<?php if (!$form->isValid()): ?>
     <div>Formularz ma błedy</div>
    <?php foreach($form->validationResult->errors as $error): ?>
        <?php echo $error ?>
    <?php endforeach ?>
<?php endif ?>
