<?php
declare(strict_types=1);
namespace IDCFTest;

use PHPUnit\Framework\TestCase;

final class WeightingTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testExecute($sentence, $expected, $w, $docname, $class)
    {
        $preprocessing = new \IDCF\Preprocessing();
        $weighting = new \IDCF\Weighting();
        $tokens = $preprocessing->execute($sentence);
        $return = $weighting->execute($tokens, $class, $docname, $w["weight"], $w["classList"]);

        $this->assertEquals($expected["weight"]["df"], $return["weight"]["df"]);
        $this->assertEquals($expected["weight"]["idf"], $return["weight"]["idf"]);
        $this->assertEquals($expected["weight"]["c"], $return["weight"]["c"]);
        $this->assertEquals($expected["weight"]["cfdetail"], $return["weight"]["cfdetail"]);
        $this->assertEquals($expected["weight"]["icf"], $return["weight"]["icf"]);
        $this->assertEquals($expected["weight"]["docs"], $return["weight"]["docs"]);
        $this->assertEquals($expected["weight"]["class"], $return["weight"]["class"]);
        $this->assertEquals($expected["weight"]["wordList"], $return["weight"]["wordList"]);
        $this->assertEquals($expected["classList"], $return["classList"]);
    }

    /**
     * @return array
     */
    public function additionProvider()
    {
        $d1 = $this->getD1();
        $d2 = $this->getD2();
        $d3 = $this->getD3();
        $d4 = $this->getD4();
        $d5 = $this->getD5();

        return [
            [$d1["sentence"], $d1["expected"], $d1["weighting"], $d1["docname"], $d1["class"]],
            [$d2["sentence"], $d2["expected"], $d2["weighting"], $d2["docname"], $d2["class"]],
            [$d3["sentence"], $d3["expected"], $d3["weighting"], $d3["docname"], $d3["class"]],
            [$d4["sentence"], $d4["expected"], $d4["weighting"], $d4["docname"], $d4["class"]],
            [$d5["sentence"], $d5["expected"], $d5["weighting"], $d5["docname"], $d5["class"]],
        ];
    }

    /**
     * @return array
     */
    protected function getD1()
    {
        $sentence  = 'Sholat wajib dalam sehari terdiri dari 5 waktu yaitu sholat dzuhur, ';
        $sentence .= 'sholat ashar, sholat subuh, sholat maghrib dan sholat isya';
        $weighting['weight'] = [];
        $weighting['classList'] = [];

        $expectedWeight  = <<<DOC
| words   | tf - d1 |   df   |   idf   | tf.idf - d1 | c - sholat |  cfdetail  |  icf  | tf.idf.icf - d1 | tdcf - sholat | dcf - sholat | idcf - sholat | tdcf.idcf - sholat |
| sholat  |   6     |   1    |    1    |    6.0      |    1       |   sholat;  |  1.0  | 6.0             |      6        |      1       | 1             | 6.0                |
| wajib   |   1     |   1    |    1    |    1.0      |    1       |   sholat;  |  1.0  | 1.0             |      1        |      1       | 1             | 1.0                |
| hari    |   1     |   1    |    1    |    1.0      |    1       |   sholat;  |  1.0  | 1.0             |      1        |      1       | 1             | 1.0                |
| dzuhur  |   1     |   1    |    1    |    1.0      |    1       |   sholat;  |  1.0  | 1.0             |      1        |      1       | 1             | 1.0                |
| ashar   |   1     |   1    |    1    |    1.0      |    1       |   sholat;  |  1.0  | 1.0             |      1        |      1       | 1             | 1.0                |
| subuh   |   1     |   1    |    1    |    1.0      |    1       |   sholat;  |  1.0  | 1.0             |      1        |      1       | 1             | 1.0                |
| maghrib |   1     |   1    |    1    |    1.0      |    1       |   sholat;  |  1.0  | 1.0             |      1        |      1       | 1             | 1.0                |
| isya    |   1     |   1    |    1    |    1.0      |    1       |   sholat;  |  1.0  | 1.0             |      1        |      1       | 1             | 1.0                |
DOC;

        $expectedClassList  = <<<DOC
| sholat |
| d1     |
DOC;
        $docname = "d1";
        $data = [
            "sentence" => $sentence,
            "expected" => [
                'weight' => $this->stringTableToArray($expectedWeight, $docname),
                'classList' => $this->stringTableToArray($expectedClassList)
            ],
            "weighting"   => $weighting,
            "docname"  => $docname,
            "class" => "sholat"
        ];

        $data["expected"]["weight"]["docnameList"] = ["d1"];

        return $data;
    }

    /**
     * @return array
     */
    protected function getD2()
    {
        $sentence  = 'Sholat sunnah rowatib merupakan sholat sunnah yang dilakukan ';
        $sentence .= 'sebelum dan sesudah sholat wajib';
        $expectedWeight  = <<<DOC
| words   | tf - d1 | tf - d2 |  df   |         idf        |    tf.idf - d1     |     tf.idf - d2    | c - sholat |  cfdetail  |  icf  | tf.idf.icf - d1    | tf.idf.icf - d2    | tdcf - sholat | dcf - sholat | idcf - sholat      | tdcf.idcf - sholat |
| sholat  |   6     |   3     |  2    | 1.0                | 6.0                | 3.0                |     1      |   sholat;  |  1.0  | 6.0                | 3.0                |       9       |      2       | 1.0                | 9.0                |
| wajib   |   1     |   1     |  2    | 1.0                | 1.0                | 1.0                |     1      |   sholat;  |  1.0  | 1.0                | 1.0                |       2       |      2       | 1.0                | 2.0                |
| hari    |   1     |         |  1    | 1.3010299956639813 | 1.3010299956639813 |                    |     1      |   sholat;  |  1.0  | 1.3010299956639813 |                    |       1       |      1       | 1.3010299956639813 | 1.3010299956639813 |
| dzuhur  |   1     |         |  1    | 1.3010299956639813 | 1.3010299956639813 |                    |     1      |   sholat;  |  1.0  | 1.3010299956639813 |                    |       1       |      1       | 1.3010299956639813 | 1.3010299956639813 |
| ashar   |   1     |         |  1    | 1.3010299956639813 | 1.3010299956639813 |                    |     1      |   sholat;  |  1.0  | 1.3010299956639813 |                    |       1       |      1       | 1.3010299956639813 | 1.3010299956639813 |
| subuh   |   1     |         |  1    | 1.3010299956639813 | 1.3010299956639813 |                    |     1      |   sholat;  |  1.0  | 1.3010299956639813 |                    |       1       |      1       | 1.3010299956639813 | 1.3010299956639813 |
| maghrib |   1     |         |  1    | 1.3010299956639813 | 1.3010299956639813 |                    |     1      |   sholat;  |  1.0  | 1.3010299956639813 |                    |       1       |      1       | 1.3010299956639813 | 1.3010299956639813 |
| isya    |   1     |         |  1    | 1.3010299956639813 | 1.3010299956639813 |                    |     1      |   sholat;  |  1.0  | 1.3010299956639813 |                    |       1       |      1       | 1.3010299956639813 | 1.3010299956639813 |
| sunnah  |         |   2     |  1    | 1.3010299956639813 |                    | 2.6020599913279625 |     1      |   sholat;  |  1.0  |                    | 2.6020599913279625 |       2       |      1       | 1.3010299956639813 | 2.6020599913279625 |
| rowatib |         |   1     |  1    | 1.3010299956639813 |                    | 1.3010299956639813 |     1      |   sholat;  |  1.0  |                    | 1.3010299956639813 |       1       |      1       | 1.3010299956639813 | 1.3010299956639813 |
DOC;

        $expectedClassList  = <<<DOC
| sholat |
| d1     |
| d2     |
DOC;

        $docname = "d2";
        $data =  [
            "sentence" => $sentence,
            "expected" => [
                'weight' => $this->stringTableToArray($expectedWeight, $docname),
                'classList' => $this->stringTableToArray($expectedClassList)
            ],
            "weighting"   => ($this->getD1()["expected"]),
            "docname"  => $docname,
            "class" => "sholat"
        ];

        $data["expected"]["weight"]["docnameList"] = ["d1", "d2"];
        return $data;
    }

    /**
     * @return array
     */
    protected function getD3()
    {
        $sentence = "Sebelum melakukan sholat yang wajib dilakukan adalah bersuci yang biasa disebut wudlu";

        $expectedWeight =<<<DOC
| words   | tf - d1 | tf - d2 | tf - d3 |  df   |         idf        | tf.idf - d1        | tf.idf - d2        | tf.idf - d3        | c - sholat | c - thoharoh |   cfdetail          |        icf          | tf.idf.icf - d1    | tf.idf.icf - d2    | tf.idf.icf - d3    | tdcf - sholat | tdcf - thoharoh | dcf - sholat |  dcf - thoharoh | idcf - sholat      | idcf - thoharoh    | tdcf.idcf - sholat | tdcf.idcf - thoharoh |
| sholat  |   6     |   3     |   1     |  3    | 1.0                | 6.0                | 3.0                | 1.0                |     1      |     1        |   sholat;thoharoh;  | 1.0                 | 6.0                | 3.0                | 1.0                |       9       |        1        |      2       |        1        | 1.0                | 1.0                | 9.0                | 1.0                  |
| wajib   |   1     |   1     |   1     |  3    | 1.0                | 1.0                | 1.0                | 1.0                |     1      |     1        |   sholat;thoharoh;  | 1.0                 | 1.0                | 1.0                | 1.0                |       2       |        1        |      2       |        1        | 1.0                | 1.0                | 2.0                | 1.0                  |
| hari    |   1     |         |         |  1    | 1.4771212547196624 | 1.4771212547196624 |                    |                    |     1      |              |   sholat;           | 1.3010299956639813  | 1.9217790596230968 |                    |                    |       1       |                 |      1       |                 | 1.3010299956639813 |                    | 1.3010299956639813 |                      |
| dzuhur  |   1     |         |         |  1    | 1.4771212547196624 | 1.4771212547196624 |                    |                    |     1      |              |   sholat;           | 1.3010299956639813  | 1.9217790596230968 |                    |                    |       1       |                 |      1       |                 | 1.3010299956639813 |                    | 1.3010299956639813 |                      |
| ashar   |   1     |         |         |  1    | 1.4771212547196624 | 1.4771212547196624 |                    |                    |     1      |              |   sholat;           | 1.3010299956639813  | 1.9217790596230968 |                    |                    |       1       |                 |      1       |                 | 1.3010299956639813 |                    | 1.3010299956639813 |                      |
| subuh   |   1     |         |         |  1    | 1.4771212547196624 | 1.4771212547196624 |                    |                    |     1      |              |   sholat;           | 1.3010299956639813  | 1.9217790596230968 |                    |                    |       1       |                 |      1       |                 | 1.3010299956639813 |                    | 1.3010299956639813 |                      |
| maghrib |   1     |         |         |  1    | 1.4771212547196624 | 1.4771212547196624 |                    |                    |     1      |              |   sholat;           | 1.3010299956639813  | 1.9217790596230968 |                    |                    |       1       |                 |      1       |                 | 1.3010299956639813 |                    | 1.3010299956639813 |                      |
| isya    |   1     |         |         |  1    | 1.4771212547196624 | 1.4771212547196624 |                    |                    |     1      |              |   sholat;           | 1.3010299956639813  | 1.9217790596230968 |                    |                    |       1       |                 |      1       |                 | 1.3010299956639813 |                    | 1.3010299956639813 |                      |
| sunnah  |         |   2     |         |  1    | 1.4771212547196624 |                    | 2.9542425094393248 |                    |     1      |              |   sholat;           | 1.3010299956639813  |                    | 3.8435581192461936 |                    |       2       |                 |      1       |                 | 1.3010299956639813 |                    | 2.6020599913279625 |                      |
| rowatib |         |   1     |         |  1    | 1.4771212547196624 |                    | 1.4771212547196624 |                    |     1      |              |   sholat;           | 1.3010299956639813  |                    | 1.9217790596230968 |                    |       1       |                 |      1       |                 | 1.3010299956639813 |                    | 1.3010299956639813 |                      |
| suci    |         |         |   1     |  1    | 1.4771212547196624 |                    |                    | 1.4771212547196624 |            |     1        |   thoharoh;         | 1.3010299956639813  |                    |                    | 1.9217790596230968 |               |        1        |              |        1        |                    | 1.0                |                    | 1.0                  |
| wudlu   |         |         |   1     |  1    | 1.4771212547196624 |                    |                    | 1.4771212547196624 |            |     1        |   thoharoh;         | 1.3010299956639813  |                    |                    | 1.9217790596230968 |               |        1        |              |        1        |                    | 1.0                |                    | 1.0                  |
DOC;

        $expectedClassList  = <<<DOC
| sholat | thoharoh |
| d1     | d3       |
| d2     |          |
DOC;
        $docname = "d3";
        $data = [
            "sentence" => $sentence,
            "expected" => [
                'weight' => $this->stringTableToArray($expectedWeight, $docname),
                'classList' => $this->stringTableToArray($expectedClassList)
            ],
            "weighting"   => ($this->getD2()["expected"]),
            "docname"  => $docname,
            "class" => "thoharoh"
        ];

        $data["expected"]["weight"]["docnameList"] = ["d1", "d2", "d3"];
        return $data;
    }

    /**
     * @return array
     */
    protected function getD4()
    {
        $sentence = "sholat jenazah merupakan sholat yang dilakukan setelah jenazah selesai dimandikan";

    $expectedWeight = <<<DOC
| words   | tf - d1 | tf - d2 | tf - d3 | tf - d4 |  df   |         idf        | tf.idf - d1        | tf.idf - d2        | tf.idf - d3        | tf.idf - d4        |c - sholat | c - thoharoh | c - jenazah |  cfdetail                   |         icf        | tf.idf.icf - d1    | tf.idf.icf - d2    | tf.idf.icf - d3    | tf.idf.icf - d4    | tdcf - sholat | tdcf - thoharoh | tdcf - jenazah |  dcf - sholat |  dcf - thoharoh |  dcf - jenazah | idcf - sholat      | idcf - thoharoh    | idcf - jenazah | tdcf.idcf - sholat | tdcf.idcf - thoharoh | tdcf.idcf - jenazah |
| sholat  |   6     |   3     |   1     |   2     |  4    | 1.0                | 6.0                | 3.0                | 1.0                | 2.0                |    1      |      1       |     1       |   sholat;thoharoh;jenazah;  | 1.0                | 6.0                | 3.0                | 1.0                | 2.0                |       9       |       1         |       2        |      2        |       1         |        1       | 1.0                | 1.0                | 1.0            | 9.0                | 1.0                  | 2.0                 |
| wajib   |   1     |   1     |   1     |         |  3    | 1.1249387366083    | 1.1249387366083    | 1.1249387366083    | 1.1249387366083    |                    |    1      |      1       |             |   sholat;thoharoh;          | 1.1760912590556813 | 1.323030615098163  | 1.323030615098163  | 1.323030615098163  |                    |       2       |       1         |                |      2        |       1         |                | 1.0                | 1.0                |                | 2.0                | 1.0                  |                     |
| hari    |   1     |         |         |         |  1    | 1.6020599913279625 | 1.6020599913279625 |                    |                    |                    |    1      |              |             |   sholat;                   | 1.4771212547196624 | 2.3664368645265315 |                    |                    |                    |       1       |                 |                |      1        |                 |                | 1.3010299956639813 |                    |                | 1.3010299956639813 |                      |                     |
| dzuhur  |   1     |         |         |         |  1    | 1.6020599913279625 | 1.6020599913279625 |                    |                    |                    |    1      |              |             |   sholat;                   | 1.4771212547196624 | 2.3664368645265315 |                    |                    |                    |       1       |                 |                |      1        |                 |                | 1.3010299956639813 |                    |                | 1.3010299956639813 |                      |                     |
| ashar   |   1     |         |         |         |  1    | 1.6020599913279625 | 1.6020599913279625 |                    |                    |                    |    1      |              |             |   sholat;                   | 1.4771212547196624 | 2.3664368645265315 |                    |                    |                    |       1       |                 |                |      1        |                 |                | 1.3010299956639813 |                    |                | 1.3010299956639813 |                      |                     |
| subuh   |   1     |         |         |         |  1    | 1.6020599913279625 | 1.6020599913279625 |                    |                    |                    |    1      |              |             |   sholat;                   | 1.4771212547196624 | 2.3664368645265315 |                    |                    |                    |       1       |                 |                |      1        |                 |                | 1.3010299956639813 |                    |                | 1.3010299956639813 |                      |                     |
| maghrib |   1     |         |         |         |  1    | 1.6020599913279625 | 1.6020599913279625 |                    |                    |                    |    1      |              |             |   sholat;                   | 1.4771212547196624 | 2.3664368645265315 |                    |                    |                    |       1       |                 |                |      1        |                 |                | 1.3010299956639813 |                    |                | 1.3010299956639813 |                      |                     |
| isya    |   1     |         |         |         |  1    | 1.6020599913279625 | 1.6020599913279625 |                    |                    |                    |    1      |              |             |   sholat;                   | 1.4771212547196624 | 2.3664368645265315 |                    |                    |                    |       1       |                 |                |      1        |                 |                | 1.3010299956639813 |                    |                | 1.3010299956639813 |                      |                     |
| sunnah  |         |   2     |         |         |  1    | 1.6020599913279625 |                    | 3.204119982655925  |                    |                    |    1      |              |             |   sholat;                   | 1.4771212547196624 |                    | 4.732873729053063  |                    |                    |       2       |                 |                |      1        |                 |                | 1.3010299956639813 |                    |                | 2.6020599913279625 |                      |                     |
| rowatib |         |   1     |         |         |  1    | 1.6020599913279625 |                    | 1.6020599913279625 |                    |                    |    1      |              |             |   sholat;                   | 1.4771212547196624 |                    | 2.3664368645265315 |                    |                    |       1       |                 |                |      1        |                 |                | 1.3010299956639813 |                    |                | 1.3010299956639813 |                      |                     |
| suci    |         |         |   1     |         |  1    | 1.6020599913279625 |                    |                    | 1.6020599913279625 |                    |           |      1       |             |   thoharoh;                 | 1.4771212547196624 |                    |                    | 2.3664368645265315 |                    |               |       1         |                |               |       1         |                |                    | 1.0                |                |                    | 1.0                  |                     |
| wudlu   |         |         |   1     |         |  1    | 1.6020599913279625 |                    |                    | 1.6020599913279625 |                    |           |      1       |             |   thoharoh;                 | 1.4771212547196624 |                    |                    | 2.3664368645265315 |                    |               |       1         |                |               |       1         |                |                    | 1.0                |                |                    | 1.0                  |                     |
| jenazah |         |         |         |   2     |  1    | 1.6020599913279625 |                    |                    |                    | 3.204119982655925  |           |              |     1       |   jenazah;                  | 1.4771212547196624 |                    |                    |                    | 4.732873729053063  |               |                 |       2        |               |                 |       1        |                    |                    | 1.0            |                    |                      | 2.0                 |
| selesai |         |         |         |   1     |  1    | 1.6020599913279625 |                    |                    |                    | 1.6020599913279625 |           |              |     1       |   jenazah;                  | 1.4771212547196624 |                    |                    |                    | 2.3664368645265315 |               |                 |       1        |               |                 |       1        |                    |                    | 1.0            |                    |                      | 1.0                 |
| mandi   |         |         |         |   1     |  1    | 1.6020599913279625 |                    |                    |                    | 1.6020599913279625 |           |              |     1       |   jenazah;                  | 1.4771212547196624 |                    |                    |                    | 2.3664368645265315 |               |                 |       1        |               |                 |       1        |                    |                    | 1.0            |                    |                      | 1.0                 |
DOC;

        $expectedClassList  = <<<DOC
| sholat | thoharoh | jenazah |
| d1     | d3       | d4      |
| d2     |          |         |
DOC;

        $docname = "d4";
        $data =  [
            "sentence" => $sentence,
            "expected" => [
                'weight' => $this->stringTableToArray($expectedWeight, $docname),
                'classList' => $this->stringTableToArray($expectedClassList)
            ],
            "weighting"   => ($this->getD3()["expected"]),
            "docname"  => $docname,
            "class" => "jenazah"
        ];

        $data["expected"]["weight"]["docnameList"] = ["d1", "d2", "d3", "d4"];
        return $data;
    }


    /**
     * @return array
     */
    protected function getD5()
    {
        $sentence = "orang yang berhak memandikan jenazah adalah muhrimnya";

        $expectedWeight = <<<DOC
| words   | tf - d1 | tf - d2 | tf - d3 | tf - d4 | tf - d5 |  df   |         idf        | tf.idf - d1        | tf.idf - d2        | tf.idf - d3        | tf.idf - d4        | tf.idf - d5        |c - sholat | c - thoharoh | c - jenazah |  cfdetail                  |         icf        | tf.idf.icf - d1    | tf.idf.icf - d2    | tf.idf.icf - d3    | tf.idf.icf - d4    | tf.idf.icf - d5    | tdcf - sholat | tdcf - thoharoh | tdcf - jenazah | dcf - sholat  |  dcf - thoharoh |  dcf - jenazah | idcf - sholat      | idcf - thoharoh    | idcf - jenazah     | tdcf.idcf - sholat | tdcf.idcf - thoharoh | tdcf.idcf - jenazah |
| sholat  |   6     |   3     |   1     |   2     |         |  4    | 1.0969100130080565 | 6.581460078048339  | 3.2907300390241696 | 1.0969100130080565 | 2.193820026016113  |                    |    1      |      1       |     1       |   sholat;thoharoh;jenazah; | 1.0                | 6.581460078048339  | 3.2907300390241696 | 1.0969100130080565 | 2.193820026016113  |                    |       9       |       1         |       2        |       2       |      1          |       1        | 1.0                | 1.0                | 1.3010299956639813 | 9.0                | 1.0                  | 2.6020599913279625  |
| wajib   |   1     |   1     |   1     |         |         |  3    | 1.2218487496163564 | 1.2218487496163564 | 1.2218487496163564 | 1.2218487496163564 |                    |                    |    1      |      1       |             |   sholat;thoharoh;         | 1.1760912590556813 | 1.4370056343119104 | 1.4370056343119104 | 1.4370056343119104 |                    |                    |       2       |       1         |                |       2       |      1          |                | 1.0                | 1.0                |                    | 2.0                | 1.0                  |                     |
| hari    |   1     |         |         |         |         |  1    | 1.6989700043360187 | 1.6989700043360187 |                    |                    |                    |                    |    1      |              |             |   sholat;                  | 1.4771212547196624 | 2.5095847045358903 |                    |                    |                    |                    |       1       |                 |                |       1       |                 |                | 1.3010299956639813 |                    |                    | 1.3010299956639813 |                      |                     |
| dzuhur  |   1     |         |         |         |         |  1    | 1.6989700043360187 | 1.6989700043360187 |                    |                    |                    |                    |    1      |              |             |   sholat;                  | 1.4771212547196624 | 2.5095847045358903 |                    |                    |                    |                    |       1       |                 |                |       1       |                 |                | 1.3010299956639813 |                    |                    | 1.3010299956639813 |                      |                     |
| ashar   |   1     |         |         |         |         |  1    | 1.6989700043360187 | 1.6989700043360187 |                    |                    |                    |                    |    1      |              |             |   sholat;                  | 1.4771212547196624 | 2.5095847045358903 |                    |                    |                    |                    |       1       |                 |                |       1       |                 |                | 1.3010299956639813 |                    |                    | 1.3010299956639813 |                      |                     |
| subuh   |   1     |         |         |         |         |  1    | 1.6989700043360187 | 1.6989700043360187 |                    |                    |                    |                    |    1      |              |             |   sholat;                  | 1.4771212547196624 | 2.5095847045358903 |                    |                    |                    |                    |       1       |                 |                |       1       |                 |                | 1.3010299956639813 |                    |                    | 1.3010299956639813 |                      |                     |
| maghrib |   1     |         |         |         |         |  1    | 1.6989700043360187 | 1.6989700043360187 |                    |                    |                    |                    |    1      |              |             |   sholat;                  | 1.4771212547196624 | 2.5095847045358903 |                    |                    |                    |                    |       1       |                 |                |       1       |                 |                | 1.3010299956639813 |                    |                    | 1.3010299956639813 |                      |                     |
| isya    |   1     |         |         |         |         |  1    | 1.6989700043360187 | 1.6989700043360187 |                    |                    |                    |                    |    1      |              |             |   sholat;                  | 1.4771212547196624 | 2.5095847045358903 |                    |                    |                    |                    |       1       |                 |                |       1       |                 |                | 1.3010299956639813 |                    |                    | 1.3010299956639813 |                      |                     |
| sunnah  |         |   2     |         |         |         |  1    | 1.6989700043360187 |                    | 3.3979400086720375 |                    |                    |                    |    1      |              |             |   sholat;                  | 1.4771212547196624 |                    | 5.019169409071781  |                    |                    |                    |       2       |                 |                |       1       |                 |                | 1.3010299956639813 |                    |                    | 2.6020599913279625 |                      |                     |
| rowatib |         |   1     |         |         |         |  1    | 1.6989700043360187 |                    | 1.6989700043360187 |                    |                    |                    |    1      |              |             |   sholat;                  | 1.4771212547196624 |                    | 2.5095847045358903 |                    |                    |                    |       1       |                 |                |       1       |                 |                | 1.3010299956639813 |                    |                    | 1.3010299956639813 |                      |                     |
| suci    |         |         |   1     |         |         |  1    | 1.6989700043360187 |                    |                    | 1.6989700043360187 |                    |                    |           |      1       |             |   thoharoh;                | 1.4771212547196624 |                    |                    | 2.5095847045358903 |                    |                    |               |       1         |                |               |      1          |                |                    | 1.0                |                    |                    | 1.0                  |                     |
| wudlu   |         |         |   1     |         |         |  1    | 1.6989700043360187 |                    |                    | 1.6989700043360187 |                    |                    |           |      1       |             |   thoharoh;                | 1.4771212547196624 |                    |                    | 2.5095847045358903 |                    |                    |               |       1         |                |               |      1          |                |                    | 1.0                |                    |                    | 1.0                  |                     |
| jenazah |         |         |         |   2     |   1     |  2    | 1.3979400086720375 |                    |                    |                    | 2.795880017344075  | 1.3979400086720375 |           |              |     1       |   jenazah;                 | 1.4771212547196624 |                    |                    |                    | 4.129853799264912  | 2.064926899632456  |               |                 |       3        |               |                 |       2        |                    |                    | 1.0                |                    |                      | 3.0                 |
| selesai |         |         |         |   1     |         |  1    | 1.6989700043360187 |                    |                    |                    | 1.6989700043360187 |                    |           |              |     1       |   jenazah;                 | 1.4771212547196624 |                    |                    |                    | 2.5095847045358903 |                    |               |                 |       1        |               |                 |       1        |                    |                    | 1.3010299956639813 |                    |                      | 1.3010299956639813  |
| mandi   |         |         |         |   1     |   1     |  2    | 1.3979400086720375 |                    |                    |                    | 1.3979400086720375 | 1.3979400086720375 |           |              |     1       |   jenazah;                 | 1.4771212547196624 |                    |                    |                    | 2.064926899632456  | 2.064926899632456  |               |                 |       2        |               |                 |       2        |                    |                    | 1.0                |                    |                      | 2.0                 |
| orang   |         |         |         |         |   1     |  1    | 1.6989700043360187 |                    |                    |                    |                    | 1.6989700043360187 |           |              |     1       |   jenazah;                 | 1.4771212547196624 |                    |                    |                    |                    | 2.5095847045358903 |               |                 |       1        |               |                 |       1        |                    |                    | 1.3010299956639813 |                    |                      | 1.3010299956639813  |
| hak     |         |         |         |         |   1     |  1    | 1.6989700043360187 |                    |                    |                    |                    | 1.6989700043360187 |           |              |     1       |   jenazah;                 | 1.4771212547196624 |                    |                    |                    |                    | 2.5095847045358903 |               |                 |       1        |               |                 |       1        |                    |                    | 1.3010299956639813 |                    |                      | 1.3010299956639813  |
| muhrim  |         |         |         |         |   1     |  1    | 1.6989700043360187 |                    |                    |                    |                    | 1.6989700043360187 |           |              |     1       |   jenazah;                 | 1.4771212547196624 |                    |                    |                    |                    | 2.5095847045358903 |               |                 |       1        |               |                 |       1        |                    |                    | 1.3010299956639813 |                    |                      | 1.3010299956639813  |
DOC;

        $expectedClassList  = <<<DOC
| sholat | thoharoh | jenazah |
| d1     | d3       | d4      |
| d2     |          | d5      |
DOC;

        $docname = "d5";
        $data =  [
            "sentence" => $sentence,
            "expected" => [
                'weight' => $this->stringTableToArray($expectedWeight, $docname),
                'classList' => $this->stringTableToArray($expectedClassList)
            ],
            "weighting"   => ($this->getD4()["expected"]),
            "docname"  => $docname,
            "class" => "jenazah"
        ];

        $data["expected"]["weight"]["docnameList"] = ["d1", "d2", "d3", "d4", "d5"];
        return $data;
    }

    /**
     * @param string $table
     * @return array
     */
    protected function stringTableToArray($table, $docname = "")
    {
        $head = [];
        $words = [];
        $arr  = [];
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $table) as $index => $line) {
            $data = explode("|", $line);
            for ($a = 1; $a<(sizeof($data)-1); $a++) {
                if (isset($data[$a]) && $index == 0) {
                    $head[$a] = trim($data[$a]);
                } elseif (isset($data[$a])) {
                    $h = explode("-", $head[$a]);
                    if (sizeof($h) == 1 && strtolower($h[0]) == 'words') {
                        $words[$index] = trim($data[$a]);
                    } elseif (sizeof($h) == 1) {
                        $d = trim($data[$a]);
                        if (is_numeric($d)) {
                            if (substr_count($d, '.') == 0) {
                                $d = (int)$d;
                            } elseif (substr_count($d, '.') == 1) {
                                $d = (float)$d;
                            }
                        }
                        if (isset($words[$index])) {
                            $d2 = (is_string($d)) ? explode(";", $d) : [$d];
                            if (sizeof($d2)>1) {
                                foreach ($d2 as $v) {
                                    if ($v != '') {
                                        $arr[trim($h[0])][$words[$index]][] = $v;
                                    }
                                }
                            } else {
                                $arr[trim($h[0])][$words[$index]] = $d;
                            }
                        } else {
                            if ($d != '') {
                                $arr[trim($h[0])][] = $d;
                            }
                        }
                    } else {
                        $d = trim($data[$a]);
                        if (is_numeric($d)) {
                            if (substr_count($d, '.') == 0) {
                                $d = (int)$d;
                            } elseif (substr_count($d, '.') == 1) {
                                $d = (float)$d;
                            }
                        }
                        if ($d !== "") {
                            if (trim($h[0]) == "c") {
                                $arr[trim($h[0])][trim($h[1])][$words[$index]] = $d;
                            } elseif(in_array(trim($h[0]),["tdcf", "dcf", "idcf", "tdcf.idcf"])) {
                                $arr["class"][trim($h[1])][trim($h[0])][$words[$index]] = $d;
                            } else {
                                $arr["docs"][trim($h[1])][trim($h[0])][$words[$index]] = $d;
                            }
                        }
                    }
                }
            }
        }
        if (!empty($words)) {
            $arr["wordList"] = array_values($words);
        }
        return $arr;
    }
}
