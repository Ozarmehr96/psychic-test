<?php
/**
 * Created by PhpStorm.
 * User: Озармехр
 * Date: 05.06.2019
 * Time: 22:04
 */

namespace app\controllers;
ob_start();

use app\models\Psychic;
use Webmozart\Assert\Assert;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use app\models\User;
use \yii\web\Response;

class UserController extends Controller
{
    /**
     * Метод сохранения введенное число пользователем и догадки экстрасенсов
     * Метод возвращает на фронт массив данных.
     * Алгоритм:
     * 1. Сохранить число введенное пользователем в историю
     * 2. Сохранить угаданные числа экстрасенсов в историю
     */
    public function actionSaveNumHistory()
    {
        $errors = [];
        $count_guesses_count =  0;                                                                                      // общее количество загаданных пользователем числа
        $user_numbers = array();
        $user_psychics_guesses_history = array();                                                                       // массив для хранения истории докадок экстрасенсов
        $post = Yii::$app->request->post();                                                                             // получение данных с сервера
        if(isset($post['user_id'], $post['user_psychics_guesses']) &&                                                   // если ИД пользователя передан, и угаданные числа экстрасенсов тоже переданы, то сохраняем их в сессию
            $post['user_id'] != "" && $post['user_psychics_guesses'] != '')
        {

            $user_id = $post['user_id'];
            $user_psychics_guesses = $post['user_psychics_guesses'];                                                    // получаем докадки экстрасенсов
            $user_num = (int)$post['user_num'];
            User::setUserGuessesCount($user_id);
            $count_guesses_count = User::getUserGuessesCount($user_id);                                                 // получаем общее количество догадок пользовталея
            if($user_num >= 10 && $user_num <= 99)
            {

                /**
                 * Сохраняем историю ввода чисел пользователя
                 */
                $user_model = new User();
                $user_model->setUserNumber($user_id, $user_num);                                                        // сохраняем в сессию число введеное пользователем
                $user_numbers = $user_model->getUserNumbers($user_id);                                                  // получаем историю чисел пользователя
                ArrayHelper::multisort($user_numbers, ['date_time'], SORT_DESC);                          // отсортируем их по дате(по убиванию)
                $psyController = new Psychic();

                foreach ($user_psychics_guesses as $user_psychic_guesse)                                                // для каждого экстрасенса сохраняем его догадку в сессию
                {
                    $user_psychic_guesse_num = $user_psychic_guesse['num'];
                    if($user_psychic_guesse_num == $user_num)                                                           // если экстрасенс угадал число пользователя, то уровень доставерноси повышается у него
                    {
                        $psyController->setToHistory($user_id, $user_psychic_guesse['id'], $user_psychic_guesse_num, 1);
                    }
                    else
                    {
                        $psyController->setToHistory($user_id, $user_psychic_guesse['id'], $user_psychic_guesse_num);   // иначеесли экстрасенс Не угадал число пользователя, то уровень доставерноси снижается у него
                    }

                }
                $user_psychics_guesses_history = (new Psychic())->getHistoryFullInfo($user_id);                         // получаем историю догадок экстрасенсов
                $this->CheckDeliveryRate($user_psychics_guesses_history, $user_id);                            // проверяем уровни доставерности ответов каждого экстрасенса
            }
            else
            {
                $errors[] = "Число должен быть в диапазоне от 10 до 99";
            }

        }
        else
        {
            $errors[] = "Входные параметры не переданы";
        }

        $result = array('errors' => $errors, 'user_numbers_history' => $user_numbers, 'psychics_guesses_history' => $user_psychics_guesses_history, 'count' => $count_guesses_count);
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = $result;
    }

    /**
     * Метод получения истори
     */
    public function actionGetUserNumbers()
    {
        $errors = array();
        $arr = array();
        $user_numbers = array();
        $post = Yii::$app->request->get();
        if(isset($post['user_id']) && $post['user_id'] != "")
        {
            $user_id = $post['user_id'];
            $user_numbers = (new User())->getUserNumbers($user_id);                                                     // получаем историю введенных пользователем числа
            ArrayHelper::multisort($user_numbers,['date_time'], SORT_DESC );                              // отсортируем их по дате(по убиванию)
        }
        else
        {
            $errors[] = "Идентификатор пользователя не передан";
            $errors[] = $post;
        }
        $result = array('errors' => $errors, 'numbers' => $user_numbers);
       Yii::$app->response->format = Response::FORMAT_JSON;
       Yii::$app->response->data = $result;
    }

    /**
     * Метод получения догадок всех эктрасенсов
     */
    public function actionGetGuessesHistory()
    {
        $post = Yii::$app->request->get();
        $history = [];
        $history = 0;
        $errors = [];
        if(isset($post['user_id']) && $post['user_id'] != '')
        {
            $user_id = $post['user_id'];
            $count_guesses_count = User::getUserGuessesCount($user_id);                                                 // получаем общее количество загадок пользователя
            $psyController = new Psychic();
            $history = $psyController->getHistoryFullInfo($user_id);                                                    // получаем докадки экстрасенсов
            $this->CheckDeliveryRate($history, $user_id);                                                      // отсортируем их по убиванию даты
        }
        else
        {
            $errors[] = "Идентификатор пользователя не передан";
        }

        $result = array('errors' => $errors, 'history' => $history, 'count' => $count_guesses_count);
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = $result;
    }

    /**
     * Метод генерирования случайных чисел для каждого экстрасенса (Угадки экстрасенса)
     * @return array - возвращает массив данных
     */
    public function generateNumPsychics()
    {
        $psychics = (new Psychic())->getPsychics();
        $i = 0;
        foreach ($psychics as $psychic) 
        {
            $psychics[$i]['num'] = rand(10, 99);
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

    /**
     * Метод получения докадок экстрасенсов
     */
    public function actionGetPsychicsGuesses()
    {
        $psychics = $this->generateNumPsychics();
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = $psychics;
    }

    /**
     * Метод подсчета уровня достоверности каждого экстрасенса.
     * Получаем общее количество загадок. Для каждого экстрасенса получим количетсво правильных ответов.
     * Количетсов правильных ответов * на 100 и делим на общее количество загадок. В итоге получим коэффицент уровня достоверности
     * каждого экстрасена
     * @param $psychics - массив экстрасенсов
     * @param $user_id - идентификатор пользователя.
     */
    public function CheckDeliveryRate(&$psychics,$user_id)
    {
        if($psychics)
        {
            $i = 0;
            $guesses_count = User::getUserGuessesCount($user_id);
            foreach ($psychics as $psychic)
            {
                $correct_answer = (int)$psychic['correct_answer'];
                $result = ($correct_answer * 100) / $guesses_count;
                $result = round($result, 1);
                $psychics[$i]['delivery_rate'] = $result;
                $i++;
            }
        }
    }
}