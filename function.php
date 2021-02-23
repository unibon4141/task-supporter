<?php
function dbConnection() {
  $url = parse_url(getenv("CLEARDB_DATABASE_URL"));
  $db_name = substr($url["path"], 1);
  $db_host = $url["host"];
  $username = $url["user"];
  $password = $url["pass"];
  $dsn = "mysql:dbname=".$db_name.";host=".$db_host.";charset=utf8";


  $options = array(
     PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
     PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      
  );
  $dbh = new PDO($dsn,$username,$password,$options);
  return $dbh;
}  

    

?>