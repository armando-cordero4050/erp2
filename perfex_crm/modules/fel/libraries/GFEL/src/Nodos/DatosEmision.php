<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class DatosEmision extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
        $this->setDefaultAtributes();
    }

    protected function getName(): string
    {
        return 'dte:DatosEmision';
    }

    private function setDefaultAtributes()
    {
        $this->setAttribute('ID', 'DatosEmision');
    }

    protected function getParentName(): string
    {
        return 'DTE';
    }
}