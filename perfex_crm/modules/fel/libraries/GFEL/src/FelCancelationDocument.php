<?php

namespace Juanj\Gfel;

use DOMDocument;
use DOMElement;
use DOMNode;

class FelCancelationDocument
{
    const SAT_NAMESPACE = 'http://www.sat.gob.gt/dte/fel/0.1.0';

    /**
     * @var DOMDocument
     */
    protected $document;

    public function __construct()
    {
        $this->document = $this->createDocument();
        $this->initalize();
    }

    /**
     * @return DOMDocument
     */
    private function createDocument(): DOMDocument
    {
        $document = new DOMDocument('1.0', 'UTF-8');
        $document->formatOutput = true;
        $document->preserveWhiteSpace = false;

        return $document;
    }

    private function initalize(): void
    {
        $this->addRootNode();
        $this->addNodeClaseDocumento();
        $this->addNodeDatosCertificados();
    }

    /**
     * @throws \DOMException
     */
    private function addRootNode(): void
    {
        $element = new DOMElement('dte:GTAnulacionDocumento', null, self::SAT_NAMESPACE);
        $this->document->appendChild($element);

        $element->setAttributeNS(
            'http://www.w3.org/2000/xmlns/',
            'xmlns:xsi',
            'http://www.w3.org/2001/XMLSchema-instance');

        $element->setAttribute('Version', '0.1');
    }

    private function addNodeClaseDocumento(): void
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'GTAnulacionDocumento');

        $element = new DOMElement('dte:SAT', null, self::SAT_NAMESPACE);
        $parent->appendChild($element);
    }

    private function addNodeDatosCertificados(): void
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'SAT');

        $element = new DOMElement('dte:AnulacionDTE', null, self::SAT_NAMESPACE);
        $parent->appendChild($element);

        $element->setAttribute('ID', 'DatosCertificados');
    }

    public function addDatosGenerales(array $values): void
    {
        $element = new DOMElement('dte:DatosGenerales', null, self::SAT_NAMESPACE);

        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'AnulacionDTE');
        $parent->appendChild($element);

        foreach ($values as $key => $value) {
            $element->setAttribute($key, $value);
        }
    }

    public function getDocument()
    {
        echo '<xmp>' . print_r($this->document->saveXML(), true) . '</xmp>';
    }

    public function getXML()
    {
        return $this->document->saveXML();
    }

    /**
     * @param string $ns
     * @param string $name
     * @return DOMNode
     */
    protected function getDocumentNode(string $ns, string $name): DOMNode
    {
        return $this->document->getElementsByTagNameNS($ns, $name)->item(0);
    }
}
