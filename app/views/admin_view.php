<div class="container-fluid">
    <div class="text-end">
        <a href="/main/logout">Выйти</a>
    </div>

    <div class="container py-4">
        
        <h3 class="mb-4">Пользователи</h3>

        <table class="table">
            <thead>
                <tr class="text-center align-middle">
                    <th scope="col">Почта</th>
                    <th scope="col">Кол-во задач</th>
                </tr>
            </thead>
            <tbody>
                
                <?php
                
                foreach ($data as $key => $value) {
                    echo ('
                        <tr class="text-center align-middle">
                            <td>' . $value['mail'] . '</td>
                            <td>' . $value['count(tasks.id)'] . '</td>
                        </tr>
                    ');
                }
                
                ?>
            </tbody>
        </table>
        
    </div>
</div>