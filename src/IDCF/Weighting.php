<?php

namespace IDCF;

use IDCF\Preprocessing;
use IDCF\Df;
use IDCF\Idf;
use IDCF\TfIdf;
use IDCF\C;
use IDCF\Cfdetail;
use IDCF\Icf;
use IDCF\TfIdfIcf;
use IDCF\Tdcf;
use IDCF\Dcf;
use IDCF\Idcf;

class Weighting
{
    protected $tempWords = [];

    /**
     * @param array $token
     * @param string|null $category
     * @param string|null $docName
     * @param  array|null $weight
     * @param  array|null $categoryList
     * @return array
     */
    public function execute(array $token, $class = "", $docname = "", $weight = [], $classList = [])
    {
        $classList = $this->addNewClass($class, $docname, $classList);
        $weight["wordList"] = $this->addTokenToWordList($token, @$weight["wordList"]);
        $weight["docnameList"] = $this->addDocNameToDocNameList($docname, @$weight["docnameList"]);
        $weight["docs"][$docname]["tf"] = $token;

        $weight["df"] = (new Df())->execute($token, @$weight["df"]);

        $weight["c"][$class] = (new C())->execute($token, @$weight["c"][$class]);
        $weight["cfdetail"] = (new Cfdetail())->execute($class, $weight["c"][$class], @$weight["cfdetail"]);

        foreach ($weight["docs"] as $_docname => $data) {
            $weight["idf"] = (new Idf())->execute($data["tf"], $weight["docnameList"], $weight["df"], @$weight["idf"]);
            $weight["docs"][$_docname]["tf.idf"] = (new TfIdf())->execute($data["tf"], $weight["idf"]);
            $weight["icf"] = (new Icf())->execute($data["tf"], $classList, $weight["cfdetail"], @$weight["icf"]);
            $weight["docs"][$_docname]["tf.idf.icf"] = (new TfIdfIcf())->execute(
                $data["tf"],
                $weight["idf"],
                $weight["icf"]
            );
        }

        $weight["class"][$class]["tdcf"] = (new Tdcf())->execute($token, @$weight["class"][$class]["tdcf"]);

        if (in_array($docname, $classList[$class])) {
            $weight["class"][$class]["dcf"] = (new Dcf())->execute($token, @$weight["class"][$class]["dcf"]);
        }

        foreach ($weight["class"] as $_class => $data) {
            $weight["class"][$_class]["idcf"] = (new Idcf())->execute(
                $data["tdcf"],
                $classList[$_class],
                $weight["class"][$_class]["dcf"],
                @$weight["class"][$_class]["idcf"]
            );

            $weight["class"][$_class]["tdcf.idcf"] = (new Tdcfidcf())->execute(
                $data["tdcf"],
                $weight["class"][$_class]["idcf"],
                @$weight["class"][$_class]["tdcf.idcf"]
            );
        }

        // $weight = $this->tfIdfIcfCounter($weight, $categoryList);
        // $weight = $this->tdcfIdcfCounter($weight, $categoryList);

        return [
            'weight' => $weight,
            'classList' => $classList
        ];
    }

    /**
     * @param string $category
     * @param string $docName
     * @param array $categoryList
     * @return array
     */
    protected function addNewClass($class, $docName, $classList)
    {
        $temp = isset($classList[$class]) ? $classList[$class] : [];
        if (! in_array($docName, $temp) && $class != "") {
            $classList[$class][] = $docName;
        }
        return $classList;
    }

    /**
     * @param array $token
     * @param array $weight
     * @return array
     */
    protected function addTokenToWordList($token, $wordList = [])
    {
        $wordList = ($wordList !== null) ? $wordList : [];
        $temp = array_flip($wordList);
        foreach ($token as $word => $count) {
            if (! isset($temp[$word])) {
                $wordList[] = $word;
            }
        }
        return $wordList;
    }

    /**
     * @param string $docName
     * @param array $weight
     * @return array
     */
    protected function addDocNameToDocnameList($docName, $docList = [])
    {
        $docList = ($docList !== null) ? $docList : [];
        $temp = array_flip($docList);
        if (! isset($temp[$docName])) {
            $docList[] = $docName;
        }
        return $docList;
    }

    // /**
    //  * @param array $weight
    //  * @return array
    //  */
    // protected function tfIdfIcfCounter($weight, $categoryList)
    // {
    //     foreach ($weight as $word => $data) {
    //         foreach ($categoryList as $category => $docNameList) {
    //             $tmp = 0;
    //             foreach ($docNameList as $docName) {
    //                 if ($weight[$word]["tf"][$docName] > 0) {
    //                     $tmp = 1;
    //                     break;
    //                 }
    //             }
    //             $weight[$word]["c"]["$category"] = $tmp;
    //         }
    //     }

    //     $icf = [];
    //     foreach ($weight as $word => $data) {
    //         $lenCat = sizeof($data["c"]);
    //         $catNoTerm = array_count_values($data["c"]);
    //         $catNoTerm = isset($catNoTerm[0]) ? $catNoTerm[0] : 0;
    //         $cf = $lenCat - $catNoTerm;
    //         $icf[$word] = 1 + log10($lenCat / $cf);
    //     }

    //     foreach ($weight as $word => $data) {
    //         foreach ($data["tf"] as $docName => $tf) {
    //             $tfIdf = $weight[$word]["tf.idf"][$docName];
    //             $weight[$word]["tf.idf.icf"][$docName] = $tfIdf * $icf[$word];
    //         }
    //     }

    //     return $weight;
    // }

    // /**
    //  * @param array $weight
    //  * @return array
    //  */
    // protected function tdcfIdcfCounter($weight, $categoryList)
    // {
    //     foreach ($weight as $word => $data) {
    //         foreach ($categoryList as $category => $docNameList) {
    //             $dc   = sizeof($docNameList);
    //             $dcf  = 0;
    //             $tdcf = 0;
    //             foreach ($docNameList as $docName) {
    //                 if ($weight[$word]["tf"][$docName] > 0) {
    //                     $dcf += 1;
    //                 }
    //                 $tdcf += $weight[$word]["tf"][$docName];
    //             }

    //             $idcf = (float)0;
    //             if ($dcf != 0) {
    //                 $idcf = 1 + log10($dc / $dcf);
    //             }
    //             $weight[$word]["tdcf.idcf"]["$category"] = $tdcf * $idcf;
    //         }
    //     }

    //     return $weight;
    // }
}
