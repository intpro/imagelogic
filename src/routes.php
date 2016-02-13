<?php

Route::group(['middleware' => 'auth', 'prefix' => 'adm'], function()
{
    Route::post('/uploadimage',  ['as' => 'post_img', 'uses' => 'Prehistorical\ImageFileLogic\ImageFileController@postImage']);
});