<?php

namespace Juanj\Gfel\Nodos;

use DOMNode;
use Juanj\Gfel\Base\AbstracBaseNode;

class GTDocumento extends AbstracBaseNode
{
    public function __construct(DOMNode $parent)
    {
        parent::__construct($parent);

        $this->setDefaultAttributeNS();
        $this->setDefaultAtributes();
    }

    protected function getName(): string
    {
        return 'dte:GTDocumento';
    }

    private function setDefaultAttributeNS()
    {
        $this->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    }

    private function setDefaultAtributes()
    {
        $this->setAttribute('Version', '0.1');
    }

    protected function getParentName(): string
    {
        return '';
    }
}