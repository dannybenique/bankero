<?php
  class cWebConfig{
    protected $prot;
    protected $host;
    protected $port;
    protected $site;

    public function cWebConfig(){ //constructor
      $this->prot = "https://"; //protocolo [https, http]
      $this->host = $_SERVER["SERVER_ADDR"];
      $this->port = $_SERVER["SERVER_PORT"];
      $this->site = explode("/",$_SERVER["PHP_SELF"])[1];
    }

    public function getURL() { return $this->prot.$this->host.":".$this->port."/".$this->site; }
  }
  $webconfig = new cWebConfig();
?>
