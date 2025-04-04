<?php
/** @var Throwable $throwable  */
?>
<html>
<head>
    <title>Storm PHP Framework - 500</title>
</head>
<body>
    <h2><?php echo $throwable->getMessage() ?></h2>
    <pre><?php echo $throwable->getTraceAsString() ?></pre>

    <?php
        $previous = $throwable->getPrevious();
        $i = 0;
        while($previous != null)
        {
            echo "<h3>#$i: {$previous->getMessage()}</h3>";
            echo "<pre>{$previous->getTraceAsString()}</pre>";
            $i++;
            $previous = $previous->getPrevious();
        }
    ?>
</body>
</html>
