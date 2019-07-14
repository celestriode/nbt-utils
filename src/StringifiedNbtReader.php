<?php namespace Celestriode\NbtUtils;

use Celestriode\Captain\StringReader;
use Celestriode\NbtUtils\Nbt\TagInterface;
use Celestriode\NbtUtils\Nbt\StringTag;
use Celestriode\NbtUtils\Nbt\FloatTag;
use Celestriode\NbtUtils\Nbt\ByteTag;
use Celestriode\NbtUtils\Nbt\LongTag;
use Celestriode\NbtUtils\Nbt\IntTag;
use Celestriode\NbtUtils\Nbt\ShortTag;
use Celestriode\NbtUtils\Nbt\DoubleTag;
use Celestriode\NbtUtils\Nbt\CompoundTag;
use Celestriode\NbtUtils\Nbt\ListTag;
use Celestriode\NbtUtils\Nbt\ByteArrayTag;
use Celestriode\NbtUtils\Nbt\LongArrayTag;
use Celestriode\NbtUtils\Nbt\IntArrayTag;
use Celestriode\NbtUtils\Exception\ReaderException;
use Celestriode\NbtUtils\Exception\NbtFormatException;
use Celestriode\NbtUtils\Nbt\TagUtils;

class StringifiedNbtReader
{
    const PATTERN_DOUBLE_IMPLICIT = '/^[-+]?(?:[0-9]+[.]|[0-9]*[.][0-9]+)(?:e[-+]?[0-9]+)?$/i';
    const PATTERN_DOUBLE_EXPLICIT = '/^[-+]?(?:[0-9]+[.]?|[0-9]*[.][0-9]+)(?:e[-+]?[0-9]+)?d$/i';
    const PATTERN_FLOAT = '/^[-+]?(?:[0-9]+[.]?|[0-9]*[.][0-9]+)(?:e[-+]?[0-9]+)?f$/i';
    const PATTERN_BYTE = '/^[-+]?(?:0|[1-9][0-9]*)b$/i';
    const PATTERN_LONG = '/^[-+]?(?:0|[1-9][0-9]*)l$/i';
    const PATTERN_SHORT = '/^[-+]?(?:0|[1-9][0-9]*)s$/i';
    const PATTERN_INT = '/^[-+]?(?:0|[1-9][0-9]*)$/';

    const COMPOUND_OPEN = '{';
    const COMPOUND_CLOSE = '}';
    const LIST_OPEN = '[';
    const LIST_CLOSE = ']';

    /** @var StringReader $reader */
    private $reader;

    public function __construct(StringReader $reader)
    {
        $this->reader = $reader;
    }

    protected function readString(): string
    {
        $this->reader->skipWhitespace();

        if (!$this->reader->canRead()) {

            throw ReaderException::getBuiltInExceptions()->expectedKey()->createWithContext($this->reader);
        }

        return $this->reader->readString();
    }

    public function parseTag(): TagInterface
    {
        $this->reader->skipWhitespace();

        $peek = $this->reader->peek();

        if ($peek == self::COMPOUND_OPEN) {

            return $this->parseCompoundTag();
        }

        return $peek == self::LIST_OPEN ? $this->parseTagArray() : $this->parseTagPrimitive();
    }

    protected function parseTagPrimitive(): TagInterface
    {
        $start = $this->reader->getCursor();

        if (StringReader::isQuotedStringStart($this->reader->peek())) {
            
            return new StringTag($this->reader->readQuotedString());
        }

        $string = $this->reader->readUnquotedString();

        if (mb_strlen($string) === 0) {

            $this->reader->setCursor($start);

            throw ReaderException::getBuiltInExceptions()->expectedValue()->createWithContext($this->reader);
        }

        return $this->parsePrimitive($string);
    }

    private function parsePrimitive(string $string): TagInterface
    {
        try {
            
            // Float

            if (preg_match(self::PATTERN_FLOAT, $string)) {

                return new FloatTag(mb_substr($string, 0, mb_strlen($string) - 1));
            }

            // Byte

            if (preg_match(self::PATTERN_BYTE, $string)) {

                return new ByteTag(mb_substr($string, 0, mb_strlen($string) - 1));
            }

            // Long

            if (preg_match(self::PATTERN_LONG, $string)) {

                return new LongTag(mb_substr($string, 0, mb_strlen($string) - 1));
            }

            // Short

            if (preg_match(self::PATTERN_SHORT, $string)) {

                return new ShortTag(mb_substr($string, 0, mb_strlen($string) - 1));
            }

            // Int

            if (preg_match(self::PATTERN_INT, $string)) {

                return new IntTag($string);
            }

            // Double (explicit)

            if (preg_match(self::PATTERN_DOUBLE_EXPLICIT, $string)) {

                return new DoubleTag(mb_substr($string, 0, mb_strlen($string) - 1));
            }

            // Double (implicit)

            if (preg_match(self::PATTERN_DOUBLE_IMPLICIT, $string)) {

                return new DoubleTag($string);
            }

            // Bool (true)

            if (strtolower($string) === 'true') {

                return new ByteTag(1);
            }

            // Bool (false)

            if (strtolower($string) === 'false') {

                return new ByteTag(0);
            }
        } catch (NbtFormatException $e) {

        }

        return new StringTag($string);
    }

