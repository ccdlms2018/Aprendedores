            <footer class="psp-grid-container-fluid" id="psp-colophon">
                <div class="psp-grid-row">
                    <div class="psp-col-md-4">
                        <p><?php bloginfo( 'name' ); ?> - <?php echo date( 'Y' ); ?></p>
                    </div>
                    <div class="psp-col-md-8">

                        <?php
                        $psp_slug = psp_get_option( 'psp_slug' );

                        $nav = ( has_nav_menu( 'psp_footer_menu' ) ? psp_get_custom_project_menu_items( 'psp_footer_menu' ) : apply_filters( 'psp_footer_nav', array(
                            array(
                                'link'  =>  home_url(),
                                'title' =>  __( 'home', 'psp_projects' )
                            ),
                            array(
                                'link'  =>  get_post_type_archive_link('psp_projects'),
                                'title' =>  __( 'dashboard', 'psp_projects' )
                            ),
                        ) ) ); ?>

                        <nav class="footer-nav">
                            <ul>
                                <?php foreach( $nav as $link ): ?>
                                    <li><a href="<?php echo esc_url( $link['link'] ); ?>"><?php echo esc_html( $link['title'] ); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </nav>

                    </div>
                </div>
            </footer>

        <?php
        if( is_user_logged_in() ) include( psp_template_hierarchy( 'global/navigation-off.php' ) );

        do_action( 'psp_footer' );
        do_action( 'psp_footer_' . __FILE__ ); ?>

        </div> <!--/.psp-container-->
    </div>

</body>
</html>
