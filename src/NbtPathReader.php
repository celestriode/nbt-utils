<?php namespace Celestriode\NbtUtils;

use Celestriode\Captain\StringReader;
use Celestriode\NbtUtils\Exception\ReaderException;
use Celestriode\NbtUtils\Nbt\Path\AllElementsNode;
use Celestriode\NbtUtils\Nbt\Path\CompoundChildNode;
use Celestriode\NbtUtils\Nbt\Path\IndexedElementNode;
use Celestriode\NbtUtils\Nbt\Path\MatchElementNode;
use Celestriode\NbtUtils\Nbt\Path\MatchObjectNode;
use Celestriode\NbtUtils\Nbt\Path\MatchRootObjectNode;
use Celestriode\NbtUtils\Nbt\Path\NbtPath;
use Celestriode\NbtUtils\Nbt\Path\NodeInterface;

class NbtPathReader
{
    private $reader;

    public function __construct(StringReader $reader)
    {
        $this->reader = $reader;
    }

    public static function parse(string $string): NbtPath
    {
        $nbtReader = new self(new StringReader($string));

        return $nbtReader->parsePath();
    }

    public function parsePath(): NbtPath
    {
        $list = [];
        $n = $this->reader->getCursor();
        $bl = true;

        while ($this->reader->canRead() && $this->reader->peek() != ' ') {

            $node = $this->parseNode($bl);
            $list[] = $node;
            $bl = false;

            if (
                !$this->reader->canRead()
                || ($char = $this->reader->peek()) == ' '
                || $char == '['
                || $char == '{'
            ) {

                continue;
            }

            $this->reader->expect('.');
        }

        return new NbtPath(mb_substr($this->reader->getString(), $n, $this->reader->getCursor() - $n), ...$list);
    }

    protected function parseNode(bool $bl): NodeInterface
    {
        switch ($this->reader->peek()) {

            case '{':
                if (!$bl) {

                    throw ReaderException::getBuiltInExceptions()->invalidNode()->createWithContext($this->reader);
                }

                $compoundTag = (new StringifiedNbtReader($this->reader))->parseCompoundTag();

                return new MatchRootObjectNode($compoundTag);
            case '[':
                $this->reader->skip();
                $char = $this->reader->peek();

                if ($char == '{') {

                    $compoundTag = (new StringifiedNbtReader($this->reader))->parseCompoundTag();
                    $this->reader->expect(']');

                    return new MatchElementNode($compoundTag);
                }

                if ($char == ']') {

                    $this->reader->skip();

                    return new AllElementsNode(); // TODO: singleton.
                }

                $n = $this->reader->readInt();
                $this->reader->expect(']');
                return new IndexedElementNode($n);
            case '"':
                $string = $this->reader->readString();
                return $this->readObjectNode($string);
        }

        $string = $this->readUnquotedName();

        return $this->readObjectNode($string);
    }

    private function readObjectNode(string $string): NodeInterface
    {
        if ($this->reader->canRead() && $this->reader->peek() == '{') {

            $compoundTag = (new StringifiedNbtReader($this->reader))->parseCompoundTag();

            return new MatchObjectNode($string, $compoundTag);
        }

        return new CompoundChildNode($string);
    }

    private function readUnquotedName(): string
    {
        $n = $this->reader->getCursor();

        while ($this->reader->canRead() && $this->isAllowedInUnquotedNames($this->reader->peek())) {

            $this->reader->skip();
        }

        if ($this->reader->getCursor() == $n) {

            throw ReaderException::getBuiltInExceptions()->invalidNode()->createWithContext($this->reader);
        }

        return mb_substr($this->reader->getString(), $n, $this->reader->getCursor() - $n);
    }

    private function isAllowedInUnquotedNames(string $char): bool
    {
        return (
            $char != ' '
            && $char != '"'
            && $char != '['
            && $char != ']'
            && $char != '.'
            && $char != '{'
            && $char != '}'
        );
    }
}