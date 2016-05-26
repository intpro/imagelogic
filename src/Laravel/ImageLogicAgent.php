<?php

namespace Interpro\ImageFileLogic\Laravel;

use Interpro\ImageFileLogic\Concept\ImageConfig as ImageConfigInterface;
use Interpro\ImageFileLogic\Concept\ImageLogicAgent as ImageLogicAgentInterface;
use Interpro\Placeholder\Concept\PlaceholderAgent as PlaceholderAgentInterface;

class ImageLogicAgent implements ImageLogicAgentInterface
{

    private $imageConfig;
    private $placeholderAgent;

    public function __construct(ImageConfigInterface $imageConfig, PlaceholderAgentInterface $placeholderAgent)
    {
        $this->imageConfig = $imageConfig;
        $this->placeholderAgent = $placeholderAgent;
    }

    /**
     * @param string $image_name
     *
     * @return array
     */
    public function getPlaceholder($image_name, $sufix)
    {
        if($this->imageConfig->configExist($image_name))
        {
            $width = $this->imageConfig->getWidth($image_name, $sufix);
            $height = $this->imageConfig->getHeight($image_name, $sufix);
            $color = $this->imageConfig->getColor($image_name, $sufix);

            $width = $width ? $width : $height;
            $height = $height ? $height : $width;
        }else{

            $width = 200;
            $height = 200;
            $color = '#808080';
        }

        $link = $this->placeholderAgent->getLink($width, $height, $color);

        return $link;
    }

}
