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
    public function execute(array $token, $category = "", $docName = "", $weight = [], $categoryList = [])
    {
        $weight = $this->addNewTokenToWeight($token, $weight);
        $weight = $this->termFrequencyCounter($token, $weight, $docName);
        $weight = $this->tfIdfCounter($weight);

        $categoryList = $this->addCategory($category, $docName, $categoryList);
        $weight = $this->tfIdfIcfCounter($weight, $categoryList);
        $weight = $this->tdcfIdcfCounter($weight, $categoryList);

        return ['weight' => $weight, 'category' => $categoryList];
    }

    /**
     * @param array $words
     * @param array $weight
     * @return array
     */
    protected function addNewTokenToWeight($token, $weight)
    {
        foreach ($token as $word => $count) {
            if (! isset($weight[$word])) {
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
                    if (! isset($weight[$word]["tf"][$dm])) {
                        $weight[$word]["tf"][$dm] = 0;
                    }
                }
            }
        }

        return $weight;
    }

    /**
     * @param array $weight
     * @return array
     */
    protected function tfIdfCounter($weight)
    {
        $idf = [];
        foreach ($weight as $word => $data) {
            $lenDoc = sizeof($data["tf"]);
            $docNoTerm = array_count_values($data["tf"]);
            $docNoTerm = isset($docNoTerm[0]) ? $docNoTerm[0] : 0;
            $df = $lenDoc - $docNoTerm;
            $idf[$word] = 1 + log10($lenDoc / $df);
        }

        foreach ($weight as $word => $data) {
            foreach ($data["tf"] as $docName => $tf) {
                $weight[$word]["tf.idf"][$docName] = $tf * $idf[$word];
            }
        }

        return $weight;
    }

    /**
     * @param string $category
     * @param string $docName
     * @param array $categoryList
     * @return array
     */
    protected function addCategory($category, $docName, $categoryList)
    {
        $temp = isset($categoryList[$category]) ? $categoryList[$category] : [];
        if (! in_array($docName, $temp) && $category != "") {
            $categoryList[$category][] = $docName;
        }
        return $categoryList;
    }

    /**
     * @param array $weight
     * @return array
     */
    protected function tfIdfIcfCounter($weight, $categoryList)
    {
        foreach ($weight as $word => $data) {
            foreach ($categoryList as $category => $docNameList) {
                $tmp = 0;
                foreach ($docNameList as $docName) {
                    if ($weight[$word]["tf"][$docName] > 0) {
                        $tmp = 1;
                        break;
                    }
                }
                $weight[$word]["c"]["$category"] = $tmp;
            }
        }

        $icf = [];
        foreach ($weight as $word => $data) {
            $lenCat = sizeof($data["c"]);
            $catNoTerm = array_count_values($data["c"]);
            $catNoTerm = isset($catNoTerm[0]) ? $catNoTerm[0] : 0;
            $cf = $lenCat - $catNoTerm;
            $icf[$word] = 1 + log10($lenCat / $cf);
        }

        foreach ($weight as $word => $data) {
            foreach ($data["tf"] as $docName => $tf) {
                $tfIdf = $weight[$word]["tf.idf"][$docName];
                $weight[$word]["tf.idf.icf"][$docName] = $tfIdf * $icf[$word];
            }
        }

        return $weight;
    }

    /**
     * @param array $weight
     * @return array
     */
    protected function tdcfIdcfCounter($weight, $categoryList)
    {
        foreach ($weight as $word => $data) {
            foreach ($categoryList as $category => $docNameList) {
                $dc   = sizeof($docNameList);
                $dcf  = 0;
                $tdcf = 0;
                foreach ($docNameList as $docName) {
                    if ($weight[$word]["tf"][$docName] > 0) {
                        $dcf += 1;
                    }
                    $tdcf += $weight[$word]["tf"][$docName];
                }

                $idcf = (float)0;
                if ($dcf != 0) {
                    $idcf = 1 + log10($dc / $dcf);
                }
                $weight[$word]["tdcf.idcf"]["$category"] = $tdcf * $idcf;
            }
        }

        return $weight;
    }
}
