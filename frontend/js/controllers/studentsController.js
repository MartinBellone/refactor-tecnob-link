import { studentsAPI } from '../api/studentsAPI.js';

document.addEventListener('DOMContentLoaded', () => 
{
    loadStudents();
    setupFormHandler();
});
  
function setupFormHandler()
{
    const form = document.getElementById('studentForm');
    form.addEventListener('submit', async e => 
    {
        e.preventDefault();
        const student = getFormData();

        try {
            if (student.id) {
                await studentsAPI.update(student);
                showSuccess("Estudiante actualizado correctamente");
            } else {
                await studentsAPI.create(student);
                showSuccess("Estudiante agregado correctamente");
            }
            clearForm();
            loadStudents();
        } catch (err) {
            showError(err.message || "Error al guardar el estudiante");
        }
    });
}
  
function getFormData()
{
    return {
        id: document.getElementById('studentId').value.trim(),
        fullname: document.getElementById('fullname').value.trim(),
        email: document.getElementById('email').value.trim(),
        age: parseInt(document.getElementById('age').value.trim(), 10)
    };
}
  
function clearForm()
{
    document.getElementById('studentForm').reset();
    document.getElementById('studentId').value = '';
}
  
async function loadStudents()
{
    try 
    {
        const students = await studentsAPI.fetchAll();
        renderStudentTable(students);
    } 
    catch (err) 
    {
        console.error('Error cargando estudiantes:', err.message);
    }
}
  
function renderStudentTable(students)
{
    const tbody = document.getElementById('studentTableBody');
    tbody.replaceChildren();
  
    students.forEach(student => 
    {
        const tr = document.createElement('tr');
    
        tr.appendChild(createCell(student.fullname));
        tr.appendChild(createCell(student.email));
        tr.appendChild(createCell(student.age.toString()));
        tr.appendChild(createActionsCell(student));
    
        tbody.appendChild(tr);
    });
}
  
function createCell(text)
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}
  
function createActionsCell(student)
{
    const td = document.createElement('td');
  
    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => fillForm(student));
  
    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDelete(student.id));
  
    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}
  
function fillForm(student)
{
    document.getElementById('studentId').value = student.id;
    document.getElementById('fullname').value = student.fullname;
    document.getElementById('email').value = student.email;
    document.getElementById('age').value = student.age;
}
  
async function confirmDelete(id) 
{
    if (!confirm('¿Estás seguro que deseas borrar este estudiante?')) return;
  
    try 
    {
        await studentsAPI.remove(id);
        showSuccess("Estudiante borrado correctamente");
        loadStudents();
    } 
    catch (err) 
    {
        console.error('Error al borrar:', err.message);
    }
}
function showSuccess(msg) {
    const $mensaje = document.getElementById("mensaje");
    if ($mensaje) {
        $mensaje.textContent = msg;
        $mensaje.className = "alert alert-success";
        $mensaje.classList.remove("d-none");
    }
     setTimeout(() => {
            hideMessage();
        }, 3000);
}

function showError(msg) {
    const $mensaje = document.getElementById("mensaje");
    if ($mensaje) {
        $mensaje.textContent = msg;
        $mensaje.className = "alert alert-danger";
        $mensaje.classList.remove("d-none");
    }
    setTimeout(() => {
            hideMessage();
        }, 4000);
}

function hideMessage() {
    const $mensaje = document.getElementById("mensaje");
    if ($mensaje) {
        $mensaje.textContent = "";
        $mensaje.classList.add("d-none");
    }
}
