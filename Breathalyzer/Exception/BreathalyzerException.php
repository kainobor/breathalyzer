<?php

namespace Breathalyzer\Exception;

/**
 * Class BreathalyzerException
 * @package Breathalyzer\Exception
 */
class BreathalyzerException extends \Exception
{
    /**
     * Return exception for wrong vocabulary path
     *
     * @param $path
     * @return BreathalyzerException
     */
    public static function wrongVocabularyPath($path)
    {
        return new self("Can't find vocabulary by path: $path");
    }

    /**
     * Return exception for wrong vocabulary path
     *
     * @param $path
     * @return BreathalyzerException
     */
    public static function wrongTestingFilePath($path)
    {
        return new self("Can't find testing file by path: $path");
    }
} 