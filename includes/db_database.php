<?php
  class database{
    protected $host;
    protected $dbname;
    protected $user;
    protected $pass;
    public $conn;

    public function database(){ //constructor
      $this->host   = "192.168.1.6";
      $this->dbname = "financiero";
      $this->user   = "sa";
      $this->pass   = "Cucvcn113@";
      $this->conn   = sqlsrv_connect($this->host,array("Database"=>$this->dbname,"UID"=>$this->user,"PWD"=>$this->pass));
    }

    //comandos SQL
    public function select($cadsql){
      $params = array();
      $options = array( "Scrollable" => SQLSRV_CURSOR_KEYSET );
      return sqlsrv_query($this->conn,$cadsql,$params,$options);
    }
    public function update($query,$parametros){ return sqlsrv_query($this->conn,$query,$parametros); }
    public function insert($query,$parametros){ return sqlsrv_query($this->conn,$query,$parametros); }
    public function delete($query,$parametros){ return sqlsrv_query($this->conn,$query,$parametros); }

    public function query($query){ return sqlsrv_query($this->conn,$query); }

    public function num_rows($rs){ return sqlsrv_num_rows($rs);   }
    public function has_rows($rs){ return sqlsrv_has_rows($rs); }
    public function fetch_array($rs){ return sqlsrv_fetch_array($rs); }
    public function close() { sqlsrv_close($this->conn); }
  }
  $db = new database();
?>
