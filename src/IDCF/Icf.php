<?php

namespace IDCF;

use IDCF\Preprocessing;

class Icf
{
    /**
     * Catatan
     * icf atau Invers Class Frequency merupakan kebalikan dari nilai cf
     * icf = 1 + log10(jumlah seluruh class / cf)
     *
     * sebaiknya icf di update berdasarkan tf baru saja
     * $icfList[$word] = 1 + log10(jumlah seluruh class / cf)
     * supaya tidak memakan banyak waktu saat looping
     *
     * @param array $token
     * @param array $idfList
     * @return array $idfList
     */
    public function execute($token, $classnameList, $cfDetailList, $icfList = [])
    {
        $icfList = ($icfList !== null) ? $icfList : [];
        foreach ($token as $word => $count) {
            $countClass = sizeof($classnameList);
            $cf = sizeOf($cfDetailList[$word]);
            $icfList[$word] = 1 + log10($countClass / $cf);
        }

        return $icfList;
    }
}
