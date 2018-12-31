<?php $panorama_access = panorama_check_access($post->ID); ?>

<div id="psp-projects" class="psp-theme-template psp-reset psp-shortcode">

		<input type="hidden" id="psp-task-style" value="<?php the_field('expand_tasks_by_default',$post->ID); ?>">

        <?php // do_action('psp_the_header'); ?>

        <?php if ( $panorama_access ) : ?>

            <?php do_action('psp_before_overview'); ?>

            <section id="overview" class="wrapper psp-section">

                <?php do_action('psp_before_essentials'); ?>
                <?php do_action('psp_the_essentials'); ?>
                <?php do_action('psp_after_essentials'); ?>

            </section> <!--/#overview-->

            <?php do_action('psp_between_overview_progress'); ?>

            <section id="psp-progress" class="cf psp-section">

                <?php do_action('psp_before_progress'); ?>
                <?php do_action('psp_the_progress'); ?>
                <?php do_action('psp_after_progress'); ?>

            </section> <!--/#progress-->

            <?php do_action('psp_between_progress_phases'); ?>

            <section id="psp-phases" class="wrapper psp-section">

                <?php do_action('psp_before_phases'); ?>
                <?php do_action('psp_the_phases'); ?>
                <?php do_action('psp_after_phases'); ?>

            </section>

            <?php do_action('psp_between_phases_discussion'); ?>

			<!-- Discussion -->
            <?php if ( comments_open() ) : ?>
                <section id="psp-discussion" class="psp-section cf">

                    <?php
                    do_action( 'psp_before_discussion' );
                    do_action( 'psp_the_discussion' );
                    do_action( 'psp_after_discussion' );
                    ?>

                </section>
            <?php
			add_filter( 'comments_template', 'psp_disable_custom_template_comments' );
			endif; ?>


        <?php endif; ?>

        <?php if( ! $panorama_access ): ?>
            <div id="overview" class="wrapper">
                <div id="psp-login">
                    <?php if( ( ! $panorama_access ) && (get_field('restrict_access_to_specific_users'))): ?>
                        <h2><?php _e('This Project Requires a Login','psp_projects'); ?></h2>
                        <?php if(!is_user_logged_in()) {
                            echo panorama_login_form();
                        } else {
                            echo "<p>".__('You don\'t have permission to access this project','psp_projects')."</p>";
                        }
                        ?>
                    <?php endif; ?>
                    <?php if((post_password_required()) && (!current_user_can('manage_options'))): ?>
                        <h2><?php _e('This Project is Password Protected','psp_projects'); ?></h2>
                        <?php echo get_the_password_form(); ?>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>

</div> <!--/#psp-project-->
