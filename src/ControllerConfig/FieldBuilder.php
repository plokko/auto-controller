<?php
namespace plokko\AutoController\ControllerConfig;

use ArrayAccess, Countable;
use plokko\AutoController\ControllerConfig;

/**
 *
 * @method $this label(string $value)
 * @method $this type(string $value)
 */
class FieldBuilder implements ArrayAccess, FieldDefinitionInterface
{
    protected
        $opts = [];

    private
        /** @var FieldListBuilder Parent FieldList builder */
        $parent;

    function __construct($name,FieldListBuilder $builder)
    {
        $this->opts['name'] = $name;
        $this->parent = $builder;
    }


    function __get($k){
        return $this->opts[$k];
    }
    function __set($k,$v){
        if($k!=='name')
            $this->opts[$k]=$v;
    }

    /*
    function __call($name, $arguments)
    {
        $this->opts[$name] = $arguments[0];
        return $this;
    }
    */

    /**
     * @param string|array $key
     * @param mixed $value
     * @return $this
     */
    function set($key,$value=null){
        if(is_array($key)){
            foreach($key AS $k=>$v){
                $this->$k = $v;
            }
        }else{
            $this->$key = $value;
        }
        return $this;
    }



    //--- callback to parent ---//

    /**
     * Return parent FieldListBuilder to continue to the next field
     * @return FieldListBuilder Return parent fields builder
     */
    public function then():FieldListBuilder{
        return $this->parent;
    }

    /**
     * Return to ControllerConfig definitions
     * @return ControllerConfig
     */
    public function endFields():ControllerConfig
    {
        return $this->parent->endFields();
    }

    /**
     * Add a new field
     * @param string $field
     * @return FieldBuilder
     */
    public function define($field):FieldBuilder{
        return $this->parent->define($field);
    }

    //-- ArrayAccess fallback to parent --//

    public function offsetExists($offset)
    {
        return isset($this->parent[$offset]);
    }

    public function offsetGet($offset):FieldBuilder
    {
        return ($this->parent[$offset]);
    }

    public function offsetSet($offset, $value)
    {
    }

    public function offsetUnset($offset)
    {
        unset($this->parent[$offset]);
    }


    ///////////
    ///
    function toArray(){
        $data = $this->opts;
        $name = $data['name'];
        if(!$name){
            throw new \UnexpectedValueException("Field must have a name");
        }
        //-- if label is not set try to get it from the dictionary --//
        if(!isset($data['label'])){
            $dictionary = $this->parent->getDictionary();
            if($dictionary && $label = trans($dictionary.'.'.$name)){
                $data['label'] = $label;
            }
        }
        //--

        return $data;
    }
}
