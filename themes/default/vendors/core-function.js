/*
 * Ajax pagination request
 *
 * How to use :
 *
 * HTML :
 * <div id="listwrap">
 *      <ul id="listparent">
 *          <li>list</li>
 *          <li>list</li>
 *      </ul>
 *      <div id="morelinkwrap">
 *          <a class="loadmore" href="url">show more</a>
 *      </div>
 * </div>
 *
 * JS :
 * jQuery('a.loadmore').ajaxPagination({
 *      append: true,    // Want to append instead replace html
 *      listwrapper: '#listwrap',
 *      listparent: '#listparent',
 *      morelinkwrap: '#morelinkwrap',
 *      onrequest: function() {
 *          // run some function
 *      },
 *      onsuccess: function() {
 *          // run some function
 *      }
 * });
*/
(function($){
    $.fn.extend({

        // Request anchor link by ajax
        ajaxPagination: function(options) {

            var defaults = {
                url         : null,
                append      : null,
                onsuccess   : false,
                onrequest   : false,
                onerror     : false,
                listwrapper : null, // Wrapper for list and show more link
                listparent  : null, // Wrapper for list
                morelinkwrap : null, // Wrapper for show more link
            };
            var options = $.extend(defaults, options);

            return this.livequery(function() {
                $(this).each(function(index, value) {
                    var element = $(this);
                    var elClass = 'sixreps-infinity-scroll';
                    var morelinkwrap = $(options.listwrapper).find(options.morelinkwrap);
                    var morelink = morelinkwrap.html();

                    element.addClass(elClass);

                    $('.' + elClass).click(function(event) {

                        event.preventDefault();

                        if (options.url != null) {
                            var url = options.url;
                        } else {
                            var linkmore = $(options.listwrapper).find(options.morelinkwrap + ' a');
                            var url = (linkmore.attr('data-ajax')) ? linkmore.attr('data-ajax') : linkmore.attr('href');
                        }

                        if (options.append != null) {
                            var append = options.append;
                        } else {
                            var append = (element.attr('rel') == 'newStream') ? false : true;
                        }

                        if (typeof morelinkwrap.loader != 'undefined') {
                            morelinkwrap.loader('show', {dimension: 'large', fullWidth: true});
                        }

                        // Show loading image

                        $.ajax({
                            url: url,
                            type: 'GET',
                            beforeSend  : function() {
                                var passed = true;

                                // Run onrequest hook
                                if(typeof options.onrequest == 'function'){
                                    passed = options.onrequest.call(this, element);
                                }

                                if (passed == false) {
                                    // Hide loading message
                                    morelinkwrap.html(morelink);
                                }

                                return passed;
                            },
                            complete: function(xhr, textStatus) {
                                //called when complete
                            },
                            success: function(data, textStatus, xhr) {
                                var result = $(data).find(options.listwrapper + ' ' + options.listparent).html();
                                var nextLink = $(data).find(elClass).parent().html();

                                if (result == null || result == '') {
                                    result = $(data).find(options.listparent).html();
                                }

                                if (nextLink == null || nextLink == '') {
                                    nextLink = $(data).find(options.listwrapper + ' ' + options.morelinkwrap).html();
                                }

                                if (nextLink == null || nextLink == '') {
                                    nextLink = $(data).find(options.morelinkwrap).html();
                                }

                                if (append == true) {
                                    // console.log($(options.listparent));
                                    if ($(options.listparent).hasClass('isotope')) {
                                        $tempA = $(options.listparent).after('<div class="temp hidden">' + result + '</div>');
                                        $('.temp').find('#addGallery').remove();
                                        newContent = $('.temp').html();
                                        $(options.listparent).isotope('insert', $(newContent));
                                    } else {
                                        $(options.listwrapper + ' ' + options.listparent).append(result);
                                    }
                                } else {
                                    var mainContent = $(data).find(options.listwrapper).html();
                                    $(options.listwrapper).html(mainContent);
                                }

                                if (nextLink != '' || nextLink != null) {
                                    nextLink = $(nextLink).addClass(elClass);
                                    morelinkwrap.html(nextLink);
                                    // morelinkwrap.find('a').attr('href', linksHref);
                                } else {
                                    morelinkwrap.remove();
                                }

                                $('img.resize').resizeImage();

                                // if ($(''))
                                // console.log(data);

                                if (typeof options.onsuccess == 'function'){
                                    options.onsuccess.call(this, data, element);
                                }

                                // Show loading image

                            },
                            error: function(xhr, textStatus, errorThrown) {
                                if(typeof options.onerror == 'function'){
                                    options.onerror.call(this);
                                }

                                morelinkwrap.html(morelink);
                            }
                        });
                    });

                });
            });
        }
    });
})(jQuery);

