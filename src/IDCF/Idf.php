<?php

namespace IDCF;

use IDCF\Preprocessing;

class Idf
{
    /**
     * Catatan
     * idf atau Invers Document Frequency merupakan kebalikan dari nilai df
     * idf = 1 + log10(jumlah seluruh dokumen / df)
     *
     * sebaiknya idf di update berdasarkan tf baru saja
     * supaya tidak memakan banyak waktu saat looping
     * $idfList[$word] = 1 + log10(jumlah seluruh dokumen / df)
     *
     * @param array $token
     * @param array $idfList
     * @return array $idfList
     */
    public function execute($token, $docnameList, $dfList, $idfList = [])
    {
        $idfList = ($idfList !== null) ? $idfList : [];
        foreach ($token as $word => $count) {
            $countDocs = sizeof($docnameList);
            $df = $dfList[$word];
            $idfList[$word] = 1 + log10($countDocs / $df);
        }

        return $idfList;
    }
}
