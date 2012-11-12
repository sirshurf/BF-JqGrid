<?php

namespace Bf\JqGrid\JqGrid\Adapter\Sql\Predicate;

use Zend\Db\Sql\Predicate\Like;

class NotLike extends Like
{

    /**
     * @var string
     */
    protected $specification = '%1$s NOT LIKE %2$s';

}