(function($){
    jQuery.fn.extend({

        // Request anchor link by ajax
        ajaxRequest: function(options) {

            var defaults = {
                url         : false,
                onsuccess   : false,
                data        : null,
                onrequest   : false,
                onerror     : false
            };
            var options = $.extend(defaults, options);

            return $(this).livequery(function() {
                $(this).each(function() {

                    $(this).click(function(event) {

                        event.preventDefault();

                        var element = $(this);

                        var url = '';

                        if (options.url == '' || options.url == null || options.url == false ) {
                            url = $(this).attr('data-ajax');

                            if (url == '' || url == null || url == false ) {
                                url = $(this).attr('href');
                            }
                        } else {
                            url = options.url;
                        }

                        if (options.data == null) {
                            options.data = element.attr('data-ajax-request');
                        }

                        if (typeof element.loader != 'undefined') {
                            element.loader('show', {dimension: 'small', fullWidth: false});
                        }

                        jQuery.ajax({
                            url         : url,
                            type        : 'POST',
                            data        : options.data,
                            dataType    : 'json',
                            beforeSend  : function(xhr, object) {
                                var passed = true;

                                // Run onrequest hook
                                if(typeof options.onrequest == 'function'){
                                    passed = options.onrequest.call(this, element, xhr, object);
                                }

                                if (passed == false) {
                                    // Hide loading message
                                    
                                }

                                return passed;
                            },
                            success     : function(data, textStatus, xhr) {
                                if(typeof options.onsuccess == 'function'){
                                    options.onsuccess.call(this, data, element);
                                }

                                // Set all error hints (if exists) and return false if success
                                var error = element.errorMessage(data);

                                // Hide loading message
                                if (typeof element.loader != 'undefined') {
                                    element.loader('hide');
                                }                                

                                // Call image reposition
                                $('img.resize').resizeImage();
                            },
                            error       : function(xhr, textStatus, errorThrown) {
                                if(typeof options.onerror == 'function'){
                                    options.onerror.call(this, element);
                                    // options.onerror.call(this, xhr);
                                }

                                if (typeof element.loader != 'undefined') {
                                    element.loader('hide');
                                }                                
                            }
                        });
                    });
                });
            });
        }
    });
})(jQuery);

(function($){
    jQuery.fn.extend({

        // Request ajax builder
        getDataByAjax: function(options) {

            var defaults = {
                url         : false,
                onsuccess   : false,
                onrequest   : false,
                onerror     : false,
                data        : '',
                method      : 'POST',
                dataType    : 'json'
            };
            var options = $.extend(defaults, options);

            url = options.url;

            // event.preventDefault();

            // Show loading image

            return jQuery.ajax({
                url         : url,
                type        : options.method,
                data        : options.data,
                dataType    : options.dataType,
                beforeSend  : function() {
                    var passed = true;

                    // Run onrequest hook
                    if(typeof options.onrequest == 'function'){
                        passed = options.onrequest.call(this);
                    }

                    if (passed == false) {
                        // Hide loading message
                    }

                    return passed;
                },
                success     : function(data, textStatus, xhr) {
                    if(typeof options.onsuccess == 'function'){
                        options.onsuccess.call(this, data);
                    }

                    // Hide loading message

                    $('img.resize').resizeImage();
                },
                error       : function(xhr, textStatus, errorThrown) {
                    if(typeof options.onerror == 'function'){
                        options.onerror.call(this, xhr);
                    }

                    // Hide loading message
                }
            });
        }
    });
})(jQuery);

