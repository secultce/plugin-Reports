<?php

namespace Report\Controllers;

use MapasCulturais\Controller;


class Report extends Controller
{
    public function generationJSONFile($json_array)
    {

        $stringFile = '{"data":' . $json_array . '}';
        $extensao = '.json';
        $somenteNome = 'data';
        $rand = rand(0, 99999999999999999);
        $arquivoData = $somenteNome . $rand . $extensao;
        $file = fopen(__DIR__ . '/../jasper/data-adapter-json/' . $arquivoData, 'w');
        fwrite($file, $stringFile);
        fclose($file);
        $dataFile = __DIR__ . '/../jasper/data-adapter-json/' . $arquivoData;
        return $dataFile;
    }
}
