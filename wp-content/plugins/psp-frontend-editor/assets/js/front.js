
acf_set_initial = false;

/**
 * 'acf/setup_fields' only fires for ACF4
 */
jQuery(document).on('acf/setup_fields', function(e, el){

    setTimeout(function() {
        /**
         * Still need to do something here to look up the section and set the appropriate place
         * @type {String}
         */

        if( acf_set_initial == true ) return;

         var initial_marker   = jQuery('.psp-timeline li.initial');

         var section   = jQuery(initial_marker).data('target');
         var title     = jQuery(initial_marker).data('title');
         var type      = jQuery(initial_marker).data('type');
         var target    = jQuery( '#' + section );

         acf_set_initial = true;

         psp_fe_update_wizard_section( initial_marker, target, title, type );

         jQuery('#cssload-pgloading').hide();

    }, 500 );

});

/**
 * 'acf/ready' only fires for ACF5
 */
jQuery(document).ready(function($) {

    if( $('#psp-projects').hasClass('psp-acf-ver-5') && $('#psp-projects').hasClass('psp-fe-edit-project') ) {

        acf.add_action( 'load', function() {

             var initial_marker   = jQuery('.psp-timeline li.initial');

             var section   = jQuery(initial_marker).data('target');
             var title     = jQuery(initial_marker).data('title');
             var type      = jQuery(initial_marker).data('type');

             jQuery('.timeline-title').text(title);
             jQuery( '#' + section ).show();

             if( type == 'tab' ) {
                 var index  = jQuery(initial_marker).index();
                 var tab    = jQuery('.acf-tab-group li').get(index);
                 jQuery(tab).find('a').click();
             }

             if( jQuery(initial_marker).is(':first-child') ) {
                 jQuery('.psp_fe_title_fields').show();
                 jQuery('.psp-wizard-edit-prev-button').hide();
             }
             if( jQuery(initial_marker).is(':last-child') ) {
                 jQuery('.psp-wizard-edit-next-button').hide();
             }

             $('#cssload-pgloading').hide();

        });

    }

});

jQuery( document ).on( 'psp-tasks-auto-expanded', function( event ) {

	 jQuery( '.psp-task-list-wrapper' ).each( function( index, wrapper ) {

		 if ( jQuery( wrapper ).find( '.psp-task-list' ).hasClass( 'active' ) ) {
			 jQuery( wrapper ).find('.psp-fe-add-task').slideToggle();
		 }

	 } );

 } );

function psp_fe_update_wizard_section( marker, target, title, type ) {

    jQuery('.timeline-title').text(title);
    jQuery(target).show().removeClass('hidden-by-tab');

    if( type == 'tab' ) {
        var index  = jQuery(marker).index();
        var tab    = jQuery('.acf-tab-group li').get(index);
        jQuery(tab).find('a').click();
    }

    if( jQuery(marker).is(':first-child') ) {
        jQuery('.psp_fe_title_fields').show();
        jQuery('.acf-field--post-title').show();
        jQuery('.psp_fe_title_field').show();
        jQuery('.psp-wizard-edit-prev-button').hide();
        if( !$('#acf-field-automatic_progress').prop('checked') ) {
            $('#acf-percent_complete').show();
        }
    } else {
        $('#acf-percent_complete').hide();
    }
    if( jQuery(marker).is(':last-child') ) {
        jQuery('.psp-wizard-edit-next-button').hide();
    }

}

