<?php namespace Celestriode\NbtUtils\Nbt;

use Celestriode\NbtUtils\Exception\NbtFormatException;

class IntTag extends AbstractNumberTag
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

        $value = (int)$value;

        if ($value < -2147483648 || $value > 2147483647) {

            throw new NbtFormatException('Integer should not be set out of -2147483648 to 2147483647 range');
        }

        return $value;
    }

    public function getType(): int
    {
        return self::TAG_INT;
    }
}