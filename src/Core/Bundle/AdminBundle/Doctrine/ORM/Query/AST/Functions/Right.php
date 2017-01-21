<?php

namespace Core\Bundle\AdminBundle\Doctrine\ORM\Query\AST\Functions;


use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\SqlWalker;
use Oro\ORM\Query\AST\Functions\SimpleFunction;
use Oro\ORM\Query\AST\Platform\Functions\PlatformFunctionNode;

class Right extends PlatformFunctionNode
{
    /**
     * {@inheritdoc}
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        echo 'oi';exit;
        /** @var Node $expression */
        $expression = $this->parameters[SimpleFunction::PARAMETER_KEY];
        return 'RIGHT(' . $this->getExpressionValue($expression, $sqlWalker) . ')';
    }
}
