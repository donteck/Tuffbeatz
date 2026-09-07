<?php
/**
 * TUFF BEATZ — Public Intake Bridge V1.1
 * Converts the public Start a Project form into canonical tb_request records
 * consumed by Producer Command Center / Studio OS.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_public_intake_service_key($label){
    $map=array(
        'Original Production'=>'full-production',
        'Beat Production'=>'beat-production',
        'Arrangement'=>'arrangement',
        'Mixing'=>'mixing',
        'Mastering'=>'mastering',
        'Vocal Production'=>'vocal-production',
        'Song Development'=>'songwriting',
        'Full Production Package'=>'full-production',
        'Creative / Production Consulting'=>'consultation',
    );
    return $map[$label]??'custom';
}

function tuff_beatz_create_public_intake_request($data){
    $name=sanitize_text_field($data['name']??'');
    $artist=sanitize_text_field($data['artist_name']??'');
    $email=sanitize_email($data['email']??'');
    $phone=sanitize_text_field($data['phone']??'');
    $service_label=sanitize_text_field($data['service']??'');
    $project=sanitize_text_field($data['project_name']??'');
    $genre=sanitize_text_field($data['genre']??'');
    $budget=sanitize_text_field($data['budget']??'');
    $timeline=sanitize_text_field($data['timeline']??'');
    $reference=esc_url_raw($data['reference_url']??'');
    $message=sanitize_textarea_field($data['message']??'');
    $display_artist=$artist?:$name;
    $display_project=$project?:'New Project';

    if(!$name||!is_email($email)||!$service_label||!$message){
        return new WP_Error('tb_intake_required','Missing required intake fields.');
    }

    $request_id=wp_insert_post(array(
        'post_type'=>'tb_request',
        'post_status'=>'publish',
        'post_title'=>sprintf('%s — %s',$display_artist,$display_project),
        'post_author'=>0,
    ),true);
    if(is_wp_error($request_id)||!$request_id)return $request_id;

    $service_key=tuff_beatz_public_intake_service_key($service_label);
    $fields=array(
        '_tb_public_intake'=>1,
        '_tb_request_artist'=>$display_artist,
        '_tb_request_client_name'=>$name,
        '_tb_request_email'=>$email,
        '_tb_request_phone'=>$phone,
        '_tb_request_service'=>$service_key,
        '_tb_request_service_label'=>$service_label,
        '_tb_request_project_type'=>$project,
        '_tb_request_genre'=>$genre,
        '_tb_request_budget'=>$budget,
        '_tb_request_timeline'=>$timeline,
        '_tb_request_target_date'=>'',
        '_tb_request_reference_url'=>$reference,
        '_tb_request_details'=>$message,
        '_tb_request_status'=>'new',
        '_tb_request_rights_confirmed'=>'public-intake-pending',
        '_tb_request_submitted_at'=>current_time('mysql'),
        '_tb_intake_source'=>'start-a-project',
    );
    foreach($fields as $key=>$value)update_post_meta($request_id,$key,$value);

    if(function_exists('tuff_beatz_project_activity')){
        $activity=get_post_meta($request_id,'_tb_project_activity',true);
        if(!is_array($activity))$activity=array();
        $activity[]=array('type'=>'intake','message'=>'Public Start a Project intake received','time'=>current_time('mysql'));
        update_post_meta($request_id,'_tb_project_activity',$activity);
    }

    return $request_id;
}

function tuff_beatz_public_intake_queue($limit=25){
    $q=new WP_Query(array(
        'post_type'=>'tb_request',
        'post_status'=>'publish',
        'posts_per_page'=>max(1,(int)$limit),
        'orderby'=>'date','order'=>'DESC',
        'meta_query'=>array(
            'relation'=>'AND',
            array('key'=>'_tb_public_intake','value'=>'1'),
            array('key'=>'_tb_request_status','value'=>array('new','reviewing'),'compare'=>'IN'),
        ),
    ));
    return $q->posts;
}
