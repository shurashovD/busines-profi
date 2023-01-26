<?php

$hide = " d-none";
$hide_empty = "";
$tasks = [];
if ( isset($data['tasks']) && (count($data['tasks']) > 0) )
{
    $hide = "";
    $hide_empty = "d-none";
    $tasks = $data['tasks'];
}

?>

<div class="container py-5">

    <h3>Задачи</h3>

    <form class="mt-4 mb-5" method="POST" action="/tasks/create">
        <div class="row">
            <div class="col">
                <input type="text" class="form-control w-100" placeholder="Текст задачи" name="text" id="create-input">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Создать задачу</button>
            </div>
        </div>
    </form>

    <table class="table d-none" id="tasks-table">
        <thead>
            <tr>
                <th class="align-middle">Задача</th>
                <th class="text-center align-middle">Дата</th>
                <th class="text-center align-middle">Важное</th>
                <th class="text-center align-middle">Выполнено</th>
                <th class="text-center align-middle">Изменить</th>
                <th class="text-center align-middle">Удалить</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <p class="fs-3 text-muted text-center d-none" id="empty">Нет задач</p>

    <nav class="d-none" id="pagination">
        <ul class="pagination justify-content-center"></ul>
    </nav>

    <div class="position-fixed top-0 bottom-0 start-0 end-0 bg-light bg-opacity-50 d-none" id="loader">
        <div class="position-absolute top-50 start-50 translate-middle">
            <div class="spinner-border text-secondary"></div>
        </div>
    </div>

    <div class="modal" tabindex="-1" id="modal">
        <form class="modal-dialog" method="POST" action="/tasks/update">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Обновить задание</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id">
                <input type="text" class="form-control me-2" name="text" />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="submit" class="btn btn-primary">OK</button>
            </div>
            </div>
        </form>
    </div>

    <div class="position-fixed top-0 start-0 end-0 p-2" id="alert-container"></div>

</div>