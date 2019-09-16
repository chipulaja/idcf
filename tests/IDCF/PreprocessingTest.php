<?php
declare(strict_types=1);
namespace IDCFTest;

use PHPUnit\Framework\TestCase;

final class PreprocessingTest extends TestCase
{
    /**
     * @dataProvider additionProvider
     */
    public function testExecute($sentence, $expected)
    {
        $preprocessing = new \IDCF\Preprocessing();
        $token = $preprocessing->execute($sentence);
        $this->assertEquals($expected, $token);
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
            [$d1["sentence"], $d1["expected"]],
            [$d2["sentence"], $d2["expected"]],
            [$d3["sentence"], $d3["expected"]],
            [$d4["sentence"], $d4["expected"]],
            [$d5["sentence"], $d5["expected"]],
        ];
    }

    /**
     * @return array
     */
    protected function getD1()
    {
        $sentence  = 'Sholat wajib dalam sehari terdiri dari 5 waktu yaitu sholat dzuhur, ';
        $sentence .= 'sholat ashar, sholat subuh, sholat maghrib dan sholat isya';
        $expected = [
            'sholat' => 6,
            'wajib'  => 1,
            'hari'   => 1,
            'dzuhur' => 1,
            'ashar'  => 1,
            'subuh'  => 1,
            'maghrib'=> 1,
            'isya'   => 1
        ];
      
        return [
            "sentence" => $sentence,
            "expected" => $expected
        ];
    }

    /**
     * @return array
     */
    protected function getD2()
    {
        $sentence  = 'Sholat sunnah rowatib merupakan sholat sunnah yang dilakukan ';
        $sentence .= 'sebelum dan sesudah sholat wajib';
        $expected = [
            'sholat'  => 3,
            'sunnah'  => 2,
            'rowatib' => 1,
            'wajib'   => 1
        ];

        return [
            "sentence" => $sentence,
            "expected" => $expected
        ];
    }

    /**
     * @return array
     */
    protected function getD3()
    {
        $sentence = 'Sebelum melakukan sholat yang wajib dilakukan adalah bersuci yang biasa disebut wudlu';
        $expected = [
            'sholat' => 1,
            'wajib'  => 1,
            'suci'   => 1,
            'wudlu'  => 1
        ];

        return [
            "sentence" => $sentence,
            "expected" => $expected
        ];
    }

    /**
     * @return array
     */
    protected function getD4()
    {
        $sentence = 'sholat jenazah merupakan sholat yang dilakukan setelah jenazah selesai dimandikan';
        $expected = [
            'sholat'  => 2,
            'jenazah' => 2,
            'selesai' => 1,
            'mandi'   => 1
        ];

        return [
            "sentence" => $sentence,
            "expected" => $expected
        ];
    }

    /**
     * @return array
     */
    protected function getD5()
    {
        $sentence = 'orang yang berhak memandikan jenazah adalah muhrimnya';
        $expected = [
            'orang'   => 1,
            'hak'     => 1,
            'mandi'   => 1,
            'jenazah' => 1,
            'muhrim'  => 1
        ];

        return [
            "sentence" => $sentence,
            "expected" => $expected
        ];
    }
}
