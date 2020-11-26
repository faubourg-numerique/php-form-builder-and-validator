<?php
namespace PierreJosselin\FormBuilderAndValidator;

class Field
{
    private $type;
    private $label;
    private $attributes = [];
    private $options = [];
    
    public function __construct(string $type)
    {
        $value = strtolower($type);
        $this -> type = $type;
    }
    
    public function setLabel(string $label)
    {
        $this -> label = $label;
    }
    
    public function setAttribute(string $name, string $value = "")
    {
        $this -> attributes[$name] = $value;
    }
    
    public function addOption(Field $field)
    {
        if(!in_array($this -> type, array("select", "datalist")))
            throw new \Exception("Options can only be added to select and datalist fields.");
        if($field -> getType() !== "option")
            throw new \Exception("Only options can be added to select and datalist.");
        $this -> options[] = $field;
    }
    
    public function getType()
    {
        return $this -> type;
    }
    
    public function getLabel()
    {
        return $this -> label;
    }
    
    public function getAttributes()
    {
        return $this -> attributes;
    }
    
    public function getOptions()
    {
        return $this -> options;
    }
}