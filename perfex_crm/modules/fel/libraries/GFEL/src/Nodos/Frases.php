<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Frases extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
    }

    public function addFrase(array $values)
    {
        $item = new Frase($this, $values);

        return $item;
    }

    protected function getName(): string
    {
        return 'dte:Frases';
    }

    protected function getParentName(): string
    {
        return 'DatosEmision';
    }
}