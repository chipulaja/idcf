<?php

namespace IDCF;

use IDCF\Preprocessing;

class Weighting
{

    protected $tempWords = [];

    /**
     * @param array $token
     * @param string|null $docName
     * @return array
     */
    public function execute(array $token, $weight = [], $docName = "")
    {
        $weight = $this->addNewTokenToWeight($token, $weight);
        $weight = $this->termFrequencyCounter($token, $weight, $docName);

        return $weight;
    }

    /**
     * @param array $words
     * @param array $weight
     * @return array
     */
    protected function addNewTokenToWeight($token, $weight)
    {
        foreach ($token as $word => $count) {
            if (!isset($weight[$word])) {
                $weight[$word] = [];
            }
        }
        return $weight;
    }

    /**
     * @param array $words
     * @param array $weight
     * @return array
     */
    protected function termFrequencyCounter($token, $weight, $docName)
    {
        foreach ($weight as $word => $w) {
            if (isset($token[$word])) {
                $weight[$word]["tf"][$docName] = $token[$word];
            } else {
                $weight[$word]["tf"][$docName] = 0;
            }
        }

        $firsWeight  = reset($weight);
        $listDocName = array_keys($firsWeight["tf"]);

        foreach ($listDocName as $dm) {
            if ($dm != $docName) {
                foreach ($weight as $word => $w) {
                    if (!isset($weight[$word]["tf"][$dm])) {
                        $weight[$word]["tf"][$dm] = 0;
                    }
                }
            }
        }

        return $weight;
    }
}
