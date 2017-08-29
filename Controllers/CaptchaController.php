<?php
namespace Controllers;

use \Core\Controller;
use \Models\Captcha;

class CaptchaController extends Controller{
    public function actionView()
    {
        $captcha = new Captcha;
        //$captcha->length = 2;
        echo $captcha->show();
    }
}
