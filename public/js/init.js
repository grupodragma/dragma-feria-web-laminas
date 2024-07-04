let Panel = {
    idioma: '',
    modalLoginUser: $(".modal-login-user"),
    sesionUsuario: '',
    formCorreoAccion: '',
    formCorreoId: '',
    contenedorAuditorio: $(".home-conferencias-zoom"),
    idferias: '',
    version: 0,
    idplataformas: 0,
    urlBackend: null,
    contenedorPromocion: $("[modal-promocion='contenedor']"),
    imagenPromocion: $("[modal-promocion='imagen']"),
    init: function(){
        this.sesionUsuario = ( this.sesionUsuario === '1' ) ? true : false;
        this.validarPromocion();
    },
    enviarCorreo: function(accion, id, obj, idempresas, event = null){
        if(event) {
            event?.preventDefault();
            event?.stopPropagation();
        }
        
        if(this.sesionUsuario){
            $(obj).addClass("disabled");
            $.post(this.idioma+"/panel/enviar-correo", {accion: accion, id: id, idempresas},function(response){
                if( response.result == "success" ) {
                    alert("Correo enviado correctamente...");
                }
                $(obj).removeClass("disabled");
            }, 'json');
        } else {
            this.modalLoginUser.css({'display': 'flex'});
        }
    },
    cambiarIdioma: function(obj){
        let idioma = $(obj).val();
        let idiomaUrl = '/'+idioma+'/';
        let url = this.getUrlIdioma(idioma, idiomaUrl);
        let urlRedirect = ( window.location.pathname === '/' ) ? url+idioma : url;
        window.location.href = urlRedirect;
    },
    getUrlIdioma(idioma, idiomaUrl){
        let idiomaIndex = '/'+idioma; 
        let url = '';
        if(window.location.pathname === '/'){
            url = window.location.href.replace('/es', idiomaUrl).replace('/en', idiomaUrl).replace('/pt', idiomaUrl).replace('/zh', idiomaUrl);
        } else {
            if(['/es','/en','/pt','/zh'].indexOf(window.location.pathname) !== -1){
                url = window.location.href.replace('/es', idiomaIndex).replace('/en', idiomaIndex).replace('/pt', idiomaIndex).replace('/zh', idiomaIndex);
            } else {
                url = window.location.href.replace('/es/', idiomaUrl).replace('/en/', idiomaUrl).replace('/pt/', idiomaUrl).replace('/zh/', idiomaUrl);
            }
        }
        return url;
    },
    validarSesion: function(event){
        console.log("Validar sesión");
        if(!this.sesionUsuario){
            if(event != null)event.preventDefault();
            this.generarCsrfToken($("input[name='csrf_token']"));
            this.modalLoginUser.css({'display': 'flex'});
        }
    },
    userSesion: function(event){
        if(this.sesionUsuario){
            return true;
        }
    },
    generarCsrfToken: function(obj) {
        $.post("/generar-csrf-token", function(response) {
            obj.val(response.csrf_token)
        }, "json");
    },
    logout: function(){
        localStorage.clear();
    },
    detectarAnuncio: function(){
        if(!this.contenedorAuditorio.find("img, iframe").is(":visible")){
            setTimeout(() => {
                $(".alerta-anuncio").show();
            }, 2000);
        }
    },
    validarPromocion: function(){
        if(Panel.sesionUsuario){
            setTimeout(() => {
                if(!localStorage.getItem('estadoPromocion')){
                    $('.modal-promocion').show();
                }
                localStorage.setItem('estadoPromocion', true);
            }, 2000);
        }
    },
    mostrarPromocionZonas: function(idpromociones, imagen){
        var href = window.location.href;
        if(/panel\/recepcion/.test(href) || /panel\/stand/.test(href)){
            console.log("Páginas no válidas para mostrar promoción programadas")
            return;
        }
        if(Panel.sesionUsuario && idpromociones != "" && imagen != "" && !localStorage.getItem(`promocionProgramada-${idpromociones}`)){
            this.contenedorPromocion.show();
            this.imagenPromocion.attr("src", `${ this.urlBackend }/promociones/imagen/${ imagen }`)
        }
    },
    cerrarPromocionZonas: function(idpromociones) {
        this.contenedorPromocion.hide();
        this.imagenPromocion.attr("src", "");
        localStorage.setItem(`promocionProgramada-${idpromociones}`, true);
    }
}

