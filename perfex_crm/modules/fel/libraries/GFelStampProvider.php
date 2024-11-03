<?php
ini_set('max_execution_time', 120);
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

use Juanj\Gfel\FelDocument;
use Juanj\Gfel\GTFelStampService;

class GFelStampProvider
{
    const GFEL_PATH = APPPATH . 'gfel/';

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
    /**
     * @var string
     */
    protected $userNit = '';
    /**
     * @var string
     */
    protected $userName = '';
    /**
     * @var string
     */
    protected $userToken = '';

    public function __construct($userNit, $userName, $userToken)
    {
        $this->userName = $userName;
        $this->userNit = $userNit;
        $this->userToken = $userToken;
    }

    public function stamp($xml): bool
    {
        $server = $this->getStampServerUrl();
        $authorization = $this->getStampAuthorization();

        $service = new GTFelStampService($server, $authorization);

        $this->response = $service->stamp($xml);

        if (false === $this->response) {
            return false;
        }

        $this->certificado = json_decode($this->response);

        if (1 === $this->certificado->Codigo) {
            return true;
        }

        return false;
    }

    public function cancel($xml): bool
    {
        $server = $this->getCancelationServerUrl();
        $authorization = $this->getStampAuthorization();

        $service = new GTFelStampService($server, $authorization);

        $this->response = $service->stamp($xml);

        if (false === $this->response) {
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

    public function saveInvoiceCert($response): bool
    {
        $path = self::GFEL_PATH . 'files';
        if (false === is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $filename = $path . '/x.json';

        if (false === file_put_contents($filename, $response)) {
            return false;
        }

        return true;
    }

    protected function getStampServerUrl(): string
    {
        return 'https://felgttestaws.digifact.com.gt/gt.com.fel.api.v3/api/FelRequestV2?NIT=' . $this->userNit
            . '&TIPO=CERTIFICATE_DTE_XML_TOSIGN&FORMAT=XML,PDF,HTML&USERNAME=' . $this->userName;
    }

    protected function getStampAuthorization(): string
    {
        return 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1bmlxdWVfbmFtZSI6IkdULjAwMDA0NDY1Mzk0OC5DSV'
            . 'ZFUk5FVFRFU1QiLCJuYmYiOjE2NTIxMzMzMTEsImV4cCI6MTY4MzIzNzMxMSwiaWF0IjoxNjUyMTMzMzExL'
            . 'CJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjQ5MjIwIiwiYXVkIjoiaHR0cDovL2xvY2FsaG9zdDo0OTIyMCJ9'
            . '.SsB-x0UsGHbNoTimKJrXBZmXyRsK2P3WoQLkWQrWMPs';
    }

    protected function getCancelationServerUrl(): string
    {
        return 'https://felgttestaws.digifact.com.gt/gt.com.fel.api.v3/api/FelRequestV2?NIT=' . $this->userNit
            . '&TIPO=ANULAR_FEL_TOSIGN&FORMAT=XML,PDF,HTML&USERNAME=' . $this->userName;
    }
}
