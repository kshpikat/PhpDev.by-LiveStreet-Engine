function is_array(mixed_var) {
    return ( mixed_var instanceof Array );
}

function is_object(mixed_var) {
    if (is_array(mixed_var)) {
        return false;
    } else {
        return (mixed_var !== null) && (typeof( mixed_var ) == 'object');
    }
}

var js_admin = {
    "info_phpinfo": {
        action: function(n, close) {
            var el = $('section_' + n);
            var cl = $('close_' + n);
            if (el) {
                if (typeof(close) == "undefined") {
                    close = !sections[n];
                }
                if (!close) {
                    el.style.display = '';
                    sections['' + n] = 0;
                    if (cl) cl.set('html', '[&nbsp;-&nbsp;]');
                    this.save();
                } else {
                    el.style.display = 'none';
                    sections['' + n] = 1;
                    if (cl) cl.set('html', '[&nbsp;+&nbsp;]');
                    this.save();
                }
            }
        },
        save: function() {
            var s = JSON.encode(sections);
            Cookie.write('adm_phpinfo_sec', s, {
                duration: 365
            });
        },
        load: function() {
            var s = Cookie.read('adm_phpinfo_sec');
            if (s) {
                var a = JSON.decode(s);
            }
            if (is_object(a)) {
                return a;
            }
            else {
                return {};

            }
        }
    },

    "ajax_url": function(url, query) {
        var request_url = DIR_WEB_ROOT + url;
        if (query)
            request_url += '?' + query + '&security_ls_key=' + LIVESTREET_SECURITY_KEY;
        else
            request_url += '?security_ls_key=' + LIVESTREET_SECURITY_KEY;
        return request_url;
    }
}

function AdminBlogDelete(msg, name, blog_id) {
    if (name) msg = msg.replace('%%blog%%', name);
    if (confirm(msg)) {
        //var url = DIR_WEB_ROOT+'/admin/blogs/delete/?blog_id='+blog_id+'&security_ls_key='+LIVESTREET_SECURITY_KEY;
        var url = js_admin.ajax_url('/admin/blogs/delete/', 'blog_id=' + blog_id);
        document.location.href = url;
        return true;
    }
    return false;
}

function AdminTopicDelete(msg, name, topic_id) {
    if (name) msg = msg.replace('%%topic%%', name);
    if (confirm(msg)) {
        //var url = DIR_WEB_ROOT+'/admin/topics/delete/?topic_id='+topic_id+'&security_ls_key='+LIVESTREET_SECURITY_KEY;
        var url = js_admin.ajax_url('/admin/topics/delete/', 'topic_id=' + topic_id);
        document.location.href = url;
        return true;
    }
    return false;
}

function AdminGetElements(className) {
    return $(document.body).getElements(className);
}

function AdminSetStyleProp(elements, property, value) {
    elements.each(function(item, index) {
        item.setStyle(property, value);
    });
}

function AdminVote(idTarget, objVote, value, type) {
    js_admin.vote.vote(idTarget, objVote, value, type);
}


var AdminVoteClass = new Class({

    Implements: Options,

    options: {
        classes_action: {
            voted:          'voted',
            plus:           'plus',
            minus:          'minus',
            positive:       'positive',
            negative:       'negative',
            quest:          'quest'
        },
        classes_element: {
            voting:         'voting',
            count:          'count',
            total:          'total',
            plus:           'plus',
            minus:          'minus'
        }
    },

    typeVote: {
        user: {
            url: aRouter['ajax'] + 'admin/vote/user/',
            targetName: 'idUser'
        }
    },

    initialize: function(options) {
        this.setOptions(options);
    },

    vote: function(idTarget, objVote, value, type) {
        if (!this.typeVote[type]) {
            return false;
        }

        this.idTarget = idTarget;
        this.objVote = $(objVote);
        this.value = value;
        this.type = type;
        thisObj = this;

        var params = new Hash();
        params['value'] = value;
        params[this.typeVote[type].targetName] = idTarget;
        params['security_ls_key'] = LIVESTREET_SECURITY_KEY;

        new Request.JSON({
            url: this.typeVote[type].url,
            noCache: true,
            data: params,
            onSuccess: function(result) {
                thisObj.onVote(result, thisObj);
            },
            onFailure: function() {
                msgErrorBox.alert('Error', 'Please try again later');
            }
        }).send();
    },

    onVote: function(result, thisObj) {
        if (!result) {
            msgErrorBox.alert('Error', 'Please try again later');
        }
        if (result.bStateError) {
            msgErrorBox.alert(result.sMsgTitle, result.sMsg);
        } else {
            msgNoticeBox.alert(result.sMsgTitle, result.sMsg);

            var divVoting = thisObj.objVote.getParent('.' + thisObj.options.classes_element.voting);
            //divVoting.addClass(thisObj.options.classes_action.voted);

            if (this.value > 0) {
                //divVoting.addClass(thisObj.options.classes_action.plus);
            }
            if (this.value < 0) {
                //divVoting.addClass(thisObj.options.classes_action.minus);
            }
            var divCount = divVoting.getChildren('.' + thisObj.options.classes_element.count);
            if (divCount && divCount[0]) {
                divCount.set('text', result.iCountVote);
            }

            var divTotal = divVoting.getChildren('.' + thisObj.options.classes_element.total);
            result.iRating = parseFloat(result.iRating);
            divVoting.removeClass(thisObj.options.classes_action.negative);
            divVoting.removeClass(thisObj.options.classes_action.positive);
            if (result.iRating > 0) {
                divVoting.addClass(thisObj.options.classes_action.positive);
                divTotal.set('text', '+' + result.iRating);
            }
            if (result.iRating < 0) {
                divVoting.addClass(thisObj.options.classes_action.negative);
                divTotal.set('text', result.iRating);
            }
            if (result.iRating == 0) {
                divTotal.set('text', '0');
            }

            if (thisObj.type == 'user' && $('user_skill_' + thisObj.idTarget)) {
                $('user_skill_' + thisObj.idTarget).set('text', result.iSkill);
            }
        }
    }

});

window.addEvent('domready', function() {
      js_admin.vote=new AdminVoteClass();
});