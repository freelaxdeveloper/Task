<?php
namespace App\Http\Controllers;

use \Core\{Controller,Captcha};

class CaptchaController extends Controller{
    public function actionView()
    {
        $captcha = new Captcha;
        //$captcha->length = 2;
        echo $captcha->show();
    }
}
