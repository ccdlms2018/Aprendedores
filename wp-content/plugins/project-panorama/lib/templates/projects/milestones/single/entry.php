<?php $date = get_sub_field( 'date' ); ?>
<div class="psp-col-md-3 psp-enhanced-milestone psp-match-height-item <?php if(get_sub_field('occurs') <= $completed) { echo 'completed'; } ?> <?php echo esc_attr( psp_late_class( $date ) ); ?>" data-milestone="<?php the_sub_field( 'occurs' ); ?>">
	<div class="psp-enhanced-milestone-wrap">
		<?php do_action( 'psp_before_milestone_entry_heading' ); ?>

		<div class="psp-milestone-heading">
			<?php do_action( 'psp_before_milestone_entry_occurs' ); ?>
			<b class="psp-mm-marker"><?php the_sub_field( 'occurs' ); ?>%</b>
			<?php do_action( 'psp_after_milestone_entry_occurs' ); ?>
		</div>

		<?php do_action( 'psp_before_milestone_entry_title' ); ?>

		<h4>
			<?php echo apply_filters( 'psp_milestone_entry_title', get_sub_field( 'title' ) ); ?>
			<?php if( !empty( $date ) ): ?>
				<span><?php psp_the_milestone_due_date( $date ); ?></span>
			<?php endif; ?>
		</h4>

		<?php
		do_action( 'psp_after_milestone_entry_title' );
		echo wpautop( do_shortcode( apply_filters( 'psp_milestone_entry_description', get_sub_field( 'description' ) ) ) );
		do_action( 'psp_after_milestone_entry_description' ); ?>
	</div>
</div>
