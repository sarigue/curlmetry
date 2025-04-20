<?php

namespace Psr\Http\Client;

use Exception;

/**
 * Every HTTP client related exception MUST implement this interface.
 */
interface ClientExceptionInterface
{
    /**
     * Gets the message
     * @link https://php.net/manual/en/exception.getmessage.php
     * @return string
     */
    public function getMessage();

    /**
     * Gets the exception code
     * @link https://php.net/manual/en/exception.getcode.php
     * @return int <p>
     * Returns the exception code as integer in
     * {@see Exception} but possibly as other type in
     * {@see Exception} descendants (for example as
     * string in {@see PDOException}).
     * </p>
     */
    public function getCode();

    /**
     * Gets the file in which the exception occurred
     * @link https://php.net/manual/en/exception.getfile.php
     * @return string Returns the name of the file from which the object was thrown.
     */
    public function getFile();

    /**
     * Gets the line on which the object was instantiated
     * @link https://php.net/manual/en/exception.getline.php
     * @return int Returns the line number where the thrown object was instantiated.
     */
    public function getLine();

    /**
     * Gets the stack trace
     * @link https://php.net/manual/en/exception.gettrace.php
     * @return array <p>
     * Returns the stack trace as an array in the same format as
     * {@see debug_backtrace()}.
     * </p>
     */
    public function getTrace();

    /**
     * Gets the stack trace as a string
     * @link https://php.net/manual/en/exception.gettraceasstring.php
     * @return string Returns the stack trace as a string.
     */
    public function getTraceAsString();

    /**
     * Returns the previous Throwable
     *
     * @link https://php.net/manual/en/exception.getprevious.php
     * @return null|Exception Returns the previous {@see Exception} if available, or <b>NULL</b> otherwise.
     */
    public function getPrevious();

    /**
     * Gets a string representation of the thrown object
     * @link https://php.net/manual/en/exception.tostring.php
     * @return string <p>Returns the string representation of the thrown object.</p>
     */
    public function __toString();
}