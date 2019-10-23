<?php

namespace IDCF;

use IDCF\Preprocessing;

class C
{

    /**
     * Catatan
     * c atau Class adalah jumlah dari class
     * yang jika salah satu dari dokumen class memiliki kata tertentu akan bernilai 1
     *
     * sebaiknya df di update berdasarkan tf baru saja
     * supaya tidak memakan banyak waktu saat looping
     * $cList[$word] = bernilai 1 jika salah satu dokumen class memiliki kata tertentu
     *
     * @param array $token
     * @param array $cList
     * @return array $cList
     */
    public function execute($token, $cList = [])
    {
        $cList = ($cList !== null) ? $cList : [];
        foreach ($token as $word => $count) {
            $cList[$word] = 1;
        }

        return $cList;
    }
}
