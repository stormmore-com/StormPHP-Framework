<?php
/**
 * @var \Stormmore\Framework\Mvc\View\View $view
 * @var \Stormmore\Framework\Mvc\View\ViewBag $bag
 * @var string $name
 * @var array $errors
 */

$view->setTitle("Storm App - Emails");
$view->setLayout("@templates/includes/layout.php");
?>

<form method="post" action="send-email">
    <div class="send-email-form">
        <label>Email</label>
        <div>
            <input name="email" type="text" value="<?= $bag->form->email ?>" />
            <div class="error"><?= $bag->form->errors->email ?></div>
        </div>
        <label>Subject</label>
        <div>
            <input name="subject" type="text" value="<?= $bag->form->subject ?>" />
            <div class="error"><?= $bag->form->errors->subject ?></div>
        </div>
        <label>Content:</label>
        <div>
            <input name="content" type="text" value="<?= $bag->form->content ?>" />
            <div class="error"><?= $bag->form->errors->content ?></div>
        </div>
        <div><button>Send</button></div>
    </div>
</form>
