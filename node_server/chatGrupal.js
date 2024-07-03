const usuarios = [];

const registrarUsuarioGrupal = ({ data, socketid }) => {
    usuarios.push({
        ...data,
        socketid
    })
    //console.log("totalUsuarios", usuarios.length);
    //console.log("usuarios", usuarios);
}

/*const obtenerUsuariosGrupal = (data) => {
    return usuarios;
}

const obtenerUsuarioGrupal = (data) => {
    return usuarios.filter(item => typeof item.usuario !== "undefined")
                    .find(item => item.idferias === data.idferias && item.usuario.tipo === data.tipo && item.usuario.idusuario === data.idusuario)
}

const eliminarUsuarioGrupal = (socketid) => {
    //console.log(`SocketId [${socketid}] Eliminado`)
    const selectedIndex = usuarios.findIndex(item => item.socketid == socketid)
    if( selectedIndex !== -1 ){
        usuarios.splice(selectedIndex, 1);
    }
}

const obtenerIdSocketsGrupal = () => {
    return usuarios.map(item => item.socketid)
} */ 

module.exports = {
    registrarUsuarioGrupal
}