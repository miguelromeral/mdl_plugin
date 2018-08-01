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

$string['add_exercise'] = 'Añadir ejercicio';
$string['add_new_exercise'] = 'Añadir nuevo ejercicio';
$string['ae_cancel'] = 'Cancelada la modificación del ejercicio';
$string['ae_cancel_new'] = 'Cancelada la creación del ejercicio';
$string['description'] = 'Descripción';
$string['ae_name'] = 'Nombre del ejercicio';
$string['ae_success'] = 'Creado con éxito el ejercicio. Actívelo en el panel de actividades.';
$string['ae_warning'] = 'ADVERTENCIA: al guardar el ejercicio automáticamente pasará a estar deshabilitado para los alumnos y sin publicar las notas (este cambio deberá hacerse manualmente desde el panel de control del profesor.';
$string['availables_exercises'] = 'Ejercicios disponibles';
$string['cant_create_url'] = 'No se ha podido generar el URL';
$string['courseidincorrect'] = 'El ID del curso es incorrecto.';
$string['coursemoduleiidincorrect'] = 'El ID del módulo del curso es incorrecto.';
$string['coursemoduleincorrect'] = 'El módulo del curso es incorrecto.';
$string['coursemodulemisconfigured'] = 'El curso está mal configurado.';
$string['currently_published'] = 'Las notas han sido publicadas. A partir de ahora, los alumnos podrán ver sus notas para este ejercicio.';
$string['currently_unpublished'] = 'Retirada la publicación de notas. Ningún alumno puede ver su nota para este ejercicio.';
$string['del'] = 'Eliminar ejercicio';
$string['disable_exercise_button'] = 'Deshabilitar';
$string['download_file_button'] = 'Descargar fichero';
$string['enable_exercise_button'] = 'Habilitar';
$string['enabled'] = 'Habilitado';
$string['eventattemptdownloaded'] = 'Intento descargado';
$string['eventattemptgraded'] = 'Intento corregido';
$string['eventattemptsubmitted'] = 'Intento enviado';
$string['eventexercisecreated'] = 'Ejercicio creado';
$string['eventexercisedeleted'] = 'Ejercicio eliminado';
$string['eventexerciseupdated'] = 'Ejercicio actualizado';
$string['eventleaguecreated'] = 'Liga creada';
$string['eventleagueupdated'] = 'Liga actualizada';
$string['exercise'] = 'Ejercicio';
$string['exercise_deleted'] = 'El ejercicio ha sido eliminado con éxito.';
$string['exercise_disabled'] = 'El ejercicio está deshabilitado. Los alumnos no pueden ver este ejercicio actualmente.';
$string['exercise_enabled'] = 'El ejercicio está habilitado para todos los alumnos del curso. Ahora podrán subir la tarea.';
$string['exercise_not_deleted'] = 'No se ha podido eliminar el ejercicio. Deshabilítelo primero.';
$string['exercises'] = 'Ejercicios';
$string['file_uploaded'] = 'Fichero subido';
$string['fill_course'] = 'Introduce el ID del curso';
$string['go_back'] = 'Volver';
$string['h_manag_exer'] = 'Administrar ejercicios de la Liga';
$string['higher_mark'] = 'Tiene una nota individual más alta';
$string['image'] = 'Foto';
$string['individual_marks'] = 'Calificaciones individuales';
$string['last_attempt'] = 'Último íntento';
$string['league'] = 'Liga';
$string['league:addinstance'] = "Crear una nueva liga";
$string['league:downloadfiles'] = "Descargar ficheros subidos";
$string['league:manageexercises'] = "Administrar ejercicios de la liga";
$string['league:markstudents'] = "Calificar a alumnos";
$string['league:seequaly'] = "Ver clasificación completa";
$string['league:seerestrictedqualy'] = "Ver clasificación restringida (para alumnos)";
$string['league:uploadfiles'] = "Subir ficheros para ejercicios";
$string['league:view'] = "Ver liga";
$string['league_name'] = 'Nombre de la liga';
$string['leagues_in_course'] = 'Ligas creadas en el curso';
$string['main_panel_student'] = 'Panel de control del alumno';
$string['manage_exercises_button'] = 'Administrar ejercicios';
$string['management_title'] = 'Administración';
$string['mark'] = 'Nota';
$string['to_grade'] = 'Corregir';
$string['mark_sent_success'] = 'Nota actualizada con éxito.';
$string['mark_student_button'] = 'Calificar alumno';
$string['mark_title'] = 'Calificar alumno';
$string['max_attempts_reached'] = "Ha alcanzado el máximo número de intentos";
$string['max_num_attempts'] = "Máximo número de intentos";
$string['max_num_attempts_desc'] = "Establece el número máximo de intentos que un usuario tiene para enviar un ejercicio.";
$string['mod_form_presentation'] = 'Mensaje de presentación de la liga';
$string['mod_form_select_method'] = 'Seleccione el criterio de ordenación';
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
$string['no_exercises_created'] = 'Aún no ha creado ningún ejercicio. Pulse el enlace inferior que indica "'.$string['add_exercise'].'" para crear el primer ejercicio de la liga.';
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
$string['q_percentage'] = '%';
$string['q_percentage_des'] = 'Porcentaje de la nota total de la liga.';
$string['q_pos'] = 'POS';
$string['q_pos_des'] = 'Posición en la clasificación.';
$string['q_tba'] = 'SD';
$string['q_total_exercises'] = 'TE';
$string['q_total_exercises_des'] = 'Número total de ejercicios.';
$string['q_total_mark'] = 'Total';
$string['q_total_mark_des'] = 'Nota total (Suma de todos los ejercicios).';
$string['q_user'] = 'Usuario';
$string['qts'] = 'Clasificación (Vista por estudiantes)';
$string['qualy_title'] = 'Clasificación';
$string['remaining_attempts'] = 'Intentos restantes';
$string['reviews'] = 'Observaciones';
$string['select_best_marks'] = 'Mejores notas';
$string['select_method'] = 'Seleccionar criterios de ordenación';
$string['select_more_exercises'] = 'Más ejercicios subidos';
$string['select_method_help'] = 'Si selecciona "'.$string['select_best_marks'].'" se ordenará la clasificación en función de cuales son las mejores notas obtenidas. Sin embargo, si selecciona "'.$string['select_more_exercises'].'" se premiará a los alumnos que hayan subido más ejercicios.';
$string['send_exercise'] = 'Enviar ejercicio';
$string['set_mark'] = 'Calificación (0 a 100)';
$string['set_observation'] = 'Observaciones';
$string['student'] = 'Alumno';
$string['teacher_panel'] = 'Panel del profesor';
$string['timemofied'] = 'Fecha de modificación';
$string['title_grade'] = 'Calificaciones';
$string['to_mark'] = 'Corregir';
$string['total_draw'] = 'Empate total';
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
$string['yes'] = 'Sí';