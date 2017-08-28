<?php
namespace Controllers;

use \Core\Controller;
use \Models\Captcha;

class CaptchaController extends Controller{
    public function actionView()
    {
        echo Captcha::image();
    }
}
