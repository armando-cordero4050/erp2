<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Item extends AbstracBaseNode
{
    public function __construct(DOMNode $parent, array $values)
    {
        parent::__construct($parent);
        $this->setDefaultAttributes($values);
    }

    public function addValues(array $values)
    {
        foreach ($values as $key => $value) {
            $name = 'dte:' . $key;
            $this->appendChild($this->newElement($name, $value));
        }
        /*$this->appendElement('dte:Cantidad', $values['Cantidad']);
        $this->appendElement('dte:UnidadMedida', $values['UnidadMedida']);
        $this->appendElement('dte:Descripcion', $values['Descripcion']);
        $this->appendElement('dte:PrecioUnitario', $values['PrecioUnitario']);
        $this->appendElement('dte:Precio', $values['Precio']);
        $this->appendElement('dte:Descuento', $values['Descuento']);
        $this->appendElement('dte:Total', $values['Total']);*/
    }

    public function addImpuesto(array $values)
    {
        $impuesto = new Impuesto(new Impuestos($this));
        $impuesto->addValues($values);
    }

    public function addImpuesto2(array $values)
    {
        //return new Impuestos($this);
        $element = $this->appendElement('dte:Impuestos', null);
        $element = $element->appendChild($this->newElement('dte:Impuesto', null));

        $element->appendChild($this->newElement('dte:NombreCorto', $values['NombreCorto']));
        $element->appendChild($this->newElement('dte:CodigoUnidadGravable', $values['CodigoUnidadGravable']));
        $element->appendChild($this->newElement('dte:MontoGravable', $values['MontoGravable']));
        $element->appendChild($this->newElement('dte:MontoImpuesto', $values['MontoImpuesto']));
    }

    protected function getName(): string
    {
        return 'dte:Item';
    }

    protected function getParentName(): string
    {
        return 'Items';
    }

    private function setDefaultAttributes(array $values)
    {
        $this->setAttribute('NumeroLinea', $values['NumeroLinea']);
        $this->setAttribute('BienOServicio', $values['BienOServicio']);
    }
}