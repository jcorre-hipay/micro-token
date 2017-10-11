<?php

/**
 * Class HttpResponse
 */
class HttpResponse
{
    /**
     * @var string
     */
    private $contents;

    /**
     * @var int
     */
    private $statusCode;

    /**
     * HttpResponse constructor.
     * @param string $contents
     * @param int $statusCode
     */
    public function __construct($contents, $statusCode)
    {
        $this->contents = $contents;
        $this->statusCode = $statusCode;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        return $this->contents;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }
}