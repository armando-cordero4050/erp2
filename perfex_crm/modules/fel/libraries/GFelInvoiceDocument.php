<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  ==============================================================================
 *  Author  : Mian Saleem
 *  Email   : saleem@tecdiary.com
 *  For     : ESC/POS Print Driver for PHP
 *  License : MIT License
 *  ==============================================================================
 */
require_once FEL_MODULE_PATH . '/libraries/GFEL/vendor/autoload.php';

use Juanj\Gfel\FelCancelationDocument;
use Juanj\Gfel\FelDocument;

class GFelInvoiceDocument
{
    private $data;

    private $totaliva;

    /**
     * @var FelDocument
     */
    protected $document;

    /**
     * @param $data
     * @return false|string
     */
    public function build($data)
    {
        $this->document = new FelDocument();

        $companyName = get_option('invoice_company_name');
        $this->setData($data);

        $this->setDatosGenerales();
        $this->setEmisor($companyName);
        $this->setReceptor();
        $this->setFrases();
        $this->setItems();
        $this->setTotales();
        //$this->setComplementos();
        //$this->document->getDocument(); //Esta linea hace que se imprima el xml en la pagina

        return $this->getXml();
    }

    /**
     * @param $data
     * @return false|string
     */
    public function buildCancelationDocument($data)
    {
        $custom_fields = get_custom_fields('company');
        foreach ($custom_fields as $field) {
            if ($field['name'] == 'nit')
            {
                $idnit = $field['id'];
            }
        }
        $nit = get_custom_field_value('0', $idnit, 'company', true);

        $this->document = new FelCancelationDocument();
        $date = date_create($data['fechacertificacion']);

        $this->document->addDatosGenerales([
            'ID' => 'DatosAnulacion',
            'NumeroDocumentoAAnular' => $data['autorizacion'],
            'NITEmisor' => $nit,
            'IDReceptor' => $data['nitreceptor'],
            'FechaEmisionDocumentoAnular' => date_format($date, 'Y-m-d\TH:i:s'),
            'FechaHoraAnulacion' => date("Y-m-d\TH:i:s"),
            'MotivoAnulacion' => $data['motivo'],
        ]);

        return $this->getXml();
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    protected function setDatosGenerales()
    {
        $this->document->addDatosGenerales([
            'Tipo' => 'FACT',
            'FechaHoraEmision' => date("Y-m-d\TH:i:s"),
            'CodigoMoneda' => 'GTQ'
        ]);
    }

    protected function setEmisor($companyName): void
    {
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

       /* $frases->addFrase([
            'CodigoEscenario' => '3',
            'TipoFrase' => '4'
        ]);*/
    }

    protected function setItems()
    {
        $items = $this->document->items();

        //$taxes = get_items_table_data($this->data, 'invoice')->taxes();
        //print_r($taxes);

        $count = 1;
        foreach ($this->data->items as $line) {
            $total = $line['rate'] * $line['qty'];
            $item = $items->addItem([
                'NumeroLinea' => $count,
                'BienOServicio' => 'B'
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
}
