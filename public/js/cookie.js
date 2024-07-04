var Cookie = {
    get: function(cName) {
        const name = cName + "=";
        const cDecoded = decodeURIComponent(document.cookie);
        const cArr = cDecoded .split('; ');
        let res;
        cArr.forEach(val => {
            if (val.indexOf(name) === 0) res = val.substring(name.length);
        })
        return res;
    },
    set: function(cName, cValue, expHours) {
        let date = new Date();
        date.setTime(date.getTime() + (expHours * 3600 * 1000));
        const expires = "expires=" + date.toUTCString();
        document.cookie = cName + "=" + cValue + "; " + expires + "; path=/";
    },
    remove: function(cName) {
        document.cookie = cName+"=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    },
    exist: function(cName) {
        if( typeof this.get(cName) !== "undefined" ) {
            return true;
        }
    }
}