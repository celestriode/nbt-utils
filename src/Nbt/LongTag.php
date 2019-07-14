<?php namespace Celestriode\NbtUtils\Nbt;

use Celestriode\NbtUtils\Exception\NbtFormatException;

class LongTag extends AbstractNumberTag
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

        if (bccomp($value, '9223372036854775807', 0) === 1 || bccomp('-9223372036854775808', $value, 0) === 1) { // TODO: make sure this works

            throw new NbtFormatException('Long should not be set out of -9223372036854775808 to 9223372036854775807 range');
        }

        return (int)$value;
    }

    public function getType(): int
    {
        return self::TAG_LONG;
    }
}