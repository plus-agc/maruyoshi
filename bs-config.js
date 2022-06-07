const SSI = require('./node_modules/browser-sync');

module.exports = {
    "files": './css/*.css, ./js/*.js, ./*.html',
    "server": {
        baseDir: './',
        index: 'index.html'
    },
    "online": true,
    "open": 'external',
    "proxy": false,
    "port": 3000
}