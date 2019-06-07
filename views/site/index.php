<?php

/* @var $this yii\web\View */

$this->title = 'Тестирование экстрасенсов';
?>
<div class="container-fluid">
    <div class="container">
            <div class="container">
                <div class="col-md-4">
                    <p>Загадайте 2-х значное число</p>
                    <form>
                        
                        <div class="input-group">
                            <div class="input-group-btn">
                                <button class="btn btn-default" type="button" id = 'save_number'>
                                    <i>Загадать</i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        <div class="container">
            <div class="col-md-6">
                <h4>Уровни достоверности экстрасенсов</h4>
                <table class="table table-bordered"  style="height: 75%; overflow: paged-y">
                    <thead>
                    <tr>
                        <th>ФИО</th>
                        <th>Уроверь достоверности(%)</th>
                        <th>Количество ответов</th>
                    </tr>
                    </thead>
                    <tbody id = "tbody_dilever_rate">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="container">
            <div class="col-md-4">
                <h4>История введенных чисел</h4>
                <table class="table table-bordered"  style="height: 75%; overflow: paged-y">
                    <thead>
                    <tr>
                        <th>Число</th>
                        <th>Дата и время ввода</th>
                    </tr>
                    </thead>
                    <tbody id = "tbody_user_num">
                    </tbody>
                </table>
            </div>
            <div class="col-md-8">
                <h4>История догадок экстрасенсов</h4>
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ФИО экстрасенса</th>
                        <th>Число</th>
                        <th>Угадал</th>
                        <th>Дата и время</th>
                    </tr>
                    </thead>
                    <tbody id = "tbody_histoty_psychics">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
  <div class="modal fade" id="myModal">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Догадки экстрасенсов</h4>
        </div>
        <div class="modal-body">
           <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>ФИО экстрасенса</th>
                        <th>Число</th>
                    </tr>
                    </thead>
                    <tbody id = "tbody_psychic_nums">
                    </tbody>
            </table>
          <form>
              <p>Введите Ваше загаданное число</p>
              <div class="input-group">
                        <input  class="form-control" type="number" min="10" max="99" name = 'renum1' placeholder="Введите число"  id = 'renum' >
                        <div class="input-group-btn">
                            <button class="btn btn-default" type="button" id = 'save_history'>
                                <i>Отправить</i>
                            </button>
                        </div>
                    </div>
                </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
        </div>
      </div>
    </div>
  </div>
  
  <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="mi-modal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Загадали число?</h4>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" id="modal-btn-si">Да</button>
        <button type="button" class="btn btn-primary" id="modal-btn-no">Нет</button>
      </div>
    </div>
  </div>
</div>

</div>


