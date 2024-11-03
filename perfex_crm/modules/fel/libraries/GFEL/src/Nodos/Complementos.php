<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Complementos extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
    }

    public function addComplemento(DOMNode $complemento)
    {
        $this->appendChild($complemento);
    }

    public function getComplemento(): Complemento
    {
        return new Complemento($this);
    }

    protected function getName(): string
    {
        return 'dte:Complementos';
    }

    protected function getParentName(): string
    {
        return 'DatosEmision';
    }
}