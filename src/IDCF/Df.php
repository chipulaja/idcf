<?php

namespace IDCF;

use IDCF\Preprocessing;

class Df
{

    /**
     * Catatan
     * df atau Document Frequency adalah jumlah dari dokumen
     * yang memiliki term (word) tertentu.
     *
     * sebaiknya df di update berdasarkan tf baru saja
     * supaya tidak memakan banyak waktu saat looping
     * $dfList[$word] = jumlah dokumen yang memiliki kata tertentu
     *
     * @param array $token
     * @param array $dfList
     * @return array $dfList
     */
    public function execute($token, $dfList = [])
    {
        $dfList = ($dfList !== null) ? $dfList : [];
        foreach ($token as $word => $count) {
            if (isset($dfList[$word])) {
                $dfList[$word] += 1;
            } else {
                $dfList[$word] = 1;
            }
        }

        return $dfList;
    }
}
