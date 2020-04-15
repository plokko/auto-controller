<?php
namespace plokko\AutoController;

use plokko\AutoController\ControllerConfig\FieldListBuilder;
use plokko\ResourceQuery\QueryBuilder;
use plokko\ResourceQuery\ResourceQuery;

/**
 * Class DefinitionBuilder
 * @package App\Wip
 *
 * @property-read FieldListBuilder $fields Field definition (For form definition and selection)
 * @property-read string $action Current controller action
 */
class ControllerConfig
{
    protected
        /** @var string Current controller action */
        $action,
        /** @var \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder */
        $query = null,
        /**@var FieldListBuilder|null **/
        $_fields = null;

    public
        /** @var ResourceQuery|null */
        $resourceQuery = null,
        /** @var string|null */
        $viewDir = null,
        /** @var string|null */
        $view = null,
        $dictionary = null,
        $userResource = null;

    function __construct($action){
        $this->action = $action;
    }

    /**
     * @param \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query
     * @return $this
     */
    function setQuery($query){
        $this->query = $query;
        return $this;
    }

    function __get($k){
        switch($k){
            case 'fields':
                if(!$this->_fields)
                    $this->_fields = new FieldListBuilder($this);
                return $this->_fields;
            case 'action':
                return $this->$k;
            default:
        }
        return null;
    }

    /**
    function fields($arg){
        if(is_array($arg)){
            $this->fields->sets($arg);
        }else{
            $arg($this->fields);
        }
        return $this;
    }

     */

    /**
     * Set translation dictionary
     * @param string|null $dictionary
     * @return $this
     */
    public function setDictionary($dictionary){
        $this->dictionary = $dictionary;
        return $this;
    }

    /**
     * @param string $viewDir
     * @return $this
     */
    public function setViewsDir($viewDir){
        $this->viewDir = $viewDir;
        return $this;
    }

    /**
     * @param string $view
     * @return $this
     */
    public function setView($view){
        $this->view = $view;
        return $this;
    }

    /**
     * @param ResourceQuery $resourceQuery
     * @return $this
     */
    function setResourceQuery(ResourceQuery $resourceQuery){
        $this->resourceQuery = $resourceQuery;
        return $this;
    }

    /**
     * Cast data with HTTP resource
     * @param string|null $resource
     * @return $this
     */
    function useResource($resource){
        $this->userResource = $resource;
        return $this;
    }


    //------ Returns to AutoController ------//

    function getHeaders(){
        if($this->fields && count($this->fields)>0){
            return $this->fields->toArray();
        }
        //-- Else get from trans --//
        if($this->dictionary || ($this->fields && $this->fields->dictionary)){
            $trans = null;
            // Field dictionary
            if($this->fields->dictionary)
                $trans = trans($this->fields->dictionary);
            // Global dictionary (Headers with fields fallback)
            if($this->dictionary)
                $trans = trans($this->dictionary.'.headers')?:trans($this->dictionary.'.fields');

            if($trans){
                $list = [];
                foreach($trans AS $k=>$v){
                    $list[]=[
                        'name'=>$k,
                        'label'=>$v,
                    ];
                }
                return $list;
            }
        }

        //-- else get from fillable --//
        if($m = $this->getModel()){
            return array_map(function($e){
                    return [
                        'name'=>$e,
                        //'label'=>$e,
                    ];
                },
                $m->getFillable()
            );
        }
        //--
        return null;
    }
    function getFields(){
        if($this->fields && count($this->fields)>0){
            return $this->fields->toArray();
        }
        //-- else get from fillable --//
        if($m = $this->getModel()){
            $dictionary = $this->fields?$this->fields->getDictionary():$this->dictionary;
            $trans = $dictionary?trans($dictionary):null;

            return array_map(function($e) use ($trans){
                    $el = ['name'  => $e];
                    if($trans && isset($trans[$e]))
                        $el['label'] = $trans[$e];
                    return $el;
                },
                $m->getFillable()
            );
        }
        //--
        return null;
    }

    /**
     * Get query
     * @return \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder
     */
    function getQuery(){
        if($this->query==null && $this->resourceQuery==null){
            throw new \UnexpectedValueException('No query specified in config');
        }
        return $this->query?:$this->resourceQuery->getQuery();
    }

    /**
     * Get ResourceQuery instance
     * @return ResourceQuery
     */
    function getResourceQuery(){
        if($this->query==null && $this->resourceQuery==null){
            throw new \UnexpectedValueException('No query specified in config');
        }
        return $this->resourceQuery?:new QueryBuilder($this->query);
    }

    /**
     * Get view name
     * @return string|null view name
     */
    function getView(){
        if($this->view)
            return $this->view;
        //TODO:FIX config
        return ($this->viewDir?:config('autocontroller.views')).'.'.$this->action;
    }

    /**
     * @return string|null Resource className to use
     */
    function getResource(){
        return $this->userResource?:($this->resourceQuery?$this->resourceQuery->useResource:null);
    }

    function getModel(){
        $q = $this->getQuery();
        return $q?$q->getModel():null;
    }
}
