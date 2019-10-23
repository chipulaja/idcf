<?php

namespace IDCF;

use IDCF\Preprocessing;

class TfIdf
{

    /**
     * Catatan
     * tf.idf merupakan perkalian dari tf dokumen tertentu dengan idf
     *
     * sebaiknya tf.idf di hitung berdasarkan tf dokumen tertentu saja
     * supaya tidak memakan banyak waktu saat looping
     * $tfidfDocs[$word] = tf . idf
     *
     * @param array $tfDocs contain tf list is raw tf [word => count]
     * @return array
     */
    public function execute($token, $idfDocs)
    {
        $tfidfDocs = [];
        foreach ($token as $word => $count) {
            $tfidfDocs[$word] = $count * $idfDocs[$word];
        }

        return $tfidfDocs;
    }
}
