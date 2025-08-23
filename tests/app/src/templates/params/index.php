<?php
/**
 * @var View $view
 * @var string $name
 */

use Stormmore\Framework\Mvc\View\View;

$view->setTitle("Storm App - Params");
$view->useLayout("@templates/includes/layout.php");
?>

<!-- non typed -->
<h3>Non typed</h3>
<div>
    Parameter `$arg1`
    <a href="/params/arg?arg1=hello-world">Valid</a>
    <a href="/params/arg?arg1=">Empty</a>
    <a href="/params/arg">None</a>
</div>
<div>
    Parameter `$arg1 = "default string"`
    <a href="/params/arg-default?arg1=hello-world">Valid</a>
    <a href="/params/arg-default?arg1=">Empty</a>
    <a href="/params/arg-default">None</a>
</div>
<div>
    Parameter `$arg1 = null`
    <a href="/params/arg-optional?string=hello-world">Valid</a>
    <a href="/params/arg-optional?string=">Invalid</a>
    <a href="/params/arg-optional">None</a>
</div>

<!-- string -->
<h3>String</h3>
<div>
    Parameter `string $string`
    <a href="/params/string?string=hello-world">Valid</a>
    <a href="/params/string?string=">Empty</a>
    <a href="/params/string">None</a>
</div>
<div>
    Parameter `string $string = "default string"`
    <a href="/params/string-default?string=hello-world">Valid</a>
    <a href="/params/string-default?string=">Empty</a>
    <a href="/params/string-default">None</a>
</div>
<div>
    Parameter `?string $string`
    <a href="/params/string-optional?string=hello-world">Valid</a>
    <a href="/params/string-optional?string=">Invalid</a>
    <a href="/params/string-optional">None</a>
</div>

<!-- bool -->
<h3>Bool</h3>
<div>
    Parameter `bool $bool`
    <a href="/params/bool?bool=true">Valid (true)</a>
    <a href="/params/bool?bool=1">Valid (1)</a>
    <a href="/params/bool?bool=false">Valid (false)</a>
    <a href="/params/bool?bool=0">Valid (0)</a>
    <a href="/params/bool?bool=xyz">Invalid</a>
    <a href="/params/bool?bool=">Empty</a>
    <a href="/params/bool">None</a>
</div>
<div>
    Parameter `bool $bool = true`
    <a href="/params/bool-default?bool=true">Valid</a>
    <a href="/params/bool-default?bool=xyz">Invalid</a>
    <a href="/params/bool-default?bool=">Empty</a>
    <a href="/params/bool-default">None</a>
</div>
<div>
    Parameter `?string $string`
    <a href="/params/bool-optional?bool=true">Valid</a>
    <a href="/params/bool-optional?bool=xyz">Invalid</a>
    <a href="/params/bool-optional?bool=">Empty</a>
    <a href="/params/bool-optional">None</a>
</div>

<!-- int -->
<h3>Int</h3>
<div>
    Parameter `int $int`
    <a href="/params/int?int=5">Valid</a>
    <a href="/params/int?int=5.5">Valid (float)</a>
    <a href="/params/int?int=5,4">Invalid (,)</a>
    <a href="/params/int?int=xyz">Invalid (xyz)</a>
    <a href="/params/int?int=">Empty</a>
    <a href="/params/int">None</a>
</div>
<div>
    Parameter `int $int = 8`
    <a href="/params/int-default?int=5">Valid</a>
    <a href="/params/int-default?int=5.5">Valid (float)</a>
    <a href="/params/int-default?int=5,4">Invalid (,)</a>
    <a href="/params/int-default?int=xyz">Invalid (xyz)</a>
    <a href="/params/int-default?int=">Empty</a>
    <a href="/params/int-default">None</a>
</div>
<div>
    Parameter `?int $int`
    <a href="/params/int-optional?int=5">Valid</a>
    <a href="/params/int-optional?int=5.5">Valid (float)</a>
    <a href="/params/int-optional?int=5,4">Invalid (,)</a>
    <a href="/params/int-optional?int=xyz">Invalid (xyz)</a>
    <a href="/params/int-optional?int=">Empty</a>
    <a href="/params/int-optional">None</a>
