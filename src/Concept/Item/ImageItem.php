<?php

namespace Interpro\ImageFileLogic\Concept\Item;

interface ImageItem
{
    //Получение имени (блок + группа + id)
    function getName();

    function getNameWoExt();

    function getExt();

    function setExt($ext);

}
