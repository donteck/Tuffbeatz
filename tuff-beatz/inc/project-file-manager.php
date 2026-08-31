<?php
if(!defined('ABSPATH'))exit;
/** TUFF BEATZ Studio File Manager V10 */
function tuff_beatz_file_manager_bucket($file){
 $cat=sanitize_key($file['category']??'source');$v=strtolower((string)($file['version']??''));$n=strtolower((string)($file['name']??''));
 if($cat==='delivery'){
  if(strpos($v,'master')!==false||strpos($n,'master')!==false)return 'masters';
  if(strpos($v,'final')!==false||strpos($v,'delivery')!==false)return 'final';
  return 'mixes';
 }
 if(strpos($v,'stem')!==false||strpos($n,'stem')!==false)return 'stems';
 return 'source';
}
function tuff_beatz_file_manager_label($bucket){$m=array('source'=>'Source Files','stems'=>'Stems','mixes'=>'Mix Versions','masters'=>'Masters','final'=>'Final Delivery');return $m[$bucket]??'Project Files';}
function tuff_beatz_file_manager_icon($name){$ext=strtolower(pathinfo($name,PATHINFO_EXTENSION));if(in_array($ext,array('wav','aiff','aif','mp3','m4a'),true))return '♫';if($ext==='zip')return 'ZIP';if($ext==='pdf')return 'PDF';if(in_array($ext,array('mid','midi'),true))return 'MIDI';return 'FILE';}
function tuff_beatz_file_manager_render(){
 if(!is_page('project-dashboard')||!is_user_logged_in())return;$id=(int)($_GET['project']??0);if(!$id||!function_exists('tuff_beatz_user_project_permission')||!tuff_beatz_user_project_permission($id,'view'))return;
 $files=function_exists('tuff_beatz_vault_files')?tuff_beatz_vault_files($id):array();$groups=array('source'=>array(),'stems'=>array(),'mixes'=>array(),'masters'=>array(),'final'=>array());foreach($files as $f)$groups[tuff_beatz_file_manager_bucket($f)][]=$f;
 $can_upload=tuff_beatz_user_project_permission($id,'upload');
 echo '<template id="tb-file-manager-template"><section id="files" class="tb-portal-card tb-workspace-section tb-file-manager"><div class="tb-fm-head"><div><p class="tb-card-kicker">STUDIO FILE MANAGER</p><h2>Files & Versions</h2><p>Private project assets organized by production stage.</p></div><div class="tb-fm-total"><strong>'.esc_html(count($files)).'</strong><span>SECURE FILES</span></div></div><div class="tb-fm-tabs">';
 foreach($groups as $key=>$items)echo '<button type="button" data-fm-filter="'.esc_attr($key).'">'.esc_html(tuff_beatz_file_manager_label($key)).'<b>'.esc_html(count($items)).'</b></button>';echo '</div><div class="tb-fm-groups">';
 foreach($groups as $key=>$items){echo '<section class="tb-fm-group" data-fm-group="'.esc_attr($key).'"><div class="tb-fm-group-head"><div><small>'.esc_html(strtoupper($key)).'</small><h3>'.esc_html(tuff_beatz_file_manager_label($key)).'</h3></div><span>'.esc_html(count($items)).' item'.(count($items)===1?'':'s').'</span></div>';
 if(!$items){echo '<div class="tb-fm-empty">No '.esc_html(strtolower(tuff_beatz_file_manager_label($key))).' yet.</div>';}else{foreach(array_reverse($items) as $f){$u=(int)($f['uploaded_by']??0);$user=$u?get_userdata($u):null;$who=$user?$user->display_name:'TUFF BEATZ';$when=!empty($f['uploaded_at'])?mysql2date('M j, Y • g:i a',$f['uploaded_at']):'';$version=$f['version']?:'Project File';echo '<a class="tb-fm-file" href="'.esc_url(tuff_beatz_vault_download_url($id,$f['id'])).'"><span class="tb-fm-icon">'.esc_html(tuff_beatz_file_manager_icon($f['name']??'')).'</span><div class="tb-fm-file-main"><strong>'.esc_html($f['name']??'File').'</strong><span>'.esc_html($version).' • '.esc_html(tuff_beatz_vault_human_size($f['size']??0)).'</span></div><div class="tb-fm-meta"><small>UPLOADED BY</small><b>'.esc_html($who).'</b><span>'.esc_html($when).'</span></div><em>Download ↓</em></a>';}}
 echo '</section>';}
 echo '</div>';
 if($can_upload){echo '<form method="post" enctype="multipart/form-data" action="'.esc_url(admin_url('admin-post.php')).'" class="tb-fm-upload"><input type="hidden" name="action" value="tb_upload_project_dashboard_files"><input type="hidden" name="request_id" value="'.esc_attr($id).'">';wp_nonce_field('tb_project_files_'.$id,'tb_files_nonce');echo '<div><small>ADD TO PRIVATE VAULT</small><h3>Upload Project Files</h3><p>Use a clear version label so the studio can identify stems, revisions and replacements.</p></div><input name="upload_version" type="text" placeholder="Example: Vocal Stems V2"><input name="project_files[]" type="file" multiple accept=".wav,.aiff,.aif,.mp3,.m4a,.zip,.pdf,.txt,.mid,.midi"><button class="btn btn-gold" type="submit">Upload Securely</button></form>';}
 echo '</section></template><script>document.addEventListener("DOMContentLoaded",function(){var t=document.getElementById("tb-file-manager-template"),old=document.getElementById("files");if(t&&old){old.replaceWith(t.content.cloneNode(true));var root=document.getElementById("files"),buttons=root?root.querySelectorAll("[data-fm-filter]"):[];function show(key){root.querySelectorAll("[data-fm-group]").forEach(function(g){g.hidden=g.getAttribute("data-fm-group")!==key;});buttons.forEach(function(b){b.classList.toggle("is-active",b.getAttribute("data-fm-filter")===key);});}buttons.forEach(function(b){b.addEventListener("click",function(){show(b.getAttribute("data-fm-filter"));});});var first=Array.prototype.find.call(buttons,function(b){return parseInt((b.querySelector("b")||{}).textContent||"0",10)>0;})||buttons[0];if(first)show(first.getAttribute("data-fm-filter"));}});</script>';
}
add_action('wp_footer','tuff_beatz_file_manager_render',5);
function tuff_beatz_file_manager_assets(){if(is_page('project-dashboard')){$p=get_template_directory().'/assets/css/file-manager.css';if(file_exists($p))wp_enqueue_style('tuff-beatz-file-manager',get_template_directory_uri().'/assets/css/file-manager.css',array('tuff-beatz-project-portal'),filemtime($p));}}
add_action('wp_enqueue_scripts','tuff_beatz_file_manager_assets',49);
