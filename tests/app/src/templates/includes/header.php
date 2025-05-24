<?php /** @var View $view */

use Stormmore\Framework\Mvc\View\View; ?>
<div style="display: flex;justify-content: space-between;">
    <h1><a href="/">Storm PHP Framework &#9889;</a></h1>
    <div style="margin-top:43px">
        <?php if ($view->appUser->isAuthenticated()): ?>
            <a href="/signout">Sign out</a>
        <?php else: ?>
            <a href="/signin">Sign in</a>
        <?php endif ?>
    </div>
</div>