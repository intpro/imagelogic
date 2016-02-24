<?php

namespace Interpro\ImageFileLogic\Concept;

use Interpro\ImageFileLogic\Concept\Action\ImageAction;

interface ActionChainFactory{

     /**
     * @param string $name
     *
     * @return void
     */
    public function buildChain(ImageAction $imageAction, $name, $config_name);

}
