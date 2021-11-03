<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use NbsPhp\Core\Controllers\RestApiController;

class ${NAME}By${ACTOR}Controller extends RestApiController
{
    public function getList(Guard \$auth, Request \$request, GetList${NAME}By${ACTOR}Service \$service)
    {
        \$input = \$this->validate(\$request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        \$dto = new Paginate${NAME}Dto(\$input + ['userId' => \$auth->id()]);
        \$result = \$service->execute(\$dto);
        
        return fractal(\$result->data, new ${NAME}SimpleTransformer())
            ->paginateWith(new LazyPaginatorAdapter(\$result->paginate));
    }

    public function getDetail(Guard \$auth, \$xid, GetDetail${NAME}By${ACTOR}Service \$service)
    {
        \$dto = new GetDetail${NAME}Dto([
            'xid' => \$xid,
            'userId' => \$auth->id()
        ]);
        \$result = \$service->execute(\$dto);
        return fractal(\$result, new ${NAME}Transformer());
    }

    public function postCreate(Guard \$auth, Request \$request)
    {
        
    }
    
    public function putUpdate(Guard \$auth, Request \$request, \$xid)
    {
        
    }
    
    public function patchUpdate(Guard \$auth, Request \$request, \$xid)
    {
        
    }
    
    public function delete(Guard \$auth, Request \$request)
    {
        
    }
}