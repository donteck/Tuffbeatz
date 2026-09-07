<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ — Proposal Live Bootstrap V1.0 */
function tuff_beatz_register_proposal_live_page(){
    $page=get_page_by_path('proposal-live');
    if(!$page){
        $id=wp_insert_post(array(
            'post_title'=>'Proposal Live',
            'post_name'=>'proposal-live',
            'post_status'=>'publish',
            'post_type'=>'page',
            'post_content'=>''
        ));
        if(!is_wp_error($id)&&$id)update_post_meta($id,'_wp_page_template','page-proposal-live.php');
    }elseif(get_post_meta($page->ID,'_wp_page_template',true)!=='page-proposal-live.php'){
        update_post_meta($page->ID,'_wp_page_template','page-proposal-live.php');
    }
}
add_action('init','tuff_beatz_register_proposal_live_page',39);
