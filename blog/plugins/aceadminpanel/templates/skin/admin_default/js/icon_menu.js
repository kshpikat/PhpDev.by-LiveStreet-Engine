var aceAdminIcon = {
    'lib': 'moo',
    'moo': {
        'createMenu': function() {
            var iconMenu = new Element('div', {
                'id': 'adm_icon_menu',
                'html': '<a href="'+DIR_WEB_ROOT+'/admin/"><img src="'+DIR_WEB_ROOT+'/plugins/aceadminpanel/templates/skin/admin_default/images/icon_menu3.png" alt="" /></a>',
                'styles': {
                    'height': '20px',
                    'width': '100px',
                    'position': 'fixed',
                    'top': '0px',
                    'left': '2px'
                }
            });
            var body = document.getElementsByTagName('body');

            iconMenu.inject(body[0]);
        }
    },
    'jq': {
        'createMenu': function() {
            var iconMenu = $('<div></div>', {
                "id": 'adm_icon_menu',
                "html": '<a href="'+DIR_WEB_ROOT+'/admin/"><img src="'+DIR_WEB_ROOT+'/plugins/aceadminpanel/templates/skin/admin_default/images/icon_menu3.png" alt="" /></a>'
            }).css({
                "height": "20px",
                "width": "100px",
                "position": "fixed",
                "top": "0px",
                "left": "2px",
                "z-index": 1
            }).prependTo("body");
        }
    }
};

var aceAdminInit = function() {};

if (window.jQuery) {
    aceAdminIcon.lib = 'jq';
    aceAdminInit = aceAdminIcon.jq.createMenu;
    $(function(){
        aceAdminInit();
    })
} else {
    aceAdminIcon.lib = 'moo';
    aceAdminInit = aceAdminIcon.moo.createMenu;
    window.addEvent('domready', function() {
        aceAdminInit();
    });
}
