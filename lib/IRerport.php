<?php
interface IReport
{
    public function executeReport($inputReport, $outputReport, $dataFile, $format, $driver, $query);
    public function buildReport($inputJRXML);
    //public function configReport();

}
