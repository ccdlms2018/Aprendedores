<?php
if( !isset($markers) ) $markers = psp_fe_get_milestone_markers(); ?>
<aside class="psp-timeline">
    <ol>
        <?php
        foreach( $markers as $marker ):
            $class = ( $completion >= $marker['complete'] ? 'active' : '' );
            if( $section == $marker['section'] ) $class .= ' initial'; ?>
            <li class="<?php echo esc_attr( 'psp-timeline--marker ' . $class ); ?>" data-complete="<?php echo esc_attr($marker['complete']); ?>" data-step="<?php echo esc_attr($marker['step']); ?>" data-type="<?php echo esc_attr($marker['type']); ?>" data-acf5="<?php echo esc_attr($marker['acf5']); ?>" data-title="<?php echo esc_attr($marker['title']); ?>" data-target="<?php echo esc_attr($marker['target']); ?>">
                <a href="#">
                    <span class="psp-timeline--dot"></span>
                    <strong><?php echo esc_html( $marker['title'] ); ?></strong>
                </a>
            </li>
        <?php endforeach; ?>
    </ol>
    <p class="psp-timeline--bar"><span style="<?php echo esc_attr('width:' . $completion . '%'); ?>"></span></p>
</aside>
