<?php

namespace Interpro\ImageFileLogic\Concept;

interface ImageConfig{

    /**
     * @param array $image_config
     *
     * @param string $config_name
     *
     * @return void
     */
    public function checkConfig(&$image_config, $config_name);

    /**
     * @param string $config_name
     *
     * @return array
     */
    public function getConfig($config_name);

    /**
    * @param string $config_name
    *
    * @return bool
    */
    public function configExist($config_name);

}
