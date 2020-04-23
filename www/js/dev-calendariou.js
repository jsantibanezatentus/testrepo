// Variable global para ser utilizada en el calendario especial de mantenimiento.
MES = null;
/**
 * Crea función Date#toISOString en aquellos navegadores donde no esté disponible.
 * En realidad es para soportar IE 8 e inferiores.
 *
 * Ref: https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/Date/toISOString
 *      2013-02-14
 *
 */

if(!Date.prototype.toISOString) {
  (function() {
    function pad(number) {
      var r = String(number);
      if(r.length === 1) {
        r = '0' + r;
      }
      return r;
    }

    Date.prototype.toISOString = function() {
      return this.getUTCFullYear()
          + '-' + pad(this.getUTCMonth() + 1)
          + '-' + pad(this.getUTCDate())
          + 'T' + pad(this.getUTCHours())
          + ':' + pad(this.getUTCMinutes())
          + ':' + pad(this.getUTCSeconds())
          + '.' + String((this.getUTCMilliseconds()/1000).toFixed(3)).slice(2, 5)
          + 'Z';
    };
  }());
}


(function($) {
   //"use strict";

   var Fecha = function(input) {
     this.init(input);
   };

   Fecha.NombresAbreviadosDeDias = ["lun", "mar", "mié", "jue", "vie", "sáb", "dom"];

   Fecha.NombresDeMeses = ["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"];

   Fecha.NombresAbreviadosDeMeses = ["ene", "feb", "mar", "abr", "may", "jun", "jul", "ago", "sep", "oct", "nov", "dic"];

   Fecha.prototype = {
     constructor: Fecha,

     init: function(input) {
       this.input = input;
       if(input instanceof Array) {
         this.date = new Date(this.parseISO8601ToTimestamp(input[0] + '-' + this._pad(input[1] + 1) + '-' + this._pad(input[2])));
       }
       else if(typeof this.input === 'object' && this.input instanceof Date) {
         this.date = input;
       }
       else if(typeof this.input === 'string') {
         /*
          * Acciona ante caso especial de formato de string.
          *
          * Cuando el string está en formato yyyy-mm-ddThh:mm:ss
          * la fecha interpretada no es consistente entre navegadores.
          *
          * En este caso se identifica este formato de string y se utiliza
          * el constructor que recibe múltiples argumentos como enteros, el
          * cual sí es consistente entre diferentes implementaciones.
          */
         var regex = /^(\d{4})-(\d{2})-(\d{2})T(\d{2})\:(\d{2})\:(\d{2})$/;
         var result = regex.exec(this.input);
         if(result) {
           var ano = parseInt(result[1], 10);
           var mes = parseInt(result[2], 10) - 1;
           var dia = parseInt(result[3], 10);
           var hora = parseInt(result[4], 10);
           var minuto = parseInt(result[5], 10);
           var segundo = parseInt(result[6], 10);

           this.date = new Date(ano, mes, dia, hora, minuto, segundo);
         }
         else {
           this.date = new Date(this.input);
         }
       }
       else {
         this.date = new Date(this.input);
       }

       this.dia = this.date.getDate();
       this.mes = this.date.getMonth();
       this.ano = this.date.getFullYear();

       this.hora = this.date.getHours();
       this.minuto = this.date.getMinutes();
       this.segundo = this.date.getSeconds();

       this.nombreDeMes = Fecha.NombresDeMeses[this.mes];
       this.nombreAbreviadoDeMes = Fecha.NombresAbreviadosDeMeses[this.mes];
       this.diaDeSemana = this.date.getDay() === 0 ? 7 : this.date.getDay();
     },

     esMayorQue: function(otro) {
       return this.date.getTime() > otro.date.getTime();
     },

     esMayorOIgualQue: function(otro) {
       return this.date.getTime() >= otro.date.getTime();
     },

     esMenorQue: function(otro) {
       return this.date.getTime() < otro.date.getTime();
     },

     esMenorOIgualQue: function(otro) {
       return this.date.getTime() <= otro.date.getTime();
     },

     esIgualQue: function(otro) {
       return this.date.getTime() === otro.date.getTime();
     },


     /**
      * Contraparte de Date.prototype.toISOString utilizando zona horaria local.
      *
      */
     toLocalString: function() {
       return this.date.getFullYear()
         + '-' + this._pad(this.date.getMonth() + 1)
         + '-' + this._pad(this.date.getDate())
         + 'T' + this._pad(this.date.getHours())
         + ':' + this._pad(this.date.getMinutes())
         + ':' + this._pad(this.date.getSeconds())
         + '.' + String((this.date.getMilliseconds()/1000).toFixed(3)).slice(2, 5);
     },

     aLas: function(hora) {
       var resultado = null;

       switch(hora) {
       case "0 horas":
         resultado = new Fecha(this.format("yyyy-mm-dd") + "T00:00:00");
         break;
       case "24 horas":
         resultado = new Fecha(this.format("yyyy-mm-dd") + "T24:00:00");
         break;
       }

       return resultado;
     },

     format: function(input) {
       var resultado = null;

       switch(input) {
       case "yyyy-mm-dd":
         resultado = this.toLocalString().slice(0, 10);
         break;
       case "hh:mm":
         resultado = this.toLocalString().slice(11, 16);
         break;
       case "yyyy-mm-ddThh:mm:ss":
         var string = this.toLocalString();
         resultado = string.slice(0, 10) + "T" + string.slice(11,19);
         break;
       case "yyyy-mm-ddThh:mm:ssZ":
         var string = this.toLocalString();
         resultado = string.slice(0, 10) + "T" + string.slice(11,19) + "Z";
         break;
       }

       return resultado;
     },


     /**
      * Parsea fecha en formato ISO 8601. Retorna timestamp o NaN si no es posible parsear.
      *
      * Ref: https://github.com/csnover/js-iso8601/tree/d0d8c37028435e1cd72205d6ab2610ba90cff166/iso8601.js
      *      2012-02-14; con modificaciones
      *
      */
     parseISO8601ToTimestamp: function(date) {
       var timestamp, struct, minutesOffset = 0;
       var numericKeys = [1, 4, 5, 6, 7, 10, 11];

       // 1 YYYY 2 MM 3 DD 4 HH 5 mm 6 ss 7 msec 8 Z 9 ± 10 tzHH 11 tzmm
       if((struct = /^(\d{4}|[+\-]\d{6})(?:-(\d{2})(?:-(\d{2}))?)?(?:T(\d{2}):(\d{2})(?::(\d{2})(?:\.(\d{3}))?)?(?:(Z)|([+\-])(\d{2})(?::(\d{2}))?)?)?$/.exec(date))) {
         // avoid NaN timestamps caused by “undefined” values being passed to Date.UTC
         for(var i = 0, k; (k = numericKeys[i]); ++i) {
           struct[k] = +struct[k] || 0;
         }

         // allow undefined days and months
         struct[2] = (+struct[2] || 1) - 1;
         struct[3] = +struct[3] || 1;

         if(struct[8] !== 'Z' && struct[9] !== undefined) {
           minutesOffset = struct[10] * 60 + struct[11];

           if(struct[9] === '+') {
             minutesOffset = 0 - minutesOffset;
           }
         }

         timestamp = Date.UTC(struct[1], struct[2], struct[3], struct[4], struct[5] + minutesOffset, struct[6], struct[7]);
       }
       else {
         timestamp = NaN;
       }

       return timestamp;
     },



     /**
      * Métodos privados
      *
      */

     _pad: function(n) {
       return n < 10 ? '0' + n : n;
     }

   };



   /**
    * Selector
    *
    * Componente que permite navegar entre calendarios, mes a mes.
    *
    */
   var Selector = function(fecha, options) {
     options = options || {};
     this.init(fecha, options);
   };

   Selector.prototype = {
     constructor: Selector,

     init: function(fecha, options) {
       var self = this;
       this.fecha = fecha;
       this.$el = $('<div class="selector"></div>');

       this.fechaMinima = options["fechaMinima"];
       this.fechaMaxima = options["fechaMaxima"];

       this.$botonPrevio    = $('<div class="boton previo">&#x25c0;</div>');
       this.$botonSiguiente = $('<div class="boton siguiente">&#x25b6;</div>');
       this.$botonHoy       = $('<div class="boton actual">Hoy</div>');

       this.$botonPrevio.on('click', function() {
        self.setFecha((new Fecha(new Date(self.fecha.ano, self.fecha.mes - 1, self.fecha.dia))).aLas("0 horas"),options['especial']);
       });

       this.$botonSiguiente.on('click', function() {
         self.setFecha((new Fecha(new Date(self.fecha.ano, self.fecha.mes + 1, self.fecha.dia))).aLas("0 horas"),options['especial']);
       });
       this.$botonHoy.on('click', function() {
         var fechaHoy = new Fecha(new Date());
         var aInicioDeMes = new Fecha(new Date(fechaHoy.ano, fechaHoy.mes, 1));
         self.setFecha(aInicioDeMes.aLas("0 horas"),options['especial']);
       });

       this.$el.append(this.$botonPrevio, this.$botonHoy, this.$botonSiguiente);
       
       self._habilitaODeshabilitaBotones(options['especial']);
       this.$el.on("calendariou:selector:cambio", function() { self._habilitaODeshabilitaBotones(options['especial']); });
     },

     setFecha: function(fecha,options) {
    	 
       // Si fecha nueva está fuera de los límites, no permite alterar
       // la configuración actual.
    	 if(!options){
       if(this.fechaMinima) {
         var fechaMinimaDeComparacion  = (new Fecha(new Date(this.fechaMinima.ano, this.fechaMinima.mes, 1))).aLas("0 horas");
         if(fecha.esMenorQue(fechaMinimaDeComparacion)) {
           this.$el.trigger("calendariou:selector:error", {
             "mensaje": "Fecha está fuera de rango (límite inferior)",
             "parametros": {
               "fecha": fecha.format("yyyy-mm-ddThh:mm:ss"),
               "fechaMinima": this.fechaMinima.format("yyyy-mm-ddThh:mm:ss")
             }
           });
           return;
         }
       }
      
	       if(this.fechaMaxima) {
	         var fechaMaximaDeComparacion = this.fechaMaxima.aLas("24 horas");
	         if(fecha.esMayorOIgualQue(fechaMaximaDeComparacion)) {
	           this.$el.trigger("calendariou:selector:error", {
	             "mensaje": "Fecha está fuera de rango (límite superior)",
	             "parametros": {
	               "fecha": fecha.format("yyyy-mm-ddThh:mm:ss"),
	               "fechaMaxima": this.fechaMaxima.format("yyyy-mm-ddThh:mm:ss")
	             }
	           });
	           return;
	         }
	       }
       }
       this.fecha = fecha;
       this.$el.trigger('calendariou:selector:cambio');
     },

     el: function() {
       return this.$el;
     },



     /**
      * Métodos privados
      */

     /**
      * Cambia el aspecto de los botones de navegación, respecto a las
      * fechas máxima y mínima, que indican si es posible navegar hacia un mes.
      */
     _habilitaODeshabilitaBotones: function(options) {
       if(this.fechaMinima) {
         var fechaAInicioDeMes = new Fecha(new Date(this.fecha.ano, this.fecha.mes, 1));

         if(fechaAInicioDeMes.aLas("0 horas").esMenorOIgualQue(this.fechaMinima)) {
           this.$botonPrevio.addClass("desactivado");
         }
         else {
           this.$botonPrevio.removeClass("desactivado");
         }
       }
       if(!options){
	       if(this.fechaMaxima) {
	         var fechaATerminoDeMes = new Fecha(new Date(this.fecha.ano, this.fecha.mes + 1, 0));
	
	         if(fechaATerminoDeMes.aLas("24 horas").esMayorOIgualQue(this.fechaMaxima)) {
	           this.$botonSiguiente.addClass("desactivado");
	         }
	         else {
	           this.$botonSiguiente.removeClass("desactivado");
	         }
	       }
       }
     }

   };


   /**
    * Seleccion
    *
    * Componente que coordina los parámetros seleccionables en calendario.
    * En este caso, corresponden `fechaInicio` y `fechaTermino`.
    *
    */
   var Seleccion = function(params) {
     if(!params) params = {};
     this.init(params);
   };

   Seleccion.prototype = {
     constructor: Seleccion,

     init: function(params) {
       this.fechaInicio  = params["fechaInicio"] ? new Fecha(params["fechaInicio"]) : null;
       this.fechaTermino = params["fechaTermino"] ? new Fecha(params["fechaTermino"]) : null;

       this.$el = $('<div class="wrapper"><div class="fecha inicio"></div><div class="separador">al</div><div class="fecha termino"></div></div>');
       this.update();
     },

     el: function() {
       return this.$el;
     },

     update: function() {
       var $fechaInicio = this.$el.find(".fecha.inicio");
       var esCalendarioMantenimiento = ($('#dialog-calendario')[0] != null)? true:false;
         
       if(this.fechaInicio) {
         var $dia = $('<div class="dia">'+ this.fechaInicio.dia +'</div>');
         var $mesYAno= $('<div class="mes-y-ano">' + this.fechaInicio.nombreAbreviadoDeMes + ' ' + this.fechaInicio.ano + '</div>');
         var inputHI = $('<div id="hI"class="hora inicio"></div>');
         $fechaInicio.empty().append($dia, $mesYAno).show();
        
         var $horaInicio = $('<div class="hora inicio">'+ this.fechaInicio.format("hh:mm") +'</div>');
         // Se agrega para ver si el calendario es calendario de mantenimiento.
         if(esCalendarioMantenimiento == true){
          $fechaInicio.append(inputHI);
         }
         else{
          $fechaInicio.append($horaInicio);
         }
       }
       else {
         $fechaInicio.hide().empty();
       }

       var $separador = this.$el.find(".separador");
       var $fechaTermino = this.$el.find(".fecha.termino");

       if(this.fechaTermino) {

         // Caso especial: la hora es 00:00:00 => se debe mostrar el día anterior,
         // con hora 24:00:00
         if(this.fechaTermino.hora === 0 && this.fechaTermino.minuto === 0 && this.fechaTermino.segundo === 0) {
           var fechaDiaAnterior = new Fecha(new Date(this.fechaTermino.ano, this.fechaTermino.mes, this.fechaTermino.dia - 1));
           var dia = fechaDiaAnterior.dia;
           var nombreDeMes = fechaDiaAnterior.nombreAbreviadoDeMes;
           var ano = fechaDiaAnterior.ano;
           var hora = "24:00";
         }
         else {
           var dia = this.fechaTermino.dia;
           var nombreDeMes = this.fechaTermino.nombreAbreviadoDeMes;
           var ano = this.fechaTermino.ano;
           var hora = this.fechaTermino.format("hh:mm");
         }

         var $dia = $('<div class="dia">'+ dia +'</div>');
         var $mesYAno = $('<div class="mes-y-ano">'+ nombreDeMes + ' '+ ano + '</div>');
         var inputHT = $('<div id="hT"class="hora termino"></div>');
         $fechaTermino.empty().append($dia, $mesYAno).show();
        
         $separador.show();

         var $horaTermino = $('<div class="hora termino">'+ hora +'</div>');
         // Se agrega para ver si el calendario es calendario de mantenimiento.
         if(esCalendarioMantenimiento == true){
          $fechaTermino.append(inputHT);
         }
         else{
          $fechaTermino.append($horaTermino);
         }
         
       }
       else {
         $separador.hide();
         $fechaTermino.hide().empty();
       }
     },

     set: function(key, value) {
       switch(key) {
       case "fechaInicio":
         this.fechaInicio = value !== null ? new Fecha(value) : null;
         this.update();
         this.$el.trigger("calendariou:seleccion:cambio");
         break;
       case "fechaTermino":
         if(value === null) {
           this.fechaTermino = this.fechaInicio;
         }
         else {
           this.fechaTermino = new Fecha(value);
         }

         if(this.fechaTermino !== null && this.fechaTermino.esMenorQue(this.fechaInicio)) {
           var temp = this.fechaInicio;
           this.fechaInicio = this.fechaTermino;
           this.fechaTermino = temp;
         }
         this.update();
         this.$el.trigger("calendariou:seleccion:cambio");
         break;
       }
     },

     get: function(key) {
       var resultado = null;
       switch(key) {
       case "fechaInicio":
         resultado = this.fechaInicio;
         break;
       case "fechaTermino":
         resultado = this.fechaTermino;
         break;
       }
       return resultado;
     }
   };



   /**
    * MesCalendario
    *
    * Componente que despliega y coordina interacción para el contenido
    * del un mes.
    *
    */
   var MesCalendario = function(fecha, opciones) {
     opciones = opciones || {};
     this.init(fecha, opciones);
   };

   MesCalendario.prototype = {
     constructor: MesCalendario,

    init: function(fecha, opciones) {
      if (opciones['actualizar'] == true){
      }
      else{
        this.$el = $('<div class="wrapper"></div>');
        this.permiteSeleccionar = opciones["permiteSeleccionar"];
        this.seleccionaIntervalo = opciones["seleccionaIntervalo"];
        this.fechaMinima = opciones["fechaMinima"];
        this.fechaMaxima = opciones["fechaMaxima"];
         /*
          * Indica en qué parte del proceso de selección estamos:
          *
          *   * `true`: seleccionando el primer parámetro; típicamente fecha de inicio
          *   * `false`: seleccionando el segundo parámetro, correspondiente al otro
          *              extremo del intervalo; típicamente fecha de término.
          *
          * Esto es para definir el comportamiento del evento `click` en un día
          * de calendario.
          */
        this.estaSeleccionandoPrimerParametro = true;
        this.generar(fecha,opciones["especial"]);
      }       
    },

     el: function() {
       return this.$el;
     },

     generar: function(fecha,especial) {
       var self = this;

       var ano = fecha.ano;
       var mes = fecha.mes;
       var dia = fecha.dia;

       var numeroDeDiasEnMes = new Date(ano, mes + 1, 0).getDate();

       var $tabla = $('<table><thead></thead><tbody></tbody></table>');


       // completa fila de encabezado
       var $filaEncabezado = $('<tr></tr>');
       for(var i=0; i<Fecha.NombresAbreviadosDeDias.length; i++) {
         $filaEncabezado.append('<th>' + Fecha.NombresAbreviadosDeDias[i] + '</th>');
       }
       $("thead", $tabla).append($filaEncabezado);


       var diaInicial = 1;
       var dateInicial = new Date(ano, mes, diaInicial);
       var fechaInicial = new Fecha(dateInicial);
       var diaDeSemanaInicial = fechaInicial.diaDeSemana;
       if(diaDeSemanaInicial > 1) {
         diaInicial = -diaDeSemanaInicial + 2;
       }


       var $tbody = $("tbody", $tabla);
       var diaActual = diaInicial;
       var $fila;

       var fechaDeHoy = new Fecha(new Date());


       while(diaActual <= numeroDeDiasEnMes) {
         $fila = $('<tr></tr>');
         for(var diaDeSemanaActual=1; diaDeSemanaActual<=7; diaDeSemanaActual++) {
           var dateActual = new Date(ano, mes, diaActual);
           var fechaActual = new Fecha(dateActual);
           var dataFecha = fechaActual.format("yyyy-mm-dd");
           var classes = ["dia"];

           // Comprueba si se está dibujando el día de hoy
           if(fechaDeHoy.ano === fechaActual.ano && fechaDeHoy.mes === fechaActual.mes && fechaDeHoy.dia === fechaActual.dia) {
             classes.push("actual");
           }

           // Comprueba si fecha actual es de un mes distinto al indicado.
           // Por ejemplo, al incluir días inmediatamente previos o posteriores al mes actual.
           if(fechaActual.mes !== mes) {
             classes.push("de-otro-mes");
           }
           //Comprueba que si el calendario es para mantenimiento o para reportes especiales
           if(!especial){
	           // Comprueba si fecha actual está dentro del rango establecido por
	           // fecha mínima y fecha máxima.
	           if(this.fechaMinima && fechaActual.esMenorQue(this.fechaMinima.aLas("0 horas"))) {
	             classes.push("fuera-de-rango");
	           }
           
	           if(this.fechaMaxima && fechaActual.esMayorOIgualQue(this.fechaMaxima)) {
	             classes.push("fuera-de-rango");
	           }
           }

           $fila.append('<td><div class="' + classes.join(' ')  +'" id ="'+dataFecha+'" data-fecha="' + dataFecha + '">' + fechaActual.dia + '</div></td>');
           diaActual++;
         }
         $tbody.append($fila);
       }


       // Configura respuesta ante eventos de clic y selección de fecha.
       // Esto es aplicable sólo si el calendario permite realizar una
       // selección manual.

       if(this.permiteSeleccionar) {
         var selectorDeDia = ".dia:not(.de-otro-mes):not(.fuera-de-rango)";
         var clickHandler = function() {
           var $dia = $tabla.find(selectorDeDia);

           if(self.estaSeleccionandoPrimerParametro) {
             // Maneja selección de límite inferior de intervalo

             $dia.removeClass("seleccionado");
             self.$el.trigger("calendariou:mesCalendario:seleccionaFechaInicio", $(this).data("fecha"));

             // Si permite seleccionar intervalo, entonces permite
             // establecer una fecha de término manualmente.
             if(self.seleccionaIntervalo) {
               self.estaSeleccionandoPrimerParametro = false;
             }
           }
           else {
             // Maneja selección de límite superior de intervalo

             self.$el.trigger("calendariou:mesCalendario:seleccionaFechaTermino", $(this).data("fecha"));
             self.estaSeleccionandoPrimerParametro = true;
           }
         };

         $tabla.find(selectorDeDia).on("click", clickHandler);
       }


       var $titulo = $('<div class="titulo">' + fecha.nombreDeMes + ' ' + fecha.ano + '</div>');

       var $contenido = $('<div class="contenido"></div>');
       $contenido.append($tabla);

       this.$el.empty().append($titulo).append($contenido);
       this.$el.trigger("calendariou:mesCalendario:generarReady");
     },

     marcarIntervalo: function(fechaInicio, fechaTermino) {
       if(fechaInicio === null) return;

       var $dias = this.$el.find(".contenido table .dia");
       $dias.removeClass("seleccionado");
       $dias.each(function(index) {
         var $this = $(this);
         var fechaDia = new Fecha($this.data("fecha") + 'T00:00:00');

         var fechaInicioCeroHoras = fechaInicio.aLas("0 horas");
         if(fechaTermino === null) {
           if(fechaDia.esIgualQue(fechaInicioCeroHoras)) {
             $this.addClass("seleccionado");
           }
         }
         else {
           var fechaTermino24Horas = fechaTermino;
           if((fechaDia.esMayorQue(fechaInicioCeroHoras) || fechaDia.esIgualQue(fechaInicioCeroHoras)) &&
              (fechaDia.esMenorQue(fechaTermino24Horas))) {
                $this.addClass("seleccionado");
           }
         }

       });
     }
   };



   /**
    * Calendariou
    *
    * Componente que despliega y coordina el calendario, con todos sus subcomponentes:
    *   * Selector
    *   * Selección
    *   * MesCalendario
    *
    */
   var Calendariou = function(element, options) {
     if(!options) options = {};
     this.init(element, options);
   };

   Calendariou.prototype = {
     constructor: Calendariou,

     init: function(element, options) {
       var self = this;

       this.$el = $(element);
       this.$calendariou = $('<div class="calendariou"><div class="mes-calendario"></div><div class="navegacion"></div><div class="seleccion"></div></div>');
       this.$el.empty().append(this.$calendariou);


       // Establece fecha de presentación de calendario (no selección)
       if(options["fechaCalendario"]) {
        var fechaCalendario = new Fecha(options["fechaCalendario"]);
       }
       else {
        // si no especifica fecha de inicio, utiliza fecha actual
        var fechaCalendario = new Fecha(new Date());
       }
       
       if (options["especial"]){
        this.fechaCalendario = (new Fecha(new Date(fechaCalendario.ano, MES, 1))).aLas("0 horas"); 
       }
       else{
        this.fechaCalendario = (new Fecha(new Date(fechaCalendario.ano, fechaCalendario.mes, 1))).aLas("0 horas");
       }
       


       // Establece fechas límite para calendario

       if(options["fechaMaxima"]) {
         this.fechaMaxima = new Fecha(options["fechaMaxima"]);
       }
       else {
         // Si no se especifica fecha máxima, por defecto corresponde
         // al día de hoy.
         this.fechaMaxima = new Fecha(new Date());
       }

       if(options["fechaMinima"]) {
         this.fechaMinima = new Fecha(options["fechaMinima"]);
       }
       else {
         // Si no se especifica fecha mínima, se asume que no es aplicable.
         // Entonces permitirá navegar ilimitadamente hacia meses en el pasado.
         this.fechaMinima = null;
       }


       // Opciones asociadas a la selección personalizada en calendario
       var opcionesDeSeleccion = options["seleccion"] || {};

       var permiteSeleccionarEnCalendario = opcionesDeSeleccion["activa"] !== undefined ? opcionesDeSeleccion["activa"] : true;
       var permiteSeleccionarIntervalo    = opcionesDeSeleccion["intervalo"] !== undefined ? opcionesDeSeleccion["intervalo"] : true;



       // Inicializa selección
       this.seleccion = new Seleccion({
         "fechaInicio": options["fechaInicio"] || null,
         "fechaTermino": options["fechaTermino"] || null,
         "especial": options["mantenimiento"] || false
       });
       this.$calendariou.find(".seleccion").append(this.seleccion.el());

       // Escucha evento de cambio de selección
       this.seleccion.el().on("calendariou:seleccion:cambio", function() {
         self.mesCalendario.marcarIntervalo(self.seleccion.get("fechaInicio"), self.seleccion.get("fechaTermino"));
       });




       // Inicializa despliegue de mes calendario
       var opcionesMesCalendario = {
         "permiteSeleccionar": permiteSeleccionarEnCalendario,
         "seleccionaIntervalo": permiteSeleccionarIntervalo,
         "fechaMinima": this.fechaMinima,
         "fechaMaxima": this.fechaMaxima,
         "especial": options["mantenimiento"] || false
       };
       this.mesCalendario = new MesCalendario(this.fechaCalendario, opcionesMesCalendario);
       this.$calendariou.find(".mes-calendario").append(this.mesCalendario.el());

       // Escucha eventos de selección de fecha de inicio
       this.mesCalendario.el().on('calendariou:mesCalendario:seleccionaFechaInicio', function(event, fecha) {
         self.actualizarSeleccion({
           "fechaInicio": fecha + 'T00:00:00',
           "fechaTermino": fecha + 'T24:00:00'
         });
       });

       // Escucha eventos de selección de fecha de término
       this.mesCalendario.el().on('calendariou:mesCalendario:seleccionaFechaTermino', function(event, fecha) {
         var fechaEventoCeroHoras = new Fecha(fecha + "T00:00:00");
         var fechaEvento24Horas = new Fecha(fecha + "T24:00:00");

         var fechaInicio = self.seleccion.get("fechaInicio");
         var fechaTermino = null;


         /*
          * Asegura que el intervalo de fechas esté ordenado,
          * es decir, fecha de inicio < fecha de término
          */
         if(fechaEventoCeroHoras.esMenorQue(fechaInicio)) {
           var temp = fechaInicio;
           fechaInicio = fechaEventoCeroHoras;
           fechaTermino = new Fecha(temp.format("yyyy-mm-dd") + "T24:00:00");
         }
         else {
           fechaTermino = fechaEvento24Horas;
         }


         /*
          * Actualiza efectivamente la selección.
          *
          * Hace que no sea posible seleccionar un intervalo mayor
          * a tres meses.
          */
         var fechaInicioMasTresMeses = new Fecha(new Date(fechaInicio.ano, fechaInicio.mes + 3, fechaInicio.dia));
         if(fechaInicioMasTresMeses.esMenorQue(fechaTermino)) {
           self.$el.trigger("calendariou:warning", {
             "mensaje": "No es posible seleccionar un intervalo mayor a tres meses.",
             "parametros": {
               "fechaInicio": fechaInicio.format("yyyy-mm-ddThh:mm:ss"),
               "fechaTermino": fechaTermino.format("yyyy-mm-ddThh:mm:ss")
             }
           });
           // Nota: la selección no es modificada en este caso.
         }
         else {
           self.actualizarSeleccion({
             "fechaInicio": fechaInicio.format("yyyy-mm-ddThh:mm:ss"),
             "fechaTermino": fechaTermino.format("yyyy-mm-ddThh:mm:ss")
           });
         }


       });




       // Inicializa selector de fecha.
       this.selector = new Selector(this.fechaCalendario, {
                                      "fechaMinima": this.fechaMinima,
                                      "fechaMaxima": this.fechaMaxima,
                                      "especial": options["mantenimiento"] || false
       });
       this.$calendariou.find(".navegacion").append(this.selector.el());

       // Escucha eventos de cambios en selector de fecha
       this.selector.el().on('calendariou:selector:cambio', function() {
         self.mesCalendario.generar(self.selector.fecha,options["mantenimiento"]);
         self.mesCalendario.el().trigger("calendariou:mesCalendario:cambiaMes", {
                                           "ano": self.selector.fecha.ano,
                                           "mes": self.selector.fecha.mes + 1
         });
       });



       // gatilla cambio de selección por primera vez, una vez que los otros componentes están
       // listos para responder
       this.seleccion.el().trigger("calendariou:seleccion:cambio");

       this.mesCalendario.el().on("calendariou:mesCalendario:generarReady", function() {
         self.mesCalendario.marcarIntervalo(self.seleccion.get("fechaInicio"), self.seleccion.get("fechaTermino"));
       });
     },

     actualizarSeleccion: function(params) {
        if(params["fechaInicio"] !== undefined) this.seleccion.set("fechaInicio", params["fechaInicio"]);
        if(params["fechaTermino"] !== undefined) this.seleccion.set("fechaTermino", params["fechaTermino"]);
     }

   };







   /**
    * fn.calendariou
    *
    * Calendariou como plugin para jQuery.
    *
    * Opciones:
    *
    *   * "fechaCalendario": fecha para la cual se desplegará el calendario de mes
    *   * "fechaInicio": fecha de inicio de selección
    *   * "fechaTermino": fecha de término de selección
    *   * "fechaMinima": fecha mínima válida para el calendario; puede ser null
    *   * "fechaMaxima": fecha máxima válida para el calendario
    *   * "seleccion":
    *     * "activa": (true|false) si es posible seleccionar libremente en calendario de mes
    *     * "intervalo": (true|false) si es posible seleccionar intervalo de días, o días individuales.
    *
    */
   $.fn.calendariou = function(options) {
    return this.each(function() {//año dia mes
      
      var fechaI = (new Fecha(new Date(options.fechaInicio))).aLas("0 horas");
      var fechaT = (new Fecha(new Date(options.fechaTermino))).aLas("0 horas");
      MES = fechaI.mes;
      
      var $this = $(this);
      var data = $this.data("calendariou");
      
      if(!data) {
        data = new Calendariou(this, options);
        $this.data("calendariou", data);
      }    
      else{
        // Eliminar instancia.
        delete data;
        $('.boton previo').off('click');
        $('.boton actual').off('click');
        $('.boton previo').off('click');
        data = new Calendariou(this, options);
        $this.data("calendariou", data);
      }
      
     });

   };

   $.fn.calendariou.Constructor = Calendariou;


   /**
    * Exporta clases a ámbito global
    */
   window.Reporte2 = window.Reporte2 || {};
   window.Reporte2.Calendariou = {};
   window.Reporte2.Calendariou.Calendariou = Calendariou;
   window.Reporte2.Calendariou.Fecha = Fecha;
   window.Reporte2.Calendariou.mes = MesCalendario;

})(window.jQuery);
