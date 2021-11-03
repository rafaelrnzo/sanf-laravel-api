<?php
#parse("PHP File Header.php")

namespace ${VENDOR}\Core\Modules\\${MODULE}\Repositories;


interface ${NAME}RepositoryInterface
{
    public function findById(\$id);

    public function findByXid(\$xid);

    public function query(\$specification);

    public function add(\$fields);

    public function update(\$fields, \$specification = null);

    public function remove(\$specification);

    public function removeById(\$id);

    public function removeByXid(\$xid);

    public function size(\$specification = null);
}