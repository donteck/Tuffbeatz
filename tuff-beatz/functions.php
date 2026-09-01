<?php
if (!defined('ABSPATH')) exit;

function tuff_beatz_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form','comment-form','comment-list','gallery','caption','style','script'));
    register_nav_menus(array('primary' => __('Primary Menu', 'tuff-beatz')));
}
add_action('after_setup_theme', 'tuff_beatz_setup');

function tuff_beatz_assets() {
    $ver = wp_get_theme()->get('Version');
    $css_path = get_template_directory() . '/assets/css/main.css';
    $restore_path = get_template_directory() . '/assets/css/v34-frontend-restore.css';
    $parity_path = get_template_directory() . '/assets/css/v34-reference-parity.css';
    $js_path = get_template_directory() . '/assets/js/main.js';
    $css_ver = $ver . '.' . (file_exists($css_path) ? filemtime($css_path) : time());
    $restore_ver = $ver . '.' . (file_exists($restore_path) ? filemtime($restore_path) : time());
    $parity_ver = $ver . '.' . (file_exists($parity_path) ? filemtime($parity_path) : time());
    $js_ver = $ver . '.' . (file_exists($js_path) ? filemtime($js_path) : time());
    wp_enqueue_style('tuff-beatz-fonts', 'https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600;700&family=Inter:wght@300;400;500;600;700&family=Parisienne&display=swap', array(), null);
    wp_enqueue_style('tuff-beatz-main', get_template_directory_uri() . '/assets/css/main.css', array('tuff-beatz-fonts'), $css_ver);
    wp_enqueue_style('tuff-beatz-v34-restore', get_template_directory_uri() . '/assets/css/v34-frontend-restore.css', array('tuff-beatz-main'), $restore_ver);
    wp_enqueue_style('tuff-beatz-v34-parity', get_template_directory_uri() . '/assets/css/v34-reference-parity.css', array('tuff-beatz-v34-restore'), $parity_ver);
    wp_enqueue_script('tuff-beatz-main', get_template_directory_uri() . '/assets/js/main.js', array(), $js_ver, true);
}
add_action('wp_enqueue_scripts', 'tuff_beatz_assets');

function tuff_beatz_customize_register($wp_customize) {
    $wp_customize->add_section('tuff_beatz_links', array('title' => __('TUFF BEATZ Links', 'tuff-beatz'),'priority' => 30,'description' => __('Set the links used by homepage buttons and platform icons.', 'tuff-beatz')));
    $fields = array(
        'showreel_url' => array('Showreel URL', '#music'),'contact_email' => array('Contact Email', get_option('admin_email')),'spotify_url' => array('Spotify URL', '#music'),'apple_music_url' => array('Apple Music URL', '#music'),'youtube_url' => array('YouTube URL', '#music'),'tidal_url' => array('TIDAL URL', '#music'),'deezer_url' => array('Deezer URL', '#music'),'audiomack_url' => array('Audiomack URL', '#music'),'instagram_url' => array('Instagram URL', '#contact'),'facebook_url' => array('Facebook URL', '#contact'),'tiktok_url' => array('TikTok URL', '#contact'),
    );
    foreach ($fields as $key => $data) {
        $wp_customize->add_setting("tuff_beatz_$key", array('default' => $data[1],'sanitize_callback' => ($key === 'contact_email') ? 'sanitize_email' : 'esc_url_raw'));
        $wp_customize->add_control("tuff_beatz_$key", array('label' => __($data[0], 'tuff-beatz'),'section' => 'tuff_beatz_links','type' => ($key === 'contact_email') ? 'email' : 'url'));
    }
}
add_action('customize_register', 'tuff_beatz_customize_register');
function tuff_beatz_get($key, $default = '') {return get_theme_mod('tuff_beatz_' . $key, $default);}

function tuff_beatz_projects_cpt() {
    register_post_type('tb_project', array('labels' => array('name' => __('Projects','tuff-beatz'),'singular_name' => __('Project','tuff-beatz'),'add_new_item' => __('Add New Project','tuff-beatz'),'edit_item' => __('Edit Project','tuff-beatz')),'public' => true,'show_in_rest' => true,'menu_icon' => 'dashicons-album','supports' => array('title','editor','thumbnail','excerpt'),'has_archive' => true,'rewrite' => array('slug' => 'music')));
}
add_action('init', 'tuff_beatz_projects_cpt');

