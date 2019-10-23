<?php

namespace IDCF;

use IDCF\Preprocessing;

class TfIdfIcf
{
    /**
     * Catatan
     * tf.idf.icf merupakan perkalian dari tf dokumen tertentu dengan idf dan icf
     *
     * sebaiknya tf.idf.icf di hitung berdasarkan tf dokumen tertentu saja
     * supaya tidak memakan banyak waktu saat looping
     * $tfidficfDocs[$word] = tf . idf . icf
     *
     * @param array $tfDocs contain tf list is raw tf [word => count]
     * @return array
     */
    public function execute($token, $idfDocs, $icf)
    {
        $tfIdfIcf = [];
        foreach ($token as $word => $count) {
            $tfIdfIcf[$word] = $count * $idfDocs[$word] * $icf[$word];
        }

        return $tfIdfIcf;
    }
}
