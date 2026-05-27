<h1>To-Do-List</h1>

<?php
session_start();

require_once __DIR__ . '/database.php';

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
    <title>Document</title>
</head>
<body>
    <div>
    <form action="index.php" method="POST">
        <label>
            Имя
            <input type="text" name="name">
        </label>
        <label>
            Описание
            <input type="text" name="description">
        </label>
        <p>Срочность</p>
        <label>
            <input type="radio" name="urgency" value="yes"> Да
        </label>
        <label>
            <input type="radio" name="urgency" value="no"> Нет
        </label>
        <button type="submit" name="submit">Добавить задачу</button>
    </form>
</div>

<div>
    <h2>Ваши записки:</h2>
    <?php foreach($list as $lis): ?>
        
        <div>
            <ul>
                <li>ID: <?= $lis['id'] ?></li>
                <li>Name: <?= $lis['name'] ?></li>
                <li>Description: <?= $lis['description'] ?></li>
                <li>Urgency: <?= $lis['urgency'] ?></li>
            </ul>
            <form action="index.php" method="POST">
            <input type="hidden" name="task_id" value="<?= $lis['id'] ?>">
            <label>
                <input type="radio" name="vibor" value="update"> Обновить
            </label>
            <label>
                <input type="radio" name="vibor" value="delete"> Удалить
            </label>
            <button type="submit" name="submitUpOrDel">Применить</button>
            </form>
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
            const taskDiv = this.closest('div');
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

