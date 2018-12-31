<?php
add_filter( 'psp_phase_fields', 'psp_sequential_task_field' );
function psp_sequential_task_field( $fields ) {

    $sequential_field = array(
        'key'           => 'psp_sequential_task',
        'label'         => __('Sequential Task','psp_projects'),
        'name'          => 'seq_task',
        'instructions'	=>	__( 'Requires previous task to be complete.', 'psp_projects' ),
        'type'          => 'checkbox',
        'choices'       => array (
            'Yes'       => 'Yes',
        ),
        'default_value' => '',
        'layout'        => 'horizontal',
    );

    $new_fields     = array();
    $new_sub_fields = array();

    // The index is different for ACF5 vs 4
    $i = ( PSP_ACF_VER == 4 ? 1 : 3 );

    foreach( $fields['fields'] as $parent_field ) {

        if( $parent_field['label'] == 'Phases' ) {

            $new_parent_field = array();

            foreach( $parent_field['sub_fields'] as $sub_field ) {

                if( $sub_field['name'] == 'tasks' ) {
                    $sub_field['sub_fields'][] = $sequential_field;
                }

                $new_parent_field[] = $sub_field;

            }

            $parent_field['sub_fields'] = $new_parent_field;

        }

        $new_fields[] = $parent_field;

    }

    $fields['fields'] = $new_fields;

    return $fields;

}