let Tiempo = {
    timeInit: 0,
    time: 0,
    interval: 0,
    socket: null,
    iniciar: function(){
        var _this = this;
        _this.time = _this.timeInit; //* 60;
        clearInterval(this.interval);
        _this.procesar();
        _this.interval = setInterval(function(){
            _this.time--;
            _this.procesar();
        },1000);
    },
    procesar: function(){
        var _this = this;
        if(_this.time<0){
            clearInterval(_this.interval);
            _this.emitir();
            _this.iniciar();
            return;
        }
        var date = new Date(1970,0,1);
        date.setSeconds(_this.time);
        var time = date.toTimeString().replace(/.*(\d{2}:\d{2}:\d{2}).*/, "$1");
        //console.log("Tiempo", time);
    },
    detener: function(){
        clearInterval(this.interval);
    },
    emitir: function(){
        socket.emit("cronograma", {
            version: Panel.version,
            idferias: Panel.idferias
        });
    }
}

let Chat = {
    usuarioSelected: null
}

let ChatGeneral = {
    chatContenedor: $("[chat-general='contenedor']"),
    chatContador: $("[chat-general='contador']"),
    chatMensajes: $("[chat-general='mensajes']"),
    chatTotalMensajes: $("[chat-general='total-mensajes']"),
    chatMensajePlantilla: `<div class="bol"><span class="prefix"></span></div><div class="te"><strong class="user"></strong><span class="msg"></span></div>`,
    chatMensajePlantillaExpositor: `<div class="bol2"><img class="logo" src=""></div><div><strong class="user"></strong><span class="msg"></span></div>`,
    campoInput: $("[chat-general='input']"),
    idcronogramas: 0,
    enviarMensaje: function(){
        let inputMsg = this.campoInput;
        if(inputMsg.val() == "")return;
        socket.emit('mensajeGeneral', {
            usuarioSelected: Chat.usuarioSelected,
            msg: inputMsg.val(),
            idcronogramas: ChatGeneral.idcronogramas
        });
        inputMsg.val('');
    },
    scrollTop: function() {
        this.chatContenedor.find(".chat-body").animate({scrollTop: 50 * 9000});
    },
    totalMensajesActivos: function(){
        let totalMensajes = this.chatMensajes.find(".msg-send2").length;
        this.chatTotalMensajes.text(`(${totalMensajes} Mensajes)`);
    }
}

