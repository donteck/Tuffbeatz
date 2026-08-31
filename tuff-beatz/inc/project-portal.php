<?php
if (!defined('ABSPATH')) exit;

/**
 * TUFF BEATZ Client / Artist Project Portal
 * Login-gated intake, private request management, account registration, and admin review.
 */

function tuff_beatz_portal_roles() {
    if (!get_role('tb_client')) {
        add_role('tb_client', __('TUFF BEATZ Client', 'tuff-beatz'), array('read' => true));
    }
    if (!get_role('tb_artist')) {
        add_role('tb_artist', __('TUFF BEATZ Artist', 'tuff-beatz'), array('read' => true));
    }
}
add_action('init', 'tuff_beatz_portal_roles', 5);

function tuff_beatz_request_cpt() {
    register_post_type('tb_request', array(
        'labels' => array(
            'name' => __('Project Requests', 'tuff-beatz'),
            'singular_name' => __('Project Request', 'tuff-beatz'),
            'menu_name' => __('Project Requests', 'tuff-beatz'),
            'add_new_item' => __('Add Project Request', 'tuff-beatz'),
            'edit_item' => __('Review Project Request', 'tuff-beatz'),
        ),
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'show_in_rest' => false,
        'menu_icon' => 'dashicons-clipboard',
        'supports' => array('title', 'author'),
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ));
}
add_action('init', 'tuff_beatz_request_cpt', 8);

function tuff_beatz_ensure_portal_page() {
    if (get_option('tuff_beatz_portal_page_ready')) return;

    $page = get_page_by_path('start-a-project');
    if (!$page) {
        $page_id = wp_insert_post(array(
            'post_title' => 'Start a Project',
            'post_name' => 'start-a-project',
            'post_status' => 'publish',
            'post_type' => 'page',
            'post_content' => '',
        ));
        if (!is_wp_error($page_id) && $page_id) {
            update_post_meta($page_id, '_wp_page_template', 'page-start-project.php');
            update_option('tuff_beatz_portal_page_ready', 1, false);
        }
    } else {
        update_post_meta($page->ID, '_wp_page_template', 'page-start-project.php');
        update_option('tuff_beatz_portal_page_ready', 1, false);
    }
}
add_action('init', 'tuff_beatz_ensure_portal_page', 30);

function tuff_beatz_portal_assets() {
    if (is_page('start-a-project')) {
        $path = get_template_directory() . '/assets/css/project-portal.css';
        wp_enqueue_style(
            'tuff-beatz-project-portal',
            get_template_directory_uri() . '/assets/css/project-portal.css',
            array('tuff-beatz-main'),
            file_exists($path) ? filemtime($path) : wp_get_theme()->get('Version')
        );
    }
}
add_action('wp_enqueue_scripts', 'tuff_beatz_portal_assets', 40);

function tuff_beatz_register_portal_account() {
    $redirect = home_url('/start-a-project/');

    if (!isset($_POST['tb_register_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_register_nonce'])), 'tb_register_account')) {
        wp_safe_redirect(add_query_arg('portal_error', 'security', $redirect));
        exit;
    }

    if (!empty($_POST['company_website'])) {
        wp_safe_redirect($redirect);
        exit;
    }

    $name = sanitize_text_field(wp_unslash($_POST['display_name'] ?? ''));
    $email = sanitize_email(wp_unslash($_POST['email'] ?? ''));
    $password = (string) wp_unslash($_POST['password'] ?? '');
    $account_type = sanitize_key(wp_unslash($_POST['account_type'] ?? 'tb_artist'));
    $role = in_array($account_type, array('tb_artist', 'tb_client'), true) ? $account_type : 'tb_artist';

    if (!$name || !is_email($email) || strlen($password) < 10) {
        wp_safe_redirect(add_query_arg('portal_error', 'registration_fields', $redirect));
        exit;
    }
    if (email_exists($email)) {
        wp_safe_redirect(add_query_arg('portal_error', 'email_exists', $redirect));
        exit;
    }

    $base = sanitize_user(strtolower(strtok($email, '@')), true);
    if (!$base) $base = 'artist';
    $username = $base;
    $i = 1;
    while (username_exists($username)) {
        $username = $base . $i;
        $i++;
    }

    $user_id = wp_insert_user(array(
        'user_login' => $username,
        'user_email' => $email,
        'user_pass' => $password,
        'display_name' => $name,
        'role' => $role,
    ));

    if (is_wp_error($user_id)) {
        wp_safe_redirect(add_query_arg('portal_error', 'registration_failed', $redirect));
        exit;
    }

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id, true);
    wp_safe_redirect(add_query_arg('welcome', '1', $redirect));
    exit;
}
add_action('admin_post_nopriv_tb_register_portal_account', 'tuff_beatz_register_portal_account');

