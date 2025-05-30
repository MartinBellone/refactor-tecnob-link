<?php
require_once("./models/subjects.php");

function handleGet($conn) 
{
    if (isset($_GET['id'])) 
    {
        $result = getSubjectById($conn, $_GET['id']);
        echo json_encode($result->fetch_assoc());
    } 
    else 
    {
        $result = getAllSubjects($conn);
        $data = [];
        while ($row = $result->fetch_assoc()) 
        {
            $data[] = $row;
        }
        echo json_encode($data);
    }
}

function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    try {
         createSubject($conn, $input['name']);
        echo json_encode(["message" => "Materia creada correctamente"]);
    } catch (mysqli_sql_exception $e) {
        if ($e->getCode() == 1062) {
            http_response_code(409); // Conflicto
            echo json_encode(["error" => "La materia ya está registrada"]);
        } else {
            http_response_code(500); // Otro error
            echo json_encode(["error" => "Error al agregar materia"]);
        }
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    if (updateSubject($conn, $input['id'], $input['name'])) 
    {
        echo json_encode(["message" => "Materia actualizada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo actualizar"]);
    }
}

function handleDelete($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    if (deleteSubject($conn, $input['id'])) 
    {
        echo json_encode(["message" => "Materia eliminada correctamente"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>