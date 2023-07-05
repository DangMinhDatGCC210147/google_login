<?php
    class Connect{
        public $server;
        public $user;
        public $password;
        public $dbName;

        public function __construct()
        {
            $this->server = "kfgk8u2ogtoylkq9.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
            $this->user = "drixkykl7zjgtxw3";
            $this->password = "f2xcfbcxhjz83h48";
            $this->dbName = "fqryt14r9eny9lyx";
        }

        //Option 1: Use mySQLi

        function connectToMySQL():mysqli{
            $conn_my = new mysqli($this->server,$this->user,$this->password,$this->dbName);
            if ($conn_my->connect_error){
                die("Failed".$conn_my->connect_error);
            }else{
                // echo "Connected!!!";
            }
            return $conn_my;
        }

        //Option 2: Use PDO
        function connectToPDO():PDO{
        try{
            $conn_pdo = new PDO("mysql:host=$this->server;
            dbname=$this->dbName", $this->user, $this->password);
            $conn_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connect to PDO";
        }catch(PDOException $e){
            die("Failed $e");
        }
        return $conn_pdo;
    } 
    }



    //test connect
    // $c = new Connect();
    // $c->connectToMySQL();

    // $c = new Connect();
    // $c->connectToPDO();
?>  