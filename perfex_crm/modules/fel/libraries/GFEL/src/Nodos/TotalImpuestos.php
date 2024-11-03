<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class TotalImpuestos extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
    }

    public function addImpuesto(array $values)
    {
        $element = $this->appendElement('dte:TotalImpuesto', null);

        foreach ($values as $key => $value) {
            $element->setAttribute($key, $value);
        }
    }

    protected function getName(): string
    {
        return 'dte:TotalImpuestos';
    }

    protected function getParentName(): string
    {
        return 'Totales';
    }
}