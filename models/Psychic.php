<?php
/**
 * Created by PhpStorm.
 * User: Озармехр
 * Date: 05.06.2019
 * Time: 21:14
 */

namespace app\models;


use yii\helpers\ArrayHelper;

class Psychic
{
    public static $user_psychic_session_key = 'UserPsychicHistory_';

    /**
     * @return mixed Метод создания/получения списк экстрасенсов
     */
    public static function getPsychics()
    {
        $psychics[0]['id'] = 15;
        $psychics[0]['name'] = 'Иванова Е.И';

        $psychics[1]['id'] = 16;
        $psychics[1]['name'] = 'Александров А.В';

        $psychics[2]['id'] = 17;
        $psychics[2]['name'] = 'Ивачева Ж.Г';
        
        $psychics[3]['id'] = 18;
        $psychics[3]['name'] = 'Шипилов И.К';
        return $psychics;
    }

    /**
     *
     * Метод сохранения угаданное число экстрасенсом
     * Для каждого пользователя хранится конкретная история докадок экстрасенсов.
     * Экстрасенсы могут участвовать в тестированиях нескольких пользователей, но история хранится конкретно для каждого
     * пользователя, то есть под конкретным пользовталем история докадок экстрасенсов
     * @param $user_id - идентификатор пользователя
     * @param $psychic_id - идентификатор экстрасенса
     * @param $number - угаданное число экстрасенсом
     * @param int $guessed - правильно угадал ли. По умолчанию указано нет.
     */
    public function setToHistory($user_id, $psychic_id, $number,$guessed = 0)
    {
        $psychics = $this->getHistory($user_id);//получаю догадки экстрасенсов
        if($psychics)                                                                                                   // если они есть, то в существующий массив добаляем значения
        {
            $key = array_search($psychic_id, array_column($psychics, 'id'));                                     // проверяем, существует ли история для конкретного экстрасенса, если до то в новый массив добаим значения и сохраняем в сессию
            if($key !== FALSE)
            {

                $count = $psychics[$key]['correct_answer'];                                                             // получаем количество ответов
                $count += $guessed;                                                                                     // суммируем на 0 или 1
                $psychics[$key]['correct_answer'] = $count;

                $psychics[$key]['history'][] = array(
                    'num' => $number,
                    'date_time' => date('Y-m-d H:i:s'),
                    'guessed' => $guessed,
                );
                \Yii::$app->session->set($this->buildKey($user_id), $psychics);                                         // записывем в сессию
            }
            else                                                                                                        // если для сенсора еще история догадок не была создана, то создадим массив данных и добавим в сессию
            {
                $psychics[] = array(
                    'id' => $psychic_id,
                    'correct_answer' => $guessed,
                    'history' => array(array(
                        'num' => $number,
                        'date_time' => date('Y-m-d H:i:s'),
                        'guessed' => $guessed,
                    ))
                );
                \Yii::$app->session->set($this->buildKey($user_id), $psychics);
            }
        }
        else
        {
            $data = array(
                'id' => $psychic_id,
                'correct_answer' => $guessed,
                'history' => array(array(
                    'num' => $number,
                    'date_time' => date('Y-m-d H:i:s'),
                    'guessed' => $guessed,
                ))
            );
            \Yii::$app->session->set($this->buildKey($user_id), array($data));
        }
    }

    /**
     * Метод создания ключа для списка экстрасенсов для пользователя.У каждого пользователя свои экстрасенсы
     * @param $user_id - идентификатор пользователя
     * @return string - созданный ключ.
     */
    public function buildKey($user_id)
    {
        return self::$user_psychic_session_key.$user_id;
    }

    /**
     * @param $user_id - идентификатор пользователя
     * @return mixed
     */
    public function getHistory($user_id)
    {
        $key = $this->buildKey($user_id);
        return \Yii::$app->session->get($key);
    }

    public function remove($user_id)
    {
        $key = $this->buildKey($user_id);
        \Yii::$app->session->remove($key);
    }

    /**
     * Метод получения догадок экстрасенсов с их именами.
     * @param $user_id - идентификатор пользователя
     * @return mixed - массив данные либо false
     */
    public function getHistoryFullInfo($user_id)
    {
        $history = $this->getHistory($user_id);                                                                         // получаем историю догадок экстрасенсов
        $psychics = self::getPsychics();                                                                                // получаем список экстрасенсов
        if($history)
        {
            $i = 0;
            foreach ($history as $item)                                                                                 // для каждого экстрасенса находим его имя и добавим в массив
            {
                foreach ($psychics as $psychic)
                {
                    if($psychic['id'] == $history[$i]['id'])
                    {
                        $history[$i]['name'] = $psychic['name'];
                    }
                }
                $i++;
            }
        }

        return $history;                                                                                                // возвращаем данные
    }
}