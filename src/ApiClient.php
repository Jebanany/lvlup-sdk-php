<?php

namespace Jebanany\Lvlup;

use Exception;

class ApiClient extends Services
{

    protected $avalaibleExceptions = [];
    private $lastHttpCode = 0;
    private $initialExceptions = [401 => 'Unauthorized Error (Probably invalid API key)'];
    private $useCurl;
    private $apiKey;
    private $apiUrl;
    private $sandbox;
    private $allowedProtocols = ['arkSurvivalEvolved', 'arma', 'gtaMultiTheftAutoSanAndreas', 'gtaSanAndreasMultiplayerMod', 'hl2Source', 'minecraftPocketEdition', 'minecraftQuery', 'mumble', 'rust', 'teamspeak2', 'teamspeak3', 'trackmaniaShootmania', 'other'];

    public function __construct($apiKey = false, $sandbox = false, $useCurl = false)
    {
        $this->sandbox = $sandbox;
        $this->apiKey = $apiKey;
        if ($useCurl) {
            $this->useCurl = $useCurl;
        } else {
            $this->useCurl = false;
        }
        if ($sandbox) {
            $this->apiUrl = 'https://sandbox-api.lvlup.pro';
        } else {
            $this->apiUrl = 'https://api.lvlup.pro';
        }
    }

    protected function getValidAmount($amount)
    {
        if (is_numeric($amount)) {
            $amount = number_format($amount, 2, '.', '');
        }
        if (preg_match('/^([1-9]\d{0,2})\.\d{2}?$/', $amount)) {
            return $amount;
        } else {
            throw new Exception('Invalid amount format. Expected: 1.00 - 999.99');
        }
    }

    protected function validatePort($port)
    {
        if ($port >= 1 and $port <= 65535) {
            throw new Exception('Invalid port number. Expected: 1 - 65535');
        } else {
            return $port;
        }
    }

    protected function validateUdpProtocol($protocol)
    {
        if (in_array($protocol, $this->allowedProtocols)) {
            return $protocol;
        } else {
            throw new Exception('Invalid game protocol. Expected: ' . implode(',', $this->allowedProtocols));
        }

    }

    protected function doRequest($method, $url, $data = false, $contentType = 'application/json')
    {
        if ($this->useCurl) {
            $method = strtoupper($method);
            if ($method == 'GET' and $data) {
                $curl = curl_init($this->apiUrl . $url . '?' . http_build_query($data));
            } else {
                $curl = curl_init($this->apiUrl . $url);
            }
            $headers = [];
            if ($this->apiKey) {
                $headers[] = 'Authorization: Bearer ' . $this->apiKey;
            }
            if (in_array($method, ['POST', 'PATCH', 'PUT'])) {
                $headers[] = 'Content-Type: ' . $contentType;
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            }
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

            $result = (curl_exec($curl));
            if ($result !== false) {
                if ($contentType == 'application/json') {
                    $result = json_decode($result, false);
                }
                $info = curl_getinfo($curl);
                $this->lastHttpCode = $info['http_code'];
                curl_close($curl);
                if ($info['http_code'] == 200) {
                    return $result;
                } else {
                    $this->generateExceptionHttpCode();
                }
            } else {
                throw new Exception("Curl error: " . curl_error($curl));
            }
        } else {
            $method = strtoupper($method);

            $options = [
                'http' => [
                    'method' => $method
                ]
            ];

            $headers = [];
            if ($this->apiKey) {
                $headers[] = 'Authorization: Bearer ' . $this->apiKey;
            }
            if (in_array($method, ['POST', 'PATCH', 'PUT'])) {
                $headers[] = 'Content-Type: ' . $contentType;
                $options['http']['content'] = json_encode($data);
            }

            $options['http']['header'] = implode("\r\n", $headers);

            if ($method == 'GET' and $data) {
                $url = $this->apiUrl . $url . '?' . http_build_query($data);
            } else {
                $url = $this->apiUrl . $url;
            }

            $streamContext = stream_context_create($options);
            $result = @file_get_contents($url, false, $streamContext);
            if ($result !== false) {
                if ($contentType == 'application/json') {
                    $result = json_decode($result, false);
                }
                return $result;
            } else {
                $error = error_get_last();
                if (isset($http_response_header)) {
                    preg_match('{HTTP\/\S*\s(\d{3})}', $http_response_header[0], $matchCode);
                    unset($http_response_header);
                    $httpCode = $matchCode[1];
                    if (is_numeric($httpCode)) {
                        $this->lastHttpCode = $httpCode;
                        $this->generateExceptionHttpCode();
                    } else {
                        throw new Exception('Request failed: ' . $error['message']);
                    }
                } else {
                    throw new Exception('Request failed: ' . $error['message']);
                }
            }
        }

    }

    protected function generateExceptionHttpCode()
    {
        $exceptions = $this->avalaibleExceptions + $this->initialExceptions;
        $this->avalaibleExceptions = [];
        if (isset($exceptions[$this->lastHttpCode])) {
            throw new Exception($exceptions[$this->lastHttpCode], $this->lastHttpCode);
        } else {
            throw new Exception('Unexpected HTTP code: ' . $this->lastHttpCode);
        }

    }

}