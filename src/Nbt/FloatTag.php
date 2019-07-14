<?php namespace Celestriode\NbtUtils\Nbt;

class FloatTag extends AbstractNumberTag
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

            throw new \InvalidArgumentException('Value must be numeric');
        }

        return (float)$value;
    }

    public function getType(): int
    {
        return self::TAG_FLOAT;
    }
}