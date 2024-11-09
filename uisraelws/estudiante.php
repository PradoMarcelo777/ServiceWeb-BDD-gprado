<?php
include "config.php";
include "utils.php";

$dbConn =  connect($db);

if ($_SERVER['REQUEST_METHOD'] == 'GET')
{
    if (isset($_GET['codigo']))
    {
      //Mostrar un post
      $sql = $dbConn->prepare("SELECT * from estudiante  where codigo=:codigo");
      $sql->bindValue(':codigo', $_GET['codigo']);
      $sql->execute();
      header("HTTP/1.1 200 OK");
      echo json_encode(  $sql->fetch(PDO::FETCH_ASSOC)  );
      exit();
	  }
      else {
        //Mostrar lista de post
        $sql = $dbConn->prepare("SELECT * FROM estudiante");
        $sql->execute();
        $sql->setFetchMode(PDO::FETCH_ASSOC);
        header("HTTP/1.1 200 OK");
        echo json_encode( $sql->fetchAll()  );
        exit();
      }
  }

  if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
    $input = $_POST;
    $sql = "INSERT INTO estudiante
          ( nombre, apellido, edad)
          VALUES
          ( :nombre, :apellido, :edad)";
    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);
    $statement->execute();

    $postCodigo = $dbConn->lastInsertId();
    if($postCodigo)
    {
      $input['codigo'] = $postCodigo;
      header("HTTP/1.1 200 OK");
      echo json_encode($input);
      exit();
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
{
	$codigo = $_GET['id'];
  $statement = $dbConn->prepare("DELETE FROM  estudiante where id=:id");
  $statement->bindValue(':id', $codigo);
  $statement->execute();
	header("HTTP/1.1 200 OK");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'PUT')
{
    $input = $_GET;
    $postCodigo = $input['id'];
    $fields = getParams($input);

    $sql = "
          UPDATE estudiante
          SET $fields
          WHERE id=:id";

    $statement = $dbConn->prepare($sql);
    bindAllValues($statement, $input);

    $statement->execute();

     // Recuperar los datos actualizados
     $sqlSelect = "SELECT * FROM estudiante WHERE id = :id";
     $statementSelect = $dbConn->prepare($sqlSelect);
     $statementSelect->bindValue(':id', $postCodigo);
     $statementSelect->execute();
     
     // Obtener los resultados y devolverlos en formato JSON
     $updatedData = $statementSelect->fetch(PDO::FETCH_ASSOC);
     
     // Enviar la respuesta como JSON
     header("Content-Type: application/json");
     echo json_encode($updatedData);
     
    exit();
}
?>
