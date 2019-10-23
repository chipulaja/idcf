<?php

namespace IDCF;

use IDCF\Preprocessing;

class Tdcf
{

    /**
     * Catatan
     * tdcf atau Term Document Class Frequency adalah jumlah kemunculan kata tertentu
     * pada suatu dokumen di klas tertentu
     *
     * $tdcfDocs[$word] = jumlah kemunculan kata tertentu pada suatu dokumen di klas tertentu
     *
     * @param array $tfDocs contain tf list is raw tf [word => count]
     * @return array
     */
    public function execute($token, $tdcfList)
    {
        $tdcfList = ($tdcfList !== null) ? $tdcfList : [];
        foreach ($token as $word => $count) {
            $tdcfList[$word] = (int)@$tdcfList[$word] + $count;
        }

        return $tdcfList;
    }
}
