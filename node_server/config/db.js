const mysql = require('mysql');
const environment = require('./environment');

class DBConnections {
    constructor(db) {
        this.db_host        = environment.database[db].MYSQL_HOST;
        this.db_user        = environment.database[db].MYSQL_USER;
        this.db_password    = environment.database[db].MYSQL_PASSWORD;
        this.db_name        = environment.database[db].MYSQL_BD;
        this.db             = db;
    }

    getConection() {
        console.log(`Connect ${ this.db }`);
        let connection = mysql.createConnection({
            host            : this.db_host,
            user            : this.db_user,
            password        : this.db_password,
            database        : this.db_name,
            charset         : 'utf8mb4',
            connectionLimit : 10
        });
        
        connection.connect(function(error) {
            if(error) throw error;
        });

        return connection;
    }
    
}

module.exports = DBConnections;
