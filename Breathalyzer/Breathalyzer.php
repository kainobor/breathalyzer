<?php

namespace Breathalyzer;

use Breathalyzer\Exception\BreathalyzerException;


/**
 * Class Breathalyzer
 * @package Breathalyzer\Breathalyzer
 */
class Breathalyzer
{
    /**
     * @const Smallest possible distance between two different strings
     */
    const SMALLEST_DISTANCE = 1;

    /**
     * @const Constant for relative path of vocabulary file
     */
    const VOCABULARY_PATH = 'Breathalyzer/src/vocabulary.txt';

    /**
     * @var string[] $vocabulary
     */
    private $vocabulary = [];

    /**
     * @var string[] $testingStringArray
     */
    private $testingStringArray = [];

    /**
     * @param string $testingFilePath
     * @throws BreathalyzerException
     */
    public function __construct($testingFilePath = '')
    {
        $this->createVocabulary();
        $this->createTestingStringArray($testingFilePath);
    }

    /**
     * Get smaller levenshtein distance between word and all another words from vocabulary
     *
     * @param string $word
     * @param string[] $vocabulary
     * @return int
     */
    public function getSmallerDistanceForWord($word = '', $vocabulary = [])
    {
        $smallerDistance = 0;

        foreach ($vocabulary as $vocabularyWord => $index) {
            $distance = levenshtein($vocabularyWord, $word);

            if ($distance < $smallerDistance || $smallerDistance == 0) {
                $smallerDistance = $distance;
            }

            if ($smallerDistance == self::SMALLEST_DISTANCE) {
                break;
            }
        }

        return $smallerDistance;
    }

    /**
     * Get smaller levenshtein distances for whole string
     *
     * @return int
     */
    public function getSmallerDistanceForString()
    {
        $smallerDistanceForString = 0;

        foreach ($this->testingStringArray as $stringWord) {
            $firstLetter = $stringWord[0];
            $currentDistance = 0;

            // Check, may be current word exists in vocabulary
            if (isset($this->vocabulary[$firstLetter][$stringWord])) {
                continue;
            }

            // First, check words with similar first letter
            if (isset($this->vocabulary[$firstLetter])) {
                $currentDistance = $this->getSmallerDistanceForWord($stringWord, $this->vocabulary[$firstLetter]);
            }

            // Than, check another words, if we haven't found smaller distance above zero
            if ($currentDistance == self::SMALLEST_DISTANCE) {
                $smallerDistanceForString += $currentDistance;
                continue;
            }

            foreach ($this->vocabulary as $letter => $wordsByLetter) {
                if ($letter == $firstLetter) {
                    continue;
                }

                $smallerDistanceForLetter = $this->getSmallerDistanceForWord($stringWord, $wordsByLetter);

                if ($currentDistance > $smallerDistanceForLetter || $currentDistance == 0) {
                    $currentDistance = $smallerDistanceForLetter;
                }
            }

            $smallerDistanceForString += $currentDistance;
        }

        return $smallerDistanceForString;
    }

    /**
     * Create our vocabulary, divided by letter
     *
     * @throws BreathalyzerException
     */
    private function createVocabulary()
    {
        $fullPath = $this->getFullPath(self::VOCABULARY_PATH);
        if (!file_exists($fullPath)) {
            throw BreathalyzerException::wrongVocabularyPath($fullPath);
        }

        $vocabularyHandle = fopen($fullPath, 'rb');
        $vocabularyData = trim(fread($vocabularyHandle, filesize($fullPath)));
        $vocabularyDataArray = explode("\n", $vocabularyData);
        fclose($vocabularyHandle);

        foreach ($vocabularyDataArray as $vocabularyWord) {
            $this->vocabulary[$vocabularyWord[0]][$vocabularyWord] = $vocabularyWord;
        }
    }

    /**
     * @param string $testingFilePath
     * @throws BreathalyzerException
     */
    private function createTestingStringArray($testingFilePath = '')
    {
        $fullPath = $this->getFullPath($testingFilePath);
        if (!file_exists($fullPath)) {
            throw BreathalyzerException::wrongTestingFilePath($fullPath);
        }

        $testingStringData = trim(strtoupper(file_get_contents($fullPath, 'r')));
        $this->testingStringArray = explode(' ', $testingStringData);
    }

    /**
     * Return full path of file by relative path, started from parent of this class
     *
     * @param string $relativePath
     * @return string
     */
    private function getFullPath($relativePath = '')
    {
        return __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . $relativePath;
    }
}
