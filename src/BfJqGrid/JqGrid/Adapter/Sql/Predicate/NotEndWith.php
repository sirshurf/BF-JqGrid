<?php

namespace BfJqGrid\JqGrid\Adapter\Sql\Predicate;

class NotEndWith extends NotLike
{

    /**
     * @return array
     */
    public function getExpressionData()
    {
        return array(
            array($this->specification, array($this->identifier, "%".$this->like), array(self::TYPE_IDENTIFIER, self::TYPE_VALUE))
        );
    }

}
