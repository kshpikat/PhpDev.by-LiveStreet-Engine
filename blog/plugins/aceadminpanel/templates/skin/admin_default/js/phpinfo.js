var js_func = js_admin['info_phpinfo'];
var sections = js_func.load();

window.addEvent('domready', function() {
    for (var key in sections) {
        if (sections[key]) {js_func.action(key, 1);}
    }
});
