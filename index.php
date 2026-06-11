<h1>To-Do-List</h1>

<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

require_once __DIR__ . '/src/database.php';

$host = 'localhost';
$dbname = 'to-do-listdb';
$username = 'root';
$password = '';

$db = new DataBase($host, $dbname, $username, $password);
print_r($_SESSION['JoinDb']);

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])){
    $name = $_POST['name'];
    $description = $_POST['description'];
    $urgency = $_POST['urgency'];
    if($urgency === 'yes'){
        $urgency = 1;
    } else {
        $urgency = 0;
    }


    $db->insert('todolist', [
        'name' => $name,
        'description' => $description,
        'urgency' => $urgency
    ]);
    header('Location: index.php');
    exit;
}

$list = $db->getAll("SELECT * FROM todolist WHERE id > :id", ['id' => 0]);

if(isset($_POST['submitUpOrDel']) && $_POST['vibor'] === 'delete'){
    $db->delete('todolist', 'id = :id', ['id' => $_POST['task_id']]);
    header('Location: index.php');
    exit;
}

if(isset($_POST['new_name']) && isset($_POST['new_description']) && isset($_POST['new_urgency']) && $_POST['vibor'] === 'update'){
    $newName = $_POST['new_name'];
    $newDescription = $_POST['new_description'];
    $newUrgensy = $_POST['new_urgency'];
    if($newUrgensy === 'yes'){
        $newUrgensy = 1;
    } else {
        $newUrgensy = 0;
    }
    $db->update('todolist', ['name' => $newName, 'description' => $newDescription, 'urgency' => $newUrgensy], 'id = :id', ['id' => $_POST['task_id']]);

    header('Location: index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./css/body.css">
    <title>To-DoFrog-List</title>
</head>
<body>
    <div class="main-div-form">
    <form action="index.php" method="POST" class="main-form">
        <label class="form-name">
            Имя
            <input type="text" name="name">
        </label>
        <label class="form-description">
            Описание
            <input type="text" name="description">
        </label>
        <p>Срочность</p>
        <label class="form-urgency">
            <input type="radio" name="urgency" value="yes"> Да
        </label>
        <label class="form-urgency">
            <input type="radio" name="urgency" value="no"> Нет
        </label>
        <button type="submit" name="submit" class="form-btn">Добавить задачу</button>
    </form>
</div>

<div class="notes">
    <h2>Ваши записки:</h2>
    <?php foreach($list as $lis): ?>
        
        <div class="notes-note">
            <ul class="notes-note-ul">
                <li class="notes-note-id">ID: <?= $lis['id'] ?></li>
                <li class="notes-note-name">Name: <?= $lis['name'] ?></li>
                <li class="notes-note-dis">Description: <?= $lis['description'] ?></li>
                <li class="notes-note-urgency">Urgency: <?= $lis['urgency'] ?></li>
            </ul>
            <div class="notes-note-div-forme">
            <form action="index.php" method="POST" class="notes-note-form">
            <input type="hidden" name="task_id" value="<?= $lis['id'] ?>">
            <label class="notes-note-update">
                <input type="radio" name="vibor" value="update"> Обновить
            </label>
            <label class="notes-note-delite">
                <input type="radio" name="vibor" value="delete"> Удалить
            </label>
            <button type="submit" name="submitUpOrDel" class="notes-note-btn">Применить</button>
            </form>
            </div>
            
        </div>
    <?php endforeach ?>
</div>

<script>
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const selectedRadio = this.querySelector('input[name="vibor"]:checked');
        
        if(selectedRadio && selectedRadio.value === 'update') {
            e.preventDefault();
            
            // Получаем текущие значения из отображаемых данных
            const taskDiv = this.closest('.notes-note');
            const items = taskDiv.querySelectorAll('li');
            const currentName = items[1].textContent.replace('Name: ', '');
            const currentDesc = items[2].textContent.replace('Description: ', '');
            const currentUrgency = items[3].textContent.replace('Urgency: ', '');
            
            // Запрашиваем новые значения
            const newName = prompt('Введите новое имя:', currentName);
            if(!newName) return;
            
            const newDesc = prompt('Введите новое описание:', currentDesc);
            if(!newDesc) return;
            
            const newUrgency = prompt('Введите важность (yes/no):', currentUrgency);
            if(!newUrgency || (newUrgency !== 'yes' && newUrgency !== 'no')) {
                alert('Важность должна быть yes или no');
                return;
            }
            
            // Добавляем скрытые поля с новыми значениями
            const nameInput = document.createElement('input');
            nameInput.type = 'hidden';
            nameInput.name = 'new_name';
            nameInput.value = newName;
            this.appendChild(nameInput);
            
            const descInput = document.createElement('input');
            descInput.type = 'hidden';
            descInput.name = 'new_description';
            descInput.value = newDesc;
            this.appendChild(descInput);
            
            const urgencyInput = document.createElement('input');
            urgencyInput.type = 'hidden';
            urgencyInput.name = 'new_urgency';
            urgencyInput.value = newUrgency;
            this.appendChild(urgencyInput);
            
            this.submit();
        }
    });
});
</script>
</body>
</html>

