<?php
add_action( 'psp_head', 'psp_add_sequential_task_styles' );
function psp_add_sequential_task_styles() {
    psp_register_style( 'sequential-tasks', PSP_SQ_URL . 'assets/sequential-tasks.css' );
}
