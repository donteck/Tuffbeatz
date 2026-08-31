<?php
if (!defined('ABSPATH')) exit;

/** TUFF BEATZ Project Collaborators & Permissions V5 */
function tuff_beatz_collaborator_roles(){return array('artist'=>'Artist','manager'=>'Manager','producer'=>'Producer','songwriter'=>'Songwriter','composer'=>'Composer','musician'=>'Musician','engineer'=>'Engineer','label'=>'Label / A&R','publisher'=>'Publisher','band-member'=>'Band Member','assistant'=>'Assistant','other'=>'Collaborator');}
function tuff_beatz_collaborator_permission_labels(){return array('view'=>'View Project','upload'=>'Upload Files','message'=>'Messages','review'=>'Mix Review','approve'=>'Approve Mix','billing'=>'View Payments');}
function tuff_beatz_project_collaborators($request_id){$c=get_post_meta((int)$request_id,'_tb_project_collaborators',true);return is_array($c)?array_values($c):array();}
function tuff_beatz_find_collaborator($request_id,$user_id){foreach(tuff_beatz_project_collaborators($request_id) as $c){if((int)($c['user_id']??0)===(int)$user_id)return $c;}return null;}
function tuff_beatz_user_project_permission($request_id,$permission='view',$user_id=0){
 $user_id=$user_id?:get_current_user_id();if(!$user_id)return false;$post=get_post((int)$request_id);if(!$post||$post->post_type!=='tb_request')return false;
 if(current_user_can('edit_post',(int)$request_id))return true;
 if((int)$post->post_author===(int)$user_id)return true;
 $c=tuff_beatz_find_collaborator($request_id,$user_id);if(!$c)return false;$perms=(array)($c['permissions']??array('view'));
 return in_array($permission,$perms,true)||($permission==='view'&&in_array('view',$perms,true));
}
function tuff_beatz_project_owner_or_admin($request_id){$p=get_post((int)$request_id);return $p&&(current_user_can('edit_post',(int)$request_id)||(int)$p->post_author===get_current_user_id());}

function tuff_beatz_invite_project_collaborator(){
 $id=(int)($_POST['request_id']??0);$redirect=tuff_beatz_project_dashboard_url($id).'#collaborators';
 if(!is_user_logged_in()||!tuff_beatz_project_owner_or_admin($id)){wp_safe_redirect(home_url('/start-a-project/'));exit;}
 if(!isset($_POST['tb_collab_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_collab_nonce'])),'tb_invite_collab_'.$id)){wp_safe_redirect($redirect);exit;}
 $email=sanitize_email(wp_unslash($_POST['collab_email']??''));$role=sanitize_key(wp_unslash($_POST['collab_role']??'other'));$name=sanitize_text_field(wp_unslash($_POST['collab_name']??''));$perms=array_map('sanitize_key',(array)($_POST['collab_permissions']??array()));
 $allowed=array_keys(tuff_beatz_collaborator_permission_labels());$perms=array_values(array_intersect($perms,$allowed));if(!in_array('view',$perms,true))$perms[]='view';
 if(!is_email($email)){wp_safe_redirect(add_query_arg('collab_error','email',$redirect));exit;}if(!array_key_exists($role,tuff_beatz_collaborator_roles()))$role='other';
 $user=get_user_by('email',$email);$created=false;
 if(!$user){$base=sanitize_user(strtolower(strtok($email,'@')),true)?:'collaborator';$login=$base;$i=1;while(username_exists($login))$login=$base.$i++;$uid=wp_insert_user(array('user_login'=>$login,'user_email'=>$email,'user_pass'=>wp_generate_password(24,true,true),'display_name'=>$name?:$email,'role'=>'tb_client'));if(is_wp_error($uid)){wp_safe_redirect(add_query_arg('collab_error','create',$redirect));exit;}$user=get_user_by('id',$uid);$created=true;}
 $list=tuff_beatz_project_collaborators($id);$found=false;foreach($list as &$c){if((int)($c['user_id']??0)===(int)$user->ID){$c['role']=$role;$c['permissions']=$perms;$c['email']=$email;$c['name']=$name?:$user->display_name;$found=true;break;}}unset($c);
 if(!$found)$list[]=array('user_id'=>(int)$user->ID,'email'=>$email,'name'=>$name?:$user->display_name,'role'=>$role,'permissions'=>$perms,'added_by'=>get_current_user_id(),'added_at'=>current_time('mysql'));
 update_post_meta($id,'_tb_project_collaborators',$list);
 $subject='TUFF BEATZ — You were added to a project';$message="You now have access to a TUFF BEATZ project workspace.\n\nProject: ".get_the_title($id)."\nOpen: ".tuff_beatz_project_dashboard_url($id)."\n";if($created){$message.="\nA TUFF BEATZ account was created for this email. Use the password reset link on the login page to set your password.";}wp_mail($email,$subject,$message);
 wp_safe_redirect(add_query_arg('collab_added','1',$redirect));exit;
}
add_action('admin_post_tb_invite_project_collaborator','tuff_beatz_invite_project_collaborator');

