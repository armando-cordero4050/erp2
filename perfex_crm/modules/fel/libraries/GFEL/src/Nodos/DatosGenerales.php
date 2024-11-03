<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class DatosGenerales extends AbstracBaseNode
{
    public function __construct(DOMNode $parent, array $values)
    {
        parent::__construct($parent);
        $this->setDefaultAtributes($values);
    }

    protected function getName(): string
    {
        return 'dte:DatosGenerales';
    }

    private function setDefaultAtributes(array $values)
    {
        foreach ($values as $key => $value) {
            $this->setAttribute($key, $value);
        }
    }

    /**
     * @return string
     */
    protected function getParentName(): string
    {
        // TODO: Implement getParentName() method.
    }
}