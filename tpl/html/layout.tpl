<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <!-- BEGIN TIENE_METADATA -->
        <meta http-equiv="X-UA-Compatible" content="IE=9"/>
        
        <!-- END TIENE_METADATA -->
        
        <title>Atentus.com: {__sitio_titulo}</title>

        <!-- Estilos calendario-->
        <link rel="stylesheet" href="css/textos-reporte.css" type="text/css"/>
        <link rel="stylesheet" href="css/calendario.css" type="text/css"/>
        <link rel="stylesheet" href="css/disponibilidad.css" type="text/css"/>
        <link rel="stylesheet" href="css/calendariou.css" type="text/css"/>
        <link rel="stylesheet" href="css/informes_disponibles.css" type="text/css"/>
        <link rel="stylesheet" href="css/especiales.css" type="text/css"/>
        <link rel="stylesheet" href="css/estiloNotificaciones.css" type="text/css">
        <style type="text/css">
            @import "{__path_dojo}dijit/themes/nihilo/nihilo.css";
        </style>
        <script type="text/javascript" src="{__path_jquery_ui}js/jquery-1.7.1.min.js"></script>
        <!-- BEGIN ES_SECCION_MANTENIMIENTO -->
        <script type="text/javascript" src="{__path_moment_js}moment.min.js"></script>
        <script type="text/javascript" src="{__path_moment_js}moment-timezone.min.js"></script>
        <!-- END ES_SECCION_MANTENIMIENTO -->
        <script type="text/javascript" src="{__path_jquery_ui}js/jquery-data-table/jquery.dataTables.js"></script>
        <!-- BEGIN ES_SECCION_CALENDARIO -->
        <script type="text/javascript" src="{__path_full_calendar}js/fullcalendar.min.js"></script>
        <!-- END ES_SECCION_CALENDARIO -->
        <script type="text/javascript" src="tools/highcharts/highcharts.js"></script>
        <script type="text/javascript" src="tools/highcharts/modules/exporting.js"></script>
        <script type="text/javascript" src="{__path_js}leyenda_svg.js"></script>
        <script type="text/javascript" src="{__path_js}flash_detect.js"></script>
        <script type="text/javascript" src="{__path_js}disponibilidad.js"></script>
        <script type="text/javascript" src="{__path_js}reportes.js"></script>
        <script type="text/javascript" src="{__path_js}validador.js"></script>
        <script type="text/javascript" src="{__path_js}calendario.js"></script>
        <script type="text/javascript" src="{__path_js}calendariou.js"></script>
        <script type="text/javascript" src="{__path_js}informes_disponibles.js"></script>
        <script type="text/javascript" src="{__path_js}htmlEncode.js"></script>
       <script type="text/javascript" src="{__path_dojo}dojo/dojo.js" djConfig="parseOnLoad:true"></script>
        <script type="text/javascript" src="{__seccion}"></script>
       
        <script type="text/JavaScript">
            // Detecta flash.
            if (FlashDetect.installed) {
            	tiene_flash = 1;
            }
            else {
            	tiene_flash = 0;
            }
            
                // Detecta SVG.
            if (document.implementation.hasFeature("http://www.w3.org/TR/SVG11/feature#BasicStructure", "1.1") == true) {
            	tiene_svg = 1;
            }
            else {
            	tiene_svg = 0;
            }
            
            function logout() {
            	location.href="index.php";
            }
            
            if(typeof String.prototype.trim !== 'function') {
            String.prototype.trim = function() {
            return this.replace(/^\s+|\s+$/g, '');
            }
            }
        </script>


        <link rel="stylesheet" href="css/especiales.css" type="text/css"/>


    </head>
    <a href="#" class="scrollup">Ir arriba</a>

    <body class="nihilo">
        <div id="seccion_mantenencion" data-seccion='{seccion}' data-calendario='{calendario}' data-historial='{historial}' data-agregar='{agregar}'></div>


           
        <form id="form_principal" name="form_principal" method="post" action="index.php">
            <!--formulario principal-->
            <input type="hidden" name="sitio_id" value="{__sitio_id}">  <!--id del controlador que se va a cargar-->
            <input type="hidden" name="menu_id" value="{__menu_id}">  <!--id de la seccion que se va a cargar-->
            <input type="hidden" name="objeto_id" value="{__objeto_id}">  <!--id del objeto que se va a cargar (objetivo_id,monitor_id,horario_id)-->
            <input type="hidden" name="accion" value="">  <!--accion que se va a realizar-->
            <input type="hidden" name="ejecutar_accion" value="">  <!--id del controlador que se va a cargar si se realiza una accion-->

            <table class="principal" align="center">
                <tr>
                    <td class="header" colspan="100%">
                        <table width="100%">
                            <tr>
                                <td><i class="spriteImg spriteImg-header"></i></td>
                                <td align="right">
                                    <table>
                                        <tr>


                                            <td class="menusuperior">&nbsp;</td>
                                            <!-- BEGIN SECCIONES_SITIO -->
                                            <td class="menusuperior">
                                                <a href="#" onclick="abrirEnlace('{__sitio_seccion_id}','0','0','0');" class="{__sitio_seccion_class}">
                                                {__sitio_seccion_nombre}</a>
                                            </td>
                                            <!-- END SECCIONES_SITIO -->
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td class="menu">
                        <!-- Tabla "Perfil Cliente" y "MenÃº SecciÃ³n Seleccionada"-->
                        <table width="100%">
                            <tr>
                                <td class="titulomenu">Perfil Cliente</td>
                            </tr>
                            <tr>
                                <td class="cuentausuario">
                                    <span class="textblanco13">{__sitio_usuario_nombre}</span></br>
                                    <span class="textblanco12">{__sitio_usuario_cliente}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="cuentausuario" align="right"><input type="button" class="boton_cancelar" onclick="document.location.href='index.php?logout=t'" value="Cerrar Sesion"></td>
                            </tr>
                        </table>
                             
                        <br>
                        <table width="100%">
                            <tr>
                                <td class="titulomenu">{__sitio_nombre}</td>
                            </tr>
                            <tr>
                                <td>
                                    <!-- BEGIN SECCIONES_MENU -->
                                    <div style="border-bottom: solid 5px #b3b1b2;">
                                    
                                        <div class="{__menu_seccion_class}" onclick="mostrarSubmenu('{__sitio_id}','{__menu_seccion_id}','{__menu_abrir_enlace}'); return false;">
                                            <i class="{__menu_flecha_posicion}" id="flecha_{__menu_seccion_id}"></i>
                                            {__menu_seccion_nombre}
                                        </div>
                                        
                                        
                                        <div style="display:{__menu_seccion_display};" id="submenu_{__menu_seccion_id}">
                                            <!-- BEGIN OBJETOS_MENU -->
                                          
                                            <div class="submenuizq" id="submenu_{__menu_seccion_id}_{__menu_objeto_id}" onclick="abrirEnlace('{__sitio_id}','{__menu_seccion_id}','{__menu_objeto_id}','0'); return false;" style="height: 26px;">
                                                <div dojoType="dijit.Tooltip" connectId="submenu_{__menu_seccion_id}_{__menu_objeto_id}">
                                                    <div class="textnegro12">{__menu_objeto_nombre}</div>
                                                </div>
                                                <div class="{__menu_objeto_class}" style="padding-top: {__menu_objeto_padding}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; width: 160px;">{__menu_objeto_nombre}</div>
                                                <div class="textgris8" style="white-space: nowrap; overflow: hidden; width: 150px; display: {__menu_objeto_display};">{__menu_objeto_tipo}</div>
                                            </div>
                                            <!-- END OBJETOS_MENU -->
                                        </div>
                                    </div>
                                    <!-- END SECCIONES_MENU -->
                                    <br>
                                </td>
                            </tr>
                   
                        </table>
                        <table width="90%" align="center">
                           
                            <tr>
                                <td class="celdatituloayuda"><i class="spriteImg spriteImg-postit_clip"></i></td>
                                <td class="celdatituloayuda">Nota de Ayuda</td>
                            </tr>
                            
                            <tr>
                                <td class="celdaayuda" colspan="100%">{__sitio_ayuda}</td>
                            </tr>
                            <tr>
                                <td bgcolor="#f5f29d" colspan="100%" align="right"><i class="spriteImg spriteImg-postit_borde"></i></td>
                            </tr>
                        </table>
                        <br>
                    </td>
            
                    <td width="15"></td>
                    <td class="contenido">
                        <!-- BEGIN TIENE_ERROR_SISTEMA -->
                        <table width="100%">
                        
                            <tr>
                                <td class="tituloerror">Warning</td>
                            </tr>
                            <tr>
                                <td class="error">{__error_sistema}</td>
                            </tr>
                        </table>
                        <br>
                        <!-- END TIENE_ERROR_SISTEMA -->
                        <!-- Tabla "Contenido SecciÃ³n Seleccionada"-->
                        <div class="definiciones" style="float: right; position: relative; margin-top: -15px;" onclick="abrirPopupDefiniciones();">Definiciones y Preguntas Frecuentes</div>
                        {__sitio_contenido}
                    </td>
                </tr>
                <tr align="center">
                    <td class="footer" colspan="100%">&copy; {__sitio_anno} Atentus Reporte2 {__version} </td>
                </tr>
                
            </table>

            <!--<div id="direcciones_xml" class="error"></div>-->
        </form>

        <!-- NOTIFICACION USUARIO -->
    <div id="modalUsuario" class="modal_noti">
        <div class="modal-content_noti">
            <div class="modal-header_noti">
                <h2>Atentus Notificaciones: Usuario</h2>
            </div>
            <div class="modal-body_noti" style="overflow-y: scroll; height: 400px;">
                <!-- BEGIN BLOQUE_NOTIFICACION_USUARIO -->
                {__notificacion_titulo}<br>
                {__notificacion_cuerpo}<br>

                <!-- END BLOQUE_NOTIFICACION_USUARIO -->
            </div>
            <div class="modal-footer_noti">
                <div align="center">
                    <label><input  style="vertical-align: middle;" id="noMostrarU" type="checkbox">No volver a mostrar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <input type="button" class="boton_accion_noti" name="" id="aceptarU" value="Aceptar">
                </div>
            </div>
        </div>
    </div>


    <!-- NOTIFICACION CLIENTE -->
    <div id="modalCliente" class="modal_noti">
        <div class="modal-content_noti">
      
            <div class="modal-header_noti">
                <h2>Atentus Notificaciones: Cliente</h2>
            </div>
            <div class="modal-body_noti" style="overflow-y: scroll; height: 400px;">
                <!-- BEGIN BLOQUE_NOTIFICACION_CLIENTE -->
                {__notificacion_titulo}<br>
                {__notificacion_cuerpo}<br>
                <!-- END BLOQUE_NOTIFICACION_CLIENTE -->
            </div>
            <div class="modal-footer_noti">
                <div align="center">
                    <label><input  style="vertical-align: middle;" id="noMostrarC" type="checkbox"> No volver a mostrar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <input type="button" class="boton_accion_noti" name="" id="aceptarC" value="Aceptar">
                
                </div>
            </div>
        </div>
    </div>

    <!-- NOTIFICACION GLOBAL -->
    <div id="modalGlobal" class="modal_noti">
        <div class="modal-content_noti">
            <div class="modal-header_noti">
                <h2>Atentus Notificaciones: Globales</h2>
            </div>
            <div class="modal-body_noti" style="overflow-y: scroll; height: 400px;">
                <h2>La versión actual del reporte es: {__version}</h2>
                <!-- BEGIN BLOQUE_NOTIFICACION_GLOBAL -->
                {__notificacion_titulo}<br>
                {__notificacion_cuerpo}<br>
                <!-- END BLOQUE_NOTIFICACION_GLOBAL -->
            </div>
            <div class="modal-footer_noti">
                <div align="center">
                    <label><input  style="vertical-align: middle;" id="noMostrarG" type="checkbox"> No volver a mostrar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>
                    <input type="button" class="boton_accion_noti" name="" id="aceptarG" value="Aceptar">
                </div>
            </div>
        </div>
    </div>

        <script type="text/javascript">

        $(document).ready(function(){
            $(window).scroll(function(){
                if ($(this).scrollTop() > 100) {
                    $('.scrollup').fadeIn();
                } else {
                    $('.scrollup').fadeOut();
                }
            });
            $('.scrollup').click(function(){
                $("html, body").animate({ scrollTop: 0 }, 600);
                return false;
            });
        });
        var cliente_usuario_id = {__cliente_usuario_cliente};
        if ({__validadorG} == 1) {
            document.getElementById('modalGlobal').style.display = "block";
        }else if ({__validadorC} == 1) {
            document.getElementById('modalCliente').style.display = "block";
        }else if ({__validadorU} == 1){
            document.getElementById('modalUsuario').style.display = "block";
        }
        var modalGlobal = document.getElementById('modalGlobal');
        var modalCliente = document.getElementById('modalCliente');
        var modalUsuario = document.getElementById('modalUsuario');
        var aceptarG = document.getElementById('aceptarG');
        var aceptarC = document.getElementById('aceptarC');
        var aceptarU = document.getElementById('aceptarU');
        var noMostrarG = document.getElementById('noMostrarG');
        var noMostrarC = document.getElementById('noMostrarC');
        var noMostrarU = document.getElementById('noMostrarU');
        noMostrarG.onclick = function(event){
            if ($(noMostrarG).prop('checked')) {
                $(noMostrarG).prop('checked', true);
            }else{
                $(noMostrarG).prop('checked', false);
            }
        }
        noMostrarC.onclick = function(event){
            if ($(noMostrarC).prop('checked')) {
                $(noMostrarC).prop('checked', true);
            }else{
                $(noMostrarC).prop('checked', false);
            }
        }
        noMostrarU.onclick = function(event){
            if ($(noMostrarU).prop('checked')) {
                $(noMostrarU).prop('checked', true);
            }else{
                $(noMostrarU).prop('checked', false);
            }
        }
        $("#aceptarG").mousedown(function(){
            document.getElementById("aceptarG").style.boxShadow = "1px 1px 4px black inset";
        });
        $("#aceptarC").mousedown(function(){
            document.getElementById("aceptarC").style.boxShadow = "1px 1px 4px black inset";
        });
        $("#aceptarU").mousedown(function(){
            document.getElementById("aceptarU").style.boxShadow = "1px 1px 4px black inset";
        });
        aceptarG.onclick = function(event) {
            var notificacionesIds = {__array_notificacionesG};
            if ($(noMostrarG).prop('checked')){
                modalGlobal.style.display = "none";
                if ({__validadorC} == 1) {
                    document.getElementById('modalCliente').style.display = "block";
                }else if ({__validadorU} == 1) {
                    document.getElementById('modalUsuario').style.display = "block";
                }
                for (var i = 0; i < notificacionesIds.length; i++) {
                    $.ajax({
                        async: false,
                        type: 'POST',
                        url: '../call_ajax.php',
                        data: {'notificacion_id': notificacionesIds[i], 'cliente_usuario_id': cliente_usuario_id, 'nameFunction':'notificacionControlInsert', 'notificacion': 1, 'noMostrar': 1},
                        success: function(data) {
                        },
                        error: function(error) {
                        }
                    });
                }
            }else{
                $.ajax({
                    async: false,
                    type: 'POST',
                    url: '../call_ajax.php',
                    data: {'notificacion': 1, 'nameFunction':'notificacionControlInsert'}
                });
                modalGlobal.style.display = "none";
                if ({__validadorC} == 1) {
                    document.getElementById('modalCliente').style.display = "block";
                }else if ({__validadorU} == 1) {
                    document.getElementById('modalUsuario').style.display = "block";
                }
            }
            $("div").remove("#modalGlobal");
        };  
        aceptarC.onclick = function(event) {
            var notificacionesIds = {__array_notificacionesC};
            if ($(noMostrarC).prop('checked')){
                modalCliente.style.display = "none";
                if ({__validadorU} == 1) {
                    document.getElementById('modalUsuario').style.display = "block";
                }
                for (var i = 0; i < notificacionesIds.length; i++) {
                    $.ajax({
                        async: false,
                        type: 'POST',
                        url: '../call_ajax.php',
                        data: {'notificacion_id': notificacionesIds[i], 'cliente_usuario_id': cliente_usuario_id, 'nameFunction':'notificacionControlInsert', 'notificacion': 1, 'noMostrar': 1},
                        success: function(data) {
                        },
                        error: function(error) {
                        }
                    });
                }
            }else{
                modalCliente.style.display = "none";
                if ({__validadorU} == 1) {
                    document.getElementById('modalUsuario').style.display = "block";
                }
                $.ajax({
                    async: false,
                    type: 'POST',
                    url: '../call_ajax.php',
                    data: {'notificacion': 1, 'nameFunction':'notificacionControlInsert'}
                });
            }
            $("div").remove("#modalCliente");
        };  
        aceptarU.onclick = function(event) {
            var notificacionesIds = {__array_notificacionesU};
            if ($(noMostrarU).prop('checked')){
                modalUsuario.style.display = "none";
                for (var i = 0; i < notificacionesIds.length; i++) {
                    $.ajax({
                        async: false,
                        type: 'POST',
                        url: '../call_ajax.php',
                        data: {'notificacion_id': notificacionesIds[i], 'cliente_usuario_id': cliente_usuario_id, 'nameFunction':'notificacionControlInsert','notificacion': 1, 'noMostrar': 1},
                        success: function(data) {
                        },
                        error: function(error) {
                        }
                    });
                }
            }else{
                modalUsuario.style.display = "none";
                $.ajax({
                    async: false,
                    type: 'POST',
                    url: '../call_ajax.php',
                    data: {'notificacion': 1, 'nameFunction':'notificacionControlInsert'}
                });
            }
            $("div").remove("#modalUsuario");
        };

            var _gaq = _gaq || [];
            
            _gaq.push(['_setAccount', '{__ga_tracking_id}']);
            _gaq.push(['_setCustomVar', 1, 'uid', '{__sitio_usuario_id}', 3]);
            
            var objetivoId = '{__objetivo_id}';
            if(objetivoId) {
            	_gaq.push(['_setCustomVar', 2, 'oid', objetivoId, 3]);
            }
            else {
            	_gaq.push(['_setCustomVar', 2, 'oid', '<ninguno>', 3]);
            }
            
            _gaq.push(['_trackPageview','{__path_ga}']);
            
            (function() {
            	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
            })();
        </script>


    </body>

    <style>
        .scrollup{
        width: 40px;
        height: 34px;
        opacity: 1;
        position: fixed;
        bottom: 50px;
        right: 20px;
        display:none;
        text-indent: -9999px;
        background: url('../img/arriba.png') no-repeat center;
        -webkit-background-size: cover;
        -moz-background-size: cover;
        -o-background-size: cover;
        background-size: cover;
        }
    </style>

    <script>
        {__menu_seccion_script}
    </script>

</html>