jQuery(document).ready(function($) {

    $('.new-project-type-group input').click(function(e) {

        if( $(this).val() == 'new' ) {
            $('.psp-fe-template-form').hide();
            $('.acf-form').show();
        } else {
            $('.psp-fe-template-form').show();
            $('.acf-form').hide();
        }

    });

    $('.all-fe-checkbox').click(function() {

        if( $(this).is(':checked') ) {
            $(this).parents('#psp-notify-users').find('input.specific-fe-checkbox').prop('checked',false);
            $(this).parents('#psp-notify-users').find('ul.psp-notify-list input').prop('checked',true);
            $(this).parents('#psp-notify-users').find('ul.psp-notify-list').slideUp('fast');
        } else {
            $(this).parents('#psp-notify-users').find('ul.psp-notify-list input').prop('checked',false);
        }

    });

    $('.specific-fe-checkbox').click(function() {

        if( $(this).is(':checked') ) {
            $(this).parents('#psp-notify-users').find('ul.psp-notify-list').slideDown('fast');
            $(this).parents('#psp-notify-users').find('input.all-fe-checkbox').prop('checked',false);
        } else {
            $(this).parents('#psp-notify-users').find('ul.psp-notify-list').slideUp('fast');
            $(this).parents('#psp-notify-users').find('ul.psp-notify-list input').prop('checked',false);
        }

    });

    if( $( '.psp-fe-notify-modal' ).length ) {
        $('.psp-fe-notify-modal').leanModal({ closeButton: ".modal-close" });
    }

    if( $('.js-psp-edit-phase').length ) {
        $('.js-psp-edit-phase').leanModal({ closeButton: ".modal-close" });
    }

    if( $('.js-psp-edit-description').length ) {
        $('.js-psp-edit-description').leanModal({ closeButton: ".modal-close" });
    }

    if( window.location.hash == '#new' ) {
        $( '.psp-fe-notify-modal' ).trigger( 'click' );
    }

    $('.psp-fe-template-form form').submit(function(e) {

        e.preventDefault();

        var template    = $('#psp-fe-use-template').val();
        var ajaxurl     = $('#psp-ajax-url').val();

        if( template == '---' ) {
            return;
        }

        $('.psp-fe-template-form input[type="submit"]').attr( 'disabled', 'true' );
        $('.psp-fe-template-form').addClass('disabled');


        $.ajax({
            url  :  ajaxurl + "?action=psp_duplicate_template",
            type :  'post',
            data : {
                template : template
            },
            success: function( response, success ) {

                window.location = response.data.redirect;

            },
            error: function( response ) {

                $('.psp-fe-template-form .message').html( response.data.message ).show();

            }
        });


    });

    $('.psp-fe-notify-send').click(function(e) {

        e.preventDefault();

        var post_id     = $('#psp-fe-post-id').val();
        var subject     = $('#psp-fe-subject').val();
        var message     = $('#psp-fe-message').val();
        var users       = [];
        var ajaxurl     = $('#psp-ajax-url').val();

        $('.psp-fe-user:checked').each(function() {
            users.push( $(this).val() );
        });

        $.ajax({
            url     :   ajaxurl + "?action=psp_fe_notify",
            type    :   'post',
            data:   {
                post_id : post_id,
                subject : subject,
                message : message,
                users   : users
            },
            success: function( success ) {

                $('.psp-notify-list-message').fadeIn( 'fast' );

                setTimeout( function() {

                    $( '#psp-notify-users' ).fadeOut( 'slow', function() {

                        psp_fe_reset_modal();

                    });

                }, 500);

            }
        });

    });

    function psp_fe_reset_modal() {

        var subject = $('#psp-fe-subject').data( 'default' );
        var message = $('#psp-fe-message').data( 'default' );

        $('#psp-fe-subject').val( $('#psp-fe-subject').data('default') );
        $('#psp-fe-message').val( $('#psp-fe-message').data('default') );
        $('.psp-notify-list-message').hide();
        $('.psp-fe-user').prop( 'checked', false );
        $('.psp-fe-notify-all').prop( 'checked', false );

    }

    $(document).on( 'click', '.js-psp-delete-document', function(e) {

        e.preventDefault();

        var parent = $(this).parents('li.psp-document');
        var data   = {
            post_id : $(this).data('project'),
            doc_id  : $(parent).data('id'),
            title   : $(parent).find('.doc-title').text(),
        };

        var ajaxurl = $('#psp-ajax-url').val() + "?action=" + "psp_delete_document";
        var r = confirm( $(this).data('confirm') );

        if( r == false ) {
            return;
        }

        $.ajax({
            url     : ajaxurl,
            type    : 'post',
            data    : data,
            success: function( response, success ) {

                if( response.data.success == false ) {
                    alert( response.data.message );
                } else {
                    $(parent).slideUp('slow');
                }

            },

        });

    });

    /*
     *
     * Front end editing tasks
     */

    $(document).on( 'click', '#psp-phases .task-list-toggle', function(e) {
        $(this).parents('.psp-task-list-wrapper').find('.psp-fe-add-task').slideToggle();
    });

    $( document ).on( 'click', '.fe-del-task-link', function(e) {

        e.preventDefault();

        var result = confirm( $(this).data('confirmation') );

        if( result == true ) {

            var form = $(this).parents('form');
            var task_id = $(form).find('input[name="task_id"]');

            var data = {
                'task_index'   : $(form).find('input[name="task_index"]').val(),
                'phase_index'  : $(form).find('input[name="phase_index"]').val(),
				'task_id'   : $(form).find('input[name="task_id"]').val(),
                'phase_id'  : $(form).find('input[name="phase_id"]').val(),
                'post_id'   : $(form).find('input[name="post_id"]').val()
            };

            psp_fe_delete_task( data.post_id, data.phase_index, data.task_index, data.phase_id, data.task_id );

            var phase_index = parseInt( data.phase_index ) + 1;
            jQuery('#phase-' + phase_index ).find('.psp-modal-btn').leanModal({ closeButton: ".modal-close" });

            $(form).find('.modal-close').click();
            $(form).trigger('reset');

        }

    });

    $('#psp-phases').on( 'click', '.psp-fe-add-element', function(e) {
        var target = $(this).attr('href');
        $(target).find('input[name="task_index"]').val('new');
		$(target).find('input[name="task"]').val('');
		$(target).find('input[name="due_date"]').val('');
        $(target).find('input[type="submit"]').val($(this).data('submit_label'));
        $(target).find('h2').text( $(this).data('modal_title') );
        $(target).find('.fe-del-task-link').addClass('psp-hide');
    });

    $(document).on( 'submit', '.psp-frontend-form', function(e) {

        e.preventDefault();

        var data        = $(this).serialize();
        var form        = $(this);
        var ajaxurl     = $('#psp-ajax-url').val() + "?action=" + $(form).find('input[name="action"]').val();

        var post_id     = $(form).find('input[name="post_id"]').val();

        $.ajax({
            url     : ajaxurl,
            type    : 'post',
            data    : data,
            success: function( response, success ) {

                // Loop through everything that needs to be modified
                response.data.modify.forEach( psp_fe_modify_frontend );
                $('.fe-edit-task-link').leanModal({ closeButton: ".modal-close" });

                $(form).find('.modal-close').click();
                $(form).trigger('reset');

                // TODO: Do a check
                psp_fe_update_phase_progress( response.data.phase_progress, $( form ).find( 'input[name="phase_index"]' ).val() );
                psp_fe_update_total_progress( response.data.project_progress, post_id );

				psp_update_my_task_count( post_id, $( form ).find( 'input[name="phase_index"]' ).val() );

				jQuery( document ).trigger( 'psp-fe-updated-task', [ $( form ).find( 'input[name="phase_index"]' ).val(), $( form ).find( '[name="task_index"]' ).val() ] );

            },
			error: function( request, status, error ) {

				// Return a 500 error or something similar in PHP via the wp_ajax_psp_fe_update_task Hook to hit the Error block, then use this Event to display whatever error message you need to
				// This allows other plugins to validate Server-side and show their own Errors Client-side
				jQuery( document ).trigger( 'psp-fe-task-update-failed', [ $( form ), $( form ).find( 'input[name="phase_index"]' ).val(), $( form ).find( '[name="task_index"]' ).val() ] );

			}
        });

    });

    $('.psp-add-task-modal .modal-close').click(function(e) {
        $(this).parents('.psp-add-task-modal').find('form').trigger('reset');
    });

    $('#psp-phases').on( 'click', '.fe-edit-task-link', function(e) {

        var target = $(this).attr('href');
        var modal  = $(target);
        var values = $(this).data();

        for ( var name in values ) {

            $( modal ).find( '[name="' + name + '"]' ).val( values[name] );

        }

        $(modal).find('input[type="submit"]').val( values['submit_label'] );
        $(modal).find('h2').text( values['modal_title'] );

		// Re-init datepickers. Important after Deleting a Task, since the Datepicker needs to be re-inited (as the DOM was removed and re-added) and ensure the datepicker knows the right value as it is created
		if ( jQuery( '.psp-datepicker' ).length ) {

			jQuery( '.psp-datepicker' ).each( function( index, field ) {

                    if( jQuery( field ).val() != '' ) {

                    var date = jQuery( field ).val().replace( /\D/g, '' ),
    					month = date.substring( 0, 2 ),
    					day = date.substring( 2, 4 ),
    					year = date.substring( 4, 8 ),
    					dateObject = new Date( year + '-' + month + '-' + day );

    				// https://stackoverflow.com/a/16048201
    				dateObject.setTime( dateObject.getTime() + dateObject.getTimezoneOffset() * 60 * 1000 );

	                jQuery( field ).datepicker( 'setDate', dateObject );

                }

			} );

		}

        $(modal).find('.fe-del-task-link').removeClass('psp-hide');

		$( document ).trigger( 'psp-fe-task-edit-modal-populated', [ $( modal ), values ] );

    });

    $('#nav-delete a').click(function(e) {

        e.preventDefault();

        var post_id     = $(this).data('postid');
        var redirect    = $(this).data('redirect');
        var ajaxurl     = $('#psp-ajax-url').val();


        confirmation = confirm( psp_delete_confirmation_message );
        if( !confirmation ) return false;

        $.ajax({
            url     :   ajaxurl + "?action=psp_fe_delete_project",
            type    :   'post',
            data:   {
                post_id  : post_id,
                redirect : redirect,
            },
            success: function( success, data ) {
                window.location = redirect;
            },
            error: function() {
                alert( 'There was a problem deleting this project, you might not have permission' );
            }
        });


    });


    $('input:radio[name="psp-fe-project-type"]').change(function() {

        if( $(this).val() == 'template' ) {
            $('.psp-wizard-actions').hide();
        } else {
            $('.psp-wizard-actions').show();
        }

    });

    /*
     *
     * Project creation wizard
     */

    $('.psp-wizard-next-button').click(function() {

        var current_tab = $('.acf-tab-group').find('li.active');
        var next_tab    = $(current_tab).next('li');
        var index       = $(next_tab).index();

        $(next_tab).find('a').click();

        // Toggle fields
        $('.acf-field--post-title').hide();
        $('.new-project-type-group').hide();
        $('.psp-fe-template-form').hide();

        if( $(current_tab).is(':first-child') ) {
            $('.psp-wizard-prev-button').show();
            $('.psp_fe_title_fields').hide();
        }

        $('#acf-percent_complete').hide();

        if( $(next_tab).is(':last-child') ) {
            $(this).hide();
            $('#psp-projects.psp-fe-manage-page-new .psp-fe-wizard-wrap input[type=submit]').show();
        }

        $('.psp-wizard-section span.timeline-title').html( $(next_tab).find('a').html() );

        var timeline_spot   = $('.psp-timeline li').get(index);
        var progress        = $(timeline_spot).data('complete');
        var step            = $(timeline_spot).data('step');

        $(timeline_spot).addClass('active');
        $('.psp-timeline--bar span').width( progress + '%' );
        $('.psp-wizard-section strong span.step').text(step);

    });

    $('.psp-wizard-prev-button').click(function() {

        var current_tab = $('.acf-tab-group').find('li.active');
        var prev_tab    = $(current_tab).prev('li');
        var index       = $(prev_tab).index();
        var old_index   = $(current_tab).index();

        $(prev_tab).find('a').click();

        if( $(current_tab).is(':last-child') ) {
            $('.psp-wizard-next-button').show();
            $('#psp-projects.psp-fe-manage-page-new .psp-fe-wizard-wrap input[type=submit]').hide();
        }

        if( $(prev_tab).is(':first-child') ) {
            $('.acf-field--post-title').hide();
            $('.new-project-type-group').show();
            $('.psp-fe-template-form').show();
            $('.psp_fe_title_fields').show();
            $(this).hide();
            if( $('#acf-field-automatic_progress').prop('checked') ) {
                $('#acf-percent_complete').show();
            }
        }

        $('.psp-wizard-section span.timeline-title').html( $(prev_tab).find('a').html() );

        var timeline_spot       = $('.psp-timeline li').get(index);
        var old_timeline_spot   = $('.psp-timeline li').get(old_index);

        var progress = $(timeline_spot).data('complete');
        var step     = $(timeline_spot).data('step');
        $(old_timeline_spot).removeClass('active');
        $('.psp-timeline--bar span').width( progress + '%' );
        $('.psp-wizard-section strong span.step').text(step);

    });

    $('.psp-wizard-edit-next-button').click(function(e) {

        e.preventDefault();

        var current_marker  = $('.psp-timeline li.active:last');
        var next_marker     = $(current_marker).next('li');

        hide_target = $(current_marker).data('target');
        show_target = $(next_marker).data('target');

        $( '#' + hide_target ).hide();
        $( '#' + show_target ).show().removeClass('hidden-by-tab');

        $('.psp_fe_title_fields').hide();
        $('.acf-field--post-title').hide();
        $('#acf-percent_complete').hide();

        $('.psp-wizard-edit-prev-button').show();

        var progress        = $(next_marker).data('complete');
        var step            = $(next_marker).data('step');
        var title           = $(next_marker).data('title');

        if( $(next_marker).data('type') == 'tab' ) {

            var index = $(next_marker).index();
            var tab = $('.acf-tab-group li').get(index);

            $(tab).find('a').click();

        }

        if( $(next_marker).is(':last-child') ) {
            $('.psp-wizard-actions input[type="submit"]').show();
            $('.psp-wizard-edit-next-button').hide();
        }

        $('.timeline-title').text(title);
        $('.psp-wizard-section span.step').text(step);

        $(next_marker).addClass('active');

        $('.psp-timeline--bar span').width( progress + '%' );

    });

    $('.psp-wizard-edit-prev-button').click(function(e) {

        e.preventDefault();

        var current_marker  = $('.psp-timeline li.active:last');
        var next_marker     = $(current_marker).prev('li');

        // Hide and Show

        $('.psp-wizard-edit-next-button').show();

        hide_target = $(current_marker).data('target');
        show_target = $(next_marker).data('target');

        $('#' + hide_target).hide();
        $('#' + show_target).show();

        $(current_marker).removeClass('active');

        var progress        = $(next_marker).data('complete');
        var step            = $(next_marker).data('step');
        var title           = $(next_marker).data('title');

        if( $(next_marker).data('type') == 'tab' ) {

            var index = $(next_marker).index();
            var tab = $('.acf-tab-group li').get(index);

            $(tab).find('a').click();

            /*
            $('.acf-tab-wrap').show();
            $('.acf-tab-group').show();
            */

        }

        if( $(next_marker).is(':first-child') ) {
            $('.psp-wizard-edit-prev-button').hide();
            $('.psp_fe_title_fields').show();
            if( !$('#acf-field-automatic_progress').prop('checked') ) {
                $('#acf-percent_complete').show();
            }
        }

        $('.timeline-title').text(title);
        $('.psp-wizard-section span.step').text(step);

        $(next_marker).addClass('active');

        $('.psp-timeline--bar span').width( progress + '%' );

    });

    if( $('body').hasClass('psp-fe-manage-page-new') && !$('body').hasClass('psp-fe-edit-project') ) {

        var button = $('#psp-projects.psp-fe-manage-page-new .psp-fe-wizard-wrap form.acf-form input[type=submit]').detach();
        $(button).insertAfter('.psp-wizard-next-button');
        $(button).click(function(e) {
            $('.psp-fe-wizard-wrap form.acf-form').submit();
        });

        $('.psp-fe-wizard-wrap .acf-tab-wrap').find('li:first-child').find('a').click();

    }

    if( $('body').hasClass('psp-fe-edit-project') ) {

        /**
         * This is an edit page, do your magic!
         * @type {String}
         */

         if( $('body').hasClass('psp-fe-edit-project-status-new') ) {
             /**
              * This is the step two, defaulting to milestones
              * @type {String}
              */
             $('#acf-milestones').show();
         }

         $('.field-repeater-toggle-all').click();

         var button = $('.psp-fe-wizard-wrap input[type="submit"]').detach();

         $(button).insertAfter('.psp-wizard-edit-prev-button');
         $(button).click(function(e) {
             $('.psp-fe-wizard-wrap form.acf-form').submit();

         });

    }

    $('.psp-timeline li a').click(function(e) {

        e.preventDefault();

        psp_fe_hide_all_sections();

        var marker    = $(this).parent();
        var section   = $(marker).data('target');
        var title     = $(marker).data('title');
        var type      = $(marker).data('type');
        var progress  = $(marker).data('complete');

        if( $(marker).is(':first-child') ) {
            $('.psp-wizard-edit-prev-button').hide();
        }
        if( $(marker).is(':last-child') ) {
            $('.psp-wizard-edit-next-button').hide();
        }

        $('.timeline-title').text(title);
        $( '#' + section ).show();

        if( type == 'tab' ) {
            var index = $(marker).index();
            var tab = $('.acf-tab-group li').get(index);
            $(tab).find('a').click();
        }

        $('.psp-timeline--bar span').width( progress + '%' );
        $(marker).addClass('active').prevAll('li').addClass('active');

        if( $(marker).is(':first-child') ) {
            $('.psp_fe_title_fields').show();
        } else {
            $('.psp_fe_title_fields').hide();
        }

    });

    function psp_fe_hide_all_sections() {

        $('.psp-timeline li').each(function() {
            var target = $(this).data('target');
            $(this).removeClass('active');
            $('#'+target).hide();
        });

    }

    if( $('body').hasClass('psp-fe-manage-template') && !$('body').hasClass('psp-acf-ver-5') ) {

        $('.psp-fe-wizard-wrap select').each(function() {
            $(this).wrap('<div class="psp-select-wrapper"></div>');
        });

    }

    $('body').on( 'click', '.js-psp-fe-set-dates', function(e) {

        e.preventDefault();

        $('.psp-fe-edit-date-input').parent().addClass('psp-is-editing');
        $('.psp-fe-edit-date-input').show();
        $(this).hide();

    });

    $('body').on( 'click', '.js-psp-edit-date', function(e) {

        e.preventDefault();

        $('.psp-fe-edit-date-input').parent().addClass('psp-is-editing');
        $('.psp-fe-edit-date-input').show();

        $('.psp-the-start-date, .psp-the-end-date').hide();

    });

    $('body').on( 'submit', '.psp-js-date-update', function(e) {

        e.preventDefault();

        var input_element = $(this).find('.psp-fe-project-date-field');

        var start_date = $(this).find('[name="psp-start-date"]').val();
        var end_date   = $(this).find('[name="psp-end-date"]').val();
        var post_id    = $(this).find('[name="psp-post-id"]').val();

        psp_fe_update_date( start_date, end_date, post_id );

        $('.psp-fe-edit-date-input').hide();

        $('.psp-the-start-date, .psp-the-end-date').show();

    });

    $('body').on( 'click', '.js-psp-edit-description', function(e) {

        e.preventDefault();

        if( typeof(tinyMCE) !== 'undefined' ) {
            tinymce.init({
                selector: 'textarea[name="description"]',
                theme : 'modern',
                skin  : 'lightgray',
                menubar   : false,
                statusbar : true,
                plugins   : 'lists',
                elements  : 'pre-details',
                mode      : 'exact',
                  toolbar: "bold italic underline justifyleft justifycenter justifyright outdent indent code formatselect numlist bullist"
            });
        }

    });

    $('body').on( 'click', '.js-psp-edit-phase', function(e) {

        e.preventDefault();

        var ajaxurl     = jQuery('#psp-ajax-url').val();
        var phase_index = $(this).data('phase_index');
        var post_id     = $(this).data('post_id');
        var modal = $('#psp-edit-phases-modal');

        $(modal).find('.psp-loading').show();

        $.ajax({
            url  :  ajaxurl + "?action=psp_fe_populate_edit_phase_modal",
            type :  'post',
            data : {
                phase_index : phase_index,
                post_id     : post_id
            },
            success: function( response, success ) {

                $(modal).find('input[name="phase-title"]').val( response.data.title );
                $(modal).find('input[name="' + response.data.progressfield + '"]').val( response.data.progress );
                $(modal).find('input[name="phase_index"]').val( phase_index );

                $(modal).find('textarea[name="phase-description"]').val( response.data.description );

                if( typeof(tinyMCE) !== 'undefined' ) {
                    tinymce.init({
                        selector: 'textarea[name="phase-description"]',
                        theme : 'modern',
                        skin  : 'lightgray',
                        menubar   : false,
                        statusbar : true,
                        plugins   : 'lists',
                        elements  : 'pre-details',
                        mode      : 'exact',
                          toolbar: "bold italic underline justifyleft justifycenter justifyright outdent indent code formatselect numlist bullist"
                    });
                }

                $(modal).find('.psp-loading').hide();

            },
            error: function( response ) {

                $('.psp-fe-template-form .message').html( response.data.message ).show();

            }
        });

    });

    $('body').on( 'submit', '.js-psp-edit-description-form', function(e) {

        e.preventDefault();

        var ajaxurl = jQuery('#psp-ajax-url').val();

        if( typeof(tinyMCE) !== 'undefined' ) {
            var description = tinyMCE.activeEditor.getContent();
            $(this).find('textarea[name="description"]').val(description);
        }

        var data = $(this).serialize();
        var form = $(this);
        var post_id = $(this).find('input[name="post_id"]').val();

        $(form).find('.psp-loading').show();

        $.ajax({
            url  :  ajaxurl + "?action=psp_fe_update_description",
            type :  'post',
            data :  data,
            success: function( response, success ) {

                response.data.modify.forEach( psp_fe_modify_frontend );

                var modal = $(form).parents('.psp-modal');

                $(modal).find('.psp-loading').hide();
                $(modal).find('.modal-close').click();

            },
            error: function( response ) {

                $('.psp-fe-template-form .message').html( response.data.message ).show();

            }
        });


    });

    $('body').on( 'submit', '.js-psp-edit-phases-form', function(e) {

        e.preventDefault();

        var ajaxurl = jQuery('#psp-ajax-url').val();

        if( typeof(tinyMCE) !== 'undefined' ) {
            var description = tinyMCE.activeEditor.getContent();
            $(this).find('textarea[name="phase-description"]').val(description);
        }
        var data    = $(this).serialize();
        var form    = $(this);

        var post_id = $(this).find('input[name="post_id"]').val();

        $(form).find('.psp-loading').show();

        $.ajax({
            url  :  ajaxurl + "?action=psp_fe_update_phase",
            type :  'post',
            data :  data,
            success: function( response, success ) {

                response.data.modify.forEach( psp_fe_modify_frontend );

                psp_fe_update_total_progress( response.data.progress, post_id );

                var modal = $(form).parents('.psp-modal');

                $(modal).find('.psp-loading').hide();
                $(modal).find('.modal-close').click();

                if( ( jQuery(window).width() > 768 ) && ( ! jQuery('#psp-projects').hasClass('psp-width-single') ) ) {
                    jQuery('.psp-phase-info').css('height','auto');
                    pspEqualHeight( jQuery('.psp-phase-info') );
                }

            },
            error: function( response ) {

                $('.psp-fe-template-form .message').html( response.data.message ).show();

            }
        });



    });

});

