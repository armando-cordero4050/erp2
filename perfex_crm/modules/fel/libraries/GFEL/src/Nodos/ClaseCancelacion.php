<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class ClaseCancelacion extends AbstracBaseNode
{
    public function getNameSpace(): string
    {
        return 'http://www.sat.gob.gt/dte/fel/0.1.0';
    }

    protected function getName(): string
    {
        return 'dte:SAT';
    }

    protected function getParentName(): string
    {
        return 'GTAnulacionDocumento';
    }
}
