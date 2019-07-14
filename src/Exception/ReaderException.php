<?php namespace Celestriode\NbtUtils\Exception;

class ReaderException extends NbtUtilsException
{
    private static $BuiltInReaderExceptions;

    public static function getBuiltInExceptions(): BuiltInReaderExceptions
    {
        return self::$BuiltInReaderExceptions ?? self::$BuiltInReaderExceptions = new BuiltInReaderExceptions();
    }
}