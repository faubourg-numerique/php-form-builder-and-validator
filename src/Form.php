<?php
namespace PierreJosselin\FormBuilderAndValidator;

class Form
{
    private $style;
    private $messages;
    private $fields = [];
    private $attributes = [];
    
    public function __construct(string $localization = "french", string $style = "default")
    {
        while(true)
        {
            $filename = __DIR__ . "/localization/{$localization}.json";
            if(!is_file($filename)) break;
            $content = @file_get_contents($filename);
            if($content === false) break;
            $array = json_decode($content, true);
            if(json_last_error() !== JSON_ERROR_NONE) break;
            $this -> messages = $array;
            break;
        }
        
        if(!$this -> messages)
        {
            throw new \Exception("Invalid localization file.");
        }
        
        if($style)
        {
            if(in_array($style, array("bootstrap", "default")))
            {
                $this -> style = $style;
            }
        }
    }
    
    public function addField(Field $field)
    {
        $this -> fields[] = $field;
    }
    
    public function setAttribute(string $name, string $value = "")
    {
        $this -> attributes[$name] = $value;
    }
    
    public function build()
    {
        $style = $this -> style;
        $messages = $this -> messages;
        
        $output = "<form ";
        foreach($this -> attributes as $name => $value)
        {
            $output .= "{$name}=" . $this -> encodeValue($value) . " ";
        }
        $output .= ">";
        
        foreach($this -> fields as $field)
        {
            $type = $field -> getType();
            $label = $field -> getLabel();
            $attributes = $field -> getAttributes();
            
            switch($style)
            {
                case "bootstrap":
                {
                    $class = [];
                    if(array_key_exists("class", $attributes))
                    {
                        $class[] = $attributes["class"];
                        unset($attributes["class"]);
                    }
                    $output .= "<div class='form-group'>";
                    switch($type)
                    {
                        case "select":
                        case "datalist":
                        {
                            $options = $field -> getOptions();
                            if($type == "select") $class[] = "custom-select";
                            if(!is_null($label))
                            {
                                $output .= "<label ";
                                if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                                $output .= ">{$label}</label>";
                            }
                            $output .= "<{$type} ";
                            $output .= "class='" . implode(" ", $class) . "' ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            if($options)
                            {
                                foreach($options as $option)
                                {
                                    $option_label = $option -> getLabel();
                                    $option_attributes = $option -> getAttributes();
                                    $output .= "<option ";
                                    foreach($option_attributes as $name => $value)
                                        $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                                    $output .= ">";
                                    if(!is_null($option_label))
                                        $output .= $option_label;
                                    $output .= "</option>";
                                }
                            }
                            $output .= "</{$type}>";
                            break;
                        }
                        case "textarea":
                        {
                            $value_backup = null;
                            if(array_key_exists("value", $attributes))
                            {
                                $value_backup = $attributes["value"];
                                unset($attributes["value"]);
                            }
                            $class[] = "form-control";
                            if(!is_null($label))
                            {
                                $output .= "<label ";
                                if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                                $output .= ">{$label}</label>";
                            }
                            $output .= "<textarea ";
                            $output .= "class='" . implode(" ", $class) . "' ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            if(!is_null($value_backup)) $output .= $value_backup;
                            $output .= "</textarea>";
                            break;
                        }
                        case "button":
                        case "reset":
                        case "submit":
                        {
                            $class[] = "btn btn-primary";
                            $output .= "<input ";
                            $output .= "type='{$type}' ";
                            $output .= "class='" . implode(" ", $class) . "' ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            break;
                        }
                        case "checkbox":
                        case "radio":
                        {
                            $class[] = "custom-control-input";
                            $output .= "<div class='custom-control custom-{$type}'>";
                            $output .= "<input ";
                            $output .= "type='{$type}' ";
                            $output .= "class='" . implode(" ", $class) . "' ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            if(!is_null($label))
                            {
                                $output .= "<label ";
                                if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                                $output .= "class='custom-control-label' ";
                                $output .= ">{$label}</label>";
                            }
                            $output .= "</div>";
                            break;
                        }
                        case "file":
                        {
                            $class[] = "custom-file-input";
                            if(!is_null($label))
                            {
                                $output .= "<label ";
                                if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                                $output .= ">{$label}</label>";
                            }
                            $output .= "<div class='custom-file'>";
                            $output .= "<input ";
                            $output .= "type='file' ";
                            $output .= "class='" . implode(" ", $class) . "' ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            $output .= "<label ";
                            $output .= "class='custom-file-label' ";
                            if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                            $output .= ">{$messages["select-file"]}</label>";
                            $output .= "</div>";
                            break;
                        }
                        case "range":
                        {
                            $class[] = "custom-range";
                            if(!is_null($label))
                            {
                                $output .= "<label ";
                                if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                                $output .= ">{$label}</label>";
                            }
                            $output .= "<input ";
                            $output .= "type='range' ";
                            $output .= "class='" . implode(" ", $class) . "' ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            break;
                        }
                        default:
                        {
                            $class[] = "form-control";
                            if(!is_null($label))
                            {
                                $output .= "<label ";
                                if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                                $output .= ">{$label}</label>";
                            }
                            $output .= "<input ";
                            $output .= "type='{$type}' ";
                            $output .= "class='" . implode(" ", $class) . "' ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            break;
                        }
                    }
                    $output .= "</div>";
                    break;
                }
                default:
                {
                    $output .= "<div>";
                    switch($type)
                    {
                        case "select":
                        case "datalist":
                        {
                            $options = $field -> getOptions();
                            if(!is_null($label))
                            {
                                $output .= "<label ";
                                if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                                $output .= ">{$label}</label>";
                            }
                            $output .= "<select ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            if($options)
                            {
                                foreach($options as $option)
                                {
                                    $option_label = $option -> getLabel();
                                    $option_attributes = $option -> getAttributes();
                                    $output .= "<option ";
                                    foreach($option_attributes as $name => $value)
                                        $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                                    $output .= ">";
                                    if(!is_null($option_label))
                                        $output .= $option_label;
                                    $output .= "</option>";
                                }
                            }
                            $output .= "</select>";
                            break;
                        }
                        case "textarea":
                        {
                            $value_backup = null;
                            if(array_key_exists("value", $attributes))
                            {
                                $value_backup = $attributes["value"];
                                unset($attributes["value"]);
                            }
                            if(!is_null($label))
                            {
                                $output .= "<label ";
                                if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                                $output .= ">{$label}</label>";
                            }
                            $output .= "<textarea ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            if(!is_null($value_backup)) $output .= $value_backup;
                            $output .= "</textarea>";
                            break;
                        }
                        case "button":
                        case "reset":
                        case "submit":
                        {
                            $output .= "<input ";
                            $output .= "type='{$type}' ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            break;
                        }
                        case "checkbox":
                        case "radio":
                        {
                            $output .= "<input ";
                            $output .= "type='{$type}' ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            if(!is_null($label))
                            {
                                $output .= "<label ";
                                if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                                $output .= ">{$label}</label>";
                            }
                            break;
                        }
                        default:
                        {
                            if(!is_null($label))
                            {
                                $output .= "<label ";
                                if(array_key_exists("id", $attributes)) $output .= "for='{$attributes["id"]}' ";
                                $output .= ">{$label}</label>";
                            }
                            $output .= "<input ";
                            $output .= "type='{$type}' ";
                            foreach($attributes as $name => $value)
                                $output .= "{$name}=" . $this -> encodeValue($value) . " ";
                            $output .= ">";
                            break;
                        }
                    }
                    $output .= "</div>";
                    break;
                }
            }
        }
        
        $output .= "</form>";
        return $output;
    }
    