function psp_fe_modify_frontend( item, index ) {

    if( item.method == 'replace' ) {
        jQuery( item.target ).replaceWith( item.markup );
        // Reset modals if needed
        jQuery( item.target ).find('.psp-modal-btn').leanModal({ closeButton: '.modal-close' });
    }

    if( item.method == 'prepend' ) {
        jQuery( item.target ).prepend( item.markup );
    }

    if( item.method == 'append' ) {
        jQuery( item.target ).append( item.markup );
    }

    if( item.method == 'next' ) {
        jQuery( item.target ).insertAfter( item.markup );
    }

    if( item.method == 'prev' ) {
        jQuery( item.target ).insertBefore( item.markup );
    }

    if( item.method == 'replace_attribute' ) {
        jQuery( item.target ).attr( item.attribute, item.value );
    }

    if( item.method == 'html' ) {
        jQuery( item.target ).html( item.markup );
    }

}

function psp_fe_update_phase_progress( progress, phase_index ) {

    if( typeof allCharts !== 'undefined' ) {
        allCharts[eval(phase_index)].segments[0].value = progress.completed;
        allCharts[eval(phase_index)].segments[1].value = progress.remaining;
        allCharts[eval(phase_index)].update();
    }

    var phase_id = parseInt(phase_index) + 1;

    var phase_elm = jQuery( '#phase-' + phase_id );
    var prev_remaining = 100 - progress.previous;

    // Update the details
    jQuery(phase_elm).find('.psp-chart-complete').html( progress.completed + '%');
    jQuery(phase_elm).attr( 'data-completed', progress.completed );
    jQuery(phase_elm).removeClass( 'psp-phase-complete-' + progress.previous ).addClass( 'psp-phase-complete-' + progress.completed )
                     .removeClass( 'psp-phase-remaining-' + prev_remaining ).addClass( 'psp-phase-remaining-' + progress.remaining );

}

