<?php
namespace plokko\AutoController\ControllerConfig;

use plokko\AutoController\ControllerConfig;

interface FieldDefinitionInterface
{
    /**
     * Define a new field
     * @param string $field Field name
     * @return FieldBuilder
     */
    public function define($field):FieldBuilder;

    public function set($key,$value=null);

    //-- ArrayAccess --//
    public function offsetExists($offset);

    public function offsetGet($offset):FieldBuilder;

    public function offsetUnset($offset);

    //-- Parent callbacks --//

    /**
     * Close current field definition
     * @return FieldListBuilder
     */
    public function then():FieldListBuilder;

    /**
     * Close fields definition and returns to ControllerConfig definitions
     * @return ControllerConfig
     */
    public function endFields():ControllerConfig;
}
