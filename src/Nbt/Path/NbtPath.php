<?php namespace Celestriode\NbtUtils\Nbt\Path;

class NbtPath
{
    /**
     * @var string
     */
    private $original;

    /**
     * @var NodeInterface[]
     */
    private $nodes;

    public function __construct(string $original, NodeInterface ...$nodes)
    {
        $this->original = $original;
        $this->nodes = $nodes;
    }
}