    protected function parseTagArray(): TagInterface
    {
        return $this->reader->canRead(3) && !StringReader::isQuotedStringStart($this->reader->peek(1)) && $this->reader->peek(2) === ';' ? $this->parseTagPrimitiveArray() : $this->parseListTag();
    }

    private function expect(string $char): void
    {
        $this->reader->skipWhitespace();

        $this->reader->expect($char);
    }

    private function readComma(): bool
    {
        $this->reader->skipWhitespace();

        if ($this->reader->canRead() && $this->reader->peek() == ',') {

            $this->reader->skip();
            $this->reader->skipWhitespace();

            return true;
        }

        return false;
    }

    public function parseCompoundTag(): CompoundTag
    {
        $this->expect(self::COMPOUND_OPEN);

        $compoundTag = new CompoundTag();
        //$this->reader->skipWhitespace(); // TODO: determine if necessary. Pretty sure I already determined it wasn't.

        while ($this->reader->canRead() && $this->reader->peek() != self::COMPOUND_CLOSE) {

            $start = $this->reader->getCursor();
            $key = $this->readString();

            if (mb_strlen($key) == 0) {

                $this->reader->setCursor($start);

                throw ReaderException::getBuiltInExceptions()->expectedKey()->createWithContext($this->reader);
            }

            $this->expect(':');

            $compoundTag->put($key, $this->parseTag());

            if (!$this->readComma()) {

                break;
            }
        }

        if (!$this->reader->canRead()) {

            throw ReaderException::getBuiltInExceptions()->expectedKey()->createWithContext($this->reader);
        }

        $this->expect(self::COMPOUND_CLOSE);
        
        return $compoundTag;
    }

    private function parseListTag(): TagInterface
    {
        $this->expect(self::LIST_OPEN);
        $this->reader->skipWhitespace();

        if (!$this->reader->canRead()) {

            throw ReaderException::getBuiltInExceptions()->expectedValue()->createWithContext($this->reader);
        }

        $listTag = new ListTag();
        $rootDatatype = -1;

        while ($this->reader->canRead() && $this->reader->peek() !== self::LIST_CLOSE) {

            $start = $this->reader->getCursor();
            $tag = $this->parseTag();
            $datatype = $tag->getType();

            if ($rootDatatype < 0) {

                $rootDatatype = $datatype;
            } else if ($rootDatatype !== $datatype) {

                $this->reader->setCursor($start);

                throw ReaderException::getBuiltInExceptions()->listMixed()->createWithContext($this->reader, TagUtils::convertToString($datatype), TagUtils::convertToString($rootDatatype));
            }

            $listTag->add($tag);

            if (!$this->readComma()) {

                break;
            }
        }

        if (!$this->reader->canRead()) {

            throw ReaderException::getBuiltInExceptions()->expectedValue()->createWithContext($this->reader);
        }

        $this->expect(self::LIST_CLOSE);

        return $listTag;
    }

    private function readArray(int $typeOfArray, int $typeOfValue): array
    {
        $array = [];

        while ($this->reader->canRead()) {

            if ($this->reader->peek() !== self::LIST_CLOSE) {

                $start = $this->reader->getCursor();
                $tag = $this->parseTag();
                $datatype = $tag->getType();

                if ($datatype !== $typeOfValue) {

                    $this->reader->setCursor($start);

                    throw ReaderException::getBuiltInExceptions()->arrayMixed()->createWithContext($this->reader, TagUtils::convertToString($datatype), TagUtils::convertToString($typeOfArray));
                }

                $array[] = $tag;

                if ($this->readComma()) {

                    continue;
                }
            }

            if (!$this->reader->canRead()) {

                throw ReaderException::getBuiltInExceptions()->expectedValue()->createWithContext($this->reader);
            }

            $this->expect(self::LIST_CLOSE);
    
            return $array;
        }
    }

    private function parseTagPrimitiveArray(): TagInterface
    {
        $this->expect(self::LIST_OPEN);

        $start = $this->reader->getCursor();
        $identifier = $this->reader->read();

        $this->reader->read();
        $this->reader->skipWhitespace();

        if (!$this->reader->canRead()) {

            throw ReaderException::getBuiltInExceptions()->expectedValue()->createWithContext($this->reader);
        }

        switch ($identifier) {

            case 'B':
                return new ByteArrayTag(...$this->readArray(TagInterface::TAG_BYTE_ARRAY, TagInterface::TAG_BYTE));
            case 'L':
                return new LongArrayTag(...$this->readArray(TagInterface::TAG_LONG_ARRAY, TagInterface::TAG_LONG));
            case 'I':
                return new IntArrayTag(...$this->readArray(TagInterface::TAG_INT_ARRAY, TagInterface::TAG_INT));
            default:
                $this->reader->setCursor($start);

                throw ReaderException::getBuiltInExceptions()->arrayInvalid()->createWithContext($this->reader, $identifier);
        }
    }

    public static function parse(string $string): CompoundTag
    {
        $nbtReader = new self(new StringReader($string));

        return $nbtReader->readCompoundTag();
    }

    private function readCompoundTag(): CompoundTag
    {
        $compoundTag = $this->parseCompoundTag();
        
        $this->reader->skipWhitespace();

        if ($this->reader->canRead()) {

            throw ReaderException::getBuiltInExceptions()->trailing()->createWithContext($this->reader);
        }

        return $compoundTag;
    }
}