<?php

namespace app\models;
use Yii;

/**
 * Класс по работе с пользователем в части добаления/редактирования/удаления из сессии
 * Class User
 * @package app\models
 */
class User
{
    public static $session_user_numbers_key = 'UserNumbers_';
    public static $session_user_guesses_count_key = 'UserGuesses_'; // общее количество догадок пользователя
    public $session;

    public function __construct()
    {
        $this->session = Yii::$app->session;
    }

    /**
     * Метод создания ключа сессии истории введенных чисел пользователем
     * @param $user_id - идентификатор пользователя
     * @return string - созданный ключ
     */
    public function buildKeyUserNum($user_id)
    {
        return self::$session_user_numbers_key.$user_id;
    }

    /**
     * Метод получения числа введенным пользователем
     * @param $user_id - идентификатор пользователя
     * @return mixed
     */
    public function getUserNumbers($user_id)
    {
        $key = $this->buildKeyUserNum($user_id);
        return $this->session->get($key);
    }

    /**
     * Метод добавления числа введенного пользователя в сессию.
     * У каждого пользователя есть своя история ввода чисел.
     * @param $user_id - идентификатор пользователя
     * @param $numbers - число
     */
    public function setUserNumber($user_id, $number)
    {
        $user_numbers = $this->getUserNumbers($user_id);
        if($user_numbers)
        {
            $user_numbers[] = array(
                'num' => $number,
                'date_time' => date('Y-m-d H:i:s')
            );
        }
        else
        {
            $user_numbers = array();
            $user_numbers[] = array(
                'num' => $number,
                'date_time' => date('Y-m-d H:i:s')
            );
        }
        $key = $this->buildKeyUserNum($user_id);
        return $this->session->set($key, $user_numbers);
    }

    /**
     * Метод добавления увелечения количество загадок конкретного пользовтеля.
     * Когда пользователь загадывает, то при плучении данных с сервера, нужно увеличить количество
     * @param $user_id - идентификатор пользовтеля
     */
    public static  function setUserGuessesCount($user_id)
    {
        $session = Yii::$app->session;
        $key = self::buildKeyUserGuessesCount($user_id);                                                                // создаем ключ
        $count = $session->get($key);                                                                                   // получем количетво докадок из сессии
        if($count)                                                                                                      // если докадки уже есть, то увеличиваем количество  на +1
        {
            $session->set($key, ++$count);
        }
        else                                                                                                            // иначе если в сессии еще записи не было о количетсве загадок конкретного пользователя, то добавим в сессию
        {
            $session->set($key, 1);
        }
    }

    /**
     * Метод создания ключа для общее количество загадок конкретного пользователя.
     * Создан для того, чтобы в дальнейшей со структурой ключа проблем не было.
     * @param $user_id - идентификтор пользователя
     * @return string - созданный ключ
     */
    public static function buildKeyUserGuessesCount($user_id)
    {
        return self::$session_user_guesses_count_key.$user_id;
    }

    /**
     * Метод получения общее количество загадок для конкретного пользователя из сессии
     * @param $user_id - идентификтор пользователя
     * @return mixed
     */
    public static function getUserGuessesCount($user_id)
    {
        $key = self::buildKeyUserGuessesCount($user_id);
        return Yii::$app->session->get($key);
    }

}
