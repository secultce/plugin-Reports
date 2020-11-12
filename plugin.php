<?php
// namespace ReportsPlugin;

// class Plugin extends \MapasCulturais\Plugin
// {
//     public function _init()
//     {
//         // enqueue scripts and styles

//         // add hooks
//     }

//     public function register()
//     {
//         // register metadata, taxonomies

//     }
// }

include("./controller/ReportController.php");

$obj = new ReportController();

$a = $obj->reportGeneration();

echo $a;
