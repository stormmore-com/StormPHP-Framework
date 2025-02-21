<?php /** @var Stormmore\Framework\Mvc\View $view */ ?>
<!DOCTYPE html>
<html>
<head>
    <?php
        $view->printCss();
        $view->printJs();
        $view->printTitle();
    ?>
</head>
    <body>
        <main>
            <?php echo $view->content ?>
        </main>
    </body>
</html>

