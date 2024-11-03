<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class DatosCertificados extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
        $this->setDefaultAtributes();
    }

    protected function getName(): string
    {
        return 'dte:DTE';
    }

    private function setDefaultAtributes()
    {
        $this->setAttribute('ID', 'DatosCertificados');
    }

    protected function getParentName(): string
    {
        return 'SAT';
    }
}