/*
 * Submit form by ajax
 *
 * How to use :
 *
 * HTML :
 * <form id="myForm">
 * <input type="text">
 * <input type="submit">
 * </form>
 *
 * JS :
 * jQuery('#myForm').submitAjax();
*/
(function($){
    jQuery.fn.extend({
        submitAjax: function(options) {

            var defaults = {
                url             : false,
                onsuccess       : false,
                onrequest       : false,
                onerror         : false,
                successMessage  : '',
                resetForm       : false,
                hideMessage     : false
            };
            var options = $.extend(defaults, options);

            return this.each(function() {

                var element = $(this);
                var current = this;

                element.find('input[type="submit"], button[type="submit"]').livequery('click', function(event) {

                    // Hold regular submit form
                    event.preventDefault();

                    var trigger = $(this);

                    element.attr('onsubmit', 'javascript:void(0);');

                    var url = '';

                    if (options.url == false ) {
                        url = element.attr('action');
                    } else {
                        url = options.url;
                    }

                    // Check required fields
                    if (element.find('.required').requiredCheck() == false) {
                        element.showMessage('Please complete this form', 'error');
                        return false;
                    }

                    // Show loading image

                    if (typeof trigger.loader != 'undefined') {
                        trigger.loader('show', {dimension: 'large', fullWidth: true});
                    }

                    // Data that will be sent
                    var datapost = element.serializeArray();
                    datapost.push({ name: trigger.attr('name'), value: trigger.attr('value') });

                    jQuery.ajax({
                        url         : url,
                        type        : 'POST',
                        data        : datapost,
                        dataType    : 'json',
                        beforeSend  : function() {
                            var passed = true;
                            // Run onrequest hook
                            if(typeof options.onrequest == 'function'){
                                passed = options.onrequest.call(current, element);
                            }

                            if (passed == false) {
                                // Hide loading message
                                
                                if (typeof trigger.loader != 'undefined') {
                                    trigger.loader('hide');
                                }
                            }

                            return passed;
                        },
                        success     : function(data, textStatus, xhr) {
                            if(typeof options.onsuccess == 'function'){
                                var ele = element[0];
                                options.onsuccess.call(this, data, $(ele));
                            }

                            if (options.resetForm == true) {
                                $(element[0]).resetForm(element[0]);
                            }

                            // Set all error hints (if exists) and return false if success
                            var error = element.errorMessage(data);

                            if (options.hideMessage == false) {
                                if (error == false) {
                                    if (options.successMessage != '') {
                                        element.showMessage(options.successMessage, 'success');
                                    } else {
                                        element.showMessage('Successfully Processed.', 'success');
                                    }
                                }
                            }

                            // Hide loading message

                            if (typeof trigger.loader != 'undefined') {
                                trigger.loader('hide');
                            }

                            $('img.resize').resizeImage();
                        },
                        error       : function(xhr, textStatus, errorThrown) {
                            if(typeof options.onerror == 'function'){
                                options.onerror.call(this, xhr);
                            }

                            trigger.loader('hide');
                        }
                    });
                });
            });
        },

        resetForm: function (form) {
            if (typeof form.find == 'undefined') {
                $(form).find('input:text, input:password, input:file, select, textarea').val('');
                $(form).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
            } else {
                form.find('input:text, input:password, input:file, select, textarea').val('');
                form.find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
            }
        }
    });
})(jQuery);

(function($){
    jQuery.fn.extend({

        // Check required field
        requiredCheck: function() {

            var valid = true;

            this.each(function() {
                elem = $(this);

                if (elem.val() == '') {
                    valid = false;
                    elem.addClass('errorEmpty input-error');
                }
            });

            return valid;
        },

        // Must select one or more
        // Required title attribute in input tag
        oneOrMoreCheck: function(elClass) {

            var valid = true;
            var name = $(this).find(elClass).attr('name');
            var title = $(this).find(elClass).attr('title');

            if (typeof name != 'undefined') {
                if ($('input[name="' + name + '"]:checked').length == 0) {
                    valid = false;
                }
            }

            if (valid == false) {
                if (typeof $.gritter != 'undefined') {
                    $.gritter.add({
                        text: 'You must choose one or more of ' + title,
                        sticky: false,
                        time: 4000,
                        class_name: 'error' // error | notification | success
                    });
                }
            }

            return valid;
        }
    });
})(jQuery);