function psp_fe_get_phase_completion( post_id, phase_id ) {

    var ajaxurl     = jQuery('#psp-ajax-url').val();

    jQuery.ajax({
        url     :   ajaxurl + "?action=psp_fe_get_phase_data",
        type    :   'post',
        data:   {
            post_id  : post_id,
            phase_id : phase_id,
        },
        success: function( success, response ) {

            // Update the circle graph
            if( typeof allCharts !== 'undefined' ) {
                allCharts[phase_id].segments[0].value = returned.data.completion;
                allCharts[phase_id].segments[1].value = returned.data.remaining;
                allCharts[phase_id].update();
            }

            // Update the details
            jQuery('#phase-' + phase_id + ' .psp-chart-complete').html(returned.data.completion + '%');
            jQuery('#phase-' + phase_id + ' .task-list-toggle span').html(returned.data.tasks_list_string);
            jQuery('#phase-' + phase_id + ' .psp-top-complete span.percentage').html(returned.data.completion + '%');
            jQuery('#phase-' + phase_id + ' .psp-top-complete span.count').html(returned.data.count_string);
            jQuery('#phase-' + phase_id + ' .psp-phase-overview').removeClass().addClass('psp-phase-overview cf psp-phase-progress-' + returned.data.completion);

        },
        error: function() {
            alert( 'There was a problem updating this phase.' );
        }
    });

    /*
    // Update all indications of completion in the phase (function)
    psp_fe_update_phase_completion_indicators( phaseID, completion, tasks_completed );

    if( ( jQuery(window).width() > 768 ) && ( ! jQuery('#psp-projects').hasClass('psp-width-single') ) ) {
		jQuery('.psp-phase-info').css('height','auto');
		pspEqualHeight( jQuery('.psp-phase-info') );
    }
    */

}

