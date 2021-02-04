<?php

namespace Report;


//require_once 'controllers/cinemaVideoController.php';

require_once PLUGINS_PATH . 'Report/controllers/CinemaVideoController.php';

use CinemaVideoController;
use MapasCulturais\App,
    MapasCulturais\Entities,
    MapasCulturais\Definitions,
    MapasCulturais\Exceptions,
    MapasCulturais\i;

class Plugin extends \MapasCulturais\Plugin
{

    private $cinemaVideo;
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }


    public function _init()
    {
       
        $app = App::i();
        function aqui()
            {
                $cinemaVideo = new CinemaVideoController();
                $cinemaVideo->reportGeneration();
            }

        //EDITAL CINEMA E VIDEO 
        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            echo '<a class="btn btn-default download" href="#">Imprimir relatório</a>';
            $opportunity = $this->controller->requestedEntity;
            $this->part('reports/button-report', ['entity' => $this->controller->requestedEntity]);
            //$this->part('/mapasculturais/src/protected/application/plugins/Report/layout/button-report.php', ['entity' => $opportunity]);
        });
        $app->hook("<<GET|POST>>()", function()use ($app){
            echo'AQUI';
        });
    }
    public function register()
    {
        // register metadata, taxonomies
        $app = App::i();
        $app->registerController('CinemaVideoController', 'Report\controllers\CinemaVideoController');
    }
}