<?php
/**
 * @var View $view
 * @var ViewBag $bag
 * @var string $name
 * @var array $errors
 */

use Stormmore\Framework\Mvc\View\View;
use Stormmore\Framework\Mvc\View\ViewBag;

$view->setTitle("Storm App - Emails");
$view->setLayout("@templates/includes/layout.php");
?>

<?php if ($view->request->messages->isset('success')): ?>
    <div class="success">Success!</div>
<?php endif ?>

<form method="post" enctype="multipart/form-data">
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
        <div class="row">
            <lable>Attachments:</lable>
            <div>
                <input name="attachment1" type="file" />
                <input name="attachment2" type="file" />
            </div>
        </div>
        <div class="row">
            <button>Send</button>
        </div>
    </div>
</form>
