<?php

namespace Interpro\ImageFileLogic\Concept;

interface CropConfig{

    /**
     * @param array $crop_config
     *
     * @param string $config_name
     *
     * @return void
     */
    public function checkConfig(&$crop_config, $config_name);

    /**
     * @param string $config_name
     *
     * @return array
     */
    public function getConfig($config_name);

    /**
     *
     * @return array
     */
    public function getConfigAll();

    /**
    * @param string $config_name
    *
    * @return bool
    */
    public function configExist($config_name);

    /**
     * @param string $image_name
     *
     * @param string $crop_name
     */
    public function getWidth($image_name, $crop_name);

    /**
     * @param string $image_name
     *
     * @param string $crop_name
     */
    public function getHeight($image_name, $crop_name);

    /**
     * @param string $image_name
     *
     * @param string $crop_name
     */
    public function getMan($image_name, $crop_name);

    /**
     * @param string $image_name
     *
     * @param string $crop_name
     */
    public function getTarget($image_name, $crop_name);

}
