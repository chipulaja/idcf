<?php

namespace IDCF;

use IDCF\Preprocessing;

class Cfdetail
{

    /**
     * Catatan
     * cfdetail atau Class Frequency details adalah daftar class
     * yang jika salah satu dari dokumen class memiliki kata tertentu
     *
     * sebaiknya df di update berdasarkan tf baru saja
     * supaya tidak memakan banyak waktu saat looping
     * $cfList[$word] = bernilai 1 jika salah satu dokumen class memiliki kata tertentu
     *
     * @param array $token
     * @param array $cfList
     * @return array $cfList
     */
    public function execute($className, $cList, $cfList)
    {
        $cfList = ($cfList !== null) ? $cfList : [];
        foreach ($cList as $word => $count) {
            $cfList[$word][] = $className;
            $cfList[$word] = array_unique($cfList[$word]);
        }

        return $cfList;
    }
}
