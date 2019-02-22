<?php

namespace Dallgoot\Yaml;

/**
 *
 * @author  Stéphane Rebai <stephane.rebai@gmail.com>
 * @license Apache 2.0
 * @link    TODO : url to specific online doc
 */
class NodeJSON extends Node
{
    private const JSON_OPTIONS = \JSON_PARTIAL_OUTPUT_ON_ERROR|\JSON_UNESCAPED_SLASHES;

    // public function __construct(string $nodeString, int $line, $json)
    // {
    //     parent::__construct($nodeString, $line);
    //     $this->value = $json;
    // }

    public function isAwaitingChildren():bool
    {
        return false;
    }

    public function build(&$parent = null)
    {
        return json_decode($this->raw, false, 512, self::JSON_OPTIONS);
    }
}