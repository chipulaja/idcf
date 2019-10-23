<?php

namespace IDCF;

use IDCF\Preprocessing;

class Tdcfidcf
{

    /**
     * Catatan
     * tdcf.idcf merupakan perkalian antara tdcf dengan idcf
     * tdcf.idcf = tdcf * idcf
     *
     * $tdcfidcf[$word] = jumlah kemunculan kata tertentu pada suatu dokumen di klas tertentu
     *
     * @param array $tfDocs contain tf list is raw tf [word => count]
     * @return array
     */
    public function execute($token, $idcfList, $tdcfidcfList)
    {
        $tdcfidcfList = ($tdcfidcfList !== null) ? $tdcfidcfList : [];
        foreach ($token as $word => $count) {
            $tdcfidcfList[$word] = (float)@$idcfList[$word] * $count;
        }

        return $tdcfidcfList;
    }
}
