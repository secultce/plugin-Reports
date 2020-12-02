<?php
namespace Reports;
use MapasCulturais\App,
    MapasCulturais\Entities,
    MapasCulturais\Definitions,
    MapasCulturais\Exceptions;

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