function tuff_beatz_submit_project_request() {
    $redirect = home_url('/start-a-project/');

    if (!is_user_logged_in()) {
        wp_safe_redirect(wp_login_url($redirect));
        exit;
    }

    if (!isset($_POST['tb_project_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_project_nonce'])), 'tb_submit_project')) {
        wp_safe_redirect(add_query_arg('portal_error', 'security', $redirect));
        exit;
    }

    $user = wp_get_current_user();
    $artist_name = sanitize_text_field(wp_unslash($_POST['artist_name'] ?? ''));
    $project_title = sanitize_text_field(wp_unslash($_POST['project_title'] ?? ''));
    $service = sanitize_text_field(wp_unslash($_POST['service'] ?? ''));
    $project_type = sanitize_text_field(wp_unslash($_POST['project_type'] ?? ''));
    $genre = sanitize_text_field(wp_unslash($_POST['genre'] ?? ''));
    $budget = sanitize_text_field(wp_unslash($_POST['budget'] ?? ''));
    $target_date = sanitize_text_field(wp_unslash($_POST['target_date'] ?? ''));
    $reference_url = esc_url_raw(wp_unslash($_POST['reference_url'] ?? ''));
    $details = sanitize_textarea_field(wp_unslash($_POST['details'] ?? ''));

    if (!$artist_name || !$project_title || !$service || !$details) {
        wp_safe_redirect(add_query_arg('portal_error', 'required_fields', $redirect));
        exit;
    }

    $request_id = wp_insert_post(array(
        'post_type' => 'tb_request',
        'post_status' => 'publish',
        'post_title' => sprintf('%s — %s', $artist_name, $project_title),
        'post_author' => $user->ID,
    ));

    if (is_wp_error($request_id) || !$request_id) {
        wp_safe_redirect(add_query_arg('portal_error', 'submit_failed', $redirect));
        exit;
    }

    $meta = array(
        '_tb_request_artist' => $artist_name,
        '_tb_request_service' => $service,
        '_tb_request_project_type' => $project_type,
        '_tb_request_genre' => $genre,
        '_tb_request_budget' => $budget,
        '_tb_request_target_date' => $target_date,
        '_tb_request_reference_url' => $reference_url,
        '_tb_request_details' => $details,
        '_tb_request_status' => 'new',
        '_tb_request_email' => $user->user_email,
    );
    foreach ($meta as $key => $value) update_post_meta($request_id, $key, $value);

    if (!empty($_FILES['project_file']['name'])) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $allowed = array('mp3', 'wav', 'm4a', 'zip', 'pdf');
        $ext = strtolower(pathinfo($_FILES['project_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed, true)) {
            $attachment_id = media_handle_upload('project_file', $request_id);
            if (!is_wp_error($attachment_id)) {
                update_post_meta($request_id, '_tb_request_attachment', (int) $attachment_id);
            }
        }
    }

    $admin_email = tuff_beatz_get('contact_email', get_option('admin_email'));
    $subject = sprintf('[TUFF BEATZ] New Project Request — %s', $project_title);
    $message = "A new project request has been submitted.\n\n";
    $message .= "Artist/Client: {$artist_name}\n";
    $message .= "Email: {$user->user_email}\n";
    $message .= "Service: {$service}\n";
    $message .= "Project: {$project_title}\n";
    $message .= "Budget: {$budget}\n";
    $message .= "\nReview it in WordPress → Project Requests.";
    wp_mail($admin_email, $subject, $message);

    wp_safe_redirect(add_query_arg(array('submitted' => '1', 'request' => $request_id), $redirect));
    exit;
}
add_action('admin_post_tb_submit_project_request', 'tuff_beatz_submit_project_request');

