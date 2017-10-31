<?php

namespace BaseKit\Faktory;

class FaktoryJob implements \JsonSerializable
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $args;

    public function __construct(string $id, string $type, array $args = [])
    {
        $this->id = $id;
        $this->type = $type;
        $this->args = $args;
    }

    public function jsonSerialize() : array
    {
        return [
            'jid' => $this->id,
            'jobtype' => $this->type,
            'args' => $this->args,
        ];
    }
}
