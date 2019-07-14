<?php namespace Celestriode\NbtUtils\Nbt;

class CompoundTag implements TagInterface
{
    private $tags = [];

    public function put(string $key, TagInterface $tag): TagInterface
    {
        return $this->tags[$key] = $tag;
    }

    public function getType(): int
    {
        return self::TAG_COMPOUND;
    }
}