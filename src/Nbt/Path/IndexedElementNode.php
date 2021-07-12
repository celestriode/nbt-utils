<?php namespace Celestriode\NbtUtils\Nbt\Path;

class IndexedElementNode implements NodeInterface
{
    /**
     * @var int
     */
    private $index;

    public function __construct(int $index)
    {
        $this->index = $index;
    }
}