<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Emisor extends AbstracBaseNode
{
    public function __construct(DOMNode $parent, array $values)
    {
        parent::__construct($parent);
        $this->setDefaultAtributes($values);
    }

    public function addDireccion(array $values)
    {
        $element = $this->appendElement('dte:DireccionEmisor', null);

        $element->appendChild($this->newElement('dte:Direccion', $values['Direccion']));
        $element->appendChild($this->newElement('dte:CodigoPostal', $values['CodigoPostal']));
        $element->appendChild($this->newElement('dte:Municipio', $values['Municipio']));
        $element->appendChild($this->newElement('dte:Departamento', $values['Departamento']));
        $element->appendChild($this->newElement('dte:Pais', $values['Pais']));
    }

    protected function getName(): string
    {
        return 'dte:Emisor';
    }

    private function setDefaultAtributes(array $values)
    {
        $this->setAttribute('NITEmisor', $values['NITEmisor']);
        $this->setAttribute('NombreEmisor', $values['NombreEmisor']);
        $this->setAttribute('CodigoEstablecimiento', $values['CodigoEstablecimiento']);
        $this->setAttribute('NombreComercial', $values['NombreComercial']);
        $this->setAttribute('AfiliacionIVA', $values['AfiliacionIVA']);
    }

    /**
     * @return string
     */
    protected function getParentName(): string
    {
        // TODO: Implement getParentName() method.
    }
}