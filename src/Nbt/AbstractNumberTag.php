<?php namespace Celestriode\NbtUtils\Nbt;

use Celestriode\NbtUtils\Exception\NbtFormatException;

abstract class AbstractNumberTag implements TagInterface
{
    protected $value = 0;

    /**
     * Changes the value into the correct datatype.
     *
     * @param mixed $value The value to change, usually being a string.
     * @return int|float
     */
    abstract public function parseValue($value);

    public function __construct($value = null)
    {
        $this->setValue($this->parseValue($value));
    }

    public function setValue($value)
    {
        if (is_string($value) || !is_numeric($value)) {

            throw new NbtFormatException('Value must not be a string and must be numeric');
        }

        $this->value = $value;
    }

    public function getType(): int
    {
        return self::TAG_NUMERIC;
    }
}