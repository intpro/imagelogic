<?php

namespace Interpro\ImageFileLogic\Laravel;

use Interpro\ImageFileLogic\Concept\Report as ReportInterface;

class Report implements ReportInterface
{
    private $images;

    public function __construct()
    {
        $this->images = [];
    }

    private function createImageIfNotExist($image_name)
    {
        if(!array_key_exists($image_name, $this->images))
        {
            $this->images[$image_name] = [];
            $this->images[$image_name]['sizes'] = [];
        }
    }

    /**
     * @param string $image_name
     *
     * @return array
     */
    public function getImageReport($image_name)
    {
        if(array_key_exists($image_name, $this->images))
        {
            return $this->images[$image_name]['sizes'];
        }else{
            return [];
        }
    }

    /**
     * @return array
     */
    public function getImageReportAll()
    {
        return $this->images;
    }

    /**
     * @param string $image_name
     * @param string $ext
     *
     * @return array
     */
    public function setImageExt($image_name, $ext)
    {

    }

    /**
     * @param string $image_name
     * @param array $resized_file_name
     *
     * @return array
     */
    public function setImageResize($image_name, $sufix, $resized_file_name)
    {
        $this->createImageIfNotExist($image_name);

        $this->images[$image_name]['sizes'][$sufix] = $resized_file_name;
    }

    /**
     * @param string $image_name
     * @param array $crop_file_name
     *
     * @return array
     */
    public function setImageCrop($image_name, $sufix, $crop_file_name)
    {
        $this->createImageIfNotExist($image_name);

        $this->images[$image_name]['crops'][$sufix] = $crop_file_name;
    }

}