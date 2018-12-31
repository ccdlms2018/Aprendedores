<?php
do_action( 'psp_before_menu' );
$post_types     = array( 'psp_projects', 'psp_teams' );
$nav_items      = psp_get_nav_items();
if( ( in_array( get_post_type(), $post_types ) && is_archive() ) || get_post_type() === 'psp_pages' ) $sub_nav_items  = psp_get_section_nav_items(); ?>

<nav id="psp-offcanvas-menu">
    <ul>
        <?php
        do_action( 'psp_before_nav_items' );

        if( !empty( $nav_items ) ):
            foreach( $nav_items as $item ):

                $atts = '';
                $class = ( isset($item['class']) ? $item['class'] : '' );

                if( isset($item['atts']) ) {
                    foreach( $item['atts'] as $attribute => $value ) $atts .= $attribute . '="' . $value . '" ';
                }

                ?>
                <li id="<?php echo esc_attr( $item['id'] ); ?>"><a href="<?php echo esc_url( $item['link'] ); ?>" class="<?php echo esc_attr($class); ?>" <?php echo $atts; ?>><?php if( isset($item['icon']) ) { echo '<i class="' . esc_attr( $item['icon'] ) . '"></i>'; } ?> <?php echo esc_html( $item['title'] ); ?></a></li>

            <?php
            endforeach;
        endif;

        do_action( 'psp_menu_items' ); do_action( 'psp_before_sub_nav_items' );

        if( !empty( $sub_nav_items ) ):
            foreach( $sub_nav_items as $link ):

                $class = apply_filters( 'psp_section_nav_link_class', 'inactive', $link[ 'slug' ] ); ?>

                <li><a class="<?php echo esc_attr( $class . ' ' . $link[ 'icon' ] ); ?>" href="<?php echo esc_url( $link[ 'url' ] ); ?>"><?php echo esc_html( $link[ 'name' ] ); ?></a></li>

            <?php
            endforeach;
        endif;

        if( is_user_logged_in() ): ?>

            <li id="nav-logout"><a href="<?php echo esc_url( wp_logout_url( $_SERVER[ 'REQUEST_URI' ] ) ); ?>"><i class="psp-fi-logout psp-fi-icon"></i> <?php esc_html_e( 'Logout', 'psp_projects' ); ?></a></li>

        <?php endif; ?>

    </ul>
</nav>

<?php
do_action( 'psp_after_menu' );
