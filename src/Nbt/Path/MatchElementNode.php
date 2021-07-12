<?php namespace Celestriode\NbtUtils\Nbt\Path;

use Celestriode\NbtUtils\Nbt\CompoundTag;
use Celestriode\NbtUtils\Nbt\TagInterface;

class MatchElementNode implements NodeInterface
{
    /**
     * @var CompoundTag
     */
    private $pattern;

    public function __construct(CompoundTag $compoundTag)
    {
        $this->pattern = $compoundTag;
        //$this->predicate = NbtPathReader::createTagPredicate($compoundTag);
    }
}