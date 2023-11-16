<?php

namespace App;
class Response
{
    private int $statusCode;
    private array $headers;
    private $body;

    public function __construct($statusCode = 200, $headers = [])
    {
        $this->statusCode = $statusCode;
        $this->headers = array_merge(
            [
                'Content-Type' => 'application/json; charset=UTF-8',
                'Access-Control-Allow-Origin' => '*',
                'Access-Control-Allow-Credentials' => 'true',
                'Access-Control-Max-Age' => '1000',
                'Access-Control-Allow-Headers' => 'X-Requested-With, Content-Type, Origin, Cache-Control, Pragma, Authorization, Accept, Accept-Encoding',
                'Access-Control-Allow-Methods' => 'PUT, POST, GET, OPTIONS, DELETE'
            ],
            $headers
        );
    }

    public function setBody($data = [], $message = '', $status = 'success')
    {
        $this->body = json_encode([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ]);

        return $this;
    }

    public function success($data = [], $message = '')
    {
        return $this->setBody($data, $message, 'success');
    }

    public function error($data = [], $message = '', $statusCode = 400)
    {
        $this->statusCode = $statusCode;
        return $this->setBody($data, $message, 'error');
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $header => $value) {
            header("$header: $value");
        }

        echo $this->body;
        exit();
    }
}
