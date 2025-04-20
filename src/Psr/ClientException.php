<?php

namespace Curlmetry\Psr;

use Psr\Http\Client\ClientExceptionInterface;

/**
 * Represents an exception specific to client-side errors.
 *
 * This exception is thrown when an error occurs during a client operation.
 * It extends the base Exception class and implements the ClientExceptionInterface
 * to provide a standard structure for client exceptions.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class ClientException extends \Exception implements ClientExceptionInterface
{
}
