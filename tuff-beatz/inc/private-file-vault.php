<?php
if (!defined('ABSPATH')) exit;

/**
 * TUFF BEATZ Private File Vault V3
 * Stores unreleased client/project files outside public_html and serves them
 * only after WordPress authorization checks.
 */

function tuff_beatz_vault_root() {
    $root = trailingslashit(dirname(ABSPATH)) . 'private/tuff-beatz-vault';
    if (!is_dir($root)) wp_mkdir_p($root);
    return $root;
}

function tuff_beatz_vault_allowed_extensions() {
    return array('wav','aiff','aif','mp3','m4a','zip','pdf','txt','mid','midi');
}

function tuff_beatz_vault_files($request_id) {
    $files = get_post_meta((int)$request_id, '_tb_vault_files', true);
    return is_array($files) ? $files : array();
}

function tuff_beatz_vault_save_files($request_id, $input_name='project_files', $category='source', $version='') {
    $request_id = (int)$request_id;
    if (!$request_id || empty($_FILES[$input_name]['name'])) return array();

    $bundle = $_FILES[$input_name];
    $is_multi = is_array($bundle['name']);
    $names = $is_multi ? $bundle['name'] : array($bundle['name']);
    $types = $is_multi ? $bundle['type'] : array($bundle['type']);
    $temps = $is_multi ? $bundle['tmp_name'] : array($bundle['tmp_name']);
    $errors = $is_multi ? $bundle['error'] : array($bundle['error']);
    $sizes = $is_multi ? $bundle['size'] : array($bundle['size']);

    $allowed = tuff_beatz_vault_allowed_extensions();
    $existing = tuff_beatz_vault_files($request_id);
    $added = array();
    $dir = trailingslashit(tuff_beatz_vault_root()) . 'project-' . $request_id;
    if (!is_dir($dir)) wp_mkdir_p($dir);

    $count = min(count($names), 20);
    for ($i=0; $i<$count; $i++) {
        if (empty($names[$i]) || !empty($errors[$i]) || !is_uploaded_file($temps[$i])) continue;
        $original = sanitize_file_name($names[$i]);
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) continue;

        $id = wp_generate_uuid4();
        $stored = $id . '.' . $ext;
        $destination = trailingslashit($dir) . $stored;
        if (!@move_uploaded_file($temps[$i], $destination)) continue;
        @chmod($destination, 0640);

        $record = array(
            'id' => $id,
            'name' => $original,
            'stored' => $stored,
            'size' => (int)$sizes[$i],
            'mime' => sanitize_mime_type($types[$i] ?: 'application/octet-stream'),
            'category' => sanitize_key($category),
            'version' => sanitize_text_field($version),
            'uploaded_by' => get_current_user_id(),
            'uploaded_at' => current_time('mysql'),
            'sha256' => @hash_file('sha256', $destination) ?: '',
        );
        $existing[] = $record;
        $added[] = $record;
    }
    if ($added) update_post_meta($request_id, '_tb_vault_files', $existing);
    return $added;
}

function tuff_beatz_vault_download_url($request_id, $file_id) {
    $args = array(
        'action' => 'tb_vault_download',
        'request_id' => (int)$request_id,
        'file_id' => sanitize_text_field($file_id),
    );
    $url = add_query_arg($args, admin_url('admin-post.php'));
    return wp_nonce_url($url, 'tb_vault_download_' . (int)$request_id . '_' . sanitize_text_field($file_id), 'tb_vault_nonce');
}

function tuff_beatz_vault_find($request_id, $file_id) {
    foreach (tuff_beatz_vault_files($request_id) as $file) {
        if (!empty($file['id']) && hash_equals((string)$file['id'], (string)$file_id)) return $file;
    }
    return null;
}

function tuff_beatz_vault_download() {
    $request_id = (int)($_GET['request_id'] ?? 0);
    $file_id = sanitize_text_field(wp_unslash($_GET['file_id'] ?? ''));
    $nonce = sanitize_text_field(wp_unslash($_GET['tb_vault_nonce'] ?? ''));

    if (!is_user_logged_in() || !function_exists('tuff_beatz_user_can_view_request') || !tuff_beatz_user_can_view_request($request_id)) {
        status_header(403); exit('Access denied.');
    }
    if (!$file_id || !wp_verify_nonce($nonce, 'tb_vault_download_' . $request_id . '_' . $file_id)) {
        status_header(403); exit('Invalid download link.');
    }

    $file = tuff_beatz_vault_find($request_id, $file_id);
    if (!$file) { status_header(404); exit('File not found.'); }

    $path = trailingslashit(tuff_beatz_vault_root()) . 'project-' . $request_id . '/' . basename($file['stored']);
    if (!is_file($path) || !is_readable($path)) { status_header(404); exit('File unavailable.'); }

    nocache_headers();
    header('X-Content-Type-Options: nosniff');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . rawurlencode($file['name']) . '"; filename*=UTF-8\'\'' . rawurlencode($file['name']));
    header('Content-Length: ' . filesize($path));
    while (ob_get_level()) ob_end_clean();
    readfile($path);
    exit;
}
add_action('admin_post_tb_vault_download', 'tuff_beatz_vault_download');

function tuff_beatz_vault_human_size($bytes) {
    return size_format((int)$bytes, 1);
}
