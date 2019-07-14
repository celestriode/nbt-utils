<?php namespace Celestriode\NbtUtils\Nbt;

use Celestriode\NbtUtils\Exception\NbtFormatException;

class ByteTag extends AbstractNumberTag
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

        $value = (int)$value;

        if ($value < -128 || $value > 127) {

            throw new NbtFormatException('Byte should not be set out of -128 to 127 range');
        }

        return $value;
    }

    public function getType(): int
    {
        return self::TAG_BYTE;
    }
}