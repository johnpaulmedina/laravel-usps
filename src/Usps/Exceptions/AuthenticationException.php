<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Exceptions;

/**
 * Thrown when OAuth token request fails or credentials are missing/invalid.
 */
class AuthenticationException extends UspsException
{
}
