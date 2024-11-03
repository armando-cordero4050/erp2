<?php
require_once 'GFel.php';
require_once 'GFelStampProvider.php';
require_once 'GFelInvoiceDocument.php';

class GFelService
{
    private $ci;

    private $invoice;

    private $builder;

    protected $xml;
    /**
     * @var GFelStampProvider
     */
    protected $stampService;

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->load->model('invoices_model');

        $userNit = get_custom_field_value(0, 'company_fel_nit', 'company', false);
        $userName = get_custom_field_value(0, 'company_fel_usuario', 'company', false);
        $userToken = get_custom_field_value(0, 'company_fel_token', 'company', false);

        $this->builder = new GFelInvoiceDocument();
        $this->stampService = new GFelStampProvider($userNit, $userName, $userToken);
    }

    public function stamp($id)
    {
        $this->invoice = $this->ci->invoices_model->get($id);

        $this->xml = $this->builder->build($this->invoice);

        if (true === $this->stampService->stamp($this->xml)) {
            $this->saveInvoiceStampCert($id);
            $this->invoice = $this->ci->invoices_model->get($id);

            return true;
        }

        return false;
    }

    public function cancel($id, $motivo)
    {
        $nit = get_custom_field_value(0, 'company_fel_nit', 'company', false);

        $this->invoice = $this->ci->invoices_model->get($id);
        //$this->customer = $this->ci->customers_model->get($this->invoice->cientid);

        $data = [
            'nit' => $nit,
            'nitreceptor' => $this->invoice->client->vat,
            'autorizacion' => $this->invoice->fel_autorizacion,
            'fechacertificacion' => $this->invoice->fel_fecha_certificacion,
            'motivo' => $motivo
        ];

        $this->invoice = $this->ci->invoices_model->get($id);
        $this->xml = $this->builder->buildCancelationDocument($data);

        if (true === $this->stampService->cancel($this->xml)) {
            //$this->saveInvoiceStampCert($id);
            //$this->invoice = $this->ci->invoices_model->get($id);

            return true;
        }

        return false;
    }

    public function getInvoice()
    {
        return $this->invoice;
    }

    public function getXml()
    {
        return $this->builder->getXml();
    }

    public function getStampMessage()
    {
        return $this->stampService->getCertificate()->Mensaje;
    }

    public function getStampMessageDetail($pattern = '/\*{5}\*/')
    {
        $response_message = $this->stampService->getCertificate()->ResponseDATA1;
        $text = preg_split($pattern, $response_message, -1, PREG_SPLIT_NO_EMPTY);

        return $text[1] ?? '';
    }

    public function invoiceNotSigned($id): bool
    {
        $this->invoice = $this->ci->invoices_model->get($id);
        return empty($this->invoice->fel_numero);
    }

    protected function saveInvoiceStampCert($id)
    {
        $certificado = $this->stampService->getCertificate();

        $data = [
            'number' => $certificado->NUMERO,
            'fel_serie' => $certificado->Serie,
            'fel_autorizacion' => $certificado->Autorizacion,
            'fel_numero' => $certificado->NUMERO,
            'fel_fecha_dte' => $certificado->Fecha_DTE,
            'fel_fecha_certificacion' => $certificado->Fecha_de_certificacion
        ];

        $this->ci->db->where('id', $id);
        $this->ci->db->update('tblinvoices', $data);
    }
}
