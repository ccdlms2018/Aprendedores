<?php
function psp_get_priorities_list() {

    return apply_filters( 'psp_priorities_list', array(
        'normal' => array(
                'label' =>  __( 'Normal', 'psp_projects' ),
                'slug'  =>  'normal',
                'color' =>  '#99C262',
                'value' =>  2,
        ),
        'low'   => array(
            'label' =>  __( 'Low', 'psp_projects' ),
            'slug'  =>  'low',
            'color' =>  '#CDD7B6',
            'value' => 4,
        ),
        'medium' => array(
            'label' =>  __( 'Medium', 'psp_projects' ),
            'slug'  =>  'medium',
            'color' =>  '#FBB829',
            'value' =>  3,
        ),
        'high' => array(
            'label' =>  __( 'High', 'psp_projects' ),
            'slug'  =>  'high',
            'color' =>  '#c44d58',
            'value' =>  1
        ),
    ));

}
