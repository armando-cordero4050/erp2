<?php

namespace Juanj\Gfel\Complementos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class AbonoFacturaCambiaria extends AbstracBaseNode
{
    const COMPLEMENTO_URI = 'http://www.sat.gob.gt/dte/fel/CompCambiaria/0.1.0';

    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
        $this->initComplemento();
    }

    public function addValues(array $values)
    {
        $this->setNameSpace(self::COMPLEMENTO_URI);

        $element = $this->appendElement('cfc:Abono', null);
        $element->setAttribute('Version', '1');

        $element->appendChild($this->newElement('cfc:NumeroAbono', $values['NumeroAbono']));
        $element->appendChild($this->newElement('cfc:FechaVencimiento', $values['FechaVencimiento']));
        $element->appendChild($this->newElement('cfc:MontoAbono', $values['MontoAbono']));
    }

    protected function getName(): string
    {
        return 'dte:Complemento';
    }

    protected function getParentName(): string
    {
        return 'dte:Complementos';
    }

    protected function initComplemento()
    {
        $this->setAttribute('IDComplemento', '1');
        $this->setAttribute('NombreComplemento', 'FCAM');
        $this->setAttribute('URIComplemento', self::COMPLEMENTO_URI);
    }
}