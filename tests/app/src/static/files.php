
<form method="post" enctype="multipart/form-data">
    <input type="file" name="file" />
    <input type="file" name="photo[a][b]" />
    <input type="file" name="photo[c][d]" />

    <button>Send</button>
</form>


<?php

use Stormmore\Framework\Mvc\IO\Request;

/** @var Request $request */

if ($request->isPost()) {
    echo '<pre>';
    echo var_export($_FILES, true);
    echo '</pre>';
}
