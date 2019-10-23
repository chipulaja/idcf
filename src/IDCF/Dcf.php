<?php

namespace IDCF;

use IDCF\Preprocessing;

class Dcf
{

    /**
     * Catatan
     * dcf atau Document Class Frequency adalah jumlah dari dokumen
     * yang memiliki term (word) tertentu di setiap classnya
     *
     * sebaiknya df di update berdasarkan tf baru saja
     * supaya tidak memakan banyak waktu saat looping
     * $dcfList[$word] = jumlah dokumen yang memiliki kata tertentu di setiap classnya
     *
     * @param array $token
     * @param array $dfList
     * @return array $dfList
     */
    public function execute($token, $dcfList)
    {
        $dcfList = ($dcfList !== null) ? $dcfList : [];
        foreach ($token as $word => $count) {
            if (isset($dcfList[$word])) {
                $dcfList[$word] += 1;
            } else {
                $dcfList[$word] = 1;
            }
        }

        return $dcfList;
    }
}
