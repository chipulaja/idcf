<?php

namespace IDCF;

class Preprocessing
{
    /**
     * @param string $sentence
     * @param array|null $stopWords
     * @return array
     */
    public function execute(string $sentence, $stopWords = [])
    {
        $sentence = $this->convertToLowerCase($sentence);
        $sentence = $this->removeSpesialChar($sentence);

        $stopWords = (empty($stopWords)) ? $this->getStopWords() : $stopWords;
        $sentence = $this->removeWordsByStopWords($sentence, $stopWords);

        $stemmerFactory = new \Sastrawi\Stemmer\StemmerFactory();
        $stemmer  = $stemmerFactory->createStemmer();
        $sentence = $stemmer->stem($sentence);

        $tokenization = $sentence;
        $tokenization = $this->tokenization($sentence);

        return $tokenization;
    }

    /**
     * @param string $sentence
     * @return string
     */
    protected function convertToLowerCase(string $sentence)
    {
        return strtolower($sentence);
    }

    /**
     * @param string $sentence
     * @return string
     */
    protected function removeSpesialChar(string $sentence)
    {
        return preg_replace(
            '/\s+|\n+|\r+/',
            ' ',
            preg_replace('/[^a-z \n\r\t]/', '', $sentence)
        );
    }

    /**
     * @return array
     */
    protected function getStopWords()
    {
        $stopWordsFile = dirname(__FILE__)."/../../data/stopwordbahasa.csv";
        return file($stopWordsFile, FILE_IGNORE_NEW_LINES);
    }

    /**
     * @param string $sentence
     * @param array $stopWords
     * @return string
     */
    protected function removeWordsByStopWords($sentence, $stopWords)
    {
        foreach ($stopWords as &$word) {
            $word = '/\b' . preg_quote($word, '/') . '\b/';
        }

        return preg_replace(
            '/\s+/',
            ' ',
            preg_replace($stopWords, '', $sentence)
        );
    }

    /**
     * @param string $sentence
     * @return array
     */
    protected function tokenization($sentence)
    {
        $words = explode(" ", $sentence);
        $token = [];
        foreach ($words as $word) {
            if (isset($token[$word])) {
                $token[$word] += 1;
            } else {
                $token[$word] = 1;
            }
        }
        return $token;
    }
}
