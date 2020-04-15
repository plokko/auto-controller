<?php
namespace plokko\AutoController\ControllerConfig;

use ArrayAccess, Countable;
use plokko\AutoController\ControllerConfig;

class FieldListBuilder implements ArrayAccess, Countable, FieldDefinitionInterface
{
    protected
        /** @var FieldBuilder[] */
        $fields = [];
    private
        /** @var ControllerConfig */
        $parent;
    public
        /** @var string|null */
        $dictionary = null;

    function __construct(ControllerConfig $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @param string|null $dictionary
     * @return $this
     */
    function setDictionary($dictionary){
        $this->dictionary = $dictionary;
        return $this;
    }

    public function define($field):FieldBuilder
    {
        if(!isset($this->fields[$field])){
            $this->fields[$field] = new FieldBuilder($field,$this);
        }
        return $this->fields[$field];
    }

    function set($key,$value=null){
        foreach(is_array($key)?$key:[$key=>$value] AS $k=>$v){
            if(is_int($k)){
                $k=$v;
                $v=null;
            }
            $field = $this->define($k);
            if($v)
                $field->set($v);
        }
        return $this;
    }


    //--- ArrayAccess implementation ---//
    public function offsetExists($offset){
        return isset($this->fields[$offset]);
    }

    public function offsetGet($offset):FieldBuilder{
        return $this->define($offset);
    }

    public function offsetSet($offset, $value){}

    public function offsetUnset($offset){
        unset($this->fields[$offset]);
    }

    //--- Countable ---//
    public function count(){
        return count($this->fields);
    }

    public function then(): FieldListBuilder
    {
        return $this;
    }

    public function endFields(): ControllerConfig
    {
        return $this->parent;
    }

    //--- Utils ---//
    function resetAllFields(){
        $this->fields = [];
    }

    /**
     * @return string|null dictionary root for fields
     */
    function getDictionary(){
        return $this->dictionary?:
            ($this->parent->dictionary?
                $this->parent->dictionary.'.fields'
                :null
            );
    }

    //---
    function toArray(){
        $fields = [];
        foreach($this->fields AS $field){
            $fields[]= $field->toArray();
        }
        return $fields;
    }

}
