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

<form method="post">
    <div class="send-email-form">
        <div class="row">
            <label>Email: </label>
            <div>
                <input name="email" type="text" value="<?= $bag->form->email ?>" />
                <div class="error"><?= $bag->form->errors->email ?></div>
            </div>
        </div>
        <div class="row">
            <label>Subject: </label>
            <div>
                <input name="subject" type="text" value="<?= $bag->form->subject ?>" />
                <div class="error"><?= $bag->form->errors->subject ?></div>
            </div>
        </div>
        <div class="row">
            <label>Content:</label>
            <div>
                <textarea name="content"><?= $bag->form->content ?></textarea>
                <div class="error"><?= $bag->form->errors->content ?></div>
            </div>
        </div>

        <button>Send</button>
    </div>
</form>
