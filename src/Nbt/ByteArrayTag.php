<?php namespace Celestriode\NbtUtils\Nbt;

class ByteArrayTag extends AbstractArrayTag
{
    public function getType(): int
    {
        return self::TAG_BYTE_ARRAY;
    }

    public function getListType(): int
    {
        return self::TAG_BYTE;
    }
}