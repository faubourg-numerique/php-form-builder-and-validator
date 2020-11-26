# PHP Form Builder And Validator

PHP Form Builder And Validator is a micro framework for PHP. Its purpose is to automate creation and validation of basic forms.

## Installation

### Requirements

* PHP >= 7.0.3

### Installing

Simply clone or download the project and move it to your server.  
There is no dependency.

## Features

Here is an exhaustive list of supported field types and what will be checked.  
In parentheses, the list of html attributes that will be checked.  
The fields that will be checked are in bold. (e.g. is this email field a valid email address)  
The `required` attribute will be automatically checked for all fields.

* button
* checkbox
* **color**
* **date**
* **datetime-local**
* **email** (`minlength`, `maxlength`, `multiple`)
* file
* hidden
* image
* **month** (`min`, `max`)
* **number** (`min`, `max`)
* password (`minlength`, `maxlength`)
* radio
* **range** (`min`, `max`)
* reset
* search (`minlength`, `maxlength`)
* submit
* **tel** (`minlength`, `maxlength`)
* text (`minlength`, `maxlength`)
* **time** (`min`, `max`)
* **url** (`minlength`, `maxlength`)
* select
* datalist
* textarea (`minlength`, `maxlength`)

### Localization

* english
* French

### Style

* bootstrap
* default

## Usage

First step is to include the **Form** and **Field** classes in your project.

```php
require_once("FormBuilderAndValidator/Form.php");
require_once("FormBuilderAndValidator/Field.php");
use PierreJosselin\FormBuilderAndValidator\Form;
use PierreJosselin\FormBuilderAndValidator\Field;
```

Start by instantiating an object of the **Form** class.  
The constructor takes two optional arguments.  
The first `$localization` is the name of the localization file, without its extension.  
The second `$style` is the name of the style to use.  
Use the `setAttribute()` function to define attributes specific to the form.

```php
$form = new Form("english", "bootstrap");
$form -> setAttribute("method", "post");
$form -> setAttribute("action", "/example.php");
```

Create as many field objects as you want and add them to the main form.  
The constructor except one mandatory argument, `$type` the field type.  
Use the `setAttribute()` function to define attributes specific to the field.

```php
$field = new Field("hello");
$field -> setLabel("Hello");
$field -> setAttribute("id", "hello");
$field -> setAttribute("name", "hello");
$field -> setAttribute("minlength", 10);
$field -> setAttribute("required");
$form -> addField($field);
```

Once your form is ready, you can build and display it.

```php
echo $form -> build();
```

In a context where the form has been sent, you can validate it.  
`$error` will be a list of errors. If the list is empty, no error has been detected, the form should be valid.

```php
if(isset($_POST["hello"]))
{
    $errors = $form -> validate();
    var_dump($errors);
}
```

### Special cases `select` and `datalist`

The use of `select` and `datalist` is a little bit special. Indeed, you will have to create a `select` (or `datalist`) field, create one or more `option` fields and add them to the `select` field with the `addOption()` function, then finally add the `select` field to the main form.

```php
$select = new Field("select");
$select -> setLabel("City");
$select -> setAttribute("id", "city");
$select -> setAttribute("name", "city");

$option = new Field("option");
$option -> setLabel("Paris");
$option -> setAttribute("value", "paris");
$select -> addOption($option);

$option = new Field("option");
$option -> setLabel("London");
$option -> setAttribute("value", "london");
$select -> addOption($option);

$option = new Field("option");
$option -> setLabel("Tokyo");
$option -> setAttribute("value", "tokyo");
$select -> addOption($option);

$form -> addField($select);
```
