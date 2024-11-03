<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class ClaseDocumento extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
        $this->setDefaultAtributes();
    }

    private function setDefaultAtributes()
    {
        $this->setAttribute('ClaseDocumento', 'dte');
    }

    protected function getName(): string
    {
        return 'dte:SAT';
    }

    protected function getParentName(): string
    {
        return 'GTDocumento';
    }
}
