 <?php
    $servername  = "localhost";
    $username    = "root";
    $password    = "";
    $dbname     = "idiscuss";

    $conn = new mysqli ($servername, $username , $password  , $dbname);
      if($conn->connect_error){
        die("you can not connect to the database due to ".$conn->connect_error);
  }

?>
