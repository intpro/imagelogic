<?php

namespace Interpro\ImageFileLogic\Concept;

interface Report
{
    /**
     * @param string $image_name
     *
     * @return array
     */
    public function getImageReport($image_name);

    /**
     * @return array
     */
    public function getImageReportAll();

    /**
     * @param string $image_name
     * @param array $resize
     *
     * @return array
     */
    public function setImageResize($image_name, $sufix, $resize);

}
