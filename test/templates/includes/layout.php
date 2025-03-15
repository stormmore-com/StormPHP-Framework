<?php /** @var Stormmore\Framework\Mvc\View $view */ ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="/public/style.css" >
    <?php
        $view->printCss();
        $view->printJs();
        $view->printTitle("StormApp");
    ?>
</head>
    <body>
        <main>
            <div style="width: 1024px; margin:0 auto">
                <?php print_view("@templates/includes/header"); ?>
                <?php echo $view->content ?>
            </div>
        </main>
    </body>
</html>

