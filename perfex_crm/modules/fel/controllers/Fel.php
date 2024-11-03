<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Fel extends AdminController
{
    protected $data = [];

    public function __construct()
    {
        parent::__construct();

        if (!is_admin()) {
            access_denied('FEL');
        }

        $this->data['title'] = 'Factura Electronica';
        $this->load->model('invoices_model');
        $this->load->library(FEL_MODULE_NAME . '/GFelService', '', 'factory');
    }

    /* Database back up functions */
    public function index()
    {
        $this->load->view('fel_invoice', $this->data);
    }

    public function invoice($id)
    {
        if ($this->factory->invoiceNotSigned($id)) {
            $this->data['title'] = 'Certificar Factura Electronica';
        } else {
            $this->data['title'] = 'Factura Electronica';
        }

        $this->data['invoice'] = $this->factory->getInvoice();
        $this->load->view('fel_invoice', $this->data);
    }

    public function stamp($id)
    {
        if ($this->factory->invoiceNotSigned($id)) {
            if (false === $this->factory->stamp($id)) {
                $this->data['stamp_message_detail'] = $this->factory->getStampMessageDetail();
            }

            $this->data['invoice_xml'] = $this->factory->getXml();
            $this->data['stamp_message'] = $this->factory->getStampMessage();
        }

        $this->data['invoice'] = $this->factory->getInvoice();
        $this->load->view('fel_invoice', $this->data);
    }
    protected function updateInvoiceSignData($invoiceID)
    {
        $certificado = $this->gfel->getCertificate();

        $data = [
            'number' => $certificado->NUMERO
        ];

        $this->db->where('id', $invoiceID);
        $this->db->update('tblinvoices', $data);
    }

    public function cancel()
    {
        if ($this->factory->invoiceNotSigned($this->input->post('id'))) {
            return;
        }

        $motivoCancelacion = $this->input->post('motivo');

        if (false === $this->factory->cancel($this->input->post('id'), $motivoCancelacion)) {
            $this->data['stamp_message_detail'] = $this->factory->getStampMessageDetail();
        }

        $this->data['invoice_xml'] = $this->factory->getXml();
        $this->data['stamp_message'] = $this->factory->getStampMessage();

        $this->data['invoice'] = $this->factory->getInvoice();
        $this->load->view('fel_invoice', $this->data);
    }
}
