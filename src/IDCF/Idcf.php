<?php

namespace IDCF;

use IDCF\Preprocessing;

class Idcf
{

    /**
     * Catatan
     * idcf atau Invers Document Class Frequency adalah kebalikan dari nilai dcf
     * idcf = 1 + log10(jumlah seluruh dokumen dalam class / dcf)
     *
     * sebaiknya df di update berdasarkan tf baru saja
     * supaya tidak memakan banyak waktu saat looping
     * $idcfList[$word] = 1 + log10(jumlah seluruh dokumen dalam class / dcf)
     *
     * @param array $token
     * @param array $cfList
     * @return array $cfList
     */
    public function execute($token, $docClassList, $dcfList, $idcfList)
    {
        $idcfList = ($idcfList !== null) ? $idcfList : [];
        foreach ($token as $word => $count) {
            $dcf = (int)@$dcfList[$word];
            $idcfList[$word] = 0;
            if ($dcf > 0) {
                $countDocClass = sizeof($docClassList);
                $idcfList[$word] = 1 + log10($countDocClass / $dcf);
            }
        }

        return $idcfList;
    }
}
