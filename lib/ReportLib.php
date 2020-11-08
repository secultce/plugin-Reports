<?php
interface ReportLibInterface
{
    public function executeReport($inputReport, $outputReport, $dataFile, $format, $params, $driver, $query);
    public function buildReport($input, $path_save_build);
    public function configReport();

}
