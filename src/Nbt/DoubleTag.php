<?php namespace Celestriode\NbtUtils\Nbt;

use Celestriode\NbtUtils\Exception\NbtFormatException;

class DoubleTag extends AbstractNumberTag
{
    /**
     * Changes the value into the correct datatype.
     *
     * @param mixed $value The value to change, usually being a string.
     * @return int|float
     */
    public function parseValue($value)
    {
        if (!is_numeric($value)) {

            throw new NbtFormatException('Value must be numeric');
        }

        return (double)$value;
    }

    public function getType(): int
    {
        return self::TAG_DOUBLE;
    }
}