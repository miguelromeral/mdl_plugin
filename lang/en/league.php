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
 * Strings for component 'league', language 'en'.
 *
 * @package    mod_league
 * @category   string
 * @copyright  2018 Miguel Romeral
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['add_exercise_button'] = 'Add exercise';
$string['add_exercise_title'] = 'Add new exercise';
$string['ae_cancel'] = 'The modification of the exercise has been canceled';
$string['ae_cancel_new'] = 'The exercise addition has been canceled';
$string['ae_description'] = 'Description';
$string['ae_name'] = 'Exercise name';
$string['ae_success'] = 'Successfully created exercise. Ativate it in the Activities Panel';
$string['ae_warning'] = "WARNING: when saving the exercise it will automatically be disabled for the students and without publishing the notes (this change must be done manually from the teacher's control panel.";
$string['availables_exercises'] = 'Available exercises';
$string['cant_create_url'] = "Can't to generate the URL";
$string['courseidincorrect'] = 'Course ID is incorrect.';
$string['coursemoduleiidincorrect'] = 'Course Module ID was incorrect.';
$string['coursemoduleincorrect'] = 'Course module was incorrect.';
$string['coursemodulemisconfigured'] = 'Course is misconfigured.';
$string['currently_published'] = 'Grades published. Now the students can see their owns grades for this exercise.';
$string['currently_unpublished'] = 'Grades has been unpublished. No student can see their own grades for this exercise.';
$string['del'] = 'Delete';
$string['disable_exercise_button'] = 'Disable';
$string['download_file_button'] = 'Download file';
$string['enable_exercise_button'] = 'Enable';
$string['enabled'] = 'Enabled';
$string['eventattemptdownloaded'] = 'Attempt downloaded';
$string['eventattemptgraded'] = 'Attempt graded';
$string['eventattemptsubmitted'] = 'Attempt submitted';
$string['eventexercisecreated'] = 'Exercise created';
$string['eventexercisedeleted'] = 'Exercise deleted';
$string['eventexerciseupdated'] = 'Exercise updated';
$string['eventleaguecreated'] = 'League created';
$string['eventleagueupdated'] = 'League updated';
$string['exercise'] = 'Exercise';
$string['exercise_deleted'] = 'Successfully deleted exercise.';
$string['exercise_disabled'] = 'Disabled exercise. No student can currently upload files for this exercise.';
$string['exercise_enabled'] = 'El ejercicio está habilitado para todos los alumnos del curso. Ahora podrán subir la tarea.';
$string['exercise_not_deleted'] = 'No se ha podido eliminar el ejercicio. Deshabilítelo primero.';
$string['exercises'] = 'Ejercicios';
$string['file_uploaded'] = 'Fichero subido';
$string['fill_course'] = 'Introduce el ID del curso';
$string['final_attempt'] = 'Definitivo';
$string['go_back'] = 'Volver';
$string['h_manag_exer'] = 'Administrar ejercicios de la Liga';
$string['higher_mark'] = 'Tiene una nota más alta';
$string['image'] = 'Foto';
$string['individual_marks'] = 'Calificaciones individuales';
$string['last_attempt'] = 'Último íntento';
$string['league'] = 'Liga';
$string['league:addinstance'] = "Crear una nueva liga";
$string['league:downloadfiles'] = "Descargar ficheros subidos";
$string['league:manageexercises'] = "Administrar ejercicios";
$string['league:markstudents'] = "Calificar a alumnos";
$string['league:seequaly'] = "Ver clasificación completa";
$string['league:seerestrictedqualy'] = "Ver clasificación restringida (para alumnos)";
$string['league:uploadfiles'] = "Subir ficheros para ejercicios";
$string['league:view'] = "Ver liga";
$string['league_name'] = 'Nombre de la liga';
$string['leagues_in_course'] = 'Ligas creadas en el curso';
$string['main_panel_student'] = "Students' control panel";
$string['manage_exercises_button'] = 'Administrar ejercicios';
$string['management_title'] = 'Administración';
$string['mark'] = 'Nota';
$string['mark_cancel'] = 'Cancelada la modificación de la nota';
$string['mark_exercise'] = 'Corregir';
$string['mark_sent_success'] = 'Nota actualizada con éxito.';
$string['mark_student_button'] = 'Calificar alumno';
$string['mark_title'] = 'Calificar alumno';
$string['max_attempts_reached'] = "Ha alcanzado el máximo número de intentos";
$string['max_num_attempts'] = "Máximo número de intentos";
$string['max_num_attempts_desc'] = "Establece el número máximo de intentos que un usuario tiene para enviar un ejercicio.";
$string['mod_form_presentation'] = 'Mensaje de presentación de la liga';
$string['mod_form_select_course'] = 'Escoja el nombre del curso al que añadir una liga.';
$string['mod_form_select_method'] = 'Seleccione el criterio de ordenación';
$string['modify'] = 'Modificar';
$string['modify_exercise_button'] = 'Modificar';
$string['modify_exercise_title'] = 'Modificar ejercicio';
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
$string['more_exercises_uploaded'] = 'Ha subido más ejercicios';
$string['more_than_5_att_remaining'] = 'Más de 5';
$string['my_marks'] = 'Mis notas';
$string['nan'] = 'NaN';
$string['no'] = 'No';
$string['no_attempts_yet'] = 'No existen intentos para este ejercicio. Habilítelo y espere a que los estudiantes envíen sus intentos.';
$string['no_exercises_availables'] = 'En este momento no hay ningún ejercicio disponible.';
$string['no_exercises_created'] = 'Aún no ha creado ningún ejercicio. Pulse el enlace inferior que indica "'.$string['add_exercise_button'].'" para crear el primer ejercicio de la liga.';
$string['no_grades_availables'] = 'Aun no ha enviado ningún intento para ningún ejercicio.';
$string['no_mark_error'] = 'Debe especificar una nota para el intento.';
$string['no_mark_yet'] = 'Sin corrección';
$string['nopermission'] = 'No tiene los permisos suficientes para poder ver esta página.';
$string['not_done'] = 'Sin hacer';
$string['not_sent_yet'] = 'SIN HACER EJERCICIO';
$string['notallowedpage'] = 'Página no encontrada';
$string['noviewdiscussionspermission'] = 'No tiene los suficientes permisos para ver esta liga.';
$string['num_attempt'] = '# Intento';
$string['only_one_attempt'] = "Sólo un intento";
$string['pluginadministration'] = 'Administrar Liga';
$string['pluginname'] = 'Liga';
$string['presentation'] = 'Presentación';
$string['publish'] = 'Publicar notas';
$string['published_marks'] = 'Notas publicadas';
$string['q_best_marks'] = 'Mejores notas';
$string['q_exercises_uploaded'] = 'ES';
$string['q_exercises_uploaded_des'] = 'Número de ejercicios realizados.';
$string['q_id'] = 'ID';
$string['q_name'] = 'Nombre';
$string['q_name_hashed'] = 'Usuario hasheado';
$string['q_notes'] = 'Criterio';
$string['q_notes_des'] = 'Criterio de desempate en caso de haberlo.';
$string['q_percentage'] = 'PERC';
$string['q_percentage_des'] = 'Porcentaje de la nota total (nota del módulo).';
$string['q_pos'] = 'POS';
$string['q_pos_des'] = 'Posición en la clasificación.';
$string['q_tba'] = 'TBA';
$string['q_total_exercises'] = 'TE';
$string['q_total_exercises_des'] = 'Número total de ejercicios.';
$string['q_total_mark'] = 'NT';
$string['q_total_mark_des'] = 'Nota total (Suma de todos los ejercicios).';
$string['q_user'] = 'Usuario';
$string['qts'] = 'Clasificación (Vista por estudiantes)';
$string['qualy_title'] = 'Clasificación';
$string['remaining_attempts'] = 'Intentos restantes';
$string['reviews'] = 'Observaciones';
$string['select_best_marks'] = 'Mejores notas';
$string['select_course_activity'] = '¿Añadir una liga a un curso?';
$string['select_course_activity_help'] = 'AYUDA';
$string['select_method'] = 'Seleccionar criterios de ordenación';
$string['select_more_exercises'] = 'Más ejercicios subidos';
$string['select_method_help'] = 'Si selecciona "'.$string['select_best_marks'].'" se ordenará la clasificación en función de cuales son las mejores notas obtenidas. Sin embargo, si selecciona "'.$string['select_more_exercises'].'" se premiará a los alumnos que hayan subido más ejercicios.';
$string['send_exercise'] = 'Enviar ejercicio';
$string['set_mark'] = 'Calificación (0 a 100)';
$string['set_observation'] = 'Observaciones';
$string['student'] = 'Alumno';
$string['teacher_panel'] = 'Panel del profesor';
$string['timemofied'] = 'Fecha de modificación';
$string['title_create_istance'] = 'Rellene los siguientes datos para crear la actividad';
$string['title_grade'] = 'Calificaciones';
$string['to_mark'] = 'Corregir';
$string['total_draw'] = 'Empate total';
$string['ue_cancel'] = 'Cancelada la subida de ficheros.';
$string['ue_error_max_size'] = 'Compruebe que el fichero está por debajo del límite del tamaño de un fichero a ser subido: ';
$string['ue_error_type'] = 'El tipo del fichero subido no es válido (Debe ser PDF).';
$string['ue_error_unknown'] = 'Hubo un problema al cargar el fichero.';
$string['ue_no_file'] = 'Debe proporcionar un fichero para poder enviar el ejercicio.';
$string['ue_success'] = 'Fichero subido con éxito';
$string['unpublish'] = 'Ocultar notas';
$string['upload_attempt'] = 'Enviar intento';
$string['upload_exercise'] = 'Subir ejercicio';
$string['upload_exercise_file'] = 'Subir fichero';
$string['upload_time'] = 'Fecha de subida';
$string['upload_title'] = 'Subir Ejercicio';
$string['usercantdownload'] = "No tiene permisos para descargar ficheros.";
$string['usercantupload'] = "No tiene permisos para subir ficheros.";
$string['view_individual_marks'] = 'Ver notas individuales';
$string['view_qualy_button'] = 'Ver clasificación';
$string['view_total_attempts_button'] = 'Ver todos los intentos para este ejercicio';
$string['yes'] = 'Sí';