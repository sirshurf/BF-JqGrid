<?php

namespace BfJqGrid\JqGrid\Adapter\Sql\Predicate;

use Zend\Db\Sql\Predicate\Like;

class Contains extends Like
{

    /**
     * @return array
     */
    public function getExpressionData()
    {
        return array(
            array($this->specification, array($this->identifier, "%".$this->like."%"), array(self::TYPE_IDENTIFIER, self::TYPE_VALUE))
        );
    }

}
