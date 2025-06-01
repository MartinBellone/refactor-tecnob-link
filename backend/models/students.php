<?php
function getAllStudents($conn) 
{
    $sql = "SELECT * FROM students";
    return $conn->query($sql);
}

function getStudentById($conn, $id) 
{
    $sql = "SELECT * FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result();
}

function createStudent($conn, $fullname, $email, $age) 
{
    $sql = "INSERT INTO students (fullname, email, age) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $fullname, $email, $age);
    return $stmt->execute();
}

function updateStudent($conn, $id, $fullname, $email, $age) 
{
    $sql = "UPDATE students SET fullname = ?, email = ?, age = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $fullname, $email, $age, $id);
    return $stmt->execute();
}

function deleteStudent($conn, $student_id) 
{
    if (!canDeleteStudent($conn, $student_id)) {
        return "has_relations";
    } 
    //verifica si tiene relaciones
    // Si no tiene relaciones, procede a eliminar
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    return $stmt->execute();
}
function canDeleteStudent($conn, $student_id) {
    $sql = "SELECT COUNT(*) FROM students_subjects WHERE student_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $count = 0;
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    return $count == 0; // true si NO tiene relaciones
}
?>