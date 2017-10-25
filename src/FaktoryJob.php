<?php
namespace BaseKit\Faktory;

class FaktoryJob
{
    private $type;
    private $args;

    public function __construct(string $type, array $args = [])
    {
        $this->type = $type;
        $this->args = $args;
    }
}
