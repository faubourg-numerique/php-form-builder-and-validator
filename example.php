<?php
require_once("FormBuilderAndValidator/Form.php");
require_once("FormBuilderAndValidator/Field.php");

use PierreJosselin\FormBuilderAndValidator\Form;
use PierreJosselin\FormBuilderAndValidator\Field;

// Form
$form = new Form("english", "bootstrap");
$form -> setAttribute("method", "post");
$form -> setAttribute("action", "");

// Text
$field = new Field("text");
$field -> setLabel("Text");
$field -> setAttribute("id", "text");
$field -> setAttribute("name", "text");
$field -> setAttribute("placeholder", "Enter text here...");
$field -> setAttribute("minlength", 5);
$field -> setAttribute("maxlength", 30);
$field -> setAttribute("required");
$form -> addField($field);

// Textarea
$field = new Field("textarea");
$field -> setLabel("Textarea");
$field -> setAttribute("id", "textarea");
$field -> setAttribute("name", "textarea");
$field -> setAttribute("placeholder", "Enter text here...");
$form -> addField($field);

// Range
$field = new Field("range");
$field -> setLabel("Range");
$field -> setAttribute("id", "range");
$field -> setAttribute("name", "range");
$field -> setAttribute("min", 1);
$field -> setAttribute("max", 5);
$field -> setAttribute("step", 1);
$form -> addField($field);

// Checkbox
$field = new Field("checkbox");
$field -> setLabel("This is a checkbox.");
$field -> setAttribute("id", "checkbox");
$field -> setAttribute("name", "checkbox");
$field -> setAttribute("required");
$form -> addField($field);

// Submit
$field = new Field("submit");
$field -> setAttribute("name", "submit");
$field -> setAttribute("value", "Validate");
$form -> addField($field);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
        <title>PHP Form Builder And Validator example page</title>
    </head>
    <body>
        <div style="width: 450px;" class="container mt-5">
            <?php
                if(isset($_POST["submit"]))
                {
                    $errors = $form -> validate();
                    
                    if($errors)
                        foreach($errors as $error)
                            echo "<div class='alert alert-danger'>{$error}</div>";
                    else
                        echo "<div class='alert alert-success'>The form was validated without error.</div>";
                }
            ?>
            <?= $form -> build() ?>
        </div>
    </body>
</html>