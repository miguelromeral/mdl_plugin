<?php

/*
 *      BÁSICO:
 */

$string['modulename'] = 'League';
$string['modulename_help'] = 'A CAMBIAR: Texto que sale al crear el recurso del modulo';
$string['modulename_link'] = 'mod/league/view';
$string['modulenameplural'] = 'Leagues';
$string['pluginadministration'] = 'League Administration';
$string['pluginname'] = 'Activities League';
$string['fill_course'] = 'Introduce el ID del curso';
$string['no'] = 'No';
$string['yes'] = 'Sí';
$string['exercise'] = 'Ejercicio';
$string['timemofied'] = 'Fecha de modificación';
$string['send_exercise'] = 'Enviar ejercicio';
$string['enabled'] = 'Habilitado';
$string['student'] = 'Alumno';
$string['upload_time'] = 'Fecha de subida';
$string['mark'] = 'Nota';
$string['reviews'] = 'Observaciones';
$string['download_file'] = 'Descargar fichero';
$string['file_uploaded'] = 'Fichero subido';
$string['to_mark'] = 'Corregir';
$string['modify'] = 'Modificar';
$string['go_back'] = 'Volver';


/*
 *      MOD_FORM:
 */

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
$string['select_method_help'] = 'Si selecciona "'.$string['select_best_marks'].
        '" se ordenará la clasificación en función de cuales son las mejores notas obtenidas. Sin embargo, '
        . 'si selecciona "'.$string['select_more_exercises'].'" se premiará a los alumnos que hayan subido más ejercicios.';

/**
 *      ERRORES
 */

$string['noviewdiscussionspermission'] = 'No tiene los suficientes permisos para ver esta liga.';

/**
 *      PÁGINA PRINCIPAL
 */

$string['view_qualy_button'] = 'Ver clasificación';
$string['manage_exercises_button'] = 'Administrar ejercicios';
$string['teacher_panel'] = 'Panel del profesor';
$string['main_panel_student'] = 'Panel de control del alumno';
$string['availables_exercises'] = 'Ejercicios disponibles';
$string['my_marks'] = 'Mis notas';

/**
 *      CLASIFICACIÓN
 */

$string['qualy_title'] = 'Clasificación';
$string['qts'] = '';
$string['qts'] = 'Clasificación (Vista para estudiantes)';
$string['q_pos'] = 'POS';
$string['q_name'] = 'Nombre';
$string['q_user'] = 'Usuario';
$string['q_id'] = 'ID';
$string['q_total_exercises'] = 'TE';
$string['q_exercises_uploaded'] = 'ES';
$string['q_total_mark'] = 'NT';
$string['q_percentage'] = 'PERC';
$string['q_notes'] = 'Apuntes';
$string['q_best_marks'] = 'Mejores notas';
$string['total_draw'] = 'Empate total';
$string['higher_mark'] = 'Tiene una nota más alta';
$string['more_exercises_uploaded'] = 'Ha subido más ejercicios';
$string['q_tba'] = 'TBA';
$string['q_name_hashed'] = 'Usuario hasheado';

/**
 *      ADMINISTRACIÓN DE EJERCICIOS
 */

$string['management_title'] = 'Administración';
$string['add_exercise_button'] = 'Añadir ejercicio';
$string['modify_exercise_button'] = 'Modificar';
$string['enable_exercise_button'] = 'Habilitar';
$string['disable_exercise_button'] = 'Deshabilitar';
$string['del'] = 'Eliminar';
$string['exercise_enabled'] = 'El ejercicio está habilitado para todos los alumnos del curso. Ahora podrán subir la tarea.';
$string['exercise_disabled'] = 'El ejercicio está deshabilitado. Los alumnos no pueden ver este ejercicio actualmente.';
$string['exercise_deleted'] = 'El ejercicio ha sido eliminado con éxito.';
$string['exercise_not_deleted'] = 'No se ha podido eliminar el ejercicio.';
$string['upload_exercise'] = 'Subir ejercicio';
$string['mark_exercise'] = 'Corregir';
$string['published_marks'] = 'Notas publicadas';
$string['publish'] = 'Publicar notas';
$string['unpublish'] = 'Ocultar notas';
$string['currently_published'] = 'Las notas han sido publicadas. A partir de ahora, los alumnos podrán ver sus notas para este ejercicio.';
$string['currently_unpublished'] = 'Retirada la publicación de notas. Ningún alumno puede ver su nota para este ejercicio.';
$string['h_manag_exer'] = 'Administrar ejercicios de la Liga';

/**
 *      AÑADIR EJERCICIOS
 */

$string['add_exercise_title'] = 'Añadir nuevo ejercicio';
$string['ae_cancel'] = 'Cancelada la modificación del ejercicio';
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

/**
 *      SUBIR EJERCICIOS (Por parte del alumno)
 */

$string['upload_title'] = 'Subir Ejercicio';
$string['ue_cancel'] = 'Cancelada la subida de ficheros.';
$string['upload_exercise_file'] = 'Subir fichero';
$string['ue_error_type'] = 'El tipo del fichero subido no es válido (Debe ser PDF).';
$string['ue_error_unknown'] = 'Hubo un problema al cargar el fichero.';
$string['ue_success'] = 'Fichero subido con éxito';
$string['ue_error_max_size'] = 'Compruebe que el fichero está por debajo del límite del tamaño de un fichero a ser subido: ';

/**
 *      CORRECCIÓN EJERCICIOS
 */

$string['no_mark_yet'] = 'Sin corrección';
$string['mark_student_button'] = 'Calificar alumno';
$string['download_file_button'] = 'Descargar fichero';

/**
 *      DESCARGA DE FICHEROS
 */

$string['dw_title'] = 'Descargar tarea';

/**
 *      CALIFICACIÓN ALUMNOS
 */

$string['mark_title'] = 'Calificar alumno';
$string['mark_cancel'] = 'Cancelada la modificación de la nota';
$string['set_mark'] = 'Calificación (0 a 100)';
$string['set_observation'] = 'Observaciones';
$string['mark_sent_success'] = 'Nota actualizada con éxito.';

/**
 *      GRADEBOOK
 */

$string['title_grade'] = 'Calificaciones';

/**
 *      NOTAS PARA ALUMNOS
 */

$string['not_sent_yet'] = 'SIN HACER EJERCICIO';