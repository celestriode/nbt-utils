<?php namespace Celestriode\NbtUtils\Nbt;

use Celestriode\NbtUtils\Exception\NbtFormatException;

abstract class AbstractListTag implements TagInterface
{
    private $values = [];

    abstract public function getListType(): int;

    public function __construct(TagInterface ...$tags)
    {
        for ($i = 0, $j = count($tags); $i < $j; $i++) {

            $this->add($tags[$i]);
        }
    }

    public function add(TagInterface $tag): TagInterface
    {
        if (!$this->canAdd($tag)) {

            throw new NbtFormatException('Cannot add tag of type ' . TagUtils::convertToString($tag->getType()) . ' to list of type ' . TagUtils::convertToString($this->getListType()));
        }

        $this->values[] = $tag;

        return $tag;
    }

    public function canAdd(TagInterface $tag): bool
    {
        return $this->getListType() === $tag->getType();
    }
}