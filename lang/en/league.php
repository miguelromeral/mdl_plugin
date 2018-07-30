<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'league', language 'es'.
 *
 * @package    mod_league
 * @category   string
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['modulename'] = 'Liga';
$string['modulename_help'] = 'Una actividad de módulo Liga permite a los estudiantes competir por quién es más participativo.
    
Los profesores pueden crear ejercicios para que suban la tarea los alumnos. Los ejercicios constan de un breve título y una descripción en la que se indica cómo debe enviarse las respuestas. Los profesores pueden habilitar ejercicios para que los alumnos pueden subir su tarea. El profesor puede entonces descargar los trabajos que han subido sus alumnos para los ejercicios y corregirlos de la manera que considere oportuna. Con esas calificaciones, se completa una clasificación entre los alumnos, correspondiéndose con los trabajos de éstos.

Entre las características del módulo relativa a los usuarios se encuentran:

* Subir ficheros por cada ejercicio.
* Ver en una clasificación las notas globales (en ningún caso mostrar información sensible de otros alumnos).
* Descargar sus propios ficheros que hayan subido.
* Ver las notas de ejercicios disponibles o realizados por el alumno.

Los profesores por su parte, disfrutarán de funciones como:

* Ver clasificación completa y restringida de los alumnos.
* Crear, modificar y eliminar ejercicios.
* Corregir tareas de los estudiantes para cada ejercicio.
* Hacer las notas visibles u ocultas.
* Ver notas individuales por cada ejercicio.
';
$string['modulenameplural'] = 'Ligas';
$string['pluginname'] = 'Liga';
$string['pluginadministration'] = 'Administrar Liga';

$string['no'] = 'No';
$string['yes'] = 'Sí';
$string['exercise'] = 'Ejercicio';
$string['exercises'] = 'Ejercicios';
$string['image'] = 'Foto';
$string['league'] = 'Liga';
$string['presentation'] = 'Presentación';
$string['num_attempt'] = '# Intento';
$string['last_attempt'] = 'Último íntento';



$string['fill_course'] = 'Introduce el ID del curso';
$string['leagues_in_course'] = 'Ligas creadas en el curso';
$string['more_than_5_att_remaining'] = 'Más de 5';
$string['timemofied'] = 'Fecha de modificación';
$string['send_exercise'] = 'Enviar ejercicio';
$string['remaining_attempts'] = 'Intentos restantes';
$string['enabled'] = 'Habilitado';
$string['student'] = 'Alumno';
$string['upload_time'] = 'Fecha de subida';
$string['mark'] = 'Nota';
$string['reviews'] = 'Observaciones';
$string['download_file'] = 'Descargar fichero';
$string['cant_create_url'] = 'No se ha podido generar el URL';
$string['file_uploaded'] = 'Fichero subido';
$string['to_mark'] = 'Corregir';
$string['modify'] = 'Modificar';
$string['nan'] = 'NaN';
$string['go_back'] = 'Volver';
$string['notallowedpage'] = 'Página no encontrada';
$string['nopermission'] = 'No tiene los permisos suficientes para poder ver esta página.';

$string['league_name'] = 'Nombre de la liga';
$string['title_create_istance'] = 'Rellene los siguientes datos para crear la actividad';
$string['mod_form_select_course'] = 'Escoja el nombre del curso al que añadir una liga.';
$string['default_selected_course'] = 'SELECCIONAR CURSO';
$string['select_course_activity'] = '¿Añadir una liga a un curso?';
$string['select_course_activity_help'] = 'AYUDA';

$string['mod_form_select_method'] = 'Seleccione el criterio de ordenación';
$string['select_best_marks'] = 'Mejores notas';
$string['select_more_exercises'] = 'Más ejercicios subidos';
$string['select_method'] = 'Seleccionar criterios de ordenación';
$string['mod_form_presentation'] = 'Mensaje de presentación de la liga';
$string['select_method_help'] = 'Si selecciona "'.$string['select_best_marks'].
        '" se ordenará la clasificación en función de cuales son las mejores notas obtenidas. Sin embargo, '
        . 'si selecciona "'.$string['select_more_exercises'].'" se premiará a los alumnos que hayan subido más ejercicios.';


