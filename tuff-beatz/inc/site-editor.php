<?php
/**
 * TUFF BEATZ Site Editor — Phase 1
 * Safe, module-driven editor foundation. Presentation/content only.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_defaults(){
    return array(
        'hero_eyebrow'=>'THE PRODUCER IDENTITY OF',
        'hero_name'=>'EMMANUEL TUFFET',
        'hero_title'=>'SOUND. PURPOSE. LEGACY.',
        'hero_lead'=>'TUFF BEATZ is where music, technology and creativity come together to create timeless records that move people, inspire generations and leave a legacy.',
        'hero_primary_label'=>'Play Showreel',
        'hero_secondary_label'=>'Work With Me',
        'hero_secondary_url'=>'#contact',
        'show_about'=>1,'show_services'=>1,'show_featured'=>1,'show_music'=>1,'show_platforms'=>1,
        'about_eyebrow'=>'ABOUT','about_title'=>'MORE THAN BEATS. I BUILD EMOTIONS.',
        'services_eyebrow'=>'WHAT I DO','services_title'=>'PROFESSIONAL MUSIC PRODUCTION',
        'music_eyebrow'=>'MUSIC THAT SPEAKS','music_title'=>'RECENT WORK',
        'footer_note'=>'SOUND. PURPOSE. LEGACY.'
    );
}
function tuff_beatz_editor_settings(){return wp_parse_args((array)get_option('tuff_beatz_site_editor',array()),tuff_beatz_editor_defaults());}
function tuff_beatz_editor_get($key,$fallback=''){$s=tuff_beatz_editor_settings();return array_key_exists($key,$s)?$s[$key]:$fallback;}
function tuff_beatz_editor_enabled($key){return !empty(tuff_beatz_editor_get($key,1));}

function tuff_beatz_site_editor_menu(){
    add_menu_page('TUFF BEATZ Site Editor','TUFF BEATZ Editor','manage_options','tuff-beatz-site-editor','tuff_beatz_site_editor_page','dashicons-admin-customizer',3);
}
add_action('admin_menu','tuff_beatz_site_editor_menu');

function tuff_beatz_site_editor_sanitize($raw){
    $d=tuff_beatz_editor_defaults();$out=array();
    foreach($d as $k=>$default){
        if(strpos($k,'show_')===0){$out[$k]=empty($raw[$k])?0:1;continue;}
        if(substr($k,-4)==='_url'){$out[$k]=isset($raw[$k])?esc_url_raw($raw[$k]):$default;continue;}
        $out[$k]=isset($raw[$k])?sanitize_textarea_field($raw[$k]):$default;
    }
    return $out;
}
function tuff_beatz_site_editor_save(){
    if(!current_user_can('manage_options'))wp_die('Unauthorized');
    check_admin_referer('tuff_beatz_site_editor_save');
    if(isset($_POST['tb_reset'])){delete_option('tuff_beatz_site_editor');wp_safe_redirect(admin_url('admin.php?page=tuff-beatz-site-editor&reset=1'));exit;}
    $raw=isset($_POST['tb_editor'])?(array)wp_unslash($_POST['tb_editor']):array();
    update_option('tuff_beatz_site_editor',tuff_beatz_site_editor_sanitize($raw),false);
    wp_safe_redirect(admin_url('admin.php?page=tuff-beatz-site-editor&saved=1'));exit;
}
add_action('admin_post_tuff_beatz_site_editor_save','tuff_beatz_site_editor_save');

function tuff_beatz_site_editor_page(){
    if(!current_user_can('manage_options'))return;$s=tuff_beatz_editor_settings();
    $checks=array('show_about'=>'About','show_services'=>'Services','show_featured'=>'Featured Experience','show_music'=>'Recent Work','show_platforms'=>'Platforms');
    ?>
    <div class="wrap tbse-wrap"><div class="tbse-head"><div><p>TUFF BEATZ • SITE SYSTEM</p><h1>Site Editor <span>Phase 1</span></h1><small>Edit the public experience without touching production, permissions, vault, payments or verification logic.</small></div><a class="button button-secondary" target="_blank" href="<?php echo esc_url(home_url('/')); ?>">Open Live Site ↗</a></div>
    <?php if(isset($_GET['saved'])):?><div class="notice notice-success is-dismissible"><p>Site Editor settings saved.</p></div><?php endif;?>
    <?php if(isset($_GET['reset'])):?><div class="notice notice-warning is-dismissible"><p>Public editor settings restored to the V3.4 baseline values.</p></div><?php endif;?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="tuff_beatz_site_editor_save"><?php wp_nonce_field('tuff_beatz_site_editor_save'); ?>
    <div class="tbse-grid">
      <section class="tbse-card"><p class="tbse-kicker">GLOBAL</p><h2>Brand Foundation</h2><label>Footer / Brand Note<input name="tb_editor[footer_note]" value="<?php echo esc_attr($s['footer_note']); ?>"></label><p class="description">Phase 1 keeps the approved V3.4 colors, typography and core CSS protected.</p></section>
      <section class="tbse-card tbse-wide"><p class="tbse-kicker">HOMEPAGE</p><h2>Hero</h2><div class="tbse-fields"><label>Eyebrow<input name="tb_editor[hero_eyebrow]" value="<?php echo esc_attr($s['hero_eyebrow']); ?>"></label><label>Producer Name<input name="tb_editor[hero_name]" value="<?php echo esc_attr($s['hero_name']); ?>"></label><label class="wide">Headline<input name="tb_editor[hero_title]" value="<?php echo esc_attr($s['hero_title']); ?>"></label><label class="wide">Lead<textarea name="tb_editor[hero_lead]" rows="4"><?php echo esc_textarea($s['hero_lead']); ?></textarea></label><label>Primary Button<input name="tb_editor[hero_primary_label]" value="<?php echo esc_attr($s['hero_primary_label']); ?>"></label><label>Secondary Button<input name="tb_editor[hero_secondary_label]" value="<?php echo esc_attr($s['hero_secondary_label']); ?>"></label><label class="wide">Secondary Button URL<input name="tb_editor[hero_secondary_url]" value="<?php echo esc_attr($s['hero_secondary_url']); ?>"></label></div></section>
      <section class="tbse-card"><p class="tbse-kicker">SECTIONS</p><h2>Visibility</h2><?php foreach($checks as $k=>$label):?><label class="tbse-toggle"><input type="checkbox" name="tb_editor[<?php echo esc_attr($k); ?>]" value="1" <?php checked(!empty($s[$k]));?>><span><?php echo esc_html($label); ?></span></label><?php endforeach;?><p class="description">Hide/show presentation only. Content and project data are never deleted.</p></section>
      <section class="tbse-card"><p class="tbse-kicker">CONTENT</p><h2>Section Headlines</h2><label>About Eyebrow<input name="tb_editor[about_eyebrow]" value="<?php echo esc_attr($s['about_eyebrow']); ?>"></label><label>About Headline<input name="tb_editor[about_title]" value="<?php echo esc_attr($s['about_title']); ?>"></label><label>Services Eyebrow<input name="tb_editor[services_eyebrow]" value="<?php echo esc_attr($s['services_eyebrow']); ?>"></label><label>Services Headline<input name="tb_editor[services_title]" value="<?php echo esc_attr($s['services_title']); ?>"></label><label>Music Eyebrow<input name="tb_editor[music_eyebrow]" value="<?php echo esc_attr($s['music_eyebrow']); ?>"></label><label>Music Headline<input name="tb_editor[music_title]" value="<?php echo esc_attr($s['music_title']); ?>"></label></section>
      <section class="tbse-card tbse-wide"><p class="tbse-kicker">SAFETY</p><h2>Protected Architecture</h2><div class="tbse-protect"><span>✓ Private File Vault</span><span>✓ Producer / Client Permissions</span><span>✓ Payments & Delivery Guards</span><span>✓ V16 Verification</span><span>✓ V3.4 main.css baseline</span></div><p class="description">The Site Editor is deliberately presentation-scoped. System logic remains code-controlled.</p></section>
    </div>
    <div class="tbse-actions"><button class="button button-primary button-hero" type="submit">Save Site Settings</button><button class="button" type="submit" name="tb_reset" value="1" onclick="return confirm('Restore Phase 1 editor values to the approved V3.4 baseline?');">Reset to V3.4 Baseline</button></div></form></div>
    <style>.tbse-wrap{max-width:1240px;margin-top:28px}.tbse-head{display:flex;justify-content:space-between;gap:24px;align-items:center;padding:26px 28px;background:#111;border:1px solid #34302a;border-radius:16px;color:#eee;margin-bottom:18px}.tbse-head p,.tbse-kicker{color:#c9a45b;font-weight:800;letter-spacing:.14em;font-size:11px;margin:0 0 7px}.tbse-head h1{color:#fff;margin:0 0 7px;font-size:30px}.tbse-head h1 span{color:#c9a45b;font-size:14px}.tbse-head small{color:#aaa}.tbse-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}.tbse-card{background:#fff;border:1px solid #ddd;border-radius:14px;padding:22px}.tbse-wide{grid-column:1/-1}.tbse-card h2{margin-top:0}.tbse-card label{display:block;font-weight:700;margin:0 0 14px}.tbse-card input[type=text],.tbse-card input:not([type]),.tbse-card textarea{display:block;width:100%;max-width:none;margin-top:6px}.tbse-fields{display:grid;grid-template-columns:1fr 1fr;gap:0 16px}.tbse-fields .wide{grid-column:1/-1}.tbse-toggle{display:flex!important;align-items:center;gap:9px;padding:10px 0;border-bottom:1px solid #eee}.tbse-toggle input{margin:0}.tbse-protect{display:flex;flex-wrap:wrap;gap:9px}.tbse-protect span{background:#f5f1e8;border:1px solid #e2d3b2;padding:8px 11px;border-radius:999px;font-weight:700}.tbse-actions{position:sticky;bottom:0;background:rgba(240,240,241,.96);padding:18px 0;display:flex;gap:10px;z-index:5}@media(max-width:782px){.tbse-grid,.tbse-fields{grid-template-columns:1fr}.tbse-wide,.tbse-fields .wide{grid-column:auto}.tbse-head{align-items:flex-start;flex-direction:column}}</style>
    <?php
}