let ChatGrupal = {
    chatContenedor: $("[chat-grupal='contenedor']"),
    chatEncabezado: $("[chat-grupal='encabezado']"),
    chatMensajes: $("[chat-grupal='mensajes']"),
    usuariosContenedor: $("[chat-grupal='contenedor-lista-usuarios']"),
    listaUsuariosContenedor: $("[chat-grupal='lista-usuarios']"),
    usuariosPaginaContenedor: $("[chat-grupal='contenedor-lista-usuarios-paginas']"),
    listaUsuariosPaginasContenedor: $("[chat-grupal='lista-usuarios-paginas']"),
    usuariosPresencialesContenedor: $("[chat-grupal='contenedor-lista-usuarios-presenciales']"),
    listaUsuariosPresencialesContenedor: $("[chat-grupal='lista-usuarios-presenciales']"),
    listaUsuariosItemHtml: `<div class="tarjeta" style="display: none;">
        <div class="left-tarjeta"><span data-item="letra-inicial"></span></div>
        <div class="right-tarjeta">
            <h3 data-item="usuario"></h3>
            <div class="datito"><small data-item="empresa"></small><big data-item="cargo"></big></div>
            <div class="datito2"><small data-item="correo"></small><big data-item="telefono"></big></div>
            <div class="datito3"><small data-item="hora"></small><big data-item="fecha"></big></div>
        </div>
    </div>
    <span data-item="letra-inicial"></span>
    <div class="compa">
        <big data-item="usuario"></big>
        <div class="res"><small data-item="empresa"></small> - <strong data-item="cargo"></strong></div>
    </div>
    <div class="iconrr">              
        <a href="javascript:void(0)">
            <img src="imagenes/chat-cloud-01.svg">
        </a>
        <a href="javascript:void(0)" onclick="ChatGrupal.action(this, 'phone', event)">
            <img src="imagenes/call.svg">
        </a>
        <a href="javascript:void(0)" onclick="ChatGrupal.action(this, 'wsp', event)">
            <img src="imagenes/wsp.svg">
        </a>
        <a href="javascript:void(0)" onclick="ChatGrupal.action(this, 'email', event)">
            <img src="imagenes/sms.svg">
        </a>
    </div>`,
    chatEncabezadoVisitanteHtml: `<div class="icon-2"><span data-item="letra-inicial"></span></div>
    <div class="names-user">
        <h3 data-item="usuario"></h3>
        <h4><span data-item="empresa"></span> - <span data-item="cargo"></span></h4>
    </div>
    <div class="si">
        <img src="https://maxcdn.icons8.com/windows10/PNG/16/Arrows/angle_down-16.png" title="Expand Arrow" width="16" alt="">
    </div>`,
    chatEncabezadoExpositorHtml: `<div class="icon">
        <span></span>
        <img data-item="logo" src="" />
    </div>
    <h2 data-item="empresa"></h2>
    <div class="si">
        <img src="https://maxcdn.icons8.com/windows10/PNG/16/Arrows/angle_down-16.png" title="Expand Arrow" width="16" />
    </div>`,
    campoInput: $("[chat-grupal='input']"),
    chatMensajePlantilla: `<div class="bol"><span class="prefix"></span></div><div class="te"><strong class="user"></strong><span class="msg"></span></div>`,
    chatMensajePlantillaExpositor: `<div class="bol2"><img class="logo" src=""></div><div><strong class="user"></strong><span class="msg"></span></div>`,
    listaChatContenedor: $("[chat-grupal='contenedor-usuarios-chat']"),
    listaChatSlider: $("[chat-grupal='contenedor-usuarios-chat-slider']"),
    nuevoMensaje: new Object(),
    ocultarListaUsuarios: function(accion) {
        this.usuariosContenedor.hide();
        this.usuariosPaginaContenedor.hide();
        this.usuariosPresencialesContenedor.hide();
        if(accion == 'total-usuarios' && !this.usuariosContenedor.is(":visible")){
            this.usuariosContenedor.show();
        }
        if(accion == 'usuarios-paginas' && !this.usuariosPaginaContenedor.is(":visible")){
            this.usuariosPaginaContenedor.show();
        }
        if(accion == 'usuarios-presenciales' && !this.usuariosPresencialesContenedor.is(":visible")){
            this.usuariosPresencialesContenedor.show();
        }
    },
    scrollTop: function() {
        this.chatContenedor.find(".chat-body").animate({scrollTop: 50 * 9000});
    },
    iniciarConversacion: function(idusuario) {
        const idvisitante = parseInt(idusuario);
        const idexpositor = Chat.usuarioSelected.idusuario;
        const idchatgrupal = `${idvisitante}-${idexpositor}`;
        socket.emit('iniciarConversacionGrupal', {
            accion: 'iniciar',
            idferias: Panel.idferias,
            idUsuarios: [idvisitante, idexpositor],
            usuarioSelected: Chat.usuarioSelected,
            idchatgrupal
        })
    },
    iniciarConversacionStand: function(visitante, expositor) {
        const idUsuarios = [ visitante.idusuario, parseInt(expositor.idexpositores) ];
        socket.emit('iniciarConversacionGrupal', {
            accion: 'iniciar',
            idferias: Panel.idferias,
            idUsuarios: idUsuarios,
            usuarioSelected: {
              tipo: expositor.tipo,
              idusuario: parseInt(expositor.idexpositores),
              nombres: expositor.nombres,
              apellido_paterno: expositor.apellido_paterno,
              apellido_materno: expositor.apellido_materno,
              cargo: expositor.cargo,
              empresa: expositor.nombre_empresa,
              idferias: expositor.idferias,
              logo_empresa: expositor.logo_empresa
            },
            idchatgrupal: idUsuarios.join("-")
        });
    },
    abrirNuevaConversacion: function(data) {
        const usuarioSelected = data.expositor.usuario.idusuario === Chat.usuarioSelected.idusuario ? data.expositor.usuario : data.visitante.usuario;
        socket.emit('iniciarConversacionGrupal', {
            accion: 'chat',
            idferias: Panel.idferias,
            idUsuarios: data.idUsuarios,
            usuarioSelected,
            idchatgrupal: data.idchatgrupal,
        })
    },
    iniciarConversacionReconexion: function(visitante, expositor) {
        const idvisitante = visitante.usuario.idusuario;
        const idexpositor = expositor.usuario.idusuario;
        const idchatgrupal = `${idvisitante}-${idexpositor}`;
        socket.emit('iniciarConversacionGrupal', {
            idferias: Panel.idferias,
            idUsuarios: [idvisitante, idexpositor],
            usuarioSelected: expositor,
            idchatgrupal
        })
    },
    enviarMensaje: function(obj) {
        const idUsuariosChatGrupal = this.chatContenedor.find(".chat-head").attr("data-idusuarios").split("-");
        const inputMsg = this.campoInput;
        const idchatgrupal = idUsuariosChatGrupal.join('-');
        if(inputMsg.val() == "")return;
        socket.emit('mensajeGrupal', {
            idferias: Panel.idferias,
            idUsuarios: idUsuariosChatGrupal,
            usuarioSelected: Chat.usuarioSelected,
            msg: inputMsg.val(),
            idchatgrupal
        })
        inputMsg.val('');
    },
    listarChatHistorial: function(chats) {
        for(let chatUsuario of chats){
            let seleccionarPlantilla = ( chatUsuario.tipo_usuario === 'E' ) ? ChatGrupal.chatMensajePlantillaExpositor : ChatGrupal.chatMensajePlantilla;
            let mensaje = $("<div>").addClass("msg-send2").html(seleccionarPlantilla);
            if( chatUsuario.tipo_usuario === 'E' ) {
                mensaje.find(".logo").attr("src", `${Panel.urlBackend}/empresas/logo/${chatUsuario.logo_empresa}`);
            } else {
                mensaje.find(".prefix").text(`${chatUsuario.usuario.charAt(0)}`);
            }
            mensaje.find(".user").text(`${chatUsuario.usuario}: `);
            mensaje.find(".msg").text(`${chatUsuario.mensaje}`);
            ChatGrupal.chatMensajes.append(mensaje);
        }
        ChatGrupal.scrollTop();
    },
    abrirNuevoMensaje: function(obj, idchatgrupal) {
        $(obj).removeClass("actives");
        //console.log("idchatgrupal", idchatgrupal)
        console.log("abrirNuevoMensaje", this.nuevoMensaje[idchatgrupal])
        if(typeof this.nuevoMensaje[idchatgrupal] !== "undefined") {
            this.abrirNuevaConversacion(this.nuevoMensaje[idchatgrupal]);
        }
    },
    action: function(obj, type, event, idUsuario, typeUser) {
        event.stopPropagation();
        $.get(`${Panel.idioma}/panel/datos-visitante`, { id: idUsuario, typeUser }, function(visitante) {
            let a = '';
            switch(type) {
                case 'phone':
                    a = document.createElement("a");
                    a.setAttribute("href", `tel:${visitante.telefono}`)
                    a.click();
                    break;

                case 'wsp':
                    if(datosExpositor){
                        a = document.createElement("a");
                        a.setAttribute("href", `https://api.whatsapp.com/send?phone=${visitante.telefono}&text=Hola%20soy%20${datosUsuarioLogin.nombres}%20de%20${datosFeria.nombre}, me gustaría enviarte información sobre los programas que tengo para ti.`);
                        a.setAttribute("target", "_blank");
                        a.click();
                        break;
                    }

                case 'email':
                    console.log("Send Mail")
                    const idempresas = datosExpositor.idempresas || 0;
                    Panel.enviarCorreo('chat-notificacion', visitante.idvisitantes, new Object(), idempresas);
                    break;
            }
        }, 'json');
    }
}