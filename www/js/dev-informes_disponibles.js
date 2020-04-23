(function($) {
  "use strict";


  var ListaDeInformesDisponibles = function(element, options) {
    options = options || {};
    this.init(element, options);
  };

  ListaDeInformesDisponibles.prototype = {

    /**
     * Inicializa objeto de informes disponibles.
     *
     * Parámetros:
     *
     *   * element: elemento raíz en el cual se insertará la lista de informes disponibles.
     *   * options: objeto que describe opciones de inicialización
     *     * <ninguna por el momento>
     *       * OBS: Antes se esperaba una referencia al objeto calendariou. Ahora no es necesario.
     *
     */
    init: function(element, options) {
      this.$el = $(element);
      this.$informesDisponibles = $('<div class="informes-disponibles-en-formulario"></div>');

      // Cache de fechas de informes seleccionados, mes a mes.
      // Sirve para recordar qué informe fue seleccionado en cada mes.
      this.cacheDeSeleccion = {};

      this.$el.append(this.$informesDisponibles);
    },

    el: function() {
      return this.$el;
    },


    /**
     * Selecciona un informe de la lista de informes disponibles.
     *
     * Genera evento `listaDeInformesDisponibles:seleccionaInforme`, incluyendo
     * los datos relevantes del informe seleccionado.
     *
     * Parámetros:
     *
     *   * $informe: objeto (jQuery) que representa al informe en lista.
     *   * ano: año de interés (numérico, 4 dígitos)
     *   * mes: mes de interés (numérico, 1-12)
     *
     */
    seleccionarInforme: function($informe, ano, mes) {
      var fechaInicio = $informe.data("fechaInicio");
      var fechaTermino = $informe.data("fechaTermino");
      var reporteInformeSubtipoId = $informe.data("reporteInformeSubtipoId");

      $informe.siblings().removeClass("seleccionado");
      $informe.addClass("seleccionado");

      // Agrega a cache fechas de informe seleccionado, para el mes correspondiente
      this.cacheDeSeleccion[ano + "-" + mes] = [fechaInicio, fechaTermino];

      this.$el.trigger("listaDeInformesDisponibles:seleccionaInforme", {
        "fechaInicio":  fechaInicio,
        "fechaTermino": fechaTermino,
        "reporteInformeSubtipoId": reporteInformeSubtipoId
      });
    },


    /**
     * Consulta y carga lista de informes disponibles para el
     * objetivo, año y mes indicados.
     *
     * Realiza consulta asincrónica al servidor, consultando por informes
     * disponibles, y automáticamente reemplaza el contenido en interfaz
     * de usuario.
     *
     * Parámetros:
     *   * objetivoId: id de objetivo
     *   * ano: año de interés (numérico, 4 dígitos)
     *   * mes: mes de interés (numérico, 1-12)
     *
     */
    cargar: function(objetivoId, ano, mes) {
      var self = this;

      this.$informesDisponibles.find("li.informe").css("opacity", "0.5");
      this.$informesDisponibles.find(".leyenda .indicador-carga").show();

      $.ajax({
        "url": "informes_disponibles.php",
        "type": "POST",
        "data": {
          "objetivo_id": objetivoId,
          "ano": ano,
          "mes": mes
        },
        "dataType": "html",
        "success": function(data) {
          self.$informesDisponibles.empty().append(data);

          self.$informesDisponibles.find(".informe").on("click", function() {
                                                          var $informe = $(this);
                                                          self.seleccionarInforme($informe, ano, mes);
          });


          // Marca automáticamente un informe de la lista.
          //
          // Existen 2 casos:
          //
          //   1. Ya se seleccionó previamente un informe en el mes actual: se vuelve a seleccionar.
          //   2. No se ha seleccionado informe para el mes: selecciona el último.
          //
          var anoMes = ano + "-" + mes;
          if(self.cacheDeSeleccion[anoMes]) {
            var $informe = self.$informesDisponibles.find(".informe[data-fecha-inicio='" + self.cacheDeSeleccion[anoMes][0] + "'][data-fecha-termino='" + self.cacheDeSeleccion[anoMes][1] + "']");
            if($informe.size() > 0) {
              self.seleccionarInforme($informe.eq(0), ano, mes);
            }
            else {
              self.cacheDeSeleccion[anoMes] = null;
            }
          }
          else {
            var $ultimoInforme = self.$informesDisponibles.find(".informe:last");
            if($ultimoInforme.size() > 0) {
              self.seleccionarInforme($ultimoInforme.eq(0), ano, mes);
            }
          }

        },
        "error": function() {
          self.$informesDisponibles.find("li.flash").html("Ocurrió un error");
          self.$informesDisponibles.find("li.informe").remove();
        },
        "complete": function() {
          self.$informesDisponibles.find(".leyenda .indicador-carga").hide();
        }
      });

    }
  };



   /**
    * Exporta clase a espacio global
    *
    */
   window.Reporte2 = window.Reporte2 || {};
   window.Reporte2.ListaDeInformesDisponibles = ListaDeInformesDisponibles;

})(window.jQuery);
