 <?php

Class Seccion_dojo {
  public function otorgarCapa($menu){
    $version_chrome;
    $asignacion='';
  if ($menu==1 ||  $menu==8 || $menu==52 || $menu==0 || $menu==92 || $menu==134  || $menu==0  || $menu==133 ) {
    $asignacion='capa_general.js';
  }
  else if ($menu==36 || $menu==6 || $menu==0 ) {
    $asignacion='capa_objetivo_misobjetivos.js';
  }
  else if ($menu==72 || $menu==99 || $menu==90 || $menu==95 || $menu==68 ||$menu==132 || $menu==78 || $menu==58 ||$menu==89 || $menu==39 || $menu==2 || $menu==0 || $menu==101   ) {
    $asignacion='capa_vista_rapida.js';
  }

  else if ($menu==31 || $menu==4   || $menu==0  ) {
    $asignacion='capa_opcionesusuario_usuario.js';
  }
  /*else if ($menu==71 || $menu==77 || $menu==9 || $menu==79 || $menu==81 ||  $menu==168 ||  $menu==169 || $menu==0 ) {
    $asignacion='capa_semaforo.js';
  }*/
  else if ($menu==17) {
    $asignacion='capa_disponibilidad.js';
  }
    
  else if ($menu==43 || $menu==32 || $menu==0  ) {
    $asignacion='capa_alertas_contactos.js';
    
  }
  else if ($menu==41  || $menu==0 ) {
    $asignacion='capa_alerta_horarios.js';
  }
  
  else if ($menu==88 || $menu==38 || $menu==0  ) {
    $asignacion='capa_objetivos_descripcion.js';
  }
  else if ($menu==94 ) {
    $asignacion='capa_ponderacion_horaria.js';
  }
  else if ($menu==18 || $menu==67 || $menu==82  ) {
    $asignacion='capa_reon_tr.js';
  }
  else if ($menu==91 ) {
    $asignacion='capa_reon_elementosplus.js';
  }
  else if ($menu==167 || $menu==135) {
    $asignacion='capa_reon_tronline.js';
  }
  else if ($menu==34) {
    $asignacion='capa_reporte_especial.js';
  }
  else if ($menu==120  || $menu==35  || $menu==118  || $menu==126  || $menu==53 || $menu==170 ||  $menu==60 ||  $menu==61 ||  $menu==62 ||  $menu==129 ||  $menu==130 ||  $menu==131   ) {
    $asignacion='capa_reporte_mobile.js';
  }else{
    $asignacion='capa_total.js';
  }
  return $asignacion;
  }  
}
?>