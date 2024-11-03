<?php

namespace Juanj\Gfel;

use DOMDocument;
use DOMNode;
use Juanj\Gfel\Nodos\ClaseDocumento;
use Juanj\Gfel\Nodos\Complementos;
use Juanj\Gfel\Nodos\DatosCertificados;
use Juanj\Gfel\Nodos\DatosEmision;
use Juanj\Gfel\Nodos\DatosGenerales;
use Juanj\Gfel\Nodos\Emisor;
use Juanj\Gfel\Nodos\Frases;
use Juanj\Gfel\Nodos\GTDocumento;
use Juanj\Gfel\Nodos\Items;
use Juanj\Gfel\Nodos\Receptor;
use Juanj\Gfel\Nodos\Totales;

class FelDocument
{
    const SAT_NAMESPACE = 'http://www.sat.gob.gt/dte/fel/0.2.0';

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
        $this->addNodeDatosEmision();
    }

    private function addRootNode(): void
    {
        $element = new GTDocumento($this->document);
    }

    private function addNodeClaseDocumento(): void
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'GTDocumento');
        $element = new ClaseDocumento($parent);
    }

    private function addNodeDatosCertificados(): void
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'SAT');
        $element = new DatosCertificados($parent);
    }

    private function addNodeDatosEmision(): void
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'DTE');
        $element = new DatosEmision($parent);
    }

    public function addDatosGenerales(array $values): void
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'DatosEmision');
        $element = new DatosGenerales($parent, $values);
    }

    public function addEmisor(array $values): Emisor
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'DatosEmision');
        return new Emisor($parent, $values);
    }

    public function addReceptor(array $values): Receptor
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'DatosEmision');
        return new Receptor($parent, $values);
    }

    public function frases(): Frases
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'DatosEmision');
        return new Frases($parent);
    }

    public function items(): Items
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'DatosEmision');
        return new Items($parent);
    }

    public function totales(): Totales
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'DatosEmision');
        return new Totales($parent);
    }

    public function complementos(): Complementos
    {
        $parent = $this->getDocumentNode(self::SAT_NAMESPACE, 'DatosEmision');
        return new Complementos($parent);
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
