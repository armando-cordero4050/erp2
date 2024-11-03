<?php

namespace Juanj\Gfel;

use DOMDocument;
use DOMElement;
use DOMNode;

abstract class DocumentElement
{
    /**
     * @param DOMDocument $document
     * @param string $name
     * @param string|null $value
     * @return DOMElement
     */
    protected function getNewDomElement(DOMDocument &$document, string $ns, string $name, string $value = null): DOMElement
    {
        return $document->createElementNS($ns, $name, $value);
    }

    /**
     * @param DOMElement $element
     * @param string $parentName
     * @return DOMElement
     */
    protected function addDocumentNode(DOMElement $element, string $parentName): DOMElement
    {
        $parent = $this->getDocumentNode($element->namespaceURI, $parentName);
        $parent->appendChild($element);

        return $element;
    }

    /**
     * @param string $ns
     * @param string $name
     * @return DOMNode|null
     */
    protected function getDocumentNode(string $ns, string $name): ?DOMNode
    {
        return $this->document->getElementsByTagNameNS($ns, $name)->item(0);
    }
}