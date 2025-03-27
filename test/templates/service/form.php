<?php
/**
 * @var array $days
 * @var \Stormmore\Framework\Mvc\View\View $view
 * @var \Configuration\BasicForm $form
 */

$view->setLayout('@templates/includes/layout.php');
?>

<?php if ($form->isSubmittedSuccessfully()): ?>
    <div class="success">Success! Form has no errors</div>
<?php endif ?>
<?php  if (!$form->isValid()): ?>
    <div class="failure">Failure! Form has errors</div>
<?php endif ?>

<form action="/form" enctype="multipart/form-data" method="post">
    <table>
        <thead>
            <tr>
                <th>Field</th>
                <th></th>
                <th>Restrictions</th>
            </tr>
        </thead>
        <!-- Alpha -->
        <tr>
            <td><label for="alpha">Alpha:</label></td>
            <td><input id="alpha" type="text" name="alpha" value="<?php echo $form->alpha ?>" /></td>
            <td>alpha, required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->alpha): ?>
                    <div><?php echo $form->errors->alpha ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Alpha min -->
        <tr>
            <td><label for="alphaMin">Alpha (2 chars min):</label></td>
            <td><input id="alphaMin" type="text" name="alphaMin" value="<?php echo $form->alphaMin ?>" /></td>
            <td>alpha, min:2, required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->alphaMin): ?>
                    <div><?php echo $form->errors->alphaMin ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Alpha max -->
        <tr>
            <td><label for="alphaMax">Alpha (5 chars max):</label></td>
            <td><input id="alphaMax" type="text" name="alphaMax" value="<?php echo $form->alphaMax ?>" /></td>
            <td>alpha, max:5, required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->alphaMax): ?>
                    <div><?php echo $form->errors->alphaMax ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Alpha num -->
        <tr>
            <td><label for="alphaNum">Alpha num:</label></td>
            <td><input id="alphaNum" type="text" name="alphaNum" value="<?php echo $form->alphaNum ?>" /></td>
            <td>alpha numerical, required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->alphaNum): ?>
                    <div><?php echo $form->errors->alphaNum ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Radio (string value) -->
        <tr>
            <td><label for="radio">Radio (string value):</label></td>
            <td>
                <?php  $view->html->radio(id: 'on', name: 'radio', value: 'on', selected: $form->radio) ?>
                <label for="on">on</label>
                <?php  $view->html->radio(id: 'off', name: 'radio', value: 'off', selected: $form->radio) ?>
                <label for="off">off</label>
            </td>
            <td>values [on, off], required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->radio): ?>
                    <div><?php echo $form->errors->radio ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Radio (boolean value) -->
        <tr>
            <td><label for="radio">Radio (bool value):</label></td>
            <td>
                <?php  $view->html->radio(id: 'true', name: 'radioBool', value:"true", selected: $form->radioBool) ?>
                <label for="true">true</label>
                <?php  $view->html->radio( id: 'false', name: 'radioBool', value:"false", selected: $form->radioBool) ?>
                <label for="false">false</label>
            </td>
            <td>values [true, false], required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->radioBool): ?>
                    <div><?php echo $form->errors->radioBool ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Checkbox -->
        <tr>
            <td><label for="checkbox">Checkbox:</label></td>
            <td><?php $view->html->checkbox(id: 'checkbox', name: 'checkbox', value: true, selected: $form->checkbox) ?></td>
            <td>required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->checkbox): ?>
                    <div><?php echo $form->errors->checkbox ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Group checkbox -->
        <tr>
            <td><label for="checkbox">Group checkox:</label></td>
            <td>
                <?php $view->html->checkbox(id: 'carrot', name: 'vegetables[]', value: 'carrot', selected: $form->vegetables) ?>
                <label for="carrot">carrot</label>
                <?php $view->html->checkbox(id: 'onion', name: 'vegetables[]', value: 'onion', selected: $form->vegetables) ?>
                <label for="onion">onion</label>
                <?php $view->html->checkbox(id: 'tomato', name: 'vegetables[]', value: 'tomato', selected: $form->vegetables) ?>
                <label for="tomato">tomato</label>
            </td>
            <td>values [carrot, onion] (tomato is fruit), required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->vegetables): ?>
                    <div><?php echo $form->errors->vegetables ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Email -->
        <tr>
            <td><label for="email">Email:</label></td>
            <td><input id="email" type="text" name="email" value="<?php echo $form->email ?>" /></td>
            <td>email</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->email): ?>
                    <div><?php echo $form->errors->email ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Number -->
        <tr>
            <td><label for="num">Number: </label></td>
            <td><input id="num" type="text" name="num" value="<?php echo $form->num ?>"  /></td>
            <td>number</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->num): ?>
                    <div><?php echo $form->errors->num ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Int number -->
        <tr>
            <td><label for="int">Integer:</label></td>
            <td><input id="int" type="text" name="int" value="<?php echo $form->int ?>" /></td>
            <td>integer</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->int): ?>
                    <div><?php echo $form->errors->int ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Float number -->
        <tr>
            <td><label for="float">Float:</label></td>
            <td><input id="float" type="text" name="float" value="<?php echo $form->float ?>" /></td>
            <td>float</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->float): ?>
                    <div><?php echo $form->errors->float ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Max number -->
        <tr>
            <td><label for="max">Max: </label></td>
            <td><input id="max" type="text" name="max" value="<?php echo $form->max ?>"  /></td>
            <td>max:10, required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->max): ?>
                    <div><?php echo $form->errors->max ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Min number -->
        <tr>
            <td><label for="min">Min:</label></td>
            <td><input id="min" type="text" name="min" value="<?php echo $form->min ?>" /></td>
            <td>min:8, required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->min): ?>
                    <div><?php echo $form->errors->min ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- after -->
        <tr>
            <td><label for="after">After: </label></td>
            <td><input id="after" type="text" name="after" value="<?php echo $form->after ?>" /></td>
            <td>01-01-2010</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->after): ?>
                    <div><?php echo $form->errors->after ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- before -->
        <tr>
            <td><label for="before">Before:</label></td>
            <td><input id="before" type="text" name="before" value="<?php echo $form->before ?>" /></td>
            <td>01-01-2020</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->before): ?>
                    <div><?php echo $form->errors->before ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Regexp -->
        <tr>
            <td><label for="regexp">Regexp: </label></td>
            <td><input id="regexp" type="text" name="regexp" value="<?php echo $form->regexp ?>" /></td>
            <td>One word with capital letter</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->regexp): ?>
                    <div><?php echo $form->errors->regexp ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Weekend -->
        <tr>
            <td><label for="option">Weekend:</label></td>
            <td>
                <select id="option" name="day">
                    <option></option>
                    <?php $view->html->options($days, $form->day) ?>
                </select>
            </td>
            <td>values [Saturday, Sunday], required</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->day): ?>
                    <div><?php echo $form->errors->day ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- File -->
        <tr>
            <td><label for="file">File:</label></td>
            <td><input name="file" type="file"/></td>
            <td>TXT extension allowed, max. 10kb</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->file): ?>
                    <div><?php echo $form->errors->file ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- Image -->
        <tr>
            <td><label for="image">Image:</label></td>
            <td><input name="image" type="file"/></td>
            <td>JPG image allowed only</td>
        </tr>
        <tr>
            <td colspan="3" class="error">
                <?php if ($form->errors->image): ?>
                    <div><?php echo $form->errors->image ?></div>
                <?php endif ?>
            </td>
        </tr>
        <!-- File required -->
        <tr>
            <td>Files required ?</td>
            <td>
                <input id="files-required-y" type="radio" name="files-required" value="true" />
                <label for="files-required-y">Yes</label>
                <input id="files-required-n" type="radio" name="files-required" value="false" />
                <label for="files-required-n">No</label>
            </td>
            <td></td>
        </tr>

        <tr>
            <td colspan="3"><button>Send</button></td>
        </tr>
    </table>
</form>
