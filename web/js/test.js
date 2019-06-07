
    let user_id = CheckUser();
    GuessesHistory(user_id);
    getUserNumbers(user_id);

    /**
     * Проверка на существовании ID для пользователя
     * Если есть, то возвращает ID, иначе создает и возвращает
     */
    function CheckUser()
    {
        if(localStorage.getItem('UserId'))
        {
            return localStorage.getItem('UserId');
        }
        id = Math.random();
        localStorage.setItem('UserId', id);
        return id;
    }


    /**
     * Получение истории загадок пользователя
    */
    function getUserNumbers(user_id) {
        $.ajax({
            url: "user/get-user-numbers",
            type: 'GET',
            data : {user_id : user_id},
            success: function(result)
            {
                $('#tbody_user_num').empty();
                for (let i=0;i< result.numbers.length;i++)
                {
                    let num = result.numbers[i].num;
                    let date = result.numbers[i].date_time;
                    $('#tbody_user_num').append("<tr><td>"+num+"</td><td>"+date+"</td></tr>")
                }
            }
        });
    }

    /**
     * Сохранение число пользователя на сервере
     */
    function SaveUserNumHistory(user_id, user_num, user_psychics_guesses)
    {
        $.ajax({
            url: "user/save-num-history",
            type: 'POST',
            data : {
                    user_id : user_id,
                    user_num : user_num, 
                    user_psychics_guesses:user_psychics_guesses
                },
            success: function(result)
            {
                if(result.errors !== 'undefined' && result.errors.length === 0 )
                {

                    packUserNumberHistory(result.user_numbers_history);
                    packPsychicsHistory(result.psychics_guesses_history, result.count);
                }
                else
                {
                    alert(result.errors)
                }

            },
            errors: function(result)
            {
                console.log(result);
            }
        });
    }

    /**
     * Функкция получения истории догадок каждого экстрасенса.
     */
    function GuessesHistory(user_id)
    {
        $.ajax({
            url: "user/get-guesses-history",
            type: 'GET',
            data : {user_id : user_id},
            success: function(result)
            {
                console.log(result);
                let count = result.count;
                packPsychicsHistory(result.history, count);
            }
        });
    }

    let number;
    $("#save_number").click(function(){
        number = $("#num").val();
       $("#num").val('');
        getPsychicsGuesses();
        //SaveUserNumHistory(number);
    });

    /**
     *
     */
    function getPsychicsGuesses()
    {
        $('#renum').val('');
        $.ajax({
            url: "user/get-psychics-guesses",
            type: 'POST',
            // data : {user_id : user_id},
            success: function(result)
            {
                $('#tbody_psychic_nums').empty();
                var user_num = $('#renum').val();
                $("#myModal").modal();
                for (let i=0;i< result.length;i++)
                {
                    let num = result[i].num;
                    let name = result[i].name;
                    $('#tbody_psychic_nums').append("<tr><td>"+name+"</td><td>"+num+"</td></tr>");
                }
               
                user_psychics_guesses = result;
            }, 
            errors: function(result){
                console.log(result);
            }
        });
    }

    var user_psychics_guesses = [];
    $('#save_history').click(function(){
        let user_num = $('#renum').val();
            SaveUserNumHistory(user_id,user_num, user_psychics_guesses);
            $("#myModal").modal('toggle');
    });

/**
 *  Метод заполнения таблицы загадок экстрасенсов
 **/
function packPsychicsHistory(result, count)
    {
        $('#tbody_histoty_psychics').empty();
        $('#tbody_dilever_rate').empty();
        console.log(count);
        // console.log(result.errors);
        let myArr = [];
        for (let i= 0;i< result.length;i++)
        {
            let name = result[i].name;
            let history = result[i].history;
            let correct_answer = result[i].correct_answer;
            let delivery_rate = result[i].delivery_rate;
            $('#tbody_dilever_rate').append("<tr><td>"+name+"</td><td>"+delivery_rate+"</td><td>"+correct_answer+" из " +count+"</td></tr>");
            for (let j=0;j< history.length;j++)
            {
                let num = history[j].num;
                let date_time = history[j].date_time;
                let guessed = history[j].guessed;
                if(guessed === 0) guessed = 'Нет';
                else guessed = 'Да';
                if(name === 'undefined') name = '';
                myArr.push({
                    'date_time' : date_time,
                    'name' : name,
                    'num' : num,
                    'guessed' : guessed,

                });
            }
        }
        myArr.sort(function(a, b){
            var keyA = new Date(a.date_time),
                keyB = new Date(b.date_time);
            // Compare the 2 dates
            if(keyA > keyB) return -1;
            if(keyA < keyB) return 1;
            return 0;
        });
        for(let i = 0; i< myArr.length; i++)
        {
            $('#tbody_histoty_psychics').append("<tr><td>"+myArr[i].name+"</td><td align='center'>"+myArr[i].num+"</td><td>"+myArr[i].guessed+"</td><td>"+myArr[i].date_time+"</td></tr>");
        }
        console.log(myArr);
    }

    function packUserNumberHistory(result) {
        $('#tbody_user_num').empty();
        for (let i=0;i< result.length;i++)
        {
            let num = result[i].num;
            let date = result[i].date_time;
            $('#tbody_user_num').append("<tr><td>"+num+"</td><td>"+date+"</td></tr>")
        }
    }