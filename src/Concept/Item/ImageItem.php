<?php

namespace Interpro\ImageFileLogic\Concept\Item;

interface ImageItem
{
    //Получение имени (блок + группа + id)
    function getName();

    function getConfigName();

    function getId();

    function getNameWoExt();

    function getExt();

    function setExt($ext);

}