$string['noviewdiscussionspermission'] = 'No tiene los suficientes permisos para ver esta liga.';
$string['coursemoduleiidincorrect'] = 'Course Module ID was incorrect.';
$string['coursemodulemisconfigured'] = 'Course is misconfigured.';
$string['coursemoduleincorrect'] = 'Course module was incorrect.';
$string['courseidincorrect'] = 'Course ID is incorrect.';


$string['view_qualy_button'] = 'Ver clasificación';
$string['manage_exercises_button'] = 'Administrar ejercicios';
$string['add_exercise_button'] = 'Añadir ejercicio';
$string['teacher_panel'] = 'Panel del profesor';
$string['main_panel_student'] = 'Panel de control del alumno';
$string['availables_exercises'] = 'Ejercicios disponibles';
$string['my_marks'] = 'Mis notas';
$string['no_exercises_created'] = 'Aún no ha creado ningún ejercicio. Pulse el enlace inferior que indica "'.
        $string['add_exercise_button'].'" para crear el primer ejercicio de la liga.';
$string['no_exercises_availables'] = 'En este momento no hay ningún ejercicio disponible.';
$string['no_grades_availables'] = 'Aun no ha enviado ningún intento para ningún ejercicio.';

$string['view_individual_marks'] = 'Ver notas individuales';
$string['individual_marks'] = 'Calificaciones individuales';
$string['qualy_title'] = 'Clasificación';
$string['qts'] = 'Clasificación (Vista por estudiantes)';
$string['q_pos'] = 'POS';
$string['q_pos_des'] = 'Posición en la clasificación.';
$string['q_name'] = 'Nombre';
$string['q_user'] = 'Usuario';
$string['q_id'] = 'ID';
$string['q_total_exercises'] = 'TE';
$string['q_total_exercises_des'] = 'Número total de ejercicios.';
$string['q_exercises_uploaded'] = 'ES';
$string['q_exercises_uploaded_des'] = 'Número de ejercicios realizados.';
$string['q_total_mark'] = 'NT';
$string['q_total_mark_des'] = 'Nota total (Suma de todos los ejercicios).';
$string['q_percentage'] = 'PERC';
$string['q_percentage_des'] = 'Porcentaje de la nota total (nota del módulo).';
$string['q_notes'] = 'Criterio';
$string['q_notes_des'] = 'Criterio de desempate en caso de haberlo.';
$string['q_best_marks'] = 'Mejores notas';
$string['total_draw'] = 'Empate total';
$string['higher_mark'] = 'Tiene una nota más alta';
$string['more_exercises_uploaded'] = 'Ha subido más ejercicios';
$string['q_tba'] = 'TBA';
$string['q_name_hashed'] = 'Usuario hasheado';

$string['management_title'] = 'Administración';
$string['modify_exercise_button'] = 'Modificar';
$string['enable_exercise_button'] = 'Habilitar';
$string['disable_exercise_button'] = 'Deshabilitar';
$string['view_total_attempts_button'] = 'Ver todos los intentos para este ejercicio';
$string['del'] = 'Eliminar';
$string['exercise_enabled'] = 'El ejercicio está habilitado para todos los alumnos del curso. Ahora podrán subir la tarea.';
$string['exercise_disabled'] = 'El ejercicio está deshabilitado. Los alumnos no pueden ver este ejercicio actualmente.';
$string['exercise_deleted'] = 'El ejercicio ha sido eliminado con éxito.';
$string['exercise_not_deleted'] = 'No se ha podido eliminar el ejercicio. Deshabilítelo primero.';
$string['upload_exercise'] = 'Subir ejercicio';
$string['mark_exercise'] = 'Corregir';
$string['published_marks'] = 'Notas publicadas';
$string['publish'] = 'Publicar notas';
$string['unpublish'] = 'Ocultar notas';
$string['currently_published'] = 'Las notas han sido publicadas. A partir de ahora, los alumnos podrán ver sus notas para este ejercicio.';
$string['currently_unpublished'] = 'Retirada la publicación de notas. Ningún alumno puede ver su nota para este ejercicio.';
$string['h_manag_exer'] = 'Administrar ejercicios de la Liga';
$string['final_attempt'] = 'Definitivo';

