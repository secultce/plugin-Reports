<?php
namespace Reports;
use MapasCulturais\App,
    MapasCulturais\Entities,
    MapasCulturais\Definitions,
    MapasCulturais\Exceptions;

<<<<<<< HEAD
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
=======
class Plugin extends \MapasCulturais\Plugin {
    public function _init() {
        parent::_init();
        $app = App::i();

        $app->hook("<<GET|POST>>()", function()use($app){
            $response = $app->response();
        });
    }

    public function register() {
        // register metadata, taxonomies

    }
}
>>>>>>> c850b3214e8d890c670e2a82ba390d57fc4618f7
