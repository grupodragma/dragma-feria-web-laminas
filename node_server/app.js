'use strict';

require('dotenv').config();

const fileSystem = require('fs')
const environment = require('./config/environment');
const DBConnections = require('./config/db');
const moment = require('moment-timezone');
const express = require('express');
const app = express();

const {
    registrarUsuarioGeneral,
    eliminarUsuarioGeneral,
	obtenerUsuariosGeneral,
	obtenerUsuarioGeneral,
	obtenerUsuarioPorIdGeneral
} = require('./chatGeneral');

let server = '';

if(!environment.server.HTTPS) {
	const http = require('http')
	server = http.createServer(app)
} else {
	const https = require('https')
	const options = {
		key: fileSystem.readFileSync(environment.https_cert.PRIVKEY),
		cert: fileSystem.readFileSync(environment.https_cert.CERT),
		//ca: fileSystem.readFileSync(environment.https_cert.FULLCHAIN)
	};
	server = https.createServer(options, app)
}

var io = require('socket.io')(server, {
  cors: {
    origin: '*',
  }
});

server.listen(environment.server.PORT, environment.server.HOST, () => {
    console.log('===> Servidor escuchando en el puerto', environment.server.PORT);
});

let connectionsDB1 = new DBConnections(`db1`).getConection();
let connectionsDB2 = new DBConnections(`db2`).getConection();
let listIdsConferencias = [];