$string['add_exercise_title'] = 'Añadir nuevo ejercicio';
$string['ae_cancel'] = 'Cancelada la modificación del ejercicio';
$string['ae_cancel_new'] = 'Cancelada la creación del ejercicio';
$string['modify_exercise_title'] = 'Modificar ejercicio';
$string['ae_name'] = 'Nombre del ejercicio';
$string['ae_description'] = 'Descripción';
$string['ae_enviar'] = 'Crear ejercicio';
$string['ae_enviar_modificado'] = 'Modificar ejercicio';
$string['ae_explanation'] = 'Los campos con * son obligatorios.';
$string['ae_errors'] = 'Existen algunos errores en la creación del ejercicio:';
$string['ae_error_name'] = 'El nombre del ejercicio debe tener entre 1 y 255 caractéres incluidos.';
$string['ae_error_description'] = 'Debe proporcionar una descripción del ejercicio.';
$string['ae_success'] = 'Creado con éxito el ejercicio. Actívelo en el panel de actividades.';
$string['ae_warning'] = 'ADVERTENCIA: al guardar el ejercicio automáticamente pasará a estar deshabilitado para los alumnos y sin publicar las notas (este cambio deberá hacerse manualmente desde el panel de control del profesor.';

$string['upload_title'] = 'Subir Ejercicio';
$string['ue_cancel'] = 'Cancelada la subida de ficheros.';
$string['upload_exercise_file'] = 'Subir fichero';
$string['upload_attempt'] = 'Enviar intento';
$string['ue_error_type'] = 'El tipo del fichero subido no es válido (Debe ser PDF).';
$string['ue_error_unknown'] = 'Hubo un problema al cargar el fichero.';
$string['ue_success'] = 'Fichero subido con éxito';
$string['ue_no_file'] = 'Debe proporcionar un fichero para poder enviar el ejercicio.';
$string['ue_error_max_size'] = 'Compruebe que el fichero está por debajo del límite del tamaño de un fichero a ser subido: ';

$string['no_mark_yet'] = 'Sin corrección';
$string['no_mark_error'] = 'Debe especificar una nota para el intento.';
$string['not_done'] = 'Sin hacer';
$string['mark_student_button'] = 'Calificar alumno';
$string['download_file_button'] = 'Descargar fichero';
$string['no_attempts_yet'] = 'No existen intentos para este ejercicio. Habilítelo y espere a que los estudiantes envíen sus intentos.';

$string['dw_title'] = 'Descargar tarea';

$string['mark_title'] = 'Calificar alumno';
$string['mark_cancel'] = 'Cancelada la modificación de la nota';
$string['set_mark'] = 'Calificación (0 a 100)';
$string['set_observation'] = 'Observaciones';
$string['mark_sent_success'] = 'Nota actualizada con éxito.';

$string['title_grade'] = 'Calificaciones';

$string['not_sent_yet'] = 'SIN HACER EJERCICIO';

$string['eventleaguecreated'] = 'League created';
$string['eventleagueupdated'] = 'League updated';
$string['eventexercisecreated'] = 'Exercise created';
$string['eventexerciseupdated'] = 'Exercise updated';
$string['eventexercisedeleted'] = 'Exercise deleted';
$string['eventattemptsubmitted'] = 'Attempt submitted';
$string['eventattemptdownloaded'] = 'Attempt downloaded';
$string['eventattemptgraded'] = 'Attempt graded';

$string['league:addinstance'] = "Crear una nueva liga";
$string['league:view'] = "Ver liga";
$string['league:manageexercises'] = "Administrar ejercicios";
$string['league:seequaly'] = "Ver clasificación completa";
$string['league:seerestrictedqualy'] = "Ver clasificación restringida (para alumnos)";
$string['league:downloadfiles'] = "Descargar ficheros subidos";
$string['league:uploadfiles'] = "Subir ficheros para ejercicios";
$string['league:markstudents'] = "Calificar a alumnos";

$string['usercantupload'] = "No tiene permisos para subir ficheros.";
$string['usercantdownload'] = "No tiene permisos para descargar ficheros.";

$string['max_attempts_reached'] = "Ha alcanzado el máximo número de intentos";
$string['only_one_attempt'] = "Sólo un intento";
$string['max_num_attempts'] = "Máximo número de intentos";
$string['max_num_attempts_desc'] = "Establece el número máximo de "
        . "intentos que un usuario tiene para enviar un ejercicio.";