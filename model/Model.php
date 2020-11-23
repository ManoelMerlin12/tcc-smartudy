<?php
abstract class Model {
    
    public $host = 'mysql';
    public $dbName = 'smartudy';
    public $user = 'root';
    public $password = 'secret';
    public $conexao;
    protected $varType = array(
        'boolean' => PDO::PARAM_BOOL,
        'integer' => PDO::PARAM_INT,
        'string' => PDO::PARAM_STR,
    );

    public function __construct()
    {
        $this->conexao = $this->connect();
    }
    
    public function connect () 
    {
        try {
            $connection = new PDO(
                "mysql:host=$this->host;dbname=$this->dbName",
                "$this->user",
                "$this->password"
            );

            $connection->exec('SET NAMES utf8');
            return $connection;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public function save($table, $data)
    {
        try {
            $endArray = end(array_keys($data));
            $columnsToInsert = '';
            $valuesToInsert = '';

            foreach ($data as $key => $value) {
                $columnsToInsert .= "$key" . ($endArray !== $key ? ', ' : '');
                $valuesToInsert .= ":$key" . ($endArray !== $key ? ', ' : '');
            }

            $query = "INSERT INTO $table ($columnsToInsert, created) VALUES ($valuesToInsert, NOW())";
        
            $query = $this->conexao->prepare($query);
            foreach ($data as $key => $value) {
                $query->bindValue(":$key", $value);
            }
            $query->execute();
            return true;
        } catch (\PDOException $exception) {
            return $exception->getMessage();
        }
    }

    public function update($table, $data) {
        $strToSave = '';
        $id = $data['id'];
        unset($data['id']);
        $endArray = end(array_keys($data));

        foreach ($data as $key => $value) {
            $strToSave .= "$key = :$key" . ($key === $endArray ? ' ' : ', ');
        }

        $query = "UPDATE $table SET $strToSave WHERE id = " . $id . "";
        $stmt = $this->conexao->prepare($query);   
        
        foreach ($data as $key => $value) {
            $stmt->bindParam(":$key", $value, $this->varType[gettype($value)]);       
        }

        $stmt->execute(); 
    }

    public function where($table, $param, $value)
    {
        try {
            $query = $this->conexao->prepare("SELECT * FROM $table WHERE $param = '".$value."'");
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);
            return $result;
        } catch (\PDOException $exception) {
            return $exception->getMessage();
        }
    }

    public function makeQuery($query) {
        try {
            $query = $this->conexao->prepare($query);
            $query->execute();
            $this->queryResult = $query->fetchAll(PDO::FETCH_ASSOC);
            return $this->queryResult;
        } catch (\PDOException $exception) {
            return $exception->getMessage();
        }
    }

}