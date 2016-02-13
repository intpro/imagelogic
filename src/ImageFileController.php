<?php

namespace Interpro\ImageFileLogic;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Request;

class ImageFileController extends Controller
{

    public function postImage(\Interpro\ImageFileLogic\ImageFileLogic $ifl) {

        if(Request::hasFile('imagefile') && Request::has('name'))
        {
            //id - это айди элемента группы, картинки принадлежащие непосредственно блоку проходят с адйди=0
            if(Request::has('id')){
                $id = Request::input('id');
            }else{
                $id = 0;
            }

            $result = $ifl->storeImage(Request::input('name'), $id, Request::file('imagefile'), false);

            return $result;

        } else {

            return ['status'=>'Не хватает параметров (файла или имени сущности) для сохранения!'];

        }

    }

}