function tuff_beatz_remove_project_collaborator(){
 $id=(int)($_POST['request_id']??0);$uid=(int)($_POST['collab_user_id']??0);$redirect=tuff_beatz_project_dashboard_url($id).'#collaborators';
 if(!is_user_logged_in()||!tuff_beatz_project_owner_or_admin($id)){wp_safe_redirect(home_url('/start-a-project/'));exit;}
 if(!isset($_POST['tb_remove_collab_nonce'])||!wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_remove_collab_nonce'])),'tb_remove_collab_'.$id.'_'.$uid)){wp_safe_redirect($redirect);exit;}
 $list=array_values(array_filter(tuff_beatz_project_collaborators($id),function($c)use($uid){return (int)($c['user_id']??0)!==$uid;}));update_post_meta($id,'_tb_project_collaborators',$list);wp_safe_redirect(add_query_arg('collab_removed','1',$redirect));exit;
}
add_action('admin_post_tb_remove_project_collaborator','tuff_beatz_remove_project_collaborator');

function tuff_beatz_render_collaborator_workspace(){
 if(!is_page('project-dashboard')||!is_user_logged_in())return;$id=(int)($_GET['project']??0);if(!$id||!tuff_beatz_user_project_permission($id,'view'))return;
 $list=tuff_beatz_project_collaborators($id);$roles=tuff_beatz_collaborator_roles();$labels=tuff_beatz_collaborator_permission_labels();$can_manage=tuff_beatz_project_owner_or_admin($id);
 echo '<template id="tb-collaborator-template"><section id="collaborators" class="tb-portal-card tb-workspace-section tb-collab-section"><p class="tb-card-kicker">PROJECT TEAM V5</p><h2>Collaborators & Permissions</h2><p class="tb-vault-note">Keep artists, band members, managers, writers, producers, engineers, labels and other project participants inside one controlled workspace.</p>';
 if($list){echo '<div class="tb-collab-list">';foreach($list as $c){$uid=(int)($c['user_id']??0);$perms=(array)($c['permissions']??array());echo '<article class="tb-collab-card"><div><strong>'.esc_html($c['name']??$c['email']??'Collaborator').'</strong><small>'.esc_html(($roles[$c['role']??'other']??'Collaborator').' • '.($c['email']??'')).'</small><span>'.esc_html(implode(' • ',array_map(function($p)use($labels){return $labels[$p]??$p;},$perms))).'</span></div>';if($can_manage){echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'"><input type="hidden" name="action" value="tb_remove_project_collaborator"><input type="hidden" name="request_id" value="'.esc_attr($id).'"><input type="hidden" name="collab_user_id" value="'.esc_attr($uid).'">';wp_nonce_field('tb_remove_collab_'.$id.'_'.$uid,'tb_remove_collab_nonce');echo '<button type="submit">Remove</button></form>';}echo '</article>';}echo '</div>';}else echo '<p class="tb-empty-state">No collaborators have been added yet.</p>';
 if($can_manage){echo '<form method="post" action="'.esc_url(admin_url('admin-post.php')).'" class="tb-collab-form"><input type="hidden" name="action" value="tb_invite_project_collaborator"><input type="hidden" name="request_id" value="'.esc_attr($id).'">';wp_nonce_field('tb_invite_collab_'.$id,'tb_collab_nonce');echo '<div class="tb-form-row"><div class="tb-field"><label>Name</label><input name="collab_name" type="text" placeholder="Professional name"></div><div class="tb-field"><label>Email *</label><input name="collab_email" type="email" required></div></div><div class="tb-field"><label>Project Role</label><select name="collab_role">';foreach($roles as $v=>$l)echo '<option value="'.esc_attr($v).'">'.esc_html($l).'</option>';echo '</select></div><div class="tb-collab-permissions"><strong>Permissions</strong>';foreach($labels as $v=>$l){$checked=in_array($v,array('view','upload','message','review'),true)?' checked':'';echo '<label><input type="checkbox" name="collab_permissions[]" value="'.esc_attr($v).'"'.$checked.'> '.esc_html($l).'</label>';}echo '</div><button class="btn btn-gold" type="submit">Invite Collaborator</button></form>';}
 echo '</section></template><script>document.addEventListener("DOMContentLoaded",function(){var t=document.getElementById("tb-collaborator-template"),credits=document.getElementById("credits"),nav=document.querySelector(".tb-dashboard-nav");if(t&&credits)credits.parentNode.insertBefore(t.content.cloneNode(true),credits);if(nav){var a=document.createElement("a");a.href="#collaborators";a.textContent="Collaborators";nav.insertBefore(a,nav.querySelector("a[href=\"#credits\"]"));}});</script>';
}
add_action('wp_footer','tuff_beatz_render_collaborator_workspace',8);