function psp_fe_update_phase_completion_indicators( phaseID, completion, tasks_completed ) {

    jQuery('#phase-'+phaseID+' .psp-chart-complete').html(completion + '%');
    jQuery('#phase-'+phaseID+' .task-list-toggle span b').html(tasks_completed);
    jQuery('#phase-'+phaseID+' .psp-top-complete span.percentage').html(completion + '%');
    jQuery('#phase-'+phaseID+' .psp-top-complete span.count span.completed').html(tasks_completed);
    jQuery('#phase-'+phaseID+' .psp-phase-overview').removeClass().addClass('psp-phase-overview cf psp-phase-progress-' + completion);

}

function psp_fe_update_progress_bar( progress ) {

    jQuery('.psp-progress span').removeClass()
                                .addClass( 'psp-' + progress )
                                .html('<b>' + progress + '%</b>')
                                .attr( 'data-original-title', progress + '%' );

}

function psp_fe_update_milestones( progress ) {

    /*
     * Reset milestones
     */
    jQuery('.psp-enhanced-milestone').removeClass('completed');
    jQuery('.psp-milestone-dot').removeClass('completed');

    jQuery('.psp-enhanced-milestone').each(function() {

        if( jQuery(this).data('milestone') <= progress ) {
            jQuery(this).addClass('completed');
        }

    });

    /*
     * Reset milestone dots
     */
    jQuery('.psp-milestone-dot').each(function() {

        if( jQuery(this).data('milestone') <= progress ) {
            jQuery(this).addClass('completed');

        }
    });

}

