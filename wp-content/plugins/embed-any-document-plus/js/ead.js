var Ead_plus = (function($) {
    'use strict';
    var file = {},
        fileurl = "",
        handle = "",
        newprovider = false,
        download = true,
        frame,
        oauthToken,
        driveapiKey = emebeder.driveapiKey,
        driveclientId = emebeder.driveclientId,
        boxapikey = emebeder.boxapikey,
        DropboxApi = emebeder.DropboxApi,
        msextension = emebeder.msextension,
        drextension = emebeder.drextension,
        init = function() {
            support();
            reset();
            bind_events();
            $(window).resize(tb_position);
        },
        bind_events = function() {
            var $embed_popup = $('#embed-popup');
            $('body').on('click', '.awsm-embed', embed_popup);

            $embed_popup.on('click', '#ead-dropbox', dropboxhandler);
            $embed_popup.on('click', '#ead-google', googlehandler);
            $embed_popup.on('click', '#ead-box', boxhandler);
            $embed_popup.on('click', '#ead-upload', open_media_window);
            $embed_popup.on('click', '#add-url', awsm_embded_url);
            $embed_popup.on('click', '#insert-doc', awsm_shortcode);

            $embed_popup.on('click', '#ead-doc-url', function(e) {
                e.preventDefault();
                $('.addurl-box').fadeIn();
                $('.ead-options').hide();
            });
            $embed_popup.on('click', '.go-back', function(e) {
                e.preventDefault();
                $('.addurl-box').hide();
                $('.ead-options').fadeIn();
                $('#embed-message').hide();
            });
            $embed_popup.on('click', '.cancel-embed,.ead-close', remove_eadpop);

            $embed_popup.on('change', '.ead-usc', function() {
                newprovider = false;
                updateshortcode($(this).attr('id'));
                customize_popup();
            });
            $embed_popup.on('change', '.ead-boxtheme', function() {
                newprovider = "box";
                updateshortcode($(this).attr('id'));
            });

            $embed_popup.on('keyup', '.embedval', function() {
                updateshortcode($(this).attr('id'));
            });

        },
        support = function() {
            if( driveapiKey && driveclientId){
                $("head").append("<script type='text/javascript' src='https://apis.google.com/js/api.js'></script>");    
            }
            if(DropboxApi){
                $("head").append("<script type='text/javascript' src='https://www.dropbox.com/static/api/2/dropins.js'></script>");       
            }
            if(boxapikey){
                $("head").append("<script type='text/javascript' src='https://app.box.com/js/static/select.js'></script>");       
            }
        },
        embed_popup = function(e) {
            reset();
            e.preventDefault();
            $('body').addClass('ead-popup-on');
            tb_show(emebeder.pluginname, "#TB_inline?inlineId=embed-popup-wrap&amp;width=1030&amp;modal=true", null);
            tb_position();
            $("#ead-upload").focus();
            return;
        },
        tb_position = function() {
            var tbWindow = $('#TB_window');
            var width = $(window).width();
            var H = $(window).height();
            var W = (1080 < width) ? 1080 : width;

            if (tbWindow.size()) {
                tbWindow.width(W - 50).height(H - 45);
                $('#TB_ajaxContent').css({ 'width': '100%', 'height': '100%', 'padding': '0' });
                tbWindow.css({ 'margin-left': '-' + parseInt(((W - 50) / 2), 10) + 'px' });
                if (typeof document.body.style.maxWidth != 'undefined')
                    tbWindow.css({ 'top': '20px', 'margin-top': '0' });
                $('#TB_title').css({ 'background-color': '#fff', 'color': '#cfcfcf' });
            };
        },
        sanitize = function(dim) {
            if (dim.indexOf("%") == -1) {
                dim = dim.replace(/[^0-9]/g, '');
                dim += "px";
            } else {
                dim = dim.replace(/[^0-9]/g, '');
                dim += "%";
            }
            return dim;
        },
        open_media_window = function() {
            handle = 'upload';
            if (frame) {
                frame.open();
                return;
            }
            frame = wp.media({
                title: emebeder.pluginname,
                multiple: false,
                library: {
                    type: emebeder.validtypes,
                },
                button: {
                    text: emebeder.insert_text,
                }
            });
            frame.on('select', function() {
                file = frame.state().get('selection').first().toJSON();
                updateprovider(file, handle);
            });
            frame.open();
        },
        getshortcode = function(file, item) {
            var shortattr = " ",
                attr = '',
                provider = $('#ead-provider').val(),
                cache = $('#ead-cache').is(':checked');
            if (file.url) {
                shortattr += 'url="' + file.url + '" ';
            } else {
                shortattr += 'id="' + file.id + '" ';
            }
            $('#embed-popup [data-setting]').each(function() {
                if (itemcheck($(this).data('setting'), item)) {
                    attr = $(this).val();
                    if ($(this).hasClass('embed-sanitize')) {
                        attr = sanitize($(this).val());
                    }
                    if ($(this).data('setting') == 'viewer' && newprovider) {
                        provider = attr = newprovider;
                    }

                    shortattr += $(this).data('setting') + '="' + attr + '" ';
                }
            });

            if (provider == 'box') {
                shortattr += ' boxtheme="' + $('#ead-boxtype').val() + '"';
            }

            if (provider == 'google') {
                $('#eadcachemain').show();
                if (cache) {
                    shortattr += ' cache="off"';
                }
            } else {
                $('#eadcachemain').hide();
            }

            return '[embeddoc' + shortattr + ']';
        },
        updateprovider = function(file, handle) {
            fileurl = file.url;
            validviewer(file, handle);
            updateshortcode();
            uploaddetails(file, handle);
            customize_popup();
        },
        itemcheck = function(item, dataitem) {
            var check = $('#ead-' + item).val();
            var datacheck = 'ead-' + item;
            if (datacheck == dataitem) {
                return true;
            } else if (check != emebeder[item]) {
                return true;
            }
            return false;
        },
        uploaddetails = function(file) {
            $('#insert-doc').removeAttr('disabled');
            $('#ead-filename').html(file.filename);
            if (file.filesizeHumanReadable) {
                $('#ead-filesize').html(file.filesizeHumanReadable);
            } else {
                $('#ead-filesize').html('&nbsp;');
            }
            $('.upload-success').fadeIn();
            $('.ead-container').hide();
            uploadclass(handle);
        },
        customize_popup = function() {
            if ($('#ead-download').val() != "none" && download) {
                $('#ead-download-text').show();
            } else {
                $('#ead-download-text').hide();
            }

        },
        awsm_embded_url = function() {
            var checkurl = $('#awsm-url').val();
            if (checkurl !== '') {
                validateurl(checkurl);
            } else {
                $('#awsm-url').addClass('urlerror');
                updateshortcode();
            }
        },
        is_url_valid = function(url) {
            var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
            return regexp.test(url);    
        },
        validateurl = function(url) {
            handle = 'link';
            $('#embed-message').hide();
            if (is_url_valid(url)) {
                fileurl = url;
                var filename = url.split('/').pop();
                if (!filename) filename = emebeder.from_url;
                file = {
                    url: url,
                    filename: filename,
                    filesizeHumanReadable: 0
                };
                $('#insert-doc').removeAttr('disabled');
                $('#ead-filename').html('From URL');
                $('#ead-filesize').html('&nbsp;');
                $('.upload-success').fadeIn();
                $('.ead-container').hide();
                updateprovider(file, handle);
            } else {
                showmsg(emebeder.invalidurl);
            }
        },
        showmsg = function(msg) {
            $('#embed-message').fadeIn();
            $('#embed-message p').text(msg);
        },
        awsm_shortcode = function(event) {
            if ($('#shortcode').text()) {
                // @rel: document guten-block
                var ins_shortcode = true;
                if(typeof wp.blocks !== 'undefined') {
                    var document_block = wp.blocks.getBlockType('embed-any-document/document');
                    if(typeof document_block !== 'undefined') {
                        ins_shortcode = false;
                    }
                }
                if(ins_shortcode) {
                    wp.media.editor.insert($('#shortcode').text());
                }
                remove_eadpop(event);
            } else {
                showmsg(emebeder.nocontent);
            }
        },
        updateshortcode = function(item) {
            item = typeof item !== 'undefined' ? item : false;
            if (file) {
                $('#shortcode').text(getshortcode(file, item));
            } else {
                $('#shortcode').text('');
            }
        },
        remove_eadpop = function(event) {
            event.preventDefault();
            tb_remove();
            setTimeout(function() {
                $('body').removeClass('ead-popup-on');
            }, 800);
        },
        uploadclass = function(uPclass) {
            $(".uploaded-doccument").removeClass("ead-link ead-upload ead-dropbox ead-drive ead-box");
            $('.uploaded-doccument').addClass('ead-' + uPclass);
        },
        human_filesize = function(bytes) {
            var thresh = 1024;
            if (bytes < thresh) return bytes + ' B';
            var units = ['KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            var u = -1;
            do {
                bytes /= thresh;
                ++u;
            } while (bytes >= thresh);
            return bytes.toFixed(1) + ' ' + units[u];
        },
        dropboxhandler = function(event) {
            event.preventDefault();
            handle = 'dropbox';
            var validext = drextension.split(',');
            Dropbox.init({
                appKey: DropboxApi
            });
            Dropbox.choose({
                linkType: "preview",
                multiselect: false, // or true
                extensions: validext,
                success: function(files) {
                    var drpbox = files[0];
                    var dropURL = drpbox.link.replace("dl=0", "dl=1");
                    file = {
                        url: dropURL,
                        filename: drpbox.name,
                        filesizeHumanReadable: human_filesize(drpbox.bytes)
                    };
                    updateprovider(file, handle);
                }
            });
        },
        googlehandler = function(event) {
            event.preventDefault();
            if (api_handling(driveclientId,emebeder.no_api)) return;
            if (!oauthToken) {
                gapi.load('auth', {
                    'callback': onauthapiload
                });
                gapi.load('picker', 1);
            } else {
                createpicker();
            }
        },
        onauthapiload = function() {
            window.gapi.auth.authorize({
                'client_id': driveclientId,
                'scope': ['https://www.googleapis.com/auth/drive']
            }, handle_auth_result);
        },
        handle_auth_result = function(authResult) {
            if (authResult && !authResult.error) {
                oauthToken = authResult.access_token;
                createpicker();
            }
        },
        createpicker = function() {
            var picker = new google.picker.PickerBuilder()
                .addView(new google.picker.DocsUploadView().setIncludeFolders(true))
                .addView(new google.picker.View(google.picker.ViewId.DOCS).setMimeTypes(emebeder.validtypes))
                .addView(google.picker.ViewId.DOCUMENTS)
                .addView(google.picker.ViewId.PRESENTATIONS)
                .addView(google.picker.ViewId.SPREADSHEETS)
                .addView(google.picker.ViewId.FORMS)
                .setOAuthToken(oauthToken)
                .setCallback(picker_callback)
                .build();
            picker.setVisible(true);
        },
        picker_callback = function(data) {
            var url = '';
            handle = 'drive';
            if (data[google.picker.Response.ACTION] == google.picker.Action.PICKED) {
                var doc = data[google.picker.Response.DOCUMENTS][0];
                var filesize = 0;
                if (doc.sizeBytes) {
                    filesize = human_filesize(doc.sizeBytes);
                }
                file = {
                    id: doc.id,
                    filename: doc.name,
                    filesizeHumanReadable: filesize
                };
                if (doc.embedUrl) {
                    file.url = doc.embedUrl;
                } else {
                    file.url = false;
                    file.id = doc.id;
                }
                newprovider = "drive";
                setpseudo('drive');
                updateprovider(file, handle);
            }
        },
        boxhandler = function() {
            if (api_handling(boxapikey, emebeder.no_api)) return;
            var boxoptions = {
                clientId: boxapikey,
                linkType: 'shared',
                multiselect: false
            };
            var boxSelect = new BoxSelect(boxoptions);
            boxSelect.launchPopup();
            boxSelect.success(function(response) {
                handle = 'box';
                var doc = response[0];
                var filesize = 0;
                file = {
                    url: doc.url,
                    filename: doc.name,
                    filesizeHumanReadable: filesize
                };
                setpseudo('box');
                if (doc.access !== 'open') {
                    showmsg(emebeder.nopublic);
                }
                newprovider = "box";
                updateprovider(file, handle);
            });
        },
        setpseudo = function(Viewer) {
            $('#new-provider').hide();
            $('#ead-pseudo').show();
            $('#ead-downloadc').hide();
            $('#doccache').hide();
            $('select[name="ead-pseudo"]').val(Viewer);
            if (Viewer == 'box') {
                $('#ead-boxtheme').show();
            }
            download = false;
        },
        validviewer = function(file, provider) {
            var cprovider = ["link", "upload", "dropbox"];
            var validext = msextension.split(',');
            var checkitem = file.filename;
            if (handle == 'link') {
                checkitem = file.url;
            }
            var ext = '.' + checkitem.split('.').pop();
            $("#new-provider option[value='microsoft']").attr('disabled', false);
            if ($.inArray(provider, cprovider) != -1) {
                if ($.inArray(ext, validext) == -1) {
                    newprovider = "google";
                    $("#new-provider option[value='google']").attr("selected", "selected");
                    $("#new-provider option[value='microsoft']").attr('disabled', true);
                } else {
                    newprovider = "microsoft";
                    $("#new-provider option[value='microsoft']").attr("selected", "selected");
                }
            }
        },
        api_handling = function(key, message) {
            if (!key) {
                showmsg(message);
                return true;
            } else {
                return false;
            }
        },
        reset = function() {
            $('.ead-container').show();
            $('#awsm-url').val('');
            $('.ead-options').fadeIn();
            $('.addurl-box').hide();
            $('.upload-success').hide();
            $('#embed-message').hide();
            $('#insert-doc').attr('disabled', 'disabled');
            $('#new-provider').show();
            $('#ead-pseudo').hide();
            newprovider = false;
            $("#new-provider option[value='microsoft']").attr('disabled', false);
            $('#ead-downloadc').show();
            $('#doccache').show();
            $('#ead-boxtheme').hide();
            download = true;
            customize_popup();
        }
    return {
        init: init
    };
})(jQuery);

jQuery(Ead_plus.init);
