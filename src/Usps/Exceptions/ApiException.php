<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Exceptions;

/**
 * Thrown when the USPS API returns an error response.
 */
class ApiException extends UspsException
{
    protected int $statusCode;

    /** @var array<string, mixed> */
    protected array $responseBody;

    /**
     * @param array<string, mixed> $responseBody
     */
    public function __construct(string $message, int $statusCode = 0, array $responseBody = [])
    {
        parent::__construct($message, $statusCode);
        $this->statusCode = $statusCode;
        $this->responseBody = $responseBody;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * @return array<string, mixed>
     */
    public function getResponseBody(): array
    {
        return $this->responseBody;
    }
}
