<?php
class DataBase {
    private $pdo;

    public function __construct($host, $dbname, $username, $password) {
        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $_SESSION['JoinDb'] = 'База данных подчлючена';
        } catch(PDOException $e) {
            $_SESSION['JoinDb'] = 'База данных не подчлючена';
            die("Ошибка подключения: " . $e->getMessage());
        }
    }

    public function query($sql, $params = []){
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function getAll($sql, $params = []){
        return $this->query($sql, $params)->fetchAll();
    }
    public function getOne($sql, $params = []){
        return $this->query($sql, $params)->fetch();
    }

    public function insert($table, $data) {
        $colomn = implode(', ', array_keys($data));
        $placeHold = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO $table ($colomn) VALUES ($placeHold)";
        $this->query($sql, $data);

        return $this->pdo->lastInsertId();
    }

    public function update($table, $data, $where, $WhereParams = []){
        $set = [];
        foreach ($data as $key => $value){
            $set[] = "$key = :$key";
        }
        $set = implode(', ', $set);

        $sql = "UPDATE $table SET $set WHERE $where";
        $params = array_merge($data,$WhereParams);

        return $this->query($sql, $params)->rowCount();
    }

    public function delete($table, $where, $params = []){
        $sql = "DELETE FROM $table WHERE $where";
        return $this->query($sql, $params)->rowCount();
    }
}