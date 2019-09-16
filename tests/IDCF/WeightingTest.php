<?php
declare(strict_types=1);
namespace IDCFTest;

use PHPUnit\Framework\TestCase;

final class WeightingTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testExecute($sentence, $expected, $weight, $docname)
    {
        $preprocessing = new \IDCF\Preprocessing();
        $weighting = new \IDCF\Weighting();
        $tokens = $preprocessing->execute($sentence);
        $weight = $weighting->execute($tokens, $weight, $docname);

        $this->assertEquals($expected, $weight);
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
            [$d1["sentence"], $d1["expected"], $d1["weight"], $d1["docname"]],
            [$d2["sentence"], $d2["expected"], $d2["weight"], $d2["docname"]],
            [$d3["sentence"], $d3["expected"], $d3["weight"], $d3["docname"]],
            [$d4["sentence"], $d4["expected"], $d4["weight"], $d4["docname"]],
            [$d5["sentence"], $d5["expected"], $d5["weight"], $d5["docname"]],
        ];
    }

    /**
     * @return array
     */
    protected function getD1()
    {
        $sentence  = 'Sholat wajib dalam sehari terdiri dari 5 waktu yaitu sholat dzuhur, ';
        $sentence .= 'sholat ashar, sholat subuh, sholat maghrib dan sholat isya';
        $weight    = [];
        $expected  = <<<DOC
| kata    | tf - d1 |
| sholat  |   6     |
| wajib   |   1     |
| hari    |   1     |
| dzuhur  |   1     |
| ashar   |   1     |
| subuh   |   1     |
| maghrib |   1     |
| isya    |   1     |
DOC;

        return [
            "sentence" => $sentence,
            "expected" => $this->stringTableToArray($expected),
            "weight"   => $weight,
            "docname"  => "d1",
        ];
    }

    /**
     * @return array
     */
    protected function getD2()
    {
        $sentence  = 'Sholat sunnah rowatib merupakan sholat sunnah yang dilakukan ';
        $sentence .= 'sebelum dan sesudah sholat wajib';
        $weight    = <<<DOC
| kata    | tf - d1 |
| sholat  |   6     |
| wajib   |   1     |
| hari    |   1     |
| dzuhur  |   1     |
| ashar   |   1     |
| subuh   |   1     |
| maghrib |   1     |
| isya    |   1     |
DOC;

$expected  = <<<DOC
| kata    | tf - d1 | tf - d2 |
| sholat  |   6     |   3     |
| wajib   |   1     |   1     |
| hari    |   1     |   0     |
| dzuhur  |   1     |   0     |
| ashar   |   1     |   0     |
| subuh   |   1     |   0     |
| maghrib |   1     |   0     |
| isya    |   1     |   0     |
| sunnah  |   0     |   2     |
| rowatib |   0     |   1     |
DOC;

        return [
            "sentence" => $sentence,
            "expected" => $this->stringTableToArray($expected),
            "weight"   => $this->stringTableToArray($weight),
            "docname"  => "d2",
        ];
    }

    /**
     * @return array
     */
    protected function getD3()
    {
        $sentence = "Sebelum melakukan sholat yang wajib dilakukan adalah bersuci yang biasa disebut wudlu";
        $weight   =<<<DOC
| kata    | tf - d1 | tf - d2 |
| sholat  |   6     |   3     |
| wajib   |   1     |   1     |
| hari    |   1     |   0     |
| dzuhur  |   1     |   0     |
| ashar   |   1     |   0     |
| subuh   |   1     |   0     |
| maghrib |   1     |   0     |
| isya    |   1     |   0     |
| sunnah  |   0     |   2     |
| rowatib |   0     |   1     |
DOC;

        $expected =<<<DOC
| kata    | tf - d1 | tf - d2 | tf - d3 |
| sholat  |   6     |   3     |   1     |
| wajib   |   1     |   1     |   1     |
| hari    |   1     |   0     |   0     |
| dzuhur  |   1     |   0     |   0     |
| ashar   |   1     |   0     |   0     |
| subuh   |   1     |   0     |   0     |
| maghrib |   1     |   0     |   0     |
| isya    |   1     |   0     |   0     |
| sunnah  |   0     |   2     |   0     |
| rowatib |   0     |   1     |   0     |
| suci    |   0     |   0     |   1     |
| wudlu   |   0     |   0     |   1     |
DOC;

        return [
            "sentence" => $sentence,
            "expected" => $this->stringTableToArray($expected),
            "weight"   => $this->stringTableToArray($weight),
            "docname"  => "d3",
        ];
    }

    /**
     * @return array
     */
    protected function getD4()
    {
        $sentence = "sholat jenazah merupakan sholat yang dilakukan setelah jenazah selesai dimandikan";
        $weight   =<<<DOC
| kata    | tf - d1 | tf - d2 | tf - d3 |
| sholat  |   6     |   3     |   1     |
| wajib   |   1     |   1     |   1     |
| hari    |   1     |   0     |   0     |
| dzuhur  |   1     |   0     |   0     |
| ashar   |   1     |   0     |   0     |
| subuh   |   1     |   0     |   0     |
| maghrib |   1     |   0     |   0     |
| isya    |   1     |   0     |   0     |
| sunnah  |   0     |   2     |   0     |
| rowatib |   0     |   1     |   0     |
| suci    |   0     |   0     |   1     |
| wudlu   |   0     |   0     |   1     |
DOC;

        $expected = <<<DOC
| kata    | tf - d1 | tf - d2 | tf - d3 | tf - d4 |
| sholat  |   6     |   3     |   1     |   2     |
| wajib   |   1     |   1     |   1     |   0     |
| hari    |   1     |   0     |   0     |   0     |
| dzuhur  |   1     |   0     |   0     |   0     |
| ashar   |   1     |   0     |   0     |   0     |
| subuh   |   1     |   0     |   0     |   0     |
| maghrib |   1     |   0     |   0     |   0     |
| isya    |   1     |   0     |   0     |   0     |
| sunnah  |   0     |   2     |   0     |   0     |
| rowatib |   0     |   1     |   0     |   0     |
| suci    |   0     |   0     |   1     |   0     |
| wudlu   |   0     |   0     |   1     |   0     |
| jenazah |   0     |   0     |   0     |   2     |
| selesai |   0     |   0     |   0     |   1     |
| mandi   |   0     |   0     |   0     |   1     |
DOC;

        return [
            "sentence" => $sentence,
            "expected" => $this->stringTableToArray($expected),
            "weight"   => $this->stringTableToArray($weight),
            "docname"  => "d4",
        ];
    }


    /**
     * @return array
     */
    protected function getD5()
    {
        $sentence = "orang yang berhak memandikan jenazah adalah muhrimnya";
        $weight   =<<<DOC
| kata    | tf - d1 | tf - d2 | tf - d3 | tf - d4 |
| sholat  |   6     |   3     |   1     |   2     |
| wajib   |   1     |   1     |   1     |   0     |
| hari    |   1     |   0     |   0     |   0     |
| dzuhur  |   1     |   0     |   0     |   0     |
| ashar   |   1     |   0     |   0     |   0     |
| subuh   |   1     |   0     |   0     |   0     |
| maghrib |   1     |   0     |   0     |   0     |
| isya    |   1     |   0     |   0     |   0     |
| sunnah  |   0     |   2     |   0     |   0     |
| rowatib |   0     |   1     |   0     |   0     |
| suci    |   0     |   0     |   1     |   0     |
| wudlu   |   0     |   0     |   1     |   0     |
| jenazah |   0     |   0     |   0     |   2     |
| selesai |   0     |   0     |   0     |   1     |
| mandi   |   0     |   0     |   0     |   1     |
DOC;

        $expected = <<<DOC
| kata    | tf - d1 | tf - d2 | tf - d3 | tf - d4 | tf - d5 |
| sholat  |   6     |   3     |   1     |   2     |   0     |
| wajib   |   1     |   1     |   1     |   0     |   0     |
| hari    |   1     |   0     |   0     |   0     |   0     |
| dzuhur  |   1     |   0     |   0     |   0     |   0     |
| ashar   |   1     |   0     |   0     |   0     |   0     |
| subuh   |   1     |   0     |   0     |   0     |   0     |
| maghrib |   1     |   0     |   0     |   0     |   0     |
| isya    |   1     |   0     |   0     |   0     |   0     |
| sunnah  |   0     |   2     |   0     |   0     |   0     |
| rowatib |   0     |   1     |   0     |   0     |   0     |
| suci    |   0     |   0     |   1     |   0     |   0     |
| wudlu   |   0     |   0     |   1     |   0     |   0     |
| jenazah |   0     |   0     |   0     |   2     |   1     |
| selesai |   0     |   0     |   0     |   1     |   0     |
| mandi   |   0     |   0     |   0     |   1     |   1     |
| orang   |   0     |   0     |   0     |   0     |   1     |
| hak     |   0     |   0     |   0     |   0     |   1     |
| muhrim  |   0     |   0     |   0     |   0     |   1     |
DOC;

        return [
            "sentence" => $sentence,
            "expected" => $this->stringTableToArray($expected),
            "weight"   => $this->stringTableToArray($weight),
            "docname"  => "d5",
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
                                $d = int($d);
                            } elseif (substr_count($d, '.') == 1) {
                                $d = float($d);
                            }
                        }
                        $arr[$kata[$index]][trim($h[0])] = $d;
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