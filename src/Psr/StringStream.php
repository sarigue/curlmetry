<?php

namespace Curlmetry\Psr;

use Psr\Http\Message\StreamInterface;

/**
 * Class StringStream
 *
 * Implements a stream interface for handling in-memory string data.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class StringStream implements StreamInterface
{
    /** @var string  */
    private $content;
    /** @var int  */
    private $position = 0;
    /** @var bool  */
    private $writable = false;

    /**
     * @param string $content
     * @param bool   $writable
     */
    public function __construct($content, $writable = false)
    {
        $this->content = (string) $content;
        $this->writable = $writable;
        $this->position = $writable ? strlen($this->content) : 0;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->position = strlen($this->content);
        return $this->content;
    }

    /**
     * @return void
     *
     * @codeCoverageIgnore
     */
    public function close()
    {
    }

    /**
     * @return null
     *
     * @codeCoverageIgnore
     */
    public function detach()
    {
        return null;
    }

    /**
     * @return int|null
     */
    public function getSize()
    {
        return strlen($this->content);
    }

    /**
     * @return int
     */
    public function tell()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function eof()
    {
        return $this->position >= strlen($this->content);
    }

    /**
     * @return true
     */
    public function isSeekable()
    {
        return true;
    }

    /**
     * @param $offset
     * @param $whence
     *
     * @return void
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $position = $this->position;
        if ($whence === SEEK_SET) {
            $position = $offset;
        }
        if ($whence === SEEK_CUR) {
            $position += $offset;
        }
        if ($whence === SEEK_END) {
            $position = strlen($this->content) - $offset;
        }

        $this->position = min(max($position, 0), strlen($this->content));
    }

    /**
     * @return void
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * @return bool
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * @param $string
     *
     * @return int
     */
    public function write($string)
    {
        $this->content = substr($this->content, 0, $this->position)
            . $string
            . substr($this->content, $this->position)
        ;
        $this->position = $this->position + strlen($string);
        return $this->position;
    }

    /**
     * @return true
     */
    public function isReadable()
    {
        return true;
    }

    /**
     * @param $length
     *
     * @return false|string
     */
    public function read($length)
    {
        $result = substr($this->content, $this->position, $length);
        $this->position += strlen($result);
        if ($this->position >= strlen($this->content)) {
            $this->position = strlen($this->content);
        }
        return $result;
    }

    /**
     * @return false|string
     */
    public function getContents()
    {
        $result = substr($this->content, $this->position);
        $this->position = strlen($this->content);
        return $result;
    }

    /**
     * @param string $key
     *
     * @return null
     *
     * @codeCoverageIgnore
     */
    public function getMetadata($key = null)
    {
        return null;
    }
}
