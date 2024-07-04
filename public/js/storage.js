var LocalStorage = {
    get: function(name) {
        return JSON.parse(localStorage.getItem(name))
    },
    set: function(name, data) {
        localStorage.setItem(name, JSON.stringify(data))
    },
    remove: function(name) {
        localStorage.removeItem(name)
    },
    clearAll: function() {
        localStorage.clear()
    },
    exist: function(name) {
        if( this.get(name) ) {
            return true
        }
    }
}