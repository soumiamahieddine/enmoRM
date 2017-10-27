<?php

namespace core;

/**
 * Description of token
 *
 * @author Alexis Ragot <alexis.ragot@maarch.org>
 */
class token
{

    /**
     * The data of token encoded in JSON
     * @var string
     */
    public $data;

    /**
     * The expiration of token
     * @var int
     */
    public $expiration;

    /**
     * Contructor of Token class
     * @param object $data
     * @param string $expiration
     */
    public function __construct($data, $expiration)
    {
        $this->data = $data;
        $this->expiration = $expiration;
    }
}