io.on('connection', (socket) => {

	socket.on('usuarioConectado', (data) => {
		//Chat General
		registrarUsuarioGeneral({ data, socketid: socket.id });
		io.sockets.emit('usuariosGeneralConectados', obtenerUsuariosGeneral())
		//Obtener Usuarios Presenciales
		let queryUsuariosPresenciales = `SELECT DISTINCT v.idvisitantes, vr.fecha_registro, v.apellido_paterno, v.apellido_materno, v.nombres, v.telefono, v.correo, v.cargo, v.idferias, v.tipo
		FROM visitantes_registros vr
		INNER JOIN visitantes v ON v.idvisitantes = vr.idvisitantes AND v.idferias = vr.idferias
		WHERE YEAR(vr.fecha_registro) = YEAR(NOW()) AND MONTH(vr.fecha_registro) = MONTH(NOW()) AND DAY(vr.fecha_registro) = DAY(NOW()) AND v.tipo = 'P'
		AND vr.fecha_registro = (
			SELECT MAX(fecha_registro)
			FROM visitantes_registros
			WHERE idvisitantes = vr.idvisitantes
		)`;
		connectionsDB1.query(queryUsuariosPresenciales, (error, results) => {
			if(error) throw error;
			io.sockets.emit('usuariosPresenciales', results)
		});
	})

	socket.on('mensajeGeneral', (data) => {
		const usuariosConectados = obtenerUsuariosGeneral()
		const usuario = obtenerUsuarioGeneral(data.usuarioSelected)
		usuariosConectados.forEach((item) => {
			io.to(item.socketid).emit('mensajeGeneral', {
				usuario,
				msg: data.msg
			});
		})
		mensajeChatGeneralDB(data);
	})

	socket.on('mensajeGrupal', (data) => {
		const idUsuarios = data.idUsuarios.map(id => parseInt(id))
		const usuariosConectados = obtenerUsuarioPorIdGeneral(data.idferias, idUsuarios)
		const expositor = usuariosConectados.find(item => item.usuario.tipo === 'E')
		const visitante = usuariosConectados.find(item => item.usuario.tipo === 'V')
		const usuario = obtenerUsuarioGeneral(data.usuarioSelected)
		const idchatgrupal = data.idchatgrupal;
		usuariosConectados.forEach((item) => {
			io.to(item.socketid).emit('mensajeGrupal', {
				idUsuarios,
				visitante,
				expositor,
				usuario,
				msg: data.msg,
				idchatgrupal
			});
		})
		mensajeChatGrupalDB(expositor, visitante, data)
	})

	socket.on('iniciarConversacionGrupal', (data) => {
		const usuarios = obtenerUsuarioPorIdGeneral(data.idferias, data.idUsuarios);
		const expositor = usuarios.find(item => item.usuario.tipo === 'E');
		const visitante = usuarios.find(item => item.usuario.tipo === 'V');

		if( typeof visitante === "undefined" || typeof expositor === "undefined" ){
			return;
		}

		const idSocketUsuarios = usuarios.map(item => item.socketid);
		const idchatgrupal = `${visitante.usuario.idusuario}-${expositor.usuario.idusuario}`;
		const usuario = obtenerUsuarioGeneral(data.usuarioSelected)
		connectionsDB1.query(`SELECT tmp.*, e.hash_logo_chat AS logo_empresa FROM (
				SELECT *,
				(
					CASE
					WHEN c.tipo_usuario = "S" THEN (SELECT CONCAT(nombres," ", apellido_paterno," ", apellido_materno) FROM speakers WHERE idspeakers = c.idusuario)
					WHEN c.tipo_usuario = "E" THEN (SELECT CONCAT(nombres," ", apellido_paterno," ", apellido_materno) FROM expositores WHERE idexpositores = c.idusuario)
					WHEN c.tipo_usuario = "V" THEN (SELECT CONCAT(nombres," ", apellido_paterno," ", apellido_materno) FROM visitantes WHERE idvisitantes = c.idusuario)
					ELSE 0
					END
				) AS usuario
				FROM chats c
			) AS tmp
			LEFT JOIN empresas e ON e.idexpositores = tmp.codigoUnico
			WHERE tmp.idchatgrupal = '${idchatgrupal}' AND DATE(fecha_hora) = DATE(NOW())
			ORDER BY tmp.idchats ASC`, (error, results) => {
			if(error) throw error;
			usuarios.forEach((item) => {
				io.to(item.socketid).emit('iniciarConversacionGrupal', {
					expositor,
					visitante,
					idUsuarios: data.idUsuarios,
					idSocketUsuarios,
					msg: '',
					chatHistorial: results,
					idchatgrupal,
					usuario,
					accion: data.accion
				});
			})
		});		
	});

	socket.on('validarUsuario', (data) => {
		listIdsConferencias.push({...data, idsocket: socket.id});
	});

	socket.on('typing', (data)=>{
      enviarMensajeUsuariosPorEmpresa('typing', 'display', data);
    });

	socket.on("chat message", function (data) {
		enviarMensajeUsuariosPorEmpresa('message', 'chat message', data);
		mensajeChatDB(data);
    });
	
	socket.on('enlaceGuardado', data => {
		io.sockets.emit('enlaceRecibido', {'response': true})
	});

	socket.on("transmitirConferencia", data => {
		io.sockets.emit('transmitirAhora', data.dataConferencia);
	});

	socket.on("transmitirConferenciaAuditorio", data => {
		io.sockets.emit('transmitirAhoraAuditorio', data);
	});

	socket.on("actualizar-datos-encabezado", data => {
		io.sockets.emit('obtener-datos-encabezado', data);
	});

	socket.on("cronograma", (data) => {
		let connections = ( data.version === 1 ) ? connectionsDB1 : connectionsDB2;
		let query = `SELECT * FROM (
			SELECT s.nombre AS sala, s.hash_url AS sala_hashurl, c.idcronogramas, c.idferias, c.fecha, c.hora_inicio, c.titulo, CONCAT(c.fecha, " ", c.hora_inicio) AS fecha_inicio,
			(TIMESTAMPDIFF(MINUTE, NOW(), CONCAT(fecha, " ", hora_inicio)) + 1) AS diferencia_minutos
			FROM cronogramas c
			LEFT JOIN salas s ON s.idsalas = c.idsalas
		) AS c
		WHERE c.idferias = ${ data.idferias } AND c.diferencia_minutos = 5
		ORDER BY c.diferencia_minutos ASC
		LIMIT 1`;
		connections.query(query, (error, results) => {
			if(error) throw error;
			io.to(socket.id).sockets.emit("cronograma", results);
		});
	});

	socket.on('disconnect', () => {
		eliminarIdSocketUsuario(socket.id);
		//Chat General
		const usuarios = obtenerUsuariosGeneral();
		const usuarioDesconectado = usuarios.filter(item => item.socketid === socket.id)
		eliminarUsuarioGeneral(socket.id);
		io.sockets.emit('usuariosGeneralConectados', obtenerUsuariosGeneral())
		io.sockets.emit('idUsuariosDesconectado', {
			usuario: usuarioDesconectado[0]
		})
	});

	function mensajeChatDB(data) {
		if(data.idusuario === '')return;

		console.log("data.version", data.version)

		let connections = ( data.version === 1 ) ? connectionsDB1 : connectionsDB2;
		let momentLocale = environment.server.ENV === 'development' ? moment() : moment().tz("America/Lima");
		let dataChat = {
			idferias: data.idferias,
			idempresas: data.idempresas,
			fecha_hora: momentLocale.format('YYYY-MM-DD HH:mm:ss'),
			idusuario: data.idusuario,
			tipo_usuario: data.tipo,
			mensaje: data.msg,
			codigoUnico: data.codigoUnico,
			idplataformas: data.idplataformas || 1 //Id Plataforma Feria
		};
		connections.query('INSERT INTO chats SET ?', dataChat, (error, results) => {
			if(error) throw error;
		});
	}

	function mensajeChatGeneralDB(data) {
		if(data.usuarioSelected.idusuario === '')return;
		let connections = connectionsDB1;
		let momentLocale = environment.server.ENV === 'development' ? moment() : moment().tz("America/Lima");
		let dataChat = {
			idferias: data.usuarioSelected.idferias,
			idcronogramas: data.idcronogramas || 0,
			fecha_hora: momentLocale.format('YYYY-MM-DD HH:mm:ss'),
			idusuario: data.usuarioSelected.idusuario,
			tipo_usuario: data.usuarioSelected.tipo,
			mensaje: data.msg
		};
		connections.query('INSERT INTO chats_conferencias SET ?', dataChat, (error, results) => {
			if(error) throw error;
		});
	}

	function mensajeChatGrupalDB(dataExpositor, dataVisitante, dataMsg) {
		if(dataMsg.usuarioSelected.idusuario === '' || typeof dataExpositor === "undefined" || typeof dataVisitante === "undefined")return;
		let connections = connectionsDB1;
		let momentLocale = environment.server.ENV === 'development' ? moment() : moment().tz("America/Lima");
		let dataChat = {
			idferias: dataMsg.idferias,
			idempresas: 0,
			fecha_hora: momentLocale.format('YYYY-MM-DD HH:mm:ss'),
			idusuario: dataMsg.usuarioSelected.idusuario,
			tipo_usuario: dataMsg.usuarioSelected.tipo,
			mensaje: dataMsg.msg,
			codigoUnico: dataExpositor.usuario.idusuario || 0,
			idplataformas: dataMsg.idplataformas || 1, //Id Plataforma Feria
			idchatgrupal: `${dataVisitante.usuario.idusuario}-${dataExpositor.usuario.idusuario}`
		};
		connections.query('INSERT INTO chats SET ?', dataChat, (error, results) => {
			if(error) throw error;
		});
	}
	
	function enviarMensajeUsuariosPorEmpresa(accion, evento, data = null){
		if(data.codigoUnico && listIdsConferencias.length) {
			for(let i in listIdsConferencias){
				if( data.codigoUnico == listIdsConferencias[i].codigoUnico ) {
					if( accion == 'message' ) {
						io.to(listIdsConferencias[i].idsocket).emit(evento, {
							msg: data.msg,
							usuario: obtenerDatosUsuarioPorIdSocket(socket.id)
						});
					} else {
						io.to(listIdsConferencias[i].idsocket).emit(evento, data);
					}
				}
			}
		}
	}
	
	function obtenerDatosUsuarioPorIdSocket(idsocket){
		let dataUsuario = [];
		if(listIdsConferencias.length) {
			for(let i in listIdsConferencias){
				if( idsocket == listIdsConferencias[i].idsocket ) {
					dataUsuario = listIdsConferencias[i];
				}
			}
		}
		return dataUsuario;
	}
	
	function eliminarIdSocketUsuario(idsocket){
		let idSocketPosicion = '';
		if(listIdsConferencias.length) {
			for(let i in listIdsConferencias){
				if( idsocket == listIdsConferencias[i].idsocket ) {
					idSocketPosicion = i;
				}
			}
		}
		if(idSocketPosicion != ''){
			listIdsConferencias.splice(idSocketPosicion, 1);
		}
	}

});