<?php
namespace plokko\AutoController;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use plokko\ResourceQuery\QueryBuilder;

abstract class AutoController extends Controller
{


    protected abstract function getConfig(ControllerConfig $config);

    private function _getConfig($action):ControllerConfig
    {
        $cfg = new ControllerConfig($action);
        $this->getConfig($cfg);
        return $cfg;
    }

    /**
     * @param string $action
     * @return ControllerData
     */
    private function _getData($action,$id=null):ControllerData
    {
        return new ControllerData($this->_getConfig($action),$id);
    }

    private function handle($action,Request $request,$id=null){
        $data = $this->_getData($action,$id);
        return $this->handleAction($data,$request,$id);
    }

    protected function handleAction(ControllerData $data,Request $request,$id=null){
        switch($data->action)
        {
            case 'index':
                if($request->ajax()){
                    return $data->resourceQuery;
                }
                return view($data->view,compact('data'));
                break;
                //--
            case 'create':
                //
            case 'show':
            case 'edit':
                return view($data->view,compact('data'));
                break;
                //--
            case 'update':
            case 'delete':
            case 'store':

                //TODO
            default:
        }


        abort(404,'Action '.$data->action.' not handled');
    }

    function index(Request $request){
        return $this->handle('index',$request);
    }

    function show(Request $request,$id){
        return $this->handle('show',$request,$id);
    }

    function create(Request $request){
        return $this->handle('create',$request);

    }

    function store(Request $request){
        return $this->handle('store',$request);
    }

    function edit(Request $request,$id){
        return $this->handle('edit',$request,$id);
    }

    function update(Request $request,$id){
        return $this->handle('update',$request,$id);
    }

    function destroy(Request $request,$id){
        return $this->handle('destroy',$request,$id);
    }
}
