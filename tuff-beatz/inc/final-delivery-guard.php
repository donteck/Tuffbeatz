<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ Final Delivery Guard V12.12 — owner authority + canonical master/final evidence */
function tuff_beatz_delivery_confirmation_authorized($request_id,$user_id=0){
 $request_id=(int)$request_id;$user_id=$user_id?:get_current_user_id();if(!$request_id||!$user_id)return false;$p=get_post($request_id);if(!$p||$p->post_type!=='tb_request')return false;
 if(user_can($user_id,'edit_post',$request_id))return false;
 return (int)$p->post_author===(int)$user_id;
}
function tuff_beatz_delivery_file_is_final_evidence($request_id,$file){
 if(!is_array($file)||!empty($file['archived']))return false;
 if(function_exists('tuff_beatz_vault_asset_type')){$type=tuff_beatz_vault_asset_type($file);if(in_array($type,array('master','final'),true))return true;}
 $studio=sanitize_key($file['studio_category']??'');if(in_array($studio,array('masters','final'),true))return true;
 $fid=(string)($file['id']??'');$states=get_post_meta((int)$request_id,'_tb_mix_review_status',true);$state=is_array($states)&&$fid&&isset($states[$fid])?sanitize_key($states[$fid]):'';if(in_array($state,array('master','final'),true))return true;
 if(($file['category']??'')!=='delivery')return false;$label=strtolower(trim((string)($file['version']??'').' '.(string)($file['name']??'')));if(!preg_match('/\b(final|master|mastered)\b/',$label))return false;
 return in_array($state,array('producer-delivery','approved','master','final'),true)||$state==='';
}
function tuff_beatz_has_final_delivery_evidence($request_id){foreach(function_exists('tuff_beatz_vault_files')?tuff_beatz_vault_files((int)$request_id):array() as $f)if(tuff_beatz_delivery_file_is_final_evidence($request_id,$f))return true;return false;}
function tuff_beatz_final_delivery_guard(){
 $id=(int)($_POST['request_id']??0);$r=function_exists('tuff_beatz_project_dashboard_url')?tuff_beatz_project_dashboard_url($id).'#delivery':home_url('/client-portal/');
 if(!$id||!is_user_logged_in()||!tuff_beatz_delivery_confirmation_authorized($id)){wp_safe_redirect(add_query_arg('delivery_blocked','authority',$r));exit;}
 if(!isset($_POST['tb_delivery_confirm_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_delivery_confirm_nonce'])),'tb_confirm_delivery_'.$id)){wp_safe_redirect(add_query_arg('delivery_blocked','security',$r));exit;}
 if((get_post_meta($id,'_tb_request_status',true)?:'new')!=='delivery'){wp_safe_redirect(add_query_arg('delivery_blocked','status',$r));exit;}
 if(!tuff_beatz_has_final_delivery_evidence($id)){wp_safe_redirect(add_query_arg('delivery_blocked','evidence',$r));exit;}
}
add_action('admin_post_tb_confirm_final_delivery','tuff_beatz_final_delivery_guard',1);
function tuff_beatz_final_delivery_guard_workspace(){
 if(!is_page('project-dashboard')||!is_user_logged_in())return;$id=(int)($_GET['project']??0);if(!$id||!function_exists('tuff_beatz_user_can_view_request')||!tuff_beatz_user_can_view_request($id))return;
 $authorized=tuff_beatz_delivery_confirmation_authorized($id);$evidence=tuff_beatz_has_final_delivery_evidence($id);$status=get_post_meta($id,'_tb_request_status',true)?:'new';$blocked=sanitize_key(wp_unslash($_GET['delivery_blocked']??''));
 ?><script>document.addEventListener('DOMContentLoaded',function(){var box=document.querySelector('#delivery .tb-delivery-confirm'),authorized=<?php echo $authorized?'true':'false';?>,evidence=<?php echo $evidence?'true':'false';?>,status=<?php echo wp_json_encode($status);?>;if(box&&(!authorized||!evidence||status!=='delivery'))box.remove();var d=document.getElementById('delivery');if(!d)return;<?php if($blocked):?>var n=document.createElement('div');n.className='tb-delivery-guard-alert';n.textContent=<?php echo wp_json_encode($blocked==='authority'?'Only the project owner can confirm final delivery.':($blocked==='evidence'?'Final acceptance requires a real Master or Final Delivery file in the Private Vault.':($blocked==='status'?'This project is not currently in Final Delivery.':'The delivery confirmation could not be verified.')));?>;d.insertBefore(n,d.firstChild);<?php endif;?>if(status==='delivery'&&authorized&&!evidence){var w=document.createElement('div');w.className='tb-delivery-guard-alert';w.innerHTML='<strong>Final acceptance locked</strong><span>TUFF BEATZ must release a Master or Final Delivery file before this project can be completed.</span>';var hero=d.querySelector('.tb-delivery-hero');if(hero)hero.insertAdjacentElement('afterend',w);}});</script><?php
}
add_action('wp_footer','tuff_beatz_final_delivery_guard_workspace',30);
function tuff_beatz_final_delivery_guard_assets(){if(!is_page('project-dashboard'))return;$p=get_template_directory().'/assets/css/final-delivery.css';if(file_exists($p))wp_add_inline_style('tuff-beatz-final-delivery','.tb-delivery-guard-alert{margin:16px 0;padding:15px 17px;border:1px solid rgba(216,180,90,.22);border-radius:14px;background:rgba(216,180,90,.045);color:#d8c89f;font-size:.72rem;line-height:1.6}.tb-delivery-guard-alert strong{display:block;color:#efd37f;font-family:Cinzel,serif;font-size:.82rem;margin-bottom:4px}.tb-delivery-guard-alert span{display:block;color:#948a79}');}
add_action('wp_enqueue_scripts','tuff_beatz_final_delivery_guard_assets',95);
