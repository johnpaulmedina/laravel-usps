<?php

declare(strict_types=1);

namespace Johnpaulmedina\Usps\Exceptions;

/**
 * Thrown when input validation fails (invalid ZIP, state, weight, etc.).
 */
class ValidationException extends UspsException
{
}
