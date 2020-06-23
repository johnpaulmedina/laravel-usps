<?php
/**
 * Created by PhpStorm.
 * User: Patrick Breen
 * Date: 2/14/2018
 * Time: 2:54 PM
 */

namespace Johnpaulmedina\Usps\Exceptions;

use Throwable;

class UspsException extends \Exception
{
    public function __construct(string $message, int $code)
    {
        parent::__construct($message, $code, null);
    }
}
