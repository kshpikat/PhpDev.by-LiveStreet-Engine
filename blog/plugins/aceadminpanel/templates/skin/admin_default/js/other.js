if (!js_admin) {
    var js_admin = {};
}

function ajaxTextPreview(textId, save, divPreview) {
    var text;
    if (BLOG_USE_TINYMCE && tinyMCE && (ed = tinyMCE.get(textId))) {
        text = ed.getContent();
    } else {
        text = $(textId).value;
    }
    /*
     JsHttpRequest.query(
     'POST ' + DIR_WEB_ROOT + '/include/ajax/textPreview.php',
     { text: text, save: save, security_ls_key: LIVESTREET_SECURITY_KEY },
     function(result, errors) {
     if (!result) {
     msgErrorBox.alert('Error', 'Please try again later');
     }
     if (result.bStateError) {
     msgErrorBox.alert('Error', 'Please try again later');
     } else {
     if (!divPreview) {
     divPreview = 'text_preview';
     }
     if ($(divPreview)) {
     $(divPreview).set('html', result.sText).setStyle('display', 'block');
     }
     }
     },
     true
     );
     */
}


// для опроса
function addField(btn) {
    tr = btn;
    while (tr.tagName != 'TR') tr = tr.parentNode;
    var newTr = tr.parentNode.insertBefore(tr.cloneNode(true), tr.nextSibling);
    checkFieldForLast();
}
function checkFieldForLast() {
    btns = document.getElementsByName('drop_answer');
    for (i = 0; i < btns.length; i++) {
        btns[i].disabled = false;
    }
    if (btns.length <= 2) {
        btns[0].disabled = true;
        btns[1].disabled = true;
    }
}
function dropField(btn) {
    tr = btn;
    while (tr.tagName != 'TR') tr = tr.parentNode;
    tr.parentNode.removeChild(tr);
    checkFieldForLast();
}


function checkAllTalk(checkbox) {
    $$('.form_talks_checkbox').each(function(chk) {
        if (checkbox.checked) {
            chk.checked = true;
        } else {
            chk.checked = false;
        }
    });
}

function checkAllReport(checkbox) {
    $$('.form_reports_checkbox').each(function(chk) {
        if (checkbox.checked) {
            chk.checked = true;
        } else {
            chk.checked = false;
        }
    });
}

function checkAllPlugins(checkbox) {
    $$('.form_plugins_checkbox').each(function(chk) {
        if (checkbox.checked) {
            chk.checked = true;
        } else {
            chk.checked = false;
        }
    });
}

function admShowModal(win, pin) {
    winFormImgUpload.show();
    winFormImgUpload.mask.element.setStyles({top:0, position:'fixed'});
    var top = ($(window).getSize().y - winFormImgUpload.element.getSize().y) / 2;
    if (pin) {
        winFormImgUpload.element.setStyle('position', 'fixed').setStyle('top', top.toInt());
    } else {
        top += $(document.body).getScroll().y;
        winFormImgUpload.element.setStyle('position', 'absolute').setStyle('top', top.toInt());
    }
}

function showImgUploadForm() {
    if (Browser.Engine.trident) {
        //return true;
    }
    if (!winFormImgUpload) {
        winFormImgUpload = new StickyWin.Modal({
            content: $('window_load_img'),
            closeClassName: 'close-block',
            useIframeShim: false,
            ignoreScroll: true,
            maskOptions: {
                style: {position: 'fixed'},
                ignoreScroll: false
            },
            showNow: false
        });
    }
    admShowModal(winFormImgUpload, 1);
    $$('input[name=img_file]').set('value', '');
    $$('input[name=img_url]').set('value', 'http://');
    return false;
}

function hideImgUploadForm() {
    winFormImgUpload.hide();
}

function ajaxUploadImg(form, sToLoad) {
    if (typeof(form) == 'string') {
        form = $(form);
    }

    var iFrame = new iFrameFormRequest(form.getProperty('id'), {
        url: aRouter['ajax'] + 'upload/image/',
        dataType: 'json',
        params: {security_ls_key: LIVESTREET_SECURITY_KEY},
        onComplete: function(response) {
            if (response.bStateError) {
                msgErrorBox.alert(response.sMsgTitle, response.sMsg);
            } else {
                lsPanel.putText(sToLoad, response.sText);
                hideImgUploadForm();
            }
        }
    });
    iFrame.send();
}

var winFormImgUpload;

