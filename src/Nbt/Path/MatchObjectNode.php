<?php namespace Celestriode\NbtUtils\Nbt\Path;

use Celestriode\NbtUtils\Nbt\CompoundTag;
use Celestriode\NbtUtils\NbtPathReader;

class MatchObjectNode implements NodeInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var CompoundTag
     */
    private $pattern;

    public function __construct(string $name, CompoundTag $compoundTag)
    {
        $this->name = $name;
        $this->pattern = $compoundTag;
        //$this->predicate = NbtPathReader::createTagPredicate($compoundTag);
    }
}