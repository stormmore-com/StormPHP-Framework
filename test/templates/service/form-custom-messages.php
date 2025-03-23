<?php
/**
 * @var array $days
 * @var \Stormmore\Framework\Mvc\View\View $view
 * @var \Configuration\CustomMessagesForm $form
 */

$view->setLayout('@templates/includes/layout.php');
?>

<?php if ($form->isSubmittedSuccessfully()): ?>
    <div class="success">Success! Form has no errors</div>
<?php endif ?>
<?php  if (!$form->isValid()): ?>
    <div class="failure">Failure! Form has errors</div>
<?php endif ?>

<form action="/form-custom-messages" enctype="multipart/form-data" method="post">
    <table>

        <!-- Required -->
        <tr>
            <td><label for="required">Required:</label></td>
            <td><input id="required" type="text" name="required" value="<?php echo $form->required ?>" /></td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->required): ?>
                    <div><?php echo $form->errors->required ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Alpha -->
        <tr>
            <td><label for="alpha">Alpha:</label></td>
            <td><input id="alpha" type="text" name="alpha" value="<?php echo $form->alpha ?>" /></td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->alpha): ?>
                    <div><?php echo $form->errors->alpha ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Alpha num -->
        <tr>
            <td><label for="alphaNum">AlphaNum:</label></td>
            <td><input id="alphaNum" type="text" name="alphaNum" value="<?php echo $form->alphaNum ?>" /></td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->alphaNum): ?>
                    <div><?php echo $form->errors->alphaNum ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Regexp -->
        <tr>
            <td><label for="regexp">Regexp: </label></td>
            <td>
                <input id="regexp" type="text" name="regexp" value="<?php echo $form->regexp ?>" />
                 *first letter should be capital
            </td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->regexp): ?>
                    <div><?php echo $form->errors->regexp ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Weekend -->
        <tr>
            <td><label for="option">Values: </label></td>
            <td>
                <select id="option" name="values">
                    <option></option>
                    <?php $view->html->options([-1, 0, 1, 2], $form->values) ?>
                </select>
                * 1 and 2 are correct values
            </td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->values): ?>
                    <div><?php echo $form->errors->values ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Email -->
        <tr>
            <td><label for="email">Email:</label></td>
            <td><input id="email" type="text" name="email" value="<?php echo $form->email ?>" /></td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->email): ?>
                    <div><?php echo $form->errors->email ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Min -->
        <tr>
            <td><label for="min">Min:</label></td>
            <td>
                <input id="min" type="text" name="min" value="<?php echo $form->min ?>" /> *min. value is 1
            </td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->min): ?>
                    <div><?php echo $form->errors->min ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Max -->
        <tr>
            <td><label for="max">Max:</label></td>
            <td>
                <input id="max" type="text" name="max" value="<?php echo $form->max ?>" /> *max value is 10
            </td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->max): ?>
                    <div><?php echo $form->errors->max ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Integer -->
        <tr>
            <td><label for="int">Integer:</label></td>
            <td><input id="int" type="text" name="int" value="<?php echo $form->int ?>" /></td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->int): ?>
                    <div><?php echo $form->errors->int ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Float -->
        <tr>
            <td><label for="float">Float:</label></td>
            <td><input id="float" type="text" name="float" value="<?php echo $form->float ?>" /></td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->float): ?>
                    <div><?php echo $form->errors->float ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Number -->
        <tr>
            <td><label for="number">Number:</label></td>
            <td><input id="number" type="text" name="number" value="<?php echo $form->number ?>" /></td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->number): ?>
                    <div><?php echo $form->errors->number ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- File -->
        <tr>
            <td><label for="file">File:</label></td>
            <td><input name="file" type="file"/>*max. file size 10Kb</td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->file): ?>
                    <div><?php echo $form->errors->file ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Image -->
        <tr>
            <td><label for="image">Image:</label></td>
            <td><input name="image" type="file"/></td>
        </tr>
        <tr>
            <td colspan="2" class="error">
                <?php if ($form->errors->image): ?>
                    <div><?php echo $form->errors->image ?></div>
                <?php endif ?>
            </td>
        </tr>

        <!-- Send -->
        <tr>
            <td colspan="2"><button>Send</button></td>
        </tr>
    </table>
</form>