function psp_fe_update_total_progress( progress, post_id ) {

    // If this is where there is a task progress
    if( jQuery('.psp-task-project-' + post_id ).length) {

        jQuery('.psp-task-project-' + post_id + ' .psp-progress span').removeClass().addClass( 'psp-' + progress ).html( '<b>' + progress + '%</b>' );
        return;

    }

    psp_fe_update_progress_bar( progress );
    psp_fe_update_milestones( progress );

}

function psp_fe_get_total_progress( post_id ) {

	var ajaxurl = jQuery('#psp-ajax-url').val();

    jQuery.ajax({
        url: ajaxurl + "?action=psp_update_total_fe",
        type: 'post',
        data: {
            post_id : post_id,
        },
        success: function( progress ) {
            psp_fe_update_total_progress( progress, post_id )
        },
        error: function(data) {
            console.log(data);
        }
    });

}

function psp_fe_delete_task( post_id, phase_index, task_index, phase_id, task_id ) {

    var ajaxurl = jQuery('#psp-ajax-url').val();

    jQuery.ajax({
        url: ajaxurl + "?action=psp_fe_delete_task",
        type: 'post',
        data: {
            post_id     : post_id,
            phase_index    : phase_index,
            task_index     : task_index,
			phase_id: phase_id,
			task_id: task_id,
        },
        success: function( response ) {

            // Update phase and total progress
            psp_fe_update_phase_progress( response.data.phase_progress, phase_index );
            psp_fe_update_total_progress( response.data.project_progress, post_id );

            // Update and replace items on screen
            jQuery('#phase-' + ( parseInt( phase_index ) + 1 ) ).find('li.task-item-' + task_index ).slideUp('slow', function() {

                response.data.modify.forEach( psp_fe_modify_frontend );

                // Redisplay all of this
                jQuery('#phase-' + ( parseInt( phase_index ) + 1 ) ).find('.psp-task-list').addClass('active').show();
                jQuery('#phase-' + ( parseInt( phase_index ) + 1 ) ).find('.task-list-toggle').addClass('active');
                jQuery('#phase-' + ( parseInt( phase_index ) + 1 ) ).find('.psp-fe-add-task').show();
                jQuery('#phase-' + ( parseInt( phase_index ) + 1 ) ).find('.psp-modal-btn').leanModal({ closeButton: ".modal-close" });

				psp_update_my_task_count( post_id, phase_index );

				// Placed here so that it happens once the animation has completed
				jQuery( document ).trigger( 'psp-fe-deleted-task', [ phase_index, task_index ] );

            });

        },
        error: function( response ) {
            alert( response.message );
        }
    });


}

