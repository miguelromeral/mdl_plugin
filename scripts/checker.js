function ask_before_enable(){
    if (confirm("¿Está seguro de que quiere habilitar la actividad?")) {
        return true;
    }
    return false;
}