(function($){
    jQuery.fn.extend({

        // Auto resize image to fit image to the image container
        resizeImage: function() {

            this.each(function() {
                var elem = $(this);

                if (elem.hasClass('resized')) {
                    return true;
                }

                elem.imagesLoaded( function( element, proper, broken) {

                    if (element.hasClass('resized')) {
                        return true;
                    }

                    this._setImageSize(element);

                });

            });

        },

        // Set image size
        _setImageSize: function(element) {
            var imageOrientation = this._getOrientation(element);
            var parentOrientation = this._getOrientation(element.parent());

            var w = element.actual('innerWidth');
            var h = element.actual('innerHeight');

            var parentw = element.parent().actual('innerWidth');
            var parenth = element.parent().actual('innerHeight');

            element.parent().addClass(parentOrientation);
            element.addClass(imageOrientation);

            if(parentw > w && parenth > h) {
                var diffW = parentw - w;
                var diffH = parenth - h;
                if(diffW > diffH) {
                    this._setSize(element, 'height');
                    var newW = element.innerWidth();
                    var newH = element.innerHeight();

                    if(newW < parentw) {
                        this._setSize(element, 'width');
                    } else if (newH < parenth) {
                        this._setSize(element, 'height');
                    }

                    return;
                } else {
                    this._setSize(element, 'width');
                    return;
                }
            } else if(parentw > w && parenth < h) {
                this._setSize(element, 'width');
                return;
            } else if(parentw < w && parenth > h) {
                this._setSize(element, 'height');
                return;
            } else if(parentw < w && parenth < h) {
                var diffW = w - parentw;
                var diffH = h - parenth;

                if(diffW > diffH) {
                    this._setSize(element, 'height');
                    var newW = element.innerWidth();
                    var newH = element.innerHeight();

                    if(newW < parentw) {
                        this._setSize(element, 'width');
                    } else if (newH < parenth) {
                        this._setSize(element, 'height');
                    }
                    return;
                } else {
                    this._setSize(element, 'width');
                    return;
                }
            }
            return;
        },

        _setSize: function (element, type) {
            var parentw = element.parent().actual('innerWidth');
            var parenth = element.parent().actual('innerHeight');
            var inHeight = (type == 'width') ? parentw : parenth;
            var elementPosition = (type == 'width') ? 'height' : 'width';

            element.css(elementPosition, '');
            element.css(type, inHeight);
            this._setImagePosition(element, elementPosition);
            element.addClass('resized');
        },

        // Set image position
        _setImagePosition: function(element, marginBy) {

            element.css('margin-left', '');
            element.css('margin-top', '');

            if (marginBy == 'width') {
                var img = element.actual('innerWidth');
                var parent = element.parent().actual('innerWidth');
            } else {
                var img = element.actual('innerHeight');
                var parent = element.parent().actual('innerHeight');
            }

            if (img > parent) {
                var margin = (img - parent)/2;

                if (marginBy == 'width') {
                    element.css('margin-left', margin * -1);
                } else {
                    element.css('margin-top', margin * -1);
                }
            } else {
                var margin = (parent - img)/2;

                if (marginBy == 'width') {
                    element.css('margin-left', margin);
                } else {
                    element.css('margin-top', margin);
                }
            }

        },

        // Get element orientation (square|portrait|landscape)
        _getOrientation: function(element) {
            var w = element.actual('innerWidth');
            var h = element.actual('innerHeight');

            if (w == h) {
                return 'square';
            }

            if (w < h) {
                return 'portrait';
            }

            if (w > h) {
                return 'landscape';
            }
        },

        // Function to get the Max size in element
        _max: function( element ){
            var w = element.actual('innerWidth');
            var h = element.actual('innerHeight');

            var maxArr = new Array(w, h);

            return Math.max.apply( Math, maxArr );
        },

        // Function to get the Min size in element
        _min: function( element ){
            var w = element.actual('innerWidth');
            var h = element.actual('innerHeight');

            var minArr = new Array(w, h);

            return Math.min.apply( Math, minArr );
        }
    });
})(jQuery);