function psp_fe_update_date( start_date, end_date, post_id ) {

    var ajaxurl = jQuery('#psp-ajax-url').val();

    jQuery.ajax({
        url: ajaxurl + "?action=psp_fe_update_date",
        type: 'post',
        data: {
            post_id       : post_id,
            start_date    : start_date,
            end_date      : end_date,
        },
        success: function( response ) {

            console.log(response);

            if( response.data.success == false ) {
                alert( response.data.message );
                return;
            }

            jQuery('#psp-time-overview .psp-tb-progress span')
                .removeClass()
                .addClass( 'psp-' + response.data.dates.ellapsed )
                .attr( 'data-original-title', response.data.dates.title );

            jQuery('#psp-time-overview .psp-tb-progress b').html( response.data.dates.ellapsed + '%' );

            jQuery('#psp-time-overview .psp-tb-progress')
                .removeClass('psp-behind')
                .addClass('psp-tb-progress')
                .addClass(response.data.dates.class);

            response.data.modify.forEach( psp_fe_modify_frontend );

            jQuery('.psp-the-start-date,.psp-the-end-date').show();

            jQuery( document ).trigger( 'psp-fe-updated-date', [ start_date, end_date, response.data.dates.ellapsed ] );

        },
        error: function( response ) {
            alert( response.message );
        }
    });


}
