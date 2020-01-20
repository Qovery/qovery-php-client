<?php

class DatabaseConfiguration
{
  public $type;
  public $name;
  public $host;
  public $port;
  public $username;
  public $password;
  public $version;

  function __construct($object)
  {
    if (empty($object)) {
      return;
    }

    $this->type = $this->parse($object, "type");
    $this->name = $this->parse($object, "name");
    $this->host = $this->parse($object, "fqdn");
    $this->port = $this->parse($object, "port");
    $this->username = $this->parse($object, "username");
    $this->password = $this->parse($object, "password");
    $this->version = $this->parse($object, "version");
  }

  function parse($object, $key)
  {
    return $object[$key];
  }
}
