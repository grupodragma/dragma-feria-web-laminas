$(document).on("click", "a", function(e){
    //e.preventDefault();

    if(!Panel.sesionUsuario)return;

    let ordenzona = 0;
    let ordenempresa = 0;
    let url_actual = window.location.pathname;
    let url_click = UsuariosEventos.limpiarUrl($(this).attr("href"));
    let video = 0;
    let whatsapp = 0;
    let rv = 0;
    let zonas = 0;
    let empresas = 0;
    let banner_izquierda_1 = 0;
    let banner_izquierda_2 = 0;
    let banner_derecha_1 = 0;
    let banner_derecha_2 = 0;
    let productos = 0;
    let promociones = 0;
    let vivo = 0;
    let atencion_inmediata = 0;
    let reserva_cita = 0;
    let atencion_virtual = 0;
    let bf_llamada = 0;
    let bf_wsp = 0;

    //if(url_click === '')return;

    if($(this).hasClass("arrow-plano"))return;

    if(url_click.search(/\/zonas\//i) != -1){
        let rsp = url_click.split("zonas/");
        ordenzona = parseInt(rsp[1]);
    }

    if(url_click.search(/\/stand\//i) != -1){
        let rsp = url_click.split("stand/")[1]?.split('empresa/');
        ordenzona = parseInt(rsp[0].split('zona/')[1]?.split('/')[0]);
        ordenempresa = parseInt(rsp[1]);
    }

    if(!ordenzona && !ordenempresa && !$(this).hasClass("menu-encabezado") && url_actual.search(/\/stand\//i) != -1){
        let rsp = url_actual.split("stand/")[1]?.split('empresa/');
        ordenzona = parseInt(rsp[0].split('zona/')[1]?.split('/')[0]);
        ordenempresa = parseInt(rsp[1]);
    }

    if(!ordenzona && !ordenempresa && url_click.search(/\/productos\//i) != -1){
        let rsp = url_click.split("productos/")[1]?.split('empresa/');
        ordenzona = parseInt(rsp[0].split('zona/')[1]?.split('/')[0]);
        ordenempresa = parseInt(rsp[1]);
    }

    if(!ordenzona && !ordenempresa && url_click.search(/\/promociones\//i) != -1){
        let rsp = url_click.split("promociones/")[1]?.split('empresa/');
        ordenzona = parseInt(rsp[0].split('zona/')[1]?.split('/')[0]);
        ordenempresa = parseInt(rsp[1]);
    }

    if(!ordenzona && !ordenempresa && ( $(this).hasClass("reserva_cita") || $(this).hasClass("atencion_virtual") ) && url_actual.search(/\/expositores-vivo\//i) != -1){
        let rsp = url_actual.split("expositores-vivo/")[1]?.split('empresa/');
        ordenzona = parseInt(rsp[0].split('zona/')[1]?.split('/')[0]);
        ordenempresa = parseInt(rsp[1].split('/')[0]);
        if( $(this).hasClass("reserva_cita") )reserva_cita = 1;
        if( $(this).hasClass("atencion_virtual") )atencion_virtual = 1;
    }

    if(ordenzona && ordenempresa && ( $(this).hasClass("bf_llamada") || $(this).hasClass("bf_wsp") ) && url_actual.search(/\/stand\//i) != -1){
        let rsp = url_actual.split("stand/")[1]?.split('empresa/');
        ordenzona = parseInt(rsp[0].split('zona/')[1]?.split('/')[0]);
        ordenempresa = parseInt(rsp[1].split('/')[0]);
        if( $(this).hasClass("bf_llamada") )bf_llamada = 1;
        if( $(this).hasClass("bf_wsp") )bf_wsp = 1;
    }
    
    //Asociar la zona y stand a la url
    if( $(this).hasClass("informacion") ){
        if(url_actual.search(/\/stand\//i) != -1){
            let rsp = url_actual.split("stand/")[1]?.split('empresa/');
            ordenzona = parseInt(rsp[0].split('zona/')[1]?.split('/')[0]);
            ordenempresa = parseInt(rsp[1]);
        }
    }

    if( $(this).hasClass("tv1_open") || $(this).hasClass("tv2_open") )video = 1;
    if( $(this).hasClass("wsp") )whatsapp = 1;
    if( $(this).hasClass("rv") )rv = 1;
    if( $(this).hasClass("zonas") )zonas = 1;
    if( $(this).hasClass("empresas") )empresas = 1;
    if( $(this).hasClass("banner_izquierda_1") )banner_izquierda_1 = 1;
    if( $(this).hasClass("banner_izquierda_2") )banner_izquierda_2 = 1;
    if( $(this).hasClass("banner_derecha_1") )banner_derecha_1 = 1;
    if( $(this).hasClass("banner_derecha_2") )banner_derecha_2 = 1;
    if( $(this).hasClass("productos") )productos = 1;
    if( $(this).hasClass("promociones") )promociones = 1;
    if( $(this).hasClass("en-vivo") )vivo = 1;

    if(["/logout"].indexOf(url_click) != -1){
        ordenzona = 0;
        ordenempresa = 0;
    }
    
    if(ordenzona && !ordenempresa){
        zonas = 1;
    }

    let params = {
        url_actual: url_actual,
        url_click: url_click,
        ordenzona: ordenzona,
        ordenempresa: ordenempresa,
        video: video,
        whatsapp: whatsapp,
        rv: rv,
        zonas: zonas,
        empresas: empresas,
        banner_izquierda_1: banner_izquierda_1,
        banner_izquierda_2: banner_izquierda_2,
        banner_derecha_1: banner_derecha_1,
        banner_derecha_2: banner_derecha_2,
        productos: productos,
        promociones: promociones,
        vivo: vivo,
        atencion_inmediata: atencion_inmediata,
        reserva_cita: reserva_cita,
        atencion_virtual: atencion_virtual,
        bf_llamada: bf_llamada,
        bf_wsp: bf_wsp
    };

    //Validar Clases Eventos Dinámicos
    const datosPaginas = UsuariosEventos.obtenerPaginaCampoReferencia($(this));

    if( datosPaginas && datosPaginas.campo !== '' ) {
        //console.log("datosPaginas", datosPaginas);
        params[datosPaginas.campo] = 1;
        if(datosPaginas.ordenzona && datosPaginas.ordenempresa){
            params['ordenzona'] = parseInt(datosPaginas.ordenzona);
            params['ordenempresa'] = parseInt(datosPaginas.ordenempresa);
        }
    }

    //console.log("params", params);

    UsuariosEventos.guardar(params);

});

let UsuariosEventos = {
    idioma: '',
    ordenzona: 0,
    ordenempresa: 0,
    guardar: function(data){
        $.post(this.idioma+"/panel/guardar-eventos-usuario",data,function(response){
            socket.emit("actualizar-datos-encabezado", {});
        }, 'json');
    },
    limpiarUrl: function(url){
        let enlace = url;
        if ( url?.search(/javascript:abrirVentanaWindow/i) != -1 ) {
            enlace = url?.split("'")[1];
        }
        return this.validarUrl(enlace);
    },
    validarUrl: function(url){
        let urlInvalidos = ["#","javascript:void(0)"];
        if( urlInvalidos.indexOf(url) != -1 )return '';
        else return url;
    },
    obtenerPaginaCampoReferencia(obj){
        let _this = this;
        let paginas = ["productos","promociones","planos","expositores-vivo","spikers-vivo"];
        let resultado = new Object();
        paginas.forEach(pagina => {
            let urlActual = window.location.href;
            let expresion = new RegExp(`/panel/${pagina}/`);
            if(expresion.test(urlActual)){
                resultado.pagina = pagina;
                resultado.campo = '';
                resultado.ordenzona = parseInt(_this.ordenzona);
                resultado.ordenempresa = parseInt(_this.ordenempresa);
                let paginaSelected = pagina.replaceAll("-","_");
                if($(obj).hasClass("_recorrido360")){
                    resultado.campo = `${paginaSelected}_recorrido360`;
                } else if($(obj).hasClass("_wsp")){
                    resultado.campo = `${paginaSelected}_wsp`;
                } else if($(obj).hasClass("_solicitar_informacion")){
                    resultado.campo = `${paginaSelected}_solicitar_informacion`;
                } else if($(obj).hasClass("_url")){
                    resultado.campo = `${paginaSelected}_url`;
                } else if($(obj).hasClass("_modal_abrir_enlace")){
                    resultado.campo = `${paginaSelected}_modal_abrir_enlace`;
                } else if($(obj).hasClass("_modal_envia_correo")){
                    resultado.campo = `${paginaSelected}_modal_envia_correo`;
                } else if($(obj).hasClass("_modal_wsp")){
                    resultado.campo = `${paginaSelected}_modal_wsp`;
                } else if($(obj).hasClass("_modal_brochure")){
                    resultado.campo = `${paginaSelected}_modal_brochure`;
                } else if($(obj).hasClass("_correo")){
                    resultado.campo = `${paginaSelected}_correo`;
                } else if($(obj).hasClass("_telefono")){
                    resultado.campo = `${paginaSelected}_telefono`;
                } else {
                    //
                }
            }
        })
        //console.log(resultado);
        return resultado;
    }
}