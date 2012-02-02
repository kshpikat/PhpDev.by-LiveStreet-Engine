// ace-wrapper.js

if (aceWrapper === undefined) {
    var aceWrapper;

    (function() {
        aceWrapper = function() {
            if (arguments.length==1) {
                if ($that.lib == 'jq')
                    return jQuery(arguments[0]);
                else
                    return $$(arguments[0]);
            }
        };
        var $that = aceWrapper;

        $that.lib = 'moo';
        $that.short = true;
        $that.store = {};

        $that.init = function() {
            if (window.jQuery) {
                $that.lib = 'jq';
            } else {
                $that.lib = 'moo';
            }
        };

        $that.dummy = function() {
        };

        $that.func = function(name) {
            if ($that[$that.lib][name]) {
                return $that[$that.lib][name];
            } else {
                return $that.dummy;
            }
        };

        $that.ready = function(fn) {
            return $that.func('ready')(fn);
        };

        $that.ajax = function(url, params, func) {
            return $that.func('ajax')(url, params, func);
        };

        $that.addEvent = function(el, event, func) {
            return $that.func('addEvent')(el, event, func);
        };

        $that.bind = function(el, event, func) {
            return $that.addEvent(el, event, func);
        };

        $that.fireEvent = function(el, event) {
            return $that.func('fireEvent')(el, event);
        };

        $that.trigger = function(el, event)
        {
            return $that.fireEvent(el, event);
        };

        $that.newElement = function(el, properties) {
            return $that.func('newElement')(el, properties);
        };

        $that.append = function(elParent, elChild) {
            return $that.func('append')(elParent, elChild);
        };

        $that.value = function(el, val) {
            if (arguments.length==1) {
                return $that.func('value')(el);
            } else {
                return $that.func('value')(el, val);
            }
        };

        $that.css = function(el, prop, val) {
            if (arguments.length==1) {
                return $that.func('css')(el);
            } else if (arguments.length==2) {
                return $that.func('css')(el, prop);
            } else {
                return $that.func('css')(el, prop, val);
            }
        };

        $that.remove = function(el) {
            return $that.func('remove')(el);
        };


        $that.setText = function(el, val) {
            return $that.func('setText')(el, val);
        };

        $that.setHtml = function(el, val) {
            return $that.func('setText')(el, val);
        };

        /* jquery */
        $that.jq = {
            ready: function(fn) {
                return $(fn);
            },

            ajax: function(url, params, func) {
                ls.ajax(url, params, func);
            },

            element: function(elName, elHtml, elClass) {
                return $('<' + elName + '>').html(elHtml).addClass(elClass);
            },

            newElement: function(elName, properties) {
                return $('<' + elName + '>', properties);
            },

            each: function(el, func) {
                $.each(el, function(idx, el) {
                    func(el)
                });
            },

            setText: function(el, text) {
                return $(el).text(text);
            },

            setHtml: function(el, html) {
                return $(el).html(html);
            },

            value: function(el, val) {
                if (arguments.length==1) {
                    return $(el).val();
                } else {
                    return $(el).val(val);
                }
            },

            css: function(el, prop, val) {
                if (arguments.length==1) {
                    return $(el).css();
                } else if (arguments.length==2) {
                    return $(el).css(prop);
                } else {
                    return $(el).css(prop, val);
                }
            },

            remove: function(el) {
                return $(el).remove();
            },

            addEvent: function(el, event, func) {
                return $(el).bind(event, func);
            },

            fireEvent: function(el, event) {
                return $(el).trigger(event);
            },

            append: function(elParent, elChild) {
                return $(elParent).append(elChild);
            }
        };

        /* mootools */
        $that.moo = {
            ready: function(fn) {
                return window.addEvent('domready', fn);
            },

            ajax: function(url, params, success, failure) {
                new Request.JSON({
                    url: url,
                    noCache: true,
                    data: params,
                    onSuccess: success,
                    onFailure: failure
                }).send();
            },

            element: function(elName, elHtml, elClass) {
                return new Element(elName, {
                    'html': elHtml,
                    'class': elClass
                });
            },

            getElement: function(el) {
                var result = $$(el);
                return result;
            },

            newElement: function(elName, properties) {
                return new Element(elName, properties);
            },

            each: function(el, func) {
                var element = $that.moo.getElement(el);
                element.each(func);
            },

            setText: function(el, text) {
                var element = $that.moo.getElement(el);
                return element.set('text', text);
            },

            setHtml: function(el, html) {
                var element = $that.moo.getElement(el);
                return element.set('html', html);
            },

            value: function(el, val) {
                var element = $that.moo.getElement(el);
                if (arguments.length==1) {
                    return element.get("value");
                } else {
                    return element.set("value", val);
                }
            },

            css: function(el, prop, val) {
                var element = $that.moo.getElement(el);
                if (arguments.length==1) {
                    return element.getStyles();
                } else if (arguments.length==2) {
                    if (typeof( prop ) == 'string') {
                        return element.getStyle(prop);
                    } else {
                        return element.setStyles(prop);
                    }
                } else {
                    return element.setStyle(prop, val);
                }
            },

            remove: function(el) {
                return $$(el).destroy();
            },

            addEvent: function(el, event, func) {
                return $$(el).addEvent(event, func);
            },

            fireEvent: function(el, event) {
                return $$(el).fireEvent(event);
            },

            append: function(elParent, elChild) {
                return $$(elParent).grab(elChild);
            }
        };

        $that.init();
        if ($that.short) $ace = $that;
    })();
}