</div>

<!-- float -->
<h3>Float</h3>
<div>
    Parameter `float $float`
    <a href="/params/float?float=5.5">Valid</a>
    <a href="/params/float?float=5">Valid (int)</a>
    <a href="/params/float?float=5,4">Invalid (,)</a>
    <a href="/params/float?float=xyz">Invalid (xyz)</a>
    <a href="/params/float?float=">Empty</a>
    <a href="/params/float">None</a>
</div>
<div>
    Parameter `float $float = 8.7`
    <a href="/params/float-default?float=5.5">Valid</a>
    <a href="/params/float-default?float=5">Valid (int)</a>
    <a href="/params/float-default?float=5,4">Invalid (,)</a>
    <a href="/params/float-default?float=xyz">Invalid (xyz)</a>
    <a href="/params/float-default?float=">Empty</a>
    <a href="/params/float-default">None</a>
</div>
<div>
    Parameter `?float $float`
    <a href="/params/float-optional?float=5.5">Valid</a>
    <a href="/params/float-optional?float=5">Valid (int)</a>
    <a href="/params/float-optional?float=5,4">Invalid (,)</a>
    <a href="/params/float-optional?float=xyz">Invalid (xyz)</a>
    <a href="/params/float-optional?float=">Empty</a>
    <a href="/params/float-optional">None</a>
</div>

<!-- array -->
<h3>Array</h3>
<div>
    Parameter `array $array`
    <a href="/params/array?array[]=1&array[]=2">Valid</a>
    <a href="/params/array?array[]=">Empty</a>
    <a href="/params/array">None</a>
</div>
<div>
    Parameter `array $array = [1,2,3,4,5]`
    <a href="/params/array-default?array[]=1&array[]=2">Valid</a>
    <a href="/params/array-default?array=">Invalid</a>
    <a href="/params/array-default">None</a>
</div>
<div>
    Parameter `?array $array`
    <a href="/params/array-optional?array[]=1">Valid</a>
    <a href="/params/array-optional?array=">Invalid</a>
    <a href="/params/array-optional">None</a>
</div>
<div>
    Parameter `?array $array = [1,23]`
    <a href="/params/array-optional-default?array[]=1">Valid</a>
    <a href="/params/array-optional-default?array=">Invalid</a>
    <a href="/params/array-optional-default">None</a>
</div>

<!-- DateTime -->
<h3>DateTime</h3>
<div>
    Parameter `DateTime $date`
    <a href="/params/date-time?date=22-02-2022">Valid</a>
    <a href="/params/date-time?date=abcdef">Invalid</a>
    <a href="/params/date-time">None</a>
</div>
<div>
    Parameter `DateTime $date = new DateTime("07-07-1997")`
    <a href="/params/date-time-default?date=22-02-2022">Valid</a>
    <a href="/params/date-time-default?date=abcdef">Invalid</a>
    <a href="/params/date-time-default">None</a>
</div>
<div>
    Parameter `?DateTime $date`
    <a href="/params/date-time-optional?date=22-02-2022">Valid</a>
    <a href="/params/date-time-optional?date=abcdef">Invalid</a>
    <a href="/params/date-time-optional">None</a>
</div>
<div>
    <a href="/params/search-object?phrase=test&prder=asc">Object</a>
</div>

<!-- multiple typed arguments -->
<h3>Multiple typed arguments</h3>
<div>
    `?string $phrase, ?string $order, ?DateTime $from, ?DateTime $to`
    <a href="/params/search">Search</a>
</div>
<div>
    `string $phrase, string order, DateTime $from, DateTime $to`
    <a href="/params/search-required?phrase=search-phrase&order=asc&from=2000-01-01&to=2001-01-01">Search</a>
</div>
<div>
    <?php
        $yesterday = (new DateTime("yesterday"))->format('Y-m-d');
        $today = (new DateTime("today"))->format('Y-m-d');
    ?>
    `string $phrase = "today news", string $order = "asc", DateTime $from = new DateTime('yesterday'), DateTime $to = new DateTime('Today')`
    <a href="/params/search-optional?phrase=php%20news&order=asc&from=<?= $yesterday ?>&to=<?= $today ?>">Search</a>
</div>