function tuff_beatz_project_metaboxes() {add_meta_box('tuff_beatz_project_audio',__('TUFF BEATZ — Audio & Links', 'tuff-beatz'),'tuff_beatz_project_audio_box','tb_project','normal','high');}
add_action('add_meta_boxes', 'tuff_beatz_project_metaboxes');
function tuff_beatz_project_audio_box($post) {
    wp_nonce_field('tuff_beatz_save_project_audio', 'tuff_beatz_project_audio_nonce');
    $audio = get_post_meta($post->ID, '_tb_audio_url', true);$artist = get_post_meta($post->ID, '_tb_artist_name', true);$stream = get_post_meta($post->ID, '_tb_stream_url', true);$buy = get_post_meta($post->ID, '_tb_buy_url', true);?>
    <style>.tb-admin-field{margin:0 0 16px}.tb-admin-field label{display:block;font-weight:700;margin-bottom:5px}.tb-admin-field input{width:100%}</style>
    <div class="tb-admin-field"><label for="tb_artist_name">Artist name</label><input id="tb_artist_name" name="tb_artist_name" value="<?php echo esc_attr($artist); ?>" placeholder="Sean Davz"></div>
    <div class="tb-admin-field"><label for="tb_audio_url">Audio preview / MP3 URL</label><input id="tb_audio_url" name="tb_audio_url" value="<?php echo esc_attr($audio); ?>" placeholder="https://.../song.mp3"><p class="description">Upload an MP3 to Media Library, then paste its file URL here.</p></div>
    <div class="tb-admin-field"><label for="tb_stream_url">Listen / Smart Link</label><input id="tb_stream_url" name="tb_stream_url" value="<?php echo esc_attr($stream); ?>" placeholder="https://open.spotify.com/..."></div>
    <div class="tb-admin-field"><label for="tb_buy_url">Buy / License Link (optional)</label><input id="tb_buy_url" name="tb_buy_url" value="<?php echo esc_attr($buy); ?>" placeholder="https://..."></div>
    <div class="tb-admin-field"><label for="tb_genre">Genre</label><input id="tb_genre" name="tb_genre" value="<?php echo esc_attr(get_post_meta($post->ID, '_tb_genre', true)); ?>" placeholder="Kompa / Afrobeats / R&B"></div>
    <div class="tb-admin-field"><label for="tb_bpm">BPM</label><input id="tb_bpm" name="tb_bpm" value="<?php echo esc_attr(get_post_meta($post->ID, '_tb_bpm', true)); ?>" placeholder="104"></div>
    <div class="tb-admin-field"><label for="tb_key">Key</label><input id="tb_key" name="tb_key" value="<?php echo esc_attr(get_post_meta($post->ID, '_tb_key', true)); ?>" placeholder="F# Minor"></div><?php
}
function tuff_beatz_save_project_audio($post_id) {
    if (!isset($_POST['tuff_beatz_project_audio_nonce']) || !wp_verify_nonce($_POST['tuff_beatz_project_audio_nonce'], 'tuff_beatz_save_project_audio')) return;if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;if (!current_user_can('edit_post', $post_id)) return;
    $map = array('tb_audio_url'=>'_tb_audio_url','tb_artist_name'=>'_tb_artist_name','tb_stream_url'=>'_tb_stream_url','tb_buy_url'=>'_tb_buy_url','tb_genre'=>'_tb_genre','tb_bpm'=>'_tb_bpm','tb_key'=>'_tb_key');
    foreach ($map as $field => $meta) if (isset($_POST[$field])) {$value = in_array($field, array('tb_artist_name','tb_genre','tb_bpm','tb_key'), true) ? sanitize_text_field($_POST[$field]) : esc_url_raw($_POST[$field]);update_post_meta($post_id, $meta, $value);}
}
add_action('save_post_tb_project', 'tuff_beatz_save_project_audio');
function tuff_beatz_player_tracks() {$tracks = array();$q = new WP_Query(array('post_type'=>'tb_project','posts_per_page'=>30,'post_status'=>'publish','meta_query'=>array(array('key'=>'_tb_audio_url','compare'=>'EXISTS'))));while ($q->have_posts()) {$q->the_post();$audio = get_post_meta(get_the_ID(), '_tb_audio_url', true);if (!$audio) continue;$tracks[] = array('id'=>get_the_ID(),'title'=>get_the_title(),'artist'=>get_post_meta(get_the_ID(), '_tb_artist_name', true) ?: 'TUFF BEATZ','audio'=>esc_url_raw($audio),'cover'=>get_the_post_thumbnail_url(get_the_ID(), 'medium') ?: get_template_directory_uri() . '/assets/images/tuff-beatz-logo.png','url'=>get_permalink(),'stream'=>get_post_meta(get_the_ID(), '_tb_stream_url', true),'buy'=>get_post_meta(get_the_ID(), '_tb_buy_url', true),'genre'=>get_post_meta(get_the_ID(), '_tb_genre', true),'bpm'=>get_post_meta(get_the_ID(), '_tb_bpm', true),'key'=>get_post_meta(get_the_ID(), '_tb_key', true));}wp_reset_postdata();return $tracks;}
function tuff_beatz_player_data() {wp_localize_script('tuff-beatz-main', 'TUFF_BEATZ_PLAYER', array('tracks'=>tuff_beatz_player_tracks(),'logo'=>get_template_directory_uri() . '/assets/images/tuff-beatz-logo.png'));}
add_action('wp_enqueue_scripts', 'tuff_beatz_player_data', 30);
function tuff_beatz_body_player_class($classes) { $classes[] = 'tb-has-player'; return $classes; }
add_filter('body_class', 'tuff_beatz_body_player_class');