function tuff_beatz_request_admin_box($post) {
    wp_nonce_field('tb_request_admin_save', 'tb_request_admin_nonce');
    $fields = array(
        'Artist / Client' => get_post_meta($post->ID, '_tb_request_artist', true),
        'Email' => get_post_meta($post->ID, '_tb_request_email', true),
        'Service' => get_post_meta($post->ID, '_tb_request_service', true),
        'Project Type' => get_post_meta($post->ID, '_tb_request_project_type', true),
        'Genre' => get_post_meta($post->ID, '_tb_request_genre', true),
        'Budget' => get_post_meta($post->ID, '_tb_request_budget', true),
        'Target Date' => get_post_meta($post->ID, '_tb_request_target_date', true),
    );
    echo '<div style="display:grid;gap:10px">';
    foreach ($fields as $label => $value) {
        echo '<div><strong>' . esc_html($label) . ':</strong> ' . esc_html($value ?: '—') . '</div>';
    }
    $ref = get_post_meta($post->ID, '_tb_request_reference_url', true);
    if ($ref) echo '<div><strong>Reference:</strong> <a href="' . esc_url($ref) . '" target="_blank" rel="noopener">' . esc_html($ref) . '</a></div>';
    $attachment = (int) get_post_meta($post->ID, '_tb_request_attachment', true);
    if ($attachment) echo '<div><strong>Uploaded file:</strong> <a href="' . esc_url(wp_get_attachment_url($attachment)) . '" target="_blank" rel="noopener">Open file</a></div>';
    echo '<div><strong>Project details:</strong><p style="white-space:pre-wrap">' . esc_html(get_post_meta($post->ID, '_tb_request_details', true)) . '</p></div>';
    echo '<label><strong>Status</strong><br><select name="tb_request_status">';
    $current = get_post_meta($post->ID, '_tb_request_status', true) ?: 'new';
    foreach (array('new'=>'New','reviewing'=>'Reviewing','approved'=>'Approved','in-progress'=>'In Progress','completed'=>'Completed','declined'=>'Declined') as $value => $label) {
        echo '<option value="' . esc_attr($value) . '" ' . selected($current, $value, false) . '>' . esc_html($label) . '</option>';
    }
    echo '</select></label></div>';
}

function tuff_beatz_request_admin_metaboxes() {
    add_meta_box('tb_request_details', __('Project Request Details', 'tuff-beatz'), 'tuff_beatz_request_admin_box', 'tb_request', 'normal', 'high');
}
add_action('add_meta_boxes_tb_request', 'tuff_beatz_request_admin_metaboxes');

function tuff_beatz_save_request_admin($post_id) {
    if (!isset($_POST['tb_request_admin_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['tb_request_admin_nonce'])), 'tb_request_admin_save')) return;
    if (!current_user_can('edit_post', $post_id)) return;
    if (isset($_POST['tb_request_status'])) {
        $status = sanitize_key(wp_unslash($_POST['tb_request_status']));
        $allowed = array('new','reviewing','approved','in-progress','completed','declined');
        if (in_array($status, $allowed, true)) update_post_meta($post_id, '_tb_request_status', $status);
    }
}
add_action('save_post_tb_request', 'tuff_beatz_save_request_admin');

function tuff_beatz_request_status_label($status) {
    $labels = array(
        'new' => 'New',
        'reviewing' => 'Reviewing',
        'approved' => 'Approved',
        'in-progress' => 'In Progress',
        'completed' => 'Completed',
        'declined' => 'Declined',
    );
    return $labels[$status] ?? 'New';
}
