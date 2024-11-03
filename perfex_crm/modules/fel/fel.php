<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Fel
Description: Module for Guatemala FEL
Version: 3
Requires at least: 2.3.*
*/

define('FEL_MODULE_NAME', 'fel');
define('FEL_MODULE_PATH', FCPATH . 'modules/fel');

/**
 * Register activation module hook
 */
register_activation_hook(FEL_MODULE_NAME, 'fel_module_activation_hook');

//hooks()->add_action('after_invoice_added', 'send_invoice_to_stamp_service');
hooks()->add_action('after_invoice_preview_more_menu', 'add_stamp_invoice_option_menu');
hooks()->add_action('after_left_panel_invoicehtml', 'add_stamp_invoice_view_data');
hooks()->add_action('after_right_panel_invoice_preview_template', 'add_stamp_invoice_view_data', 10, 1);
hooks()->add_filter('invoice_pdf_header_before_custom_fields', 'add_stamp_invoice_print_data', 10, 2);

function fel_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');

    $customFile = FEL_MODULE_PATH . '/views/my_invoicepdf.php';
    $coreFile = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_invoicepdf.php';

    if (false === copy($customFile, $coreFile)) {
        echo 'Error con el pdf copaindo';
    }
}

function add_stamp_invoice_option_menu()
{
    $CI =& get_instance();
    $invoice = $CI->load->get_var('invoice');
    echo '<li data-toggle="tooltip" data-title="' . print_r($invoice->id, true) . '">'
        . ' <a href="' . admin_url('fel/invoice/' . $invoice->id) . '">' . 'FEL' . '</a>'
        . '</li>';
}

function add_stamp_invoice_view_data($invoice)
{
    echo 'Numero : ' . $invoice->fel_numero . '<br />'
        . 'Autorizacion: ' . $invoice->fel_autorizacion . '<br />'
        . 'Fecha de certificado: ' . $invoice->fel_fecha_certificacion . '<br />';
}

function add_stamp_invoice_print_data($invoice_info, $invoice)
{
    return $invoice_info .= 'Numero de certificado: ' . $invoice->fel_numero . '<br />'
        . 'Fecha de certificado: ' . $invoice->fel_fecha_certificacion . '<br />';
}

function send_invoice_to_stamp_service($id)
{
    $ci =& get_instance();
    $ci->load->library(FEL_MODULE_NAME . '/GFelService', '', 'factory');

    if ($ci->factory->invoiceNotSigned($id)) {
        $ci->factory->stamp($id);

        $ci->data['invoice_xml'] = $ci->factory->getXml();
        //echo $ci->factory->getStampMessage();
        //echo $ci->factory->getStampMessageDetail();
    }
}
