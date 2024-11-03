<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Impuestos extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
    }

    public function addImpuesto()
    {
        $item = new Impuesto($this);

        return $item;
    }

    protected function getName(): string
    {
        return 'dte:Impuestos';
    }

    protected function getParentName(): string
    {
        return 'DatosEmision';
    }
}