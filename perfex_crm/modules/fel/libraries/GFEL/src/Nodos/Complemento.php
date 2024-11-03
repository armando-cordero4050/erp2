<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class Complemento extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);
    }

    public function setAttributes(array $values): Complemento
    {
        foreach ($values as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    protected function getName(): string
    {
        return 'dte:Complemento';
    }

    protected function getParentName(): string
    {
        return 'Complementos';
    }
}