<?php
namespace  plokko\AutoController;

use Illuminate\Database\Eloquent\Model;
use plokko\ResourceQuery\ResourceQuery;

/**
 * Class ControllerData
 * @package plokko\AutoController
 *
 * @property-read string $action Current controller action (index,show,edit,create,store,update)
 * @property-read string|null $dictionary Translation root
 * @property-read string $view Current view
 *
 * @property-read \Illuminate\Database\Query\Builder|\Illuminate\Database\Eloquent\Builder $query Get query
 * @property-read ResourceQuery $resourceQuery
 *
 * @property-read Model|null $item
 */
class ControllerData
{
    private
        /** @var ControllerConfig */
        $config,
        /** @var Model|null */
        $item;

    function __construct(ControllerConfig $config,$id=null)
    {
        $this->config = $config;
        if($id){
            $this->item = $this->query->findOrFail($id);
        }
    }

    function __get($name)
    {
        switch ($name){
            case 'action':
            case 'dictionary':
                return $this->config->$name;

            case 'view':
                return $this->config->getView();

            case 'resource':
                return $this->config->getResource();

            case 'query':
                return $this->config->getQuery();

            case 'resourceQuery':
            case 'list':
                return $this->config->getResourceQuery();
                //

            case 'item':
                return $this->item;

            default:
        }
        return null;
    }


    /**
     * @param string $key
     * @param array $replace
     * @return string
     */
    public function trans($key, $replace=[], $locale=null){
        $dictionary = $this->config->dictionary;
        return trans($dictionary?$dictionary.'.'.$key:$key,$replace);
    }

    /**f
     * @param string $key
     * @param  \Countable|int|array  $number
     * @param  array  $replace
     * @param  string|null  $locale
     * @return string
     */
    public function trans_choice($key, $number, $replace=[], $locale=null){
        $dictionary = $this->config->dictionary;
        return trans_choice($dictionary?$dictionary.'.'.$key:$key,$number, $replace);
    }

    /**
     * @param string $key
     * @param array $replace
     * @return string
     */
    public function __($key, $args=[], $locale=null){
        return $this->trans($key,$args);
    }

}
