<div class="psp-archive-navigation">
    <hgroup class="psp-sub-masthead cf">
        <nav class="psp-section-nav psp-grid-container-fluid">

            <?php do_action( 'psp_before_sub_navigation' ); ?>

            <?php
            if( is_single() && get_post_type() == 'psp_projects' && psp_can_edit_project() ):
                $link = apply_filters( 'psp_project_edit_post_link', psp_get_edit_post_link() ); ?>
                <a href="<?php echo esc_url($link); ?>" class="psp-pull-right psp-btn"><i class="fa fa-pencil"></i> <?php esc_html_e( 'Edit Project', 'psp_projects' ); ?></a>
            <?php endif; ?>

            <ul>
                <?php
                $nav_items = psp_get_section_nav_items();

                foreach( $nav_items as $link ):

                    $class = apply_filters( 'psp_section_nav_link_class', 'inactive', $link[ 'slug' ] ); ?>

                    <li id="psp-sub-nav-<?php echo esc_attr($link['slug']); ?>"><a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_url( $link[ 'url' ] ); ?>"><i class="<?php echo esc_attr( $link[ 'icon' ] ); ?>"></i> <?php echo esc_html( $link[ 'name' ] ); ?></a></li>

                <?php endforeach; ?>
            </ul>

            <?php do_action( 'psp_after_sub_navigation' ); ?>

        </nav>
    </hgroup>
</div> <!--/.psp-archive-navigation-->
