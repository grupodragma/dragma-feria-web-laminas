const usuarios = [];

const registrarUsuarioGeneral = ({ data, socketid }) => {
    
    const existeUsuario = usuarios.find(item => item.idferias === data.idferias && item.usuario.idusuario === data.usuario.idusuario)

    if(existeUsuario)return;

    usuarios.push({
        ...data,
        socketid
    })

    //console.log("=============================>")
    //console.log("usuarios", usuarios);
}

const obtenerUsuariosGeneral = () => {
    return usuarios.filter(item => typeof item.usuario !== "undefined");
}

const obtenerUsuarioGeneral = (data) => {
    return usuarios.filter(item => typeof item.usuario !== "undefined")
                    .find(item => item.idferias === data.idferias && item.usuario.tipo === data.tipo && item.usuario.idusuario === data.idusuario)
}

const obtenerUsuarioPorIdGeneral = (idferias, idUsuarios) => {
    return usuarios.filter(item => item.idferias === idferias && idUsuarios.indexOf(item.usuario.idusuario) !== -1)
}

const eliminarUsuarioGeneral = (socketid) => {
    //console.log(`SocketId [${socketid}] Eliminado`)
    const selectedIndex = usuarios.findIndex(item => item.socketid == socketid)
    if( selectedIndex !== -1 ){
        usuarios.splice(selectedIndex, 1);
    }
    //console.log("=============================> UPDATE")
    //console.log("usuarios", usuarios);
}

const obtenerIdSocketsGeneral = () => {
    return usuarios.map(item => item.socketid)
}

module.exports = {
    registrarUsuarioGeneral,
    eliminarUsuarioGeneral,
    obtenerIdSocketsGeneral,
    obtenerUsuariosGeneral,
    obtenerUsuarioGeneral,
    obtenerUsuarioPorIdGeneral
}