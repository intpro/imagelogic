<?php

namespace Interpro\ImageFileLogic\Laravel;

use Interpro\ImageFileLogic\Concept\Action\ImageAction;
use Interpro\ImageFileLogic\Concept\ActionChainFactory as ActionChainFactoryInterface;
use Interpro\ImageFileLogic\Concept\Exception\ImageConfigException;
use Interpro\ImageFileLogic\Concept\Exception\ImageFileSystemException;
use Interpro\ImageFileLogic\Concept\ImageConfig;
use Interpro\ImageFileLogic\Laravel\Action\CleanImageAction;
use Interpro\ImageFileLogic\Laravel\Action\MaskImageAction;
use Interpro\ImageFileLogic\Laravel\Action\ResizeImageAction;
use Interpro\ImageFileLogic\Laravel\Action\WaterImageAction;

class ActionChainFactory implements ActionChainFactoryInterface
{
    private $imageConfig;

    public function __construct(ImageConfig $imageConfig)
    {
        $this->imageConfig = $imageConfig;
    }

    /**
     * @param string $name
     *
     * @return void
     */
    public function buildChain(ImageAction $headAction, $name, $config_name)
    {
        try{

            $config = $this->imageConfig->getConfig($config_name);

            $parentAction = $headAction;

            if('update' or 'refresh')
            {
                $cleanAction = new CleanImageAction();
                $parentAction->succeedWith($cleanAction);
                $parentAction = $cleanAction;
            }

            if('update' or 'refresh')
            {
                if(array_key_exists('mask', $config))
                {
                    $mask_config = & $config['mask'];

                    $_x = array_key_exists('x', $mask_config) ? $mask_config['x'] : null;
                    $_y = array_key_exists('y', $mask_config) ? $mask_config['y'] : null;

                    $maskAction = new MaskImageAction($mask_config['file'], $mask_config['position'], $_x, $_y);
                    $parentAction->succeedWith($maskAction);
                    $parentAction = $maskAction;
                }

                if(array_key_exists('water', $config))
                {
                    $water_config = & $config['water'];

                    $_x = array_key_exists('x', $water_config) ? $water_config['x'] : null;
                    $_y = array_key_exists('y', $water_config) ? $water_config['y'] : null;

                    $waterAction = new WaterImageAction($water_config['file'], $water_config['position'], $_x, $_y);
                    $parentAction->succeedWith($waterAction);
                    $parentAction = $waterAction;
                }

                if(array_key_exists('sizes', $config))
                {
                    foreach($config['sizes'] as $npp => $size_config)
                    {
                        $mod = array_key_exists('mod', $size_config) ? $size_config['mod'] : false;
                        $resizeAction = new ResizeImageAction($size_config['width'], $size_config['height'], $size_config['sufix'], $mod);
                        $parentAction->succeedWith($resizeAction);
                        $parentAction = $resizeAction;
                    }
                }
            }

        }catch (ImageConfigException $imconfexc){

            throw new ImageFileSystemException('Ошибка в конфигурации картинок: '.$imconfexc->getMessage());
        }


    }

}
