<?php
require_once("./models/students.php");

function handleGet($conn) 
{
    if (isset($_GET['id'])) 
    {
        $result = getStudentById($conn, $_GET['id']);
        echo json_encode($result->fetch_assoc());
    } 
    else 
    {
        $result = getAllStudents($conn);
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
       $result=createStudent($conn, $input['fullname'], $input['email'], $input['age']);
        if ($result)
            echo json_encode(["message" => "Estudiante agregado correctamente"]);
        else
            throw new Exception("Error al agregar estudiante",$result->errno);
    } catch (Exception $e) {
        if ($e->getCode() == 1062) {
            http_response_code(409); // Conflicto
            echo json_encode(["error" => "El email ya está registrado"]);
        } else {
            http_response_code(500); // Otro error
            echo json_encode(["error" => "Error al agregar estudiante"]);
        }
    }
}

function handlePut($conn) {
    $input = json_decode(file_get_contents("php://input"), true);
    if (updateStudent($conn, $input['id'], $input['fullname'], $input['email'], $input['age'])) 
    {
        echo json_encode(["message" => "Actualizado correctamente"]);
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
    $result = deleteStudent($conn, $input['id']);

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