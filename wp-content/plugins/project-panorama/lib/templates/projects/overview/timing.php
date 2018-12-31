<div id="psp-time-overview">
    <?php
    $post_id        = ( isset( $post_id ) ? $post_id : get_the_ID() );
    $start_date     = psp_get_the_start_date( NULL, $post_id );
    $end_date       = psp_get_the_end_date( NULL, $post_id );

    do_action( 'psp_timing_before_timebar', $post_id );

    psp_the_simplified_timebar($post_id);

    do_action( 'psp_timing_after_timebar', $post_id );

    if( $start_date || $end_date ): ?>
        <div class="psp-row">
            <?php if( $start_date ): ?>
                <div class="psp-col-sm-6 psp-archive-list-dates">
                    <h5><?php esc_html_e( 'Start Date', 'psp_projects' ); ?></h5>
                    <p><?php echo esc_html($start_date); ?></p>
                </div>
            <?php endif;
            if( $end_date ): ?>
                <div class="psp-col-sm-6 psp-archive-list-dates">
                    <h5><?php esc_html_e( 'End Date', 'psp_projects' ); ?></h5>
                    <p><?php echo esc_html($end_date); ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif;

    do_action( 'psp_timing_before_header', $post_id );

    do_action( 'psp_timing_after_header', $post_id );

    do_action( 'psp_timing_after_dates', $post_id ); ?>
</div>
