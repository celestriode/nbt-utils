<?php namespace Celestriode\NbtUtils\Nbt;

class LongArrayTag extends AbstractArrayTag
{
    public function getType(): int
    {
        return self::TAG_LONG_ARRAY;
    }

    public function getListType(): int
    {
        return self::TAG_LONG;
    }
}