<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Items extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
    }

    public function addItem(array $values)
    {
        $item = new Item($this, $values);

        return $item;
    }

    protected function getName(): string
    {
        return 'dte:Items';
    }

    protected function getParentName(): string
    {
        return 'DatosEmision';
    }
}