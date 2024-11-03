<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Frase extends AbstracBaseNode
{
    public function __construct(DOMNode $parent, array $values)
    {
        parent::__construct($parent);
        $this->setDefaultAttributes($values);
    }

    protected function getName(): string
    {
        return 'dte:Frase';
    }

    protected function getParentName(): string
    {
        return 'Frases';
    }

    private function setDefaultAttributes(array $values)
    {
        $this->setAttribute('TipoFrase', $values['TipoFrase']);
        $this->setAttribute('CodigoEscenario', $values['CodigoEscenario']);
    }
}