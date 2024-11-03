<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Receptor extends AbstracBaseNode
{
    public function __construct(DOMNode $parent, array $values)
    {
        parent::__construct($parent);
        $this->setDefaultAtributes($values);
    }

    public function addDireccion(array $values)
    {
        $element = $this->appendElement('dte:DireccionReceptor', null);

        $element->appendChild($this->newElement('dte:Direccion', $values['Direccion']));
        $element->appendChild($this->newElement('dte:CodigoPostal', $values['CodigoPostal']));
        $element->appendChild($this->newElement('dte:Municipio', $values['Municipio']));
        $element->appendChild($this->newElement('dte:Departamento', $values['Departamento']));
        $element->appendChild($this->newElement('dte:Pais', $values['Pais']));
    }

    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    protected function getName(): string
    {
        return 'dte:Receptor';
    }

    protected function getParentName(): string
    {
        return 'DatosEmision';
    }

    private function setDefaultAtributes(array $values)
    {
        $this->setAttribute('IDReceptor', $values['IDReceptor']);
        $this->setAttribute('NombreReceptor', $values['NombreReceptor']);
    }
}