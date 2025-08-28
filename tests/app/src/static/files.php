
<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" />

    <input type="file" name="tab[]" />
    <input type="file" name="tab[]" />

    <input type="file" name="assoc[a][b][0][0]" />
    <input type="file" name="assoc[a][c]" />
    <input type="file" name="assoc[a][b][1]" />
    <input type="file" name="assoc[d][e]" />
    <input type="file" name="assoc[1][]" />

    <button>Send</button>
</form>


<?php

use Stormmore\Framework\Mvc\IO\Request;

/** @var Request $request */


if ($request->isPost()) {

    $fileParser = new Request\FileArrayParser();
    $files = $fileParser->parse($_FILES);
    echo '<pre>';
    var_dump($request->files->getAll());
    echo "</pre>";
}
