<?php

declare(strict_types=1);

namespace InnStudio\Request;

use CurlHandle;

final class Request
{
    private string $basicUrl;

    private string $route = '';

    private ?array $body = null;

    private array $query = [];

    private CurlHandle $ch;

    private int $timeout = 30;

    private array $headers = [
        'Content-Type: application/json; charset=utf-8',
        'Cache-Control: no-cache',
        'Pragma: no-cache',
        'Accept: application/json',
    ];

    private ?array $info;

    private string $ua = '';

    private int $errNo = 0;

    private string $errMsg = '';

    public function __construct()
    {
        $this->ch = curl_init();
    }

    public function hasErr(): bool
    {
        return 0 !== $this->errNo;
    }

    public function errNo(): int
    {
        return $this->errNo;
    }

    public function errMsg(): string
    {
        return $this->errMsg;
    }

    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;

        return $this;
    }

    public function setHeader(string $key, string $value): self
    {
        foreach ($this->headers as &$header) {
            if (0 === mb_strpos($header, "{$key}:")) {
                $header = "{$key}: {$value}";

                return $this;
            }
        }

        $this->addHeader($key, $value);

        return $this;
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function addHeader(string $key, string $value): self
    {
        $this->headers[] = "{$key}: {$value}";

        return $this;
    }

    public function setTimeout(int $timeout): self
    {
        $this->timeout = $timeout;

        return $this;
    }

    public function setQuery(array $query): self
    {
        $this->query = $query;

        return $this;
    }

    public function setBasicUrl(string $basicUrl): self
    {
        $this->basicUrl = $basicUrl;

        return $this;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;

        return $this;
    }

    public function setBody(array $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function GET(): array
    {
        return $this->exec();
    }

    public function POST(): array
    {
        curl_setopt($this->ch, \CURLOPT_POSTFIELDS, json_encode($this->body, \JSON_UNESCAPED_UNICODE));
        curl_setopt($this->ch, \CURLOPT_CUSTOMREQUEST, 'POST');

        return $this->exec();
    }

    public function PUT(): array
    {
        curl_setopt($this->ch, \CURLOPT_POSTFIELDS, json_encode($this->body, \JSON_UNESCAPED_UNICODE));
        curl_setopt($this->ch, \CURLOPT_CUSTOMREQUEST, 'PUT');

        return $this->exec();
    }

    public function DELETE(): array
    {
        curl_setopt($this->ch, \CURLOPT_CUSTOMREQUEST, 'DELETE');

        return $this->exec();
    }

    public function setUa(string $ua): self
    {
        $this->ua = $ua;

        return $this;
    }

    /**
     * Fetch request results.
     *
     * @return array $array
     *               + status int
     *               + data array
     *               + info array
     */
    private function exec(): array
    {
        $url = $this->url();
        curl_setopt_array($this->ch, [
            \CURLOPT_URL            => $url,
            \CURLOPT_HTTPHEADER     => $this->headers,
            \CURLOPT_CONNECTTIMEOUT => $this->timeout,
            \CURLOPT_RETURNTRANSFER => true,
            \CURLOPT_SSL_VERIFYPEER => true,
            \CURLOPT_FOLLOWLOCATION => true,
            \CURLOPT_USERAGENT      => $this->ua,
        ]);
        $data         = curl_exec($this->ch);
        $this->info   = curl_getinfo($this->ch);
        $this->errNo  = curl_errno($this->ch);
        $this->errMsg = curl_error($this->ch);
        curl_close($this->ch);

        return [
            'status' => $this->info['http_code'],
            'data'   => $data ? json_decode($data, true) : null,
            'raw'    => $data,
            'info'   => $this->info,
        ];
    }

    private function url(): string
    {
        if ($this->basicUrl) {
            if ($this->route) {
                $route = ltrim($this->route, '/');
                $url   = rtrim($this->basicUrl, '/') . "/{$route}";
            } else {
                $url = $this->basicUrl;
            }
        } else {
            $url = $this->route;
        }

        if ($this->query) {
            return "{$url}?" . http_build_query($this->query);
        }

        return $url;
    }
}
