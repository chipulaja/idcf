<?php
declare(strict_types=1);
namespace IDCFTest;

use PHPUnit\Framework\TestCase;

final class WeightingTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testExecute($sentence, $expected, $w, $docname, $category)
    {
        $preprocessing = new \IDCF\Preprocessing();
        $weighting = new \IDCF\Weighting();
        $tokens = $preprocessing->execute($sentence);
        $return = $weighting->execute($tokens, $category, $docname, $w["weight"], $w["category"]);

        $this->assertEquals($expected["weight"], $return["weight"]);
        $this->assertEquals($expected["category"], $return["category"]);
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
            [$d1["sentence"], $d1["expected"], $d1["weighting"], $d1["docname"], $d1["category"]],
            [$d2["sentence"], $d2["expected"], $d2["weighting"], $d2["docname"], $d2["category"]],
            [$d3["sentence"], $d3["expected"], $d3["weighting"], $d3["docname"], $d3["category"]],
            [$d4["sentence"], $d4["expected"], $d4["weighting"], $d4["docname"], $d4["category"]],
            [$d5["sentence"], $d5["expected"], $d5["weighting"], $d5["docname"], $d5["category"]],
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
        $weighting['category'] = [];
        $expectedWeight  = <<<DOC
| kata    | tf - d1 | tf.idf - d1 | c - sholat | tf.idf.icf - d1 | tdcf.idcf - sholat |
| sholat  |   6     |    6.0      |    1       | 6.0             | 6.0                |
| wajib   |   1     |    1.0      |    1       | 1.0             | 1.0                |
| hari    |   1     |    1.0      |    1       | 1.0             | 1.0                |
| dzuhur  |   1     |    1.0      |    1       | 1.0             | 1.0                |
| ashar   |   1     |    1.0      |    1       | 1.0             | 1.0                |
| subuh   |   1     |    1.0      |    1       | 1.0             | 1.0                |
| maghrib |   1     |    1.0      |    1       | 1.0             | 1.0                |
| isya    |   1     |    1.0      |    1       | 1.0             | 1.0                |
DOC;

        $expectedCategory  = <<<DOC
| sholat |
| d1     |
DOC;

        return [
            "sentence" => $sentence,
            "expected" => [
                'weight' => $this->stringTableToArray($expectedWeight),
                'category' => $this->stringTableToArray($expectedCategory)
            ],
            "weighting"   => $weighting,
            "docname"  => "d1",
            "category" => "sholat"
        ];
    }

    /**
     * @return array
     */
    protected function getD2()
    {
        $sentence  = 'Sholat sunnah rowatib merupakan sholat sunnah yang dilakukan ';
        $sentence .= 'sebelum dan sesudah sholat wajib';
        $weightingWeight = <<<DOC
| kata    | tf - d1 | tf.idf - d1 | c - sholat | tf.idf.icf - d1 | tdcf.idcf - sholat |
| sholat  |   6     |    6.0      |    1       | 6.0             | 6.0                |
| wajib   |   1     |    1.0      |    1       | 1.0             | 1.0                |
| hari    |   1     |    1.0      |    1       | 1.0             | 1.0                |
| dzuhur  |   1     |    1.0      |    1       | 1.0             | 1.0                |
| ashar   |   1     |    1.0      |    1       | 1.0             | 1.0                |
| subuh   |   1     |    1.0      |    1       | 1.0             | 1.0                |
| maghrib |   1     |    1.0      |    1       | 1.0             | 1.0                |
| isya    |   1     |    1.0      |    1       | 1.0             | 1.0                |
DOC;
        $weightingCategory = <<<DOC
| sholat |
| d1     |
DOC;

        $expectedWeight  = <<<DOC
| kata    | tf - d1 | tf - d2 | tf.idf - d1        | tf.idf - d2        | c - sholat | tf.idf.icf - d1    | tf.idf.icf - d2    | tdcf.idcf - sholat |
| sholat  |   6     |   3     | 6.0                | 3.0                |     1      | 6.0                | 3.0                | 9.0                |
| wajib   |   1     |   1     | 1.0                | 1.0                |     1      | 1.0                | 1.0                | 2.0                |
| hari    |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
| dzuhur  |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
| ashar   |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
| subuh   |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
| maghrib |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
| isya    |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
| sunnah  |   0     |   2     | 0.0                | 2.6020599913279625 |     1      | 0.0                | 2.6020599913279625 | 2.6020599913279625 |
| rowatib |   0     |   1     | 0.0                | 1.3010299956639813 |     1      | 0.0                | 1.3010299956639813 | 1.3010299956639813 |
DOC;

        $expectedCategory  = <<<DOC
| sholat |
| d1     |
| d2     |
DOC;

        return [
            "sentence" => $sentence,
            "expected" => [
                'weight' => $this->stringTableToArray($expectedWeight),
                'category' => $this->stringTableToArray($expectedCategory)
            ],
            "weighting"   => [
                'weight' => $this->stringTableToArray($weightingWeight),
                'category' => $this->stringTableToArray($weightingCategory)
            ],
            "docname"  => "d2",
            "category" => "sholat"
        ];
    }

    /**
     * @return array
     */
    protected function getD3()
    {
        $sentence = "Sebelum melakukan sholat yang wajib dilakukan adalah bersuci yang biasa disebut wudlu";
        $weightingWeight =<<<DOC
        | kata    | tf - d1 | tf - d2 | tf.idf - d1        | tf.idf - d2        | c - sholat | tf.idf.icf - d1    | tf.idf.icf - d2    | tdcf.idcf - sholat |
        | sholat  |   6     |   3     | 6.0                | 3.0                |     1      | 6.0                | 3.0                | 9.0                |
        | wajib   |   1     |   1     | 1.0                | 1.0                |     1      | 1.0                | 1.0                | 2.0                |
        | hari    |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
        | dzuhur  |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
        | ashar   |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
        | subuh   |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
        | maghrib |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
        | isya    |   1     |   0     | 1.3010299956639813 | 0.0                |     1      | 1.3010299956639813 | 0.0                | 1.3010299956639813 |
        | sunnah  |   0     |   2     | 0.0                | 2.6020599913279625 |     1      | 0.0                | 2.6020599913279625 | 2.6020599913279625 |
        | rowatib |   0     |   1     | 0.0                | 1.3010299956639813 |     1      | 0.0                | 1.3010299956639813 | 1.3010299956639813 |
DOC;
        $weightingCategory =<<<DOC
| sholat |
| d1     |
| d2     |
DOC;

        $expectedWeight =<<<DOC
| kata    | tf - d1 | tf - d2 | tf - d3 | tf.idf - d1        | tf.idf - d2        | tf.idf - d3        | c - sholat | c - thoharoh | tf.idf.icf - d1    | tf.idf.icf - d2    | tf.idf.icf - d3    | tdcf.idcf - sholat | tdcf.idcf - thoharoh |
| sholat  |   6     |   3     |   1     | 6.0                | 3.0                | 1.0                |     1      |     1        | 6.0                | 3.0                | 1.0                | 9.0                | 1.0                  |
| wajib   |   1     |   1     |   1     | 1.0                | 1.0                | 1.0                |     1      |     1        | 1.0                | 1.0                | 1.0                | 2.0                | 1.0                  |
| hari    |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| dzuhur  |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| ashar   |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| subuh   |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| maghrib |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| isya    |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| sunnah  |   0     |   2     |   0     | 0.0                | 2.9542425094393248 | 0.0                |     1      |     0        | 0.0                | 3.8435581192461936 | 0.0                | 2.6020599913279625 | 0.0                  |
| rowatib |   0     |   1     |   0     | 0.0                | 1.4771212547196624 | 0.0                |     1      |     0        | 0.0                | 1.9217790596230968 | 0.0                | 1.3010299956639813 | 0.0                  |
| suci    |   0     |   0     |   1     | 0.0                | 0.0                | 1.4771212547196624 |     0      |     1        | 0.0                | 0.0                | 1.9217790596230968 | 0.0                | 1.0                  |
| wudlu   |   0     |   0     |   1     | 0.0                | 0.0                | 1.4771212547196624 |     0      |     1        | 0.0                | 0.0                | 1.9217790596230968 | 0.0                | 1.0                  |
DOC;

$expectedCategory  = <<<DOC
| sholat | thoharoh |
| d1     | d3       |
| d2     |          |
DOC;

        return [
            "sentence" => $sentence,
            "expected" => [
                'weight' => $this->stringTableToArray($expectedWeight),
                'category' => $this->stringTableToArray($expectedCategory)
            ],
            "weighting"   => [
                'weight' => $this->stringTableToArray($weightingWeight),
                'category' => $this->stringTableToArray($weightingCategory)
            ],
            "docname"  => "d3",
            "category" => "thoharoh"
        ];
    }

    /**
     * @return array
     */
    protected function getD4()
    {
        $sentence = "sholat jenazah merupakan sholat yang dilakukan setelah jenazah selesai dimandikan";
        $weightingWeight =<<<DOC
| kata    | tf - d1 | tf - d2 | tf - d3 | tf.idf - d1        | tf.idf - d2        | tf.idf - d3        | c - sholat | c - thoharoh | tf.idf.icf - d1    | tf.idf.icf - d2    | tf.idf.icf - d3    | tdcf.idcf - sholat | tdcf.idcf - thoharoh |
| sholat  |   6     |   3     |   1     | 6.0                | 3.0                | 1.0                |     1      |     1        | 6.0                | 3.0                | 1.0                | 9.0                | 1.0                  |
| wajib   |   1     |   1     |   1     | 1.0                | 1.0                | 1.0                |     1      |     1        | 1.0                | 1.0                | 1.0                | 2.0                | 1.0                  |
| hari    |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| dzuhur  |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| ashar   |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| subuh   |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| maghrib |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| isya    |   1     |   0     |   0     | 1.4771212547196624 | 0.0                | 0.0                |     1      |     0        | 1.9217790596230968 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  |
| sunnah  |   0     |   2     |   0     | 0.0                | 2.9542425094393248 | 0.0                |     1      |     0        | 0.0                | 3.8435581192461936 | 0.0                | 2.6020599913279625 | 0.0                  |
| rowatib |   0     |   1     |   0     | 0.0                | 1.4771212547196624 | 0.0                |     1      |     0        | 0.0                | 1.9217790596230968 | 0.0                | 1.3010299956639813 | 0.0                  |
| suci    |   0     |   0     |   1     | 0.0                | 0.0                | 1.4771212547196624 |     0      |     1        | 0.0                | 0.0                | 1.9217790596230968 | 0.0                | 1.0                  |
| wudlu   |   0     |   0     |   1     | 0.0                | 0.0                | 1.4771212547196624 |     0      |     1        | 0.0                | 0.0                | 1.9217790596230968 | 0.0                | 1.0                  |
DOC;
        $weightingCategory =<<<DOC
| sholat | thoharoh |
| d1     | d3       |
| d2     |          |
DOC;
        $expectedWeight = <<<DOC
| kata    | tf - d1 | tf - d2 | tf - d3 | tf - d4 | tf.idf - d1        | tf.idf - d2        | tf.idf - d3        | tf.idf - d4        |c - sholat | c - thoharoh | c - jenazah | tf.idf.icf - d1    | tf.idf.icf - d2    | tf.idf.icf - d3    | tf.idf.icf - d4    | tdcf.idcf - sholat | tdcf.idcf - thoharoh | tdcf.idcf - jenazah |
| sholat  |   6     |   3     |   1     |   2     | 6.0                | 3.0                | 1.0                | 2.0                |    1      |      1       |     1       | 6.0                | 3.0                | 1.0                | 2.0                | 9.0                | 1.0                  | 2.0                 |
| wajib   |   1     |   1     |   1     |   0     | 1.1249387366083    | 1.1249387366083    | 1.1249387366083    | 0.0                |    1      |      1       |     0       | 1.323030615098163  | 1.323030615098163  | 1.323030615098163  | 0.0                | 2.0                | 1.0                  | 0.0                 |
| hari    |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| dzuhur  |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| ashar   |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| subuh   |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| maghrib |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| isya    |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| sunnah  |   0     |   2     |   0     |   0     | 0.0                | 3.204119982655925  | 0.0                | 0.0                |    1      |      0       |     0       | 0.0                | 4.732873729053063  | 0.0                | 0.0                | 2.6020599913279625 | 0.0                  | 0.0                 |
| rowatib |   0     |   1     |   0     |   0     | 0.0                | 1.6020599913279625 | 0.0                | 0.0                |    1      |      0       |     0       | 0.0                | 2.3664368645265315 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| suci    |   0     |   0     |   1     |   0     | 0.0                | 0.0                | 1.6020599913279625 | 0.0                |    0      |      1       |     0       | 0.0                | 0.0                | 2.3664368645265315 | 0.0                | 0.0                | 1.0                  | 0.0                 |
| wudlu   |   0     |   0     |   1     |   0     | 0.0                | 0.0                | 1.6020599913279625 | 0.0                |    0      |      1       |     0       | 0.0                | 0.0                | 2.3664368645265315 | 0.0                | 0.0                | 1.0                  | 0.0                 |
| jenazah |   0     |   0     |   0     |   2     | 0.0                | 0.0                | 0.0                | 3.204119982655925  |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 4.732873729053063  | 0.0                | 0.0                  | 2.0                 |
| selesai |   0     |   0     |   0     |   1     | 0.0                | 0.0                | 0.0                | 1.6020599913279625 |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 2.3664368645265315 | 0.0                | 0.0                  | 1.0                 |
| mandi   |   0     |   0     |   0     |   1     | 0.0                | 0.0                | 0.0                | 1.6020599913279625 |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 2.3664368645265315 | 0.0                | 0.0                  | 1.0                 |
DOC;

$expectedCategory  = <<<DOC
| sholat | thoharoh | jenazah |
| d1     | d3       | d4      |
| d2     |          |         |
DOC;

        return [
            "sentence" => $sentence,
            "expected" => [
                'weight' => $this->stringTableToArray($expectedWeight),
                'category' => $this->stringTableToArray($expectedCategory)
            ],
            "weighting"   => [
                'weight' => $this->stringTableToArray($weightingWeight),
                'category' => $this->stringTableToArray($weightingCategory)
            ],
            "docname"  => "d4",
            "category" => "jenazah"
        ];
    }


    /**
     * @return array
     */
    protected function getD5()
    {
        $sentence = "orang yang berhak memandikan jenazah adalah muhrimnya";
        $weightingWeight =<<<DOC
| kata    | tf - d1 | tf - d2 | tf - d3 | tf - d4 | tf.idf - d1        | tf.idf - d2        | tf.idf - d3        | tf.idf - d4        |c - sholat | c - thoharoh | c - jenazah | tf.idf.icf - d1    | tf.idf.icf - d2    | tf.idf.icf - d3    | tf.idf.icf - d4    | tdcf.idcf - sholat | tdcf.idcf - thoharoh | tdcf.idcf - jenazah |
| sholat  |   6     |   3     |   1     |   2     | 6.0                | 3.0                | 1.0                | 2.0                |    1      |      1       |     1       | 6.0                | 3.0                | 1.0                | 2.0                | 9.0                | 1.0                  | 2.0                 |
| wajib   |   1     |   1     |   1     |   0     | 1.1249387366083    | 1.1249387366083    | 1.1249387366083    | 0.0                |    1      |      1       |     0       | 1.323030615098163  | 1.323030615098163  | 1.323030615098163  | 0.0                | 2.0                | 1.0                  | 0.0                 |
| hari    |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| dzuhur  |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| ashar   |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| subuh   |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| maghrib |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| isya    |   1     |   0     |   0     |   0     | 1.6020599913279625 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.3664368645265315 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| sunnah  |   0     |   2     |   0     |   0     | 0.0                | 3.204119982655925  | 0.0                | 0.0                |    1      |      0       |     0       | 0.0                | 4.732873729053063  | 0.0                | 0.0                | 2.6020599913279625 | 0.0                  | 0.0                 |
| rowatib |   0     |   1     |   0     |   0     | 0.0                | 1.6020599913279625 | 0.0                | 0.0                |    1      |      0       |     0       | 0.0                | 2.3664368645265315 | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| suci    |   0     |   0     |   1     |   0     | 0.0                | 0.0                | 1.6020599913279625 | 0.0                |    0      |      1       |     0       | 0.0                | 0.0                | 2.3664368645265315 | 0.0                | 0.0                | 1.0                  | 0.0                 |
| wudlu   |   0     |   0     |   1     |   0     | 0.0                | 0.0                | 1.6020599913279625 | 0.0                |    0      |      1       |     0       | 0.0                | 0.0                | 2.3664368645265315 | 0.0                | 0.0                | 1.0                  | 0.0                 |
| jenazah |   0     |   0     |   0     |   2     | 0.0                | 0.0                | 0.0                | 3.204119982655925  |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 4.732873729053063  | 0.0                | 0.0                  | 2.0                 |
| selesai |   0     |   0     |   0     |   1     | 0.0                | 0.0                | 0.0                | 1.6020599913279625 |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 2.3664368645265315 | 0.0                | 0.0                  | 1.0                 |
| mandi   |   0     |   0     |   0     |   1     | 0.0                | 0.0                | 0.0                | 1.6020599913279625 |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 2.3664368645265315 | 0.0                | 0.0                  | 1.0                 |
DOC;

        $weightingCategory =<<<DOC
| sholat | thoharoh | jenazah |
| d1     | d3       | d4      |
| d2     |          |         |
DOC;

        $expectedWeight = <<<DOC
| kata    | tf - d1 | tf - d2 | tf - d3 | tf - d4 | tf - d5 | tf.idf - d1        | tf.idf - d2        | tf.idf - d3        | tf.idf - d4        | tf.idf - d5        |c - sholat | c - thoharoh | c - jenazah | tf.idf.icf - d1    | tf.idf.icf - d2    | tf.idf.icf - d3    | tf.idf.icf - d4    | tf.idf.icf - d5    | tdcf.idcf - sholat | tdcf.idcf - thoharoh | tdcf.idcf - jenazah |
| sholat  |   6     |   3     |   1     |   2     |   0     | 6.581460078048339  | 3.2907300390241696 | 1.0969100130080565 | 2.193820026016113  | 0.0                |    1      |      1       |     1       | 6.581460078048339  | 3.2907300390241696 | 1.0969100130080565 | 2.193820026016113  | 0.0                | 9.0                | 1.0                  | 2.6020599913279625  |
| wajib   |   1     |   1     |   1     |   0     |   0     | 1.2218487496163564 | 1.2218487496163564 | 1.2218487496163564 | 0.0                | 0.0                |    1      |      1       |     0       | 1.4370056343119104 | 1.4370056343119104 | 1.4370056343119104 | 0.0                | 0.0                | 2.0                | 1.0                  | 0.0                 |
| hari    |   1     |   0     |   0     |   0     |   0     | 1.6989700043360187 | 0.0                | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.5095847045358903 | 0.0                | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| dzuhur  |   1     |   0     |   0     |   0     |   0     | 1.6989700043360187 | 0.0                | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.5095847045358903 | 0.0                | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| ashar   |   1     |   0     |   0     |   0     |   0     | 1.6989700043360187 | 0.0                | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.5095847045358903 | 0.0                | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| subuh   |   1     |   0     |   0     |   0     |   0     | 1.6989700043360187 | 0.0                | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.5095847045358903 | 0.0                | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| maghrib |   1     |   0     |   0     |   0     |   0     | 1.6989700043360187 | 0.0                | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.5095847045358903 | 0.0                | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| isya    |   1     |   0     |   0     |   0     |   0     | 1.6989700043360187 | 0.0                | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 2.5095847045358903 | 0.0                | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| sunnah  |   0     |   2     |   0     |   0     |   0     | 0.0                | 3.3979400086720375 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 0.0                | 5.019169409071781  | 0.0                | 0.0                | 0.0                | 2.6020599913279625 | 0.0                  | 0.0                 |
| rowatib |   0     |   1     |   0     |   0     |   0     | 0.0                | 1.6989700043360187 | 0.0                | 0.0                | 0.0                |    1      |      0       |     0       | 0.0                | 2.5095847045358903 | 0.0                | 0.0                | 0.0                | 1.3010299956639813 | 0.0                  | 0.0                 |
| suci    |   0     |   0     |   1     |   0     |   0     | 0.0                | 0.0                | 1.6989700043360187 | 0.0                | 0.0                |    0      |      1       |     0       | 0.0                | 0.0                | 2.5095847045358903 | 0.0                | 0.0                | 0.0                | 1.0                  | 0.0                 |
| wudlu   |   0     |   0     |   1     |   0     |   0     | 0.0                | 0.0                | 1.6989700043360187 | 0.0                | 0.0                |    0      |      1       |     0       | 0.0                | 0.0                | 2.5095847045358903 | 0.0                | 0.0                | 0.0                | 1.0                  | 0.0                 |
| jenazah |   0     |   0     |   0     |   2     |   1     | 0.0                | 0.0                | 0.0                | 2.795880017344075  | 1.3979400086720375 |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 4.129853799264912  | 2.064926899632456  | 0.0                | 0.0                  | 3.0                 |
| selesai |   0     |   0     |   0     |   1     |   0     | 0.0                | 0.0                | 0.0                | 1.6989700043360187 | 0.0                |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 2.5095847045358903 | 0.0                | 0.0                | 0.0                  | 1.3010299956639813  |
| mandi   |   0     |   0     |   0     |   1     |   1     | 0.0                | 0.0                | 0.0                | 1.3979400086720375 | 1.3979400086720375 |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 2.064926899632456  | 2.064926899632456  | 0.0                | 0.0                  | 2.0                 |
| orang   |   0     |   0     |   0     |   0     |   1     | 0.0                | 0.0                | 0.0                | 0.0                | 1.6989700043360187 |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 0.0                | 2.5095847045358903 | 0.0                | 0.0                  | 1.3010299956639813  |
| hak     |   0     |   0     |   0     |   0     |   1     | 0.0                | 0.0                | 0.0                | 0.0                | 1.6989700043360187 |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 0.0                | 2.5095847045358903 | 0.0                | 0.0                  | 1.3010299956639813  |
| muhrim  |   0     |   0     |   0     |   0     |   1     | 0.0                | 0.0                | 0.0                | 0.0                | 1.6989700043360187 |    0      |      0       |     1       | 0.0                | 0.0                | 0.0                | 0.0                | 2.5095847045358903 | 0.0                | 0.0                  | 1.3010299956639813  |
DOC;

$expectedCategory  = <<<DOC
| sholat | thoharoh | jenazah |
| d1     | d3       | d4      |
| d2     |          | d5      |
DOC;

        return [
            "sentence" => $sentence,
            "expected" => [
                'weight' => $this->stringTableToArray($expectedWeight),
                'category' => $this->stringTableToArray($expectedCategory)
            ],
            "weighting"   => [
                'weight' => $this->stringTableToArray($weightingWeight),
                'category' => $this->stringTableToArray($weightingCategory)
            ],
            "docname"  => "d5",
            "category" => "jenazah"
        ];
    }

    /**
     * @param string $table
     * @return array
     */
    protected function stringTableToArray($table)
    {
        $head = [];
        $kata = [];
        $arr  = [];
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $table) as $index => $line) {
            $data = explode("|", $line);
            for ($a = 1; $a<(sizeof($data)-1); $a++) {
                if (isset($data[$a]) && $index == 0) {
                    $head[$a] = trim($data[$a]);
                } elseif (isset($data[$a])) {
                    $h = explode("-", $head[$a]);
                    if (sizeof($h) == 1 && strtolower($h[0]) == 'kata') {
                        $content = trim($data[$a]);
                        $kata[$index] = $content;
                        $arr[$content] = [];
                    } elseif (sizeof($h) == 1) {
                        $d = trim($data[$a]);
                        $d = trim($data[$a]);;
                        if (is_numeric($d)) {
                            if (substr_count($d, '.') == 0) {
                                $d = (int)$d;
                            } elseif (substr_count($d, '.') == 1) {
                                $d = float($d);
                            }
                        }
                        if (isset($kata[$index])) {
                            $arr[$kata[$index]][trim($h[0])] = $d;
                        } else {
                            if ($d != '') {
                                $arr[trim($h[0])][] = $d;
                            }
                        }

                    } else {
                        $d = trim($data[$a]);;
                        if (is_numeric($d)) {
                            if (substr_count($d, '.') == 0) {
                                $d = (int)$d;
                            } elseif (substr_count($d, '.') == 1) {
                                $d = (float)$d;
                            }
                        }
                        $arr[$kata[$index]][trim($h[0])][trim($h[1])] = $d ;
                    }
                }
            }
        }
        return $arr;
    }
}