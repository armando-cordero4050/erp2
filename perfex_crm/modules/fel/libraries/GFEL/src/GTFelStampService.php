<?php

namespace Juanj\Gfel;

class GTFelStampService
{
    public function __construct($server, $authorization)
    {
        $this->server = $server;
        $this->authorization = $authorization;
    }

    const LOG_PATH = './Log.json';

    /**
     * @param string $xml
     * @return bool|string
     */
    public function stamp(string $xml)
    {
        $options = [
            CURLOPT_HTTPHEADER => $this->getHeaders(),
            CURLOPT_POSTFIELDS => $xml
        ];

        $handler = curl_init();
        curl_setopt_array($handler, $options + $this->getCurlOptions());
        $response = curl_exec($handler);

        if (false === $response OR empty($response)) {
            echo 'CURL Response Code: ' . curl_getinfo($handler, CURLINFO_HTTP_CODE);
            echo 'CURL Error: ' . curl_error($handler);

            curl_close($handler);
            return false;
        }
        curl_close($handler);
        //$this->saveLog($response);

        return $response;
    }

    protected function getHeaders(): array
    {
        return [
            'Authorization: ' . $this->authorization,
            'Content-Type: application/json'
        ];
    }

    protected function getCurlOptions(): array
    {
        return [
            CURLOPT_URL => $this->server,
            CURLOPT_VERBOSE => 1,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
            CURLOPT_CONNECTTIMEOUT => 300
        ];
    }

    protected function saveLog(string $result)
    {
        if (false === file_put_contents(self::LOG_PATH, $result)) {
            return false;
        }

        return true;
    }
}
