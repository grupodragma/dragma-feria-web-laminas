'use strict';

module.exports = (() => {

  return {
    server: {
      PORT: process.env.PORT || 3000,
      HOST: process.env.HOST,
      HTTPS: process.env.HTTPS === 'true',
      ENV: process.env.ENV
    },
    https_cert: {
        PRIVKEY: process.env.HTTPS_PRIVKEY,
        CERT: process.env.HTTPS_CERT,
        FULLCHAIN: process.env.HTTPS_FULLCHAIN
    },
    database: {
      db1: {
        MYSQL_HOST: process.env.MYSQL_HOST_1,
        MYSQL_USER: process.env.MYSQL_USER_1,
        MYSQL_PASSWORD: process.env.MYSQL_PASSWORD_1,
        MYSQL_BD: process.env.MYSQL_BD_1
      },
      db2: {
        MYSQL_HOST: process.env.MYSQL_HOST_2,
        MYSQL_USER: process.env.MYSQL_USER_2,
        MYSQL_PASSWORD: process.env.MYSQL_PASSWORD_2,
        MYSQL_BD: process.env.MYSQL_BD_2
      }
    }
  };

})();
