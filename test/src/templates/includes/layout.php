<?php /** @var Stormmore\Framework\Mvc\View $view */ ?>
<!DOCTYPE html>
<html>
<head>
    <?php
        $view->printCss();
        $view->printJs();
        $view->printTitle("StormApp");
    ?>
</head>
    <body>
        <main>
            <div style="width: 1024px; margin:0 auto">
                <h1>Storm PHP Framework &#9889;</h1>
                <?php echo $view->content ?>
            </div>
        </main>
    </body>
</html>