require_once get_template_directory() . '/inc/private-file-vault.php';
require_once get_template_directory() . '/inc/canonical-asset-bridge.php';
require_once get_template_directory() . '/inc/project-portal.php';
require_once get_template_directory() . '/inc/project-dashboard.php';
require_once get_template_directory() . '/inc/final-delivery-guard.php';
require_once get_template_directory() . '/inc/delivery-engine.php';
require_once get_template_directory() . '/inc/project-invoicing.php';
require_once get_template_directory() . '/inc/portal-hubs.php';
require_once get_template_directory() . '/inc/project-notifications.php';
require_once get_template_directory() . '/inc/client-crm.php';
require_once get_template_directory() . '/inc/project-file-manager.php';
require_once get_template_directory() . '/inc/project-conversation.php';
require_once get_template_directory() . '/inc/workflow-automation.php';
require_once get_template_directory() . '/inc/producer-audio-console.php';
require_once get_template_directory() . '/inc/activity-intelligence.php';
require_once get_template_directory() . '/inc/production-hardening.php';
require_once get_template_directory() . '/inc/permission-hardening.php';
require_once get_template_directory() . '/inc/data-integrity.php';
require_once get_template_directory() . '/inc/event-deduplication.php';
require_once get_template_directory() . '/inc/runtime-health.php';
require_once get_template_directory() . '/inc/release-candidate.php';
require_once get_template_directory() . '/inc/business-opportunities.php';
require_once get_template_directory() . '/inc/business-command.php';
require_once get_template_directory() . '/inc/business-proposals.php';
require_once get_template_directory() . '/inc/business-contracts.php';
require_once get_template_directory() . '/inc/business-conversion.php';
require_once get_template_directory() . '/inc/business-automation.php';
require_once get_template_directory() . '/inc/business-forecasting.php';
function tuff_beatz_v14_proposal_assets(){if(is_page('proposal')){$p=get_template_directory().'/assets/css/business-proposal.css';wp_enqueue_style('tuff-beatz-business-proposal',get_template_directory_uri().'/assets/css/business-proposal.css',array('tuff-beatz-v34-parity'),file_exists($p)?filemtime($p):wp_get_theme()->get('Version'));}}
add_action('wp_enqueue_scripts','tuff_beatz_v14_proposal_assets',70);
function tuff_beatz_v145_contract_assets(){if(is_page('contract')){$p=get_template_directory().'/assets/css/business-contract.css';wp_enqueue_style('tuff-beatz-business-contract',get_template_directory_uri().'/assets/css/business-contract.css',array('tuff-beatz-v34-parity'),file_exists($p)?filemtime($p):wp_get_theme()->get('Version'));}}
add_action('wp_enqueue_scripts','tuff_beatz_v145_contract_assets',71);