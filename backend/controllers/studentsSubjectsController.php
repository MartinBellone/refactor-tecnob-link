<?php
require_once("./models/studentsSubjects.php");

function handleGet($conn) 
{
    $result = getAllSubjectsStudents($conn);
    $data = [];
    while ($row = $result->fetch_assoc()) 
    {
        $data[] = $row;
    }
    echo json_encode($data);
}


function handlePost($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);
    $result = assignSubjectToStudent($conn, $input['student_id'], $input['subject_id'], $input['approved']);

    if ($result === true) {
        echo json_encode(["message" => "Inscripción realizada correctamente"]);
    } elseif ($result === "duplicate") {
        http_response_code(409);
        echo json_encode(["error" => "El estudiante ya está inscripto en esa materia"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Error al inscribir"]);
    }
}

function handlePut($conn) 
{
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        http_response_code(400);
        echo json_encode(["error" => "Datos incompletos"]);
        return;
    }

    if (updateStudentSubject($conn, $input['id'], $input['student_id'], $input['subject_id'], $input['approved'])) 
    {
        echo json_encode(["message" => "Actualización correcta"]);
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
    if (removeStudentSubject($conn, $input['id'])) 
    {
        echo json_encode(["message" => "Relación eliminada"]);
    } 
    else 
    {
        http_response_code(500);
        echo json_encode(["error" => "No se pudo eliminar"]);
    }
}
?>
