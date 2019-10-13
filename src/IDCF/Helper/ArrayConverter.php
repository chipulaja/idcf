<?php

namespace IDCF\Helper;

class ArrayConverter
{
    public function convertToTableSinggleKey($data)
    {
        $newData = [];
        $head = [];
        foreach ($data as $word => $dataType) {
            $newData[0]['word'] = 'word';
            $head[] = 'word';
            foreach ($dataType as $type => $dataDocOrCat) {
                foreach ($dataDocOrCat as $docOrCatName => $value) {
                    $newData[0][$type." - ".$docOrCatName] = $type." - ".$docOrCatName;
                    $head[] = $type." - ".$docOrCatName;
                }
            }
            break;
        }
        $i = 1;
        foreach ($data as $word => $dataType) {
            $newData[$i]['word'] = $word;
            foreach ($dataType as $type => $dataDocOrCat) {
                foreach ($dataDocOrCat as $docOrCatName => $value) {
                    $newData[$i][$type." - ".$docOrCatName] = $value;
                }
            }
            // order by $head
            $newData[$i] = array_merge(array_flip($head), $newData[$i]);
            $i++;
        }

        return $newData;
    }
}
