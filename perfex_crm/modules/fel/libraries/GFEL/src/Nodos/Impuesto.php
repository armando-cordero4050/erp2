<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Impuesto extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
    }

    public function addValues(array $values)
    {
        $this->appendChild($this->newElement('dte:NombreCorto', $values['NombreCorto']));
        $this->appendChild($this->newElement('dte:CodigoUnidadGravable', $values['CodigoUnidadGravable']));
        $this->appendChild($this->newElement('dte:MontoGravable', $values['MontoGravable']));
        $this->appendChild($this->newElement('dte:MontoImpuesto', $values['MontoImpuesto']));
        //$this->appendElement('dte:NombreCorto', $values['NombreCorto']);
        //$this->appendElement('dte:CodigoUnidadGravable', $values['CodigoUnidadGravable']);
        //$this->appendElement('dte:MontoGravable', $values['MontoGravable']);
        //$this->appendElement('dte:MontoImpuesto', $values['MontoImpuesto']);
    }

    protected function getName(): string
    {
        return 'dte:Impuesto';
    }

    protected function getParentName(): string
    {
        return 'Impuestos';
    }
}