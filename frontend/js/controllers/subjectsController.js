import { subjectsAPI } from '../api/subjectsAPI.js';

document.addEventListener('DOMContentLoaded', () => 
{
    loadSubjects();
    setupSubjectFormHandler();
});

function setupSubjectFormHandler() 
{
  const form = document.getElementById('subjectForm');
  form.addEventListener('submit', async e => 
  {
        e.preventDefault();
        const subject = 
        {
            id: document.getElementById('subjectId').value.trim(),
            name: document.getElementById('name').value.trim()
        };

        try 
        {
            if (subject.id) 
            {
                await subjectsAPI.update(subject);
                showSuccess("Materia actualizada correctamente");
            }
            else
            {
                await subjectsAPI.create(subject);
                showSuccess("Materia agregada correctamente");   
            }
            
            form.reset();
            document.getElementById('subjectId').value = '';
            loadSubjects();
        }
        catch (err)
        {
            showError(err.message || "Error al guardar la materia");   
        }
  });
}

async function loadSubjects()
{
    try
    {
        const subjects = await subjectsAPI.fetchAll();
        renderSubjectTable(subjects);
    }
    catch (err)
    {
        console.error('Error cargando materias:', err.message);
    }
}

function renderSubjectTable(subjects)
{
    const tbody = document.getElementById('subjectTableBody');
    tbody.replaceChildren();

    subjects.forEach(subject =>
    {
        const tr = document.createElement('tr');

        tr.appendChild(createCell(subject.name));
        tr.appendChild(createSubjectActionsCell(subject));

        tbody.appendChild(tr);
    });
}

function createCell(text)
{
    const td = document.createElement('td');
    td.textContent = text;
    return td;
}

function createSubjectActionsCell(subject)
{
    const td = document.createElement('td');

    const editBtn = document.createElement('button');
    editBtn.textContent = 'Editar';
    editBtn.className = 'w3-button w3-blue w3-small';
    editBtn.addEventListener('click', () => 
    {
        document.getElementById('subjectId').value = subject.id;
        document.getElementById('name').value = subject.name;
    });

    const deleteBtn = document.createElement('button');
    deleteBtn.textContent = 'Borrar';
    deleteBtn.className = 'w3-button w3-red w3-small w3-margin-left';
    deleteBtn.addEventListener('click', () => confirmDeleteSubject(subject.id));

    td.appendChild(editBtn);
    td.appendChild(deleteBtn);
    return td;
}

async function confirmDeleteSubject(id)
{
    if (!confirm('¿Seguro que deseas borrar esta materia?')) return;

    try
    {
        await subjectsAPI.remove(id);
        showSuccess("Materia borrada correctamente");
        loadSubjects();
    }
    catch (err)
    {
        showError('Error: Materia con estudiantes asignados');
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