<?php namespace Celestriode\NbtUtils\Nbt\Path;

class CompoundChildNode implements NodeInterface
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}