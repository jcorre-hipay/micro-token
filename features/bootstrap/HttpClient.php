<?php

/**
 * Class HttpClient
 */
class HttpClient
{
    /**
     * @var string
     */
    private $domain;

    /**
     * @var int
     */
    private $port;

    /**
     * HttpClient constructor.
     * @param string $domain
     * @param int $port
     */
    public function __construct($domain, $port = 80)
    {
        $this->domain = $domain;
        $this->port = $port;
    }

    /**
     * @param string $endpoint
     * @param array $request
     * @return HttpResponse
     */
    public function post($endpoint, array $request = [])
    {
        $curl = curl_init("http://".$this->domain.$endpoint);

        curl_setopt($curl, CURLOPT_PORT, $this->port);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($request));

        $response = curl_exec($curl);
        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if (false === $response) {
            throw new RuntimeException($error);
        }

        return new HttpResponse($response, $statusCode);
    }
}
