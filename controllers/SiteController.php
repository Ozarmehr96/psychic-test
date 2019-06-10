<?php

namespace app\controllers;

use app\models\Psychic;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Получить догадки экстрасенсов
     */
    public function actionGetPsychicGuesses()
    {
        $psychics_numbers = $this->GuessesNum();
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = $psychics_numbers;
    }

    /**
     * Формирования чисел для каждого экстрасенса
     * @return array|mixed
     */
    public function GuessesNum()
    {
        $psychics = Psychic::getPsychics();
        $i = 0;
        foreach ($psychics as $psychic)
        {
            $psychics[$i]['number'] = rand(0, 99);
            $i++;
        }
        return $psychics;
    }

    public function PrintR($array)
    {
        echo '<pre>';
        print_r($array);
        echo '</pre>';
    }
}
