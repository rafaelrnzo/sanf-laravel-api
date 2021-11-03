<?php
#parse("PHP File Header.php")

#if (${NAMESPACE})

namespace ${NAMESPACE};

#end
use Illuminate\Http\Request;
use NbsPhp\Core\Controllers\RestApiController;

class ${NAME}Controller extends RestApiController
{
    public function getList(Guard \$auth, Request \$request, GetList${NAME}Service \$service)
    {
        \$input = \$this->validate(\$request, [
            'skip' => ['nullable', 'integer'],
            'limit' => ['nullable', 'integer'],
            'sort_by' => ['nullable', 'string'],
            'keyword' => ['nullable', 'string'],
        ]);
        \$dto = new Paginate${NAME}Dto(\$input);
        \$result = \$service->execute(\$dto);
        return fractal(\$result, new ${NAME}SimpleTransformer());
    }

    public function getDetail(Guard \$auth, \$xid, GetDetail${NAME}Service \$service)
    {
        \$dto = new GetDetail${NAME}Dto([
            'xid' => \$xid,
            'userId' => \$auth->id()
        ]);
        \$result = \$service->execute(\$dto);
        return fractal(\$result, new ${NAME}Transformer());
    }
    
    public function postCreate(Request \$request)
    {
        
    }
    
    public function putUpdate(Request \$request)
    {
        
    }
    
    public function patchUpdate(Request \$request)
    {
        
    }
}