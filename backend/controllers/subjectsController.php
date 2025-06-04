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
         $result= createSubject($conn, $input['name']);
         if ($result)
            echo json_encode(["message" => "Materia creada correctamente"]);
        else
            throw new Exception("Error al agregar materia", $result->errno);
    } catch (Exception $e) {
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
    $result = deleteSubject($conn, $input['id']);

    if ($result === true) {
        echo json_encode(["message" => "Estudiante eliminado"]);
    } elseif ($result === "has_relations") {
        http_response_code(409);
        echo json_encode(["error" => "No se puede eliminar: el estudiante está inscripto en materias"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al eliminar estudiante"]);
    }
}
?>