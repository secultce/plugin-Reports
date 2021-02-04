<?php

namespace Report;


//require_once 'controllers/cinemaVideoController.php';

require_once PLUGINS_PATH . '/controllers/CinemaVideoController.php';

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


        //EDITAL CINEMA E VIDEO 
        $app->hook("<<GET|POST>>(opportunity.reportResultEvaluationsDocumental)", function () use ($app) {
        });
        $app->hook('template(opportunity.single.header-inscritos):end', function () use ($app) {
            //echo '<h1>AQUI</h1>';
            function aqui()
            {
                $cinemaVideo = new CinemaVideoController();
                return print_r('AQUI');
            }

            echo '<button class="btn btn-default download" onclick="' . aqui() . '">Imprimir Resultado</button>';
        });
    }
    public function register()
    {
        // register metadata, taxonomies
        $app = App::i();
        $app->registerController('CinemaVideoController', 'Report\controllers\CinemaVideoController');
    }
}