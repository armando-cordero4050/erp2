<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Totales extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
    }

    public function addTotalImpuestos()
    {
        return new TotalImpuestos($this);
    }

    public function addGrandTotal(string $total)
    {
        $this->appendElement('dte:GranTotal', $total);
    }

    protected function getName(): string
    {
        return 'dte:Totales';
    }

    protected function getParentName(): string
    {
        return 'DatosEmision';
    }
}