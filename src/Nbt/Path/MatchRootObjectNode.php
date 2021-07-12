<?php namespace Celestriode\NbtUtils\Nbt\Path;

use Celestriode\NbtUtils\Nbt\CompoundTag;
use Celestriode\NbtUtils\NbtPathReader;

class MatchRootObjectNode implements NodeInterface
{
    public function __construct(CompoundTag $compoundTag)
    {
        //$this->predicate = NbtPathReader::createTagPredicate($compoundTag);
    }
}