(function($){
    jQuery.fn.extend({

        // Parse link from string and capture the url page
        parseLink: function(options) {
            var defaults = {
                onsuccess       : false,
                onrequest       : false,
                onerror         : false,
                target          : ''
            };

            var options = $.extend(defaults, options);

            return this.livequery(function() {
                $(this).each(function() {
                    elem = $(this);

                    elem.keyup(function(evt) {
                        var pass = true;
                        if (evt.keyCode != 32) {
                            pass = false;
                        }

                        elem.bind('paste', function(e) {
                            pass = true;
                        });

                        if (pass == false) {
                            return pass;
                        }

                        if (elem.hasClass('link-loaded')) {
                            return false;
                        }

                        if (elem.val() != '') {
                            var url = elem._getUrl(elem.val());

                            if(url != false) {
                                var pathArray = String(url).split('/');

                                if(pathArray[0] == 'http:') {
                                    var host = pathArray[0] + '//' + pathArray[2];
                                } else if(pathArray[0] == 'https:') {
                                    var host = pathArray[0] + '//' + pathArray[2];
                                } else {
                                    var host = 'http:' + '//' + pathArray[0];
                                }

                                var isVideo = elem._isVideoUrl(url);

                                if (isVideo == false) {
                                    isVideo = 'generic'
                                }

                                elem.getDataByAjax({
                                    url         : '/sixreps_links/process/parseurl/?url=' + encodeURIComponent(url) + '&type=' + isVideo,
                                    method      : 'GET',
                                    dataType    : 'json',
                                    onrequest   : function() {
                                        var passed = true;

                                        if(typeof options.onrequest == 'function'){
                                            passed = options.onrequest.call(this, elem);
                                        }

                                        return passed;
                                    },
                                    onsuccess   : function(result) {
                                        var data = result.body;

                                        var returnObject = new Object();
                                        var img = $(data).find('img');
                                        var imgSrc = [];

                                        // Images
                                        if (typeof result.metadata.image != 'undefined' && result.metadata.image != '') {
                                            src = result.metadata.image;

                                            if (elem._checkURL(result.metadata.image) == false) {
                                                src = host + result.metadata.image;
                                            }

                                            imgSrc.push(src);
                                        } else {
                                            img.each(function() {
                                                var src = $(this).attr('src');

                                                if (elem._checkURL(src) == false) {
                                                    src = host + src;
                                                }

                                                imgSrc.push(src);
                                            });
                                        }

                                        // Title
                                        if (typeof result.metadata.title != 'undefined' && result.metadata.title != '') {
                                            var title = result.metadata.title;
                                        } else {
                                            var titles = data.match(/<title>(.*?)<\/title>/);
                                            var title = (titles != null) ? titles[1] : '';
                                        }

                                        // Description
                                        var description = '';
                                        if (typeof result.metadata.description != 'undefined' && result.metadata.description != '' && result.metadata.description != null) {
                                            description = result.metadata.description.substring(0,160);
                                        } else {
                                            description = $(data).find('p:first').text().substring(0,160);
                                        }

                                        returnObject.img = imgSrc;
                                        returnObject.title = title;
                                        returnObject.description = description;
                                        returnObject.url = result.url;

                                        elem.addClass('link-loaded');

                                        if(typeof options.onsuccess == 'function'){
                                            passed = options.onsuccess.call(this, returnObject, elem);
                                        }
                                    },
                                    onerror     : function() {
                                        if(typeof options.onerror == 'function'){
                                            passed = options.onerror.call(this, elem);
                                            return passed;
                                        }
                                    }
                                });
                            }
                        }
                    });
                });
            });
        },

        _isVideoUrl: function(content) {
            var urlRegex = /youtube/i;
            var url = String(content).match(urlRegex);

            if (url != null && url != "") {
                return 'youtube';
            } else {
                var urlRegex = /vimeo/i;
                var url = String(content).match(urlRegex);

                if (url != null && url != "") {
                    return 'vimeo';
                } else {
                    return false;
                }
            }
        },

        _getUrl: function(content) {
            // var urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
            // var urlRegex = /(https?:\/\/)?(www\.)?([a-zA-Z0-9_%-]*)\b\.[a-z]{2,4}(\.[a-z]{2})?((\/[a-zA-Z0-9_%-]*)+)?(\.[a-z]*)?/gi;
            var urlRegex = /[-a-zA-Z0-9@:%_\+.~#?&\/\/=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&\/\/=]*)?/gi;
            var url = content.match(urlRegex);

            if (url != null) {
                return url;
            } else {
                return false;
            }
        },

        _checkURL: function(value) {
            var urlRegex = /(ht|f)tps?:\/\/w{0,3}[a-zA-Z0-9_\-.:#\/~}]+/gi;

            var test = value.match(urlRegex);

            if (test != null) {
                return true;
            } else {
                return false;
            }

            // var urlregex = new RegExp("((ht|f)tp:\/\/w{0,3}[a-zA-Z0-9_\-.:#/~}]+)");

            // if (urlregex.test(value)) {
            //     return (true);
            // }

            // return (false);
        }
    });
})(jQuery);

// Long polling for update notification
function poll(){
    $.ajax({ url: "/notification", success: function(data){
        if (parseInt(data.notification) > 0) {
            $('#siteNav .icon-notification .notification-count').html(data.notification);
            $('#siteNav .icon-notification .notification-count').show();
        } else {
            $('#siteNav .icon-notification .notification-count').hide();
        }

        if (parseInt(data.message) > 0) {
            $('#siteNav .icon-message .notification-count').html(data.message);
            $('#siteNav .icon-message .notification-count').show();
        } else {
            $('#siteNav .icon-message .notification-count').hide();
        }
    },
    dataType: "json",
    complete: function() {
        setTimeout(function(){
            poll();
        }, 5000);
    },
    timeout: 30000 });
};

// Handling error validation from API
jQuery.fn.errorMessage = function(data) {
    if (typeof data.error != "undefined" && data.error != "") {
        if (typeof $.gritter != 'undefined') {
            $.gritter.add({
                text: data.error,
                sticky: false,
                time: 4000,
                class_name: 'error' // error | notification | success
            });
        } else {

            var form = this;
            form.find('.errorSummary, .alert').remove();
            form.prepend('<div id="errorSummary" class="alert errorSummary alert-error"> ' +
                            '<a type="button" class="close" data-dismiss="alert">×</a>' +
                            '<div class="errorContent">' +
                                '<strong>Warning!</strong> ' + data.error +
                            '</div>' +
                        '</div>');

            if(typeof data.hints != "undefined") {
                jQuery.each(data.hints, function(key) {
                    form.find('#' + key).addClass('errorEmpty').after('<div class="alert alert-error hints">' + this + '</div>')
                });
            }

        }
        return true;
    } else {
        return false;
    }
}

// Put alert on prepend form
// @param string    text    Message body
// @param string    type    Message type (error|success|notification/warning)
jQuery.fn.showMessage = function(text, type) {
    if (typeof $.gritter != 'undefined') {
        $.gritter.add({
            text: text,
            sticky: false,
            time: 4000,
            class_name: type // error | notification | success
        });
    } else {
        this.find('.errorSummary, .alert').remove();
        this.prepend('<div id="errorSummary" class="errorSummary alert alert-' + type + '"> ' +
                        '<a type="button" class="close" data-dismiss="alert">×</a>' +
                        '<div class="errorContent">' +
                            '<strong>' + type + '!</strong> ' + text +
                        '</div>' +
                    '</div>');
    }
}