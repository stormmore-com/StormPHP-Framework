<?php
/** @var View $view */

use Stormmore\Framework\Mvc\View\View;

$view->useLayout('@templates/includes/layout.php');
?>
<h2>Sign in</h2>
<div style="border:solid 1px; padding:10px">
    <form action="/signin" method="post">
        <p>
            It's made just for demonstration purposes so user data are written in cookie and there is no password validation. Don't worry about that.
        </p>
        <input type="text" name="username">
        <button><?php echo t('signin.post') ?></button>
        <p>
            <input id="admin" type="checkbox" name="privileges[]" value="administrator" />
            <label for="admin">Administrator</label>
        </p>
    </form>
</div>


