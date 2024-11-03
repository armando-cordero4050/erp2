<?php

defined('BASEPATH') or exit('No direct script access allowed');

if ($CI->db->table_exists('tblinvoices')) {
    $CI->db->query('ALTER TABLE `tblinvoices` ADD COLUMN IF NOT EXISTS `fel_autorizacion` VARCHAR(36) DEFAULT NULL;');
    $CI->db->query('ALTER TABLE `tblinvoices` ADD COLUMN IF NOT EXISTS `fel_numero` VARCHAR(10) DEFAULT NULL;');
    $CI->db->query('ALTER TABLE `tblinvoices` ADD COLUMN IF NOT EXISTS `fel_serie` VARCHAR(10) DEFAULT NULL;');
    $CI->db->query('ALTER TABLE `tblinvoices` ADD COLUMN IF NOT EXISTS `fel_fecha_certificacion` DATETIME DEFAULT NULL;');
    $CI->db->query('ALTER TABLE `tblinvoices` ADD COLUMN IF NOT EXISTS `fel_fecha_dte` DATETIME DEFAULT NULL;');
}

$customFile = FEL_MODULE_PATH . '/views/my_invoice.php';
$coreFile = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_invoicepdf.php';

if (false === copy($customFile, $coreFile)) {
    echo 'Error con el pdf copaindo';
}
