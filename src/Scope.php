<?php

namespace Curlmetry;

use Curlmetry\Exception\ContextException;
use Curlmetry\Utils\JsonUtils;

/**
 * Represents a scope that manages the lifecycle and context association of a span.
 *
 * The Scope class is designed to facilitate the management of contextual spans,
 * allowing for operations such as detaching, closing, and serialization.
 * It implements the JsonSerializable interface to support JSON-based
 * representation of its internal state.
 *
 * @author  github.com/sarigue
 * @license Apache 2.0
 */
class Scope implements \JsonSerializable
{
    /** @var Span  */
    private $span;

    public function __construct(Span $span)
    {
        $this->span = $span;
    }

    /**
     * @return void
     * @throws ContextException
     */
    public function detach()
    {
        if (Context::current() === $this->span) {
            Context::pop();
        }
        /*
        $popped = Context::pop();
        if ($popped !== $this->span) {
            throw new ContextException('⚠️ Erreur : contexte désynchronisé dans detach()');
        }
        */
    }

    /**
     * @return Span
     */
    public function getSpan()
    {
        return $this->span;
    }

    /**
     * @return void
     * @throws ContextException
     */
    public function close()
    {
        $this->detach();
    }

    /**
     * @return static|null
     */
    public static function current()
    {
        $span = Context::current();
        return $span ? new static($span) : null;
    }


    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return [
            'span' => $this->span
        ];
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return json_encode($this, JSON_PRETTY_PRINT);
    }

    /**
     * @param $json
     *
     * @return static
     */
    public static function fromJson($json)
    {
        return new static(Span::fromJson(JsonUtils::create($json)->getValue('span')));
    }
}
