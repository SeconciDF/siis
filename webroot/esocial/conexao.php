<?php

class Conexao extends PDO {

    private $engine;
    private $host;
    private $database;
    private $user;
    private $pass;

    public function __construct($options) {
        $this->engine = 'mysql';
        $this->host = $options['host'];
        $this->database = $options['database'];
        $this->user = $options['user'];
        $this->pass = $options['pass'];


        $dns = $this->engine . ':dbname=' . $this->database . ";host=" . $this->host;
        parent::__construct($dns, $this->user, $this->pass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
    }

    public function select($sql = '') {
        try {
            $query = $this->prepare($sql);
            $query->execute();
            return $query->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            echo $e->getMessage(); exit;
        }
    }

    public function insert($sql = '') {
        try {
            $query = $this->prepare($sql);
            $query->execute();
        } catch (Exception $e) {
            echo $e->getMessage();exit;
        }
    }

    public function command($sql = '') {
        try {
            $query = $this->prepare($sql);
            $query->execute();
        } catch (Exception $e) {
            echo $e->getMessage();exit;
        }
    }

}