    public function validate()
    {
        $errors = [];
        $method = (array_key_exists("method", $this -> attributes) ? strtolower($this -> attributes["method"]) : "get");
        $data = ($method == "post" ? $_POST : $_GET);
        $messages = $this -> messages;
        
        foreach($this -> fields as $field)
        {
            $type = $field -> getType();
            $attributes = $field -> getAttributes();
            $required = array_key_exists("required", $attributes);
            $disabled = array_key_exists("disabled", $attributes);
            $multiple = array_key_exists("multiple", $attributes);
            
            if($disabled) continue;
            if(in_array($type, array("button", "image", "reset", "submit", "datalist", "file"))) continue;
            if(!array_key_exists("name", $attributes)) continue;
            
            $name = $attributes["name"];
            $label = (!is_null($field -> getLabel()) ? $field -> getLabel() : $name);
            
            if(!array_key_exists($attributes["name"], $data))
            {
                if($required) $errors[] = sprintf($messages["required"], $label);
                continue;
            }
            
            $value = $data[$attributes["name"]];
            
            if(!is_string($value))
            {
                $errors[] = sprintf($messages["invalid"], $label);
                continue;
            }
            
            if($value === "")
            {
                if($required)
                    $errors[] = sprintf($messages["required"], $label);
                continue;
            }
            
            switch($type)
            {
                case "textarea":
                {
                    if(array_key_exists("minlength", $attributes))
                        if(mb_strlen($value) < $attributes["minlength"])
                            $errors[] = sprintf($messages["min-length"], $label, $attributes["minlength"]);
                    if(array_key_exists("maxlength", $attributes))
                        if(mb_strlen($value) > $attributes["maxlength"])
                            $errors[] = sprintf($messages["max-length"], $label, $attributes["maxlength"]);
                    break;
                }
                case "color":
                {
                    $success = false;
                    while(true)
                    {
                        if(substr($value, 0, 1) !== "#") break;
                        $temp = ltrim($value, "#");
                        if(!ctype_xdigit($temp)) break;
                        if(strlen($temp) !== 6) break;
                        $success = true;
                        break;
                    }
                    if(!$success)
                        $errors[] = sprintf($messages["color"], $label);
                    break;
                }
                case "date":
                {
                    if(!$this -> validateDateTime($value, "Y-m-d"))
                    {
                        $errors[] = sprintf($messages["date"], $label);
                        break;
                    }
                    $dateTime = new \DateTime($value);
                    if(array_key_exists("min", $attributes))
                        if(new \DateTime($attributes["min"]) > $dateTime)
                            $errors[] = sprintf($messages["min"], $label, $attributes["min"]);
                    if(array_key_exists("max", $attributes))
                        if(new \DateTime($attributes["max"]) < $dateTime)
                            $errors[] = sprintf($messages["max"], $label, $attributes["max"]);
                    break;
                }
                case "datetime-local":
                {
                    if(!$this -> validateDateTime($value, "Y-m-d\TH:i") && !$this -> validateDateTime($value, "Y-m-d\TH:i:s"))
                    {
                        $errors[] = sprintf($messages["date"], $label);
                        break;
                    }
                    $dateTime = new \DateTime($value);
                    if(array_key_exists("min", $attributes))
                        if(new \DateTime($attributes["min"]) > $dateTime)
                            $errors[] = sprintf($messages["min"], $label, $attributes["min"]);
                    if(array_key_exists("max", $attributes))
                        if(new \DateTime($attributes["max"]) < $dateTime)
                            $errors[] = sprintf($messages["max"], $label, $attributes["max"]);
                    break;
                }
                case "email":
                {
                    if($multiple)
                    {
                        $pieces = explode(",", $value);
                        foreach($pieces as $piece)
                        {
                            if(!filter_var($piece, FILTER_VALIDATE_EMAIL))
                            {
                                $errors[] = sprintf($messages["email"], $label);
                                break 2;
                            }
                        }
                    }
                    else
                    {
                        if(!filter_var($value, FILTER_VALIDATE_EMAIL))
                        {
                            $errors[] = sprintf($messages["email"], $label);
                            break;
                        }
                    }
                    if(array_key_exists("minlength", $attributes))
                        if(mb_strlen($value) < $attributes["minlength"])
                            $errors[] = sprintf($messages["min-length"], $label, $attributes["minlength"]);
                    if(array_key_exists("maxlength", $attributes))
                        if(mb_strlen($value) > $attributes["maxlength"])
                            $errors[] = sprintf($messages["max-length"], $label, $attributes["maxlength"]);
                    break;
                }
                case "month":
                {
                    if(!$this -> validateDateTime($value, "Y-m"))
                    {
                        $errors[] = sprintf($messages["date"], $label);
                        break;
                    }
                    $dateTime = new \DateTime($value);
                    if(array_key_exists("min", $attributes))
                        if(new \DateTime($attributes["min"]) > $dateTime)
                            $errors[] = sprintf($messages["min"], $label, $attributes["min"]);
                    if(array_key_exists("max", $attributes))
                        if(new \DateTime($attributes["max"]) < $dateTime)
                            $errors[] = sprintf($messages["max"], $label, $attributes["max"]);
                    break;
                }
                case "number":
                case "range":
                {
                    if(!is_numeric($value))
                    {
                        $errors[] = sprintf($messages["number"], $label);
                        break;
                    }
                    if(array_key_exists("min", $attributes))
                        if(floatval($value) < floatval($attributes["min"]))
                            $errors[] = sprintf($messages["min"], $label, $attributes["min"]);
                    if(array_key_exists("max", $attributes))
                        if(floatval($value) > floatval($attributes["max"]))
                            $errors[] = sprintf($messages["max"], $label, $attributes["max"]);
                    break;
                }
                case "password":
                case "search":
                case "text":
                {
                    if(array_key_exists("minlength", $attributes))
                        if(mb_strlen($value) < $attributes["minlength"])
                            $errors[] = sprintf($messages["min-length"], $label, $attributes["minlength"]);
                    if(array_key_exists("maxlength", $attributes))
                        if(mb_strlen($value) > $attributes["maxlength"])
                            $errors[] = sprintf($messages["max-length"], $label, $attributes["maxlength"]);
                    break;
                }
                case "tel":
                {
                    $temp = ltrim($value, "+");
                    if(!ctype_digit($temp))
                    {
                        $errors[] = sprintf($messages["tel"], $label);
                        break;
                    }
                    if(array_key_exists("minlength", $attributes))
                        if(mb_strlen($value) < $attributes["minlength"])
                            $errors[] = sprintf($messages["min-length"], $label, $attributes["minlength"]);
                    if(array_key_exists("maxlength", $attributes))
                        if(mb_strlen($value) > $attributes["maxlength"])
                            $errors[] = sprintf($messages["max-length"], $label, $attributes["maxlength"]);
                    break;
                }
                case "time":
                {
                    if(!$this -> validateDateTime($value, "H:i") && !$this -> validateDateTime($value, "H:i:s"))
                    {
                        $errors[] = sprintf($messages["date"], $label);
                        break;
                    }
                    $dateTime = new \DateTime($value);
                    if(array_key_exists("min", $attributes))
                        if(new \DateTime($attributes["min"]) > $dateTime)
                            $errors[] = sprintf($messages["min"], $label, $attributes["min"]);
                    if(array_key_exists("max", $attributes))
                        if(new \DateTime($attributes["max"]) < $dateTime)
                            $errors[] = sprintf($messages["max"], $label, $attributes["max"]);
                    break;
                }
                case "url":
                {
                    if(!filter_var($value, FILTER_VALIDATE_URL))
                    {
                        $errors[] = sprintf($messages["url"], $label);
                        break;
                    }
                    if(array_key_exists("minlength", $attributes))
                        if(mb_strlen($value) < $attributes["minlength"])
                            $errors[] = sprintf($messages["min-length"], $label, $attributes["minlength"]);
                    if(array_key_exists("maxlength", $attributes))
                        if(mb_strlen($value) > $attributes["maxlength"])
                            $errors[] = sprintf($messages["max-length"], $label, $attributes["maxlength"]);
                    break;
                }
            }
        }
        return $errors;
    }
    
    private function validateDateTime($string, $format)
    {
        $dateTime = \DateTime::createFromFormat($format, $string);
        return $dateTime && $dateTime -> format($format) === $string;
    }
    
    private function encodeValue($value)
    {
        return '"' . str_replace('"', "&quot;", $value) . '"'; 
    }
}