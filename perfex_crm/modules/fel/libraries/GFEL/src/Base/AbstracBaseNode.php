<?php
/*
 * This file is part of PrintTicket plugin for FacturaScripts
 * Copyright (c) 2021.  Juan José Prieto Dzul <juanjoseprieto88@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Juanj\Gfel\Base;

use DOMDocument;
use DOMElement;
use DOMNode;

abstract class AbstracBaseNode extends DOMElement
{
    protected $nameSpace = 'http://www.sat.gob.gt/dte/fel/0.2.0';

    public function __construct(DOMNode $parent)
    {
        parent::__construct($this->getName(), $this->getValue(), $this->getNameSpace());

        $this->appendParent($parent);
    }

    /**
     * @return string
     */
    public function getNameSpace(): string
    {
        return $this->nameSpace;
    }

    /**
     * @param string $nameSpace
     */
    public function setNameSpace(string $nameSpace): void
    {
        $this->nameSpace = $nameSpace;
    }

    /**
     * @param string $name
     * @param string|null $value
     * @return DOMElement
     */
    protected function newElement(string $name, ?string $value): DOMElement
    {
        return new DOMElement($name, $value, $this->getNameSpace());
    }

    protected function getValue()
    {
        return null;
    }

    /**
     * @param string $name
     * @param string|null $value
     * @return DOMNode
     */
    protected function appendElement(string $name, ?string $value): DOMNode
    {
        return $this->appendChild($this->newElement($name, $value));
    }

    /**
     * @param DOMNode $parent
     */
    protected function appendParent(DOMNode $parent)
    {
        $parent->appendChild($this);
    }

    /**
     * @param DOMDocument $document
     * @return DOMNode|null
     */
    protected function getParent(DOMDocument $document): ?DOMNode
    {
        return $document->getElementsByTagNameNS($this->getNameSpace(), $this->getParentName())->item(0);
    }

    abstract protected function getName(): string;
    abstract protected function getParentName(): string;
}
