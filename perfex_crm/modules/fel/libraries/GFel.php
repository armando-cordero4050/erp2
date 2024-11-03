<?php
ini_set('max_execution_time', 120);
defined('BASEPATH') or exit('No direct script access allowed');

require_once FEL_MODULE_PATH . '/libraries/GFEL/vendor/autoload.php';

use Juanj\Gfel\FelDocument;
use Juanj\Gfel\GTFelStampService;

class GFel
{
    const GFEL_PATH = APPPATH . 'gfel/';

    private $data;

    private $detail;

    private $totaliva;

    /**
     * @var FelDocument
     */
    protected $document;
    /**
     * @var mixed
     */
    private $certificado;
    /**
     * @var bool|string
     */
    private $response;

    public function __construct()
    {
        $this->document = new FelDocument();
    }

    public function build($data)
    {
        $this->setData($data);

        $this->setDatosGenerales();
        $this->setEmisor();
        $this->setReceptor();
        $this->setFrases();
        $this->setItems();
        $this->setTotales();
        //$this->setComplementos();
        //$this->document->getDocument(); //Esta linea hace que se imprima el xml en la pagina
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param mixed $detail
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
    }

    protected function setDatosGenerales()
    {
        $this->document->addDatosGenerales([
            'Tipo' => 'FACT',
            'FechaHoraEmision' => date("Y-m-d\TH:i:s"),
            'CodigoMoneda' => 'GTQ'
        ]);
    }

    protected function setEmisor(): void
    {
        //$company = $this->factura->getCompany();
        $companyName = get_option('invoice_company_name');

        $custom_fields = get_custom_fields('company');
        foreach ($custom_fields as $field) {
            if ($field['name'] == 'nit')
            {
                $idnit = $field['id'];
            }
        }
        $nit = get_custom_field_value('0', $idnit, 'company', true);

        $emisor = [
            'NITEmisor' => $nit,
            'NombreEmisor' => $companyName,
            'CodigoEstablecimiento' => '1',
            'NombreComercial' => $companyName,
            'AfiliacionIVA' => 'GEN'
        ];

        $direccion = [
            'Direccion' => 'GUATEMALA',
            'CodigoPostal' => '0100',
            'Municipio' => 'GUATEMALA',
            'Departamento' => 'GUATEMALA',
            'Pais' => 'GT'
        ];

        $this->document->addEmisor($emisor)->addDireccion($direccion);
    }

    protected function setReceptor(): void
    {
        $customer = $this->data->client;
        //$contact = $customer->getDefaultAddress();

        $this->document->addReceptor([
            'IDReceptor' => $customer->vat,
            'NombreReceptor' => $customer->company
        ])->addDireccion([
            'Direccion' => 'CIUDAD',
            'CodigoPostal' => '01000',
            'Municipio' => 'GUATEMALA',
            'Departamento' => 'GUATEMALA',
            'Pais' => 'GT'
        ]);
    }

    protected function setFrases()
    {
        $frases = $this->document->frases();

        $frases->addFrase([
            'CodigoEscenario' => '1',
            'TipoFrase' => '1'
        ]);

    }

    protected function setItems()
    {
        $items = $this->document->items();

        $custom_fields = get_custom_fields('items');
        foreach ($custom_fields as $field) {
            if ($field['name'] == 'Tipo')
            {
                $idtipo = $field['id'];
            }
        }
        $count = 1;
        foreach ($this->data->items as $line) {
            $idfactura = $line['id'];
            $tipo = get_custom_field_value($idfactura, $idtipo, 'items', true);
            if ($tipo == 'BIEN'){
                $tipoItem = 'B';
            }else{
                $tipoItem = 'S';
            }
            $total = $line['rate'] * $line['qty'];
            $item = $items->addItem([
                'NumeroLinea' => $count,
                'BienOServicio' => $tipoItem
            ]);

            $item->addValues([
                'Cantidad' => $line['qty'],
                'UnidadMedida' => 'UNI',
                'Descripcion' => $line['description'],
                'PrecioUnitario' => $line['rate'],
                'Precio' => $total,
                'Descuento' => 0
            ]);

            $item->addImpuesto([
                'NombreCorto' => 'IVA',
                'CodigoUnidadGravable' => '1',
                'MontoGravable' => $this->getMontoGravable($total),
                'MontoImpuesto' => $this->getMontoImpuesto($total)
            ]);

            /*$item->addImpuesto([
                'NombreCorto' => 'IVA',
                'CodigoUnidadGravable' => $this->getCodigoUnidadGravable($line),
                'MontoGravable' => $this->getMontoGravable($line),
                'MontoImpuesto' => $this->getMontoImpuesto($line)
            ]);*/

            $item->addValues([
                'Total' => $total
            ]);

            $count++;
        }
    }

    protected function setTotales()
    {
        $totales = $this->document->totales();

        $totales->addTotalImpuestos()->addImpuesto([
            'NombreCorto' => 'IVA',
            'TotalMontoImpuesto' => number_format($this->totaliva, 6)
            //number_format((($lineas->pvptotal) / 1.12), 6, ".", "")
        ]);
        $totales->addGrandTotal($this->data->subtotal);
    }

    protected function setComplementos()
    {
        $fecha = date("Y-m-d", strtotime($this->factura->fecha . "+ 1 month"));

        $complementos = $this->document->complementos();
        $complemento = new AbonoFacturaCambiaria($complementos);

        $complemento->addValues([
            'NumeroAbono' => 1,
            'FechaVencimiento' => $fecha,
            'MontoAbono' => 0
        ]);
    }

    /**
     * @return void
     */
    public function getDocument()
    {
        $this->document->getDocument();
    }

    public function getXml()
    {
        return $this->document->getXML();
    }

    private function isExcento($line)
    {
        return (isset($line->excento) && (true === $line->excento));
    }

    private function getCodigoUnidadGravable($line): string
    {
        return $this->isExcento($line->excento) ? '1' : '2';
    }

    private function getMontoGravable($importe): string
    {
        /*if (false === $this->isExcento($line->excento)) { //si es exento devuelve 0 eso esta bien
            return '0';
        }*/

        return $this->formatValue($importe / 1.12); ///esto hay que modificar
    }

    private function getMontoImpuesto($importe): string
    {
        /*if (false === $this->isExcento($line->excento)) {
            return '0';
        }*/
        $lineaiva = $importe - ($importe / 1.12);
        $this->totaliva += $lineaiva;

        return $this->formatValue($lineaiva);
    }

    protected function getIva($base, $iva = 12)
    {
        return ($base * $iva) / 100;
    }

    private function formatValue($value): string
    {
        return number_format($value, 6, ".", "");
    }

    public function stamp($nit, $serviceUser, $serviceToken)
    {
        $service = new GTFelStampService();

        $this->response = $service->stamp($this->getXml());

        if (false === $this->response)  {
            return false;
        }

        $this->certificado = json_decode($this->response);

        if (1 === $this->certificado->Codigo) {
            return true;
        }

        return false;
    }

    public function getCertificate()
    {
        return $this->certificado;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function saveResponseLog($response)
    {
        if (false === is_dir(self::GFEL_PATH . 'log')) {
            mkdir(self::GFEL_PATH . 'log', 0777, true);
        }

        if (false === file_put_contents(self::GFEL_PATH . 'log/log.json', $response)) {
            return false;
        }

        return true;
    }

    public function saveInvoiceCert($response)
    {
        $path = self::GFEL_PATH . 'files';
        if (false === is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $filename = $path . '/' . $this->data->sale_no . '.json';

        if (false === file_put_contents($filename, $response)) {
            return false;
        }

        return true;
    }
}
