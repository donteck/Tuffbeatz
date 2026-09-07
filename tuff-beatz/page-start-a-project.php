<?php
/**
 * TUFF BEATZ — Start a Project V1.0
 * Public intake page. Stores qualified submissions as draft tb_project records
 * for producer review without touching protected Studio OS permissions.
 */
if (!defined('ABSPATH')) exit;

wp_enqueue_style(
    'tuff-beatz-start-project',
    get_template_directory_uri() . '/assets/css/start-a-project.css',
    array(),
    '1.0.0'
);

$tbsp_errors = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tbsp_submit'])) {
    $nonce = isset($_POST['tbsp_nonce']) ? sanitize_text_field(wp_unslash($_POST['tbsp_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'tbsp_submit_project')) {
        $tbsp_errors[] = 'Your session expired. Please refresh the page and try again.';
    }

    // Honeypot. Real visitors never fill this field.
    $website = isset($_POST['website']) ? trim((string) wp_unslash($_POST['website'])) : '';
    if ($website !== '') {
        $tbsp_errors[] = 'Unable to submit this request.';
    }

    $name        = isset($_POST['name']) ? sanitize_text_field(wp_unslash($_POST['name'])) : '';
    $artist_name = isset($_POST['artist_name']) ? sanitize_text_field(wp_unslash($_POST['artist_name'])) : '';
    $email       = isset($_POST['email']) ? sanitize_email(wp_unslash($_POST['email'])) : '';
    $phone       = isset($_POST['phone']) ? sanitize_text_field(wp_unslash($_POST['phone'])) : '';
    $service     = isset($_POST['service']) ? sanitize_text_field(wp_unslash($_POST['service'])) : '';
    $project     = isset($_POST['project_name']) ? sanitize_text_field(wp_unslash($_POST['project_name'])) : '';
    $genre       = isset($_POST['genre']) ? sanitize_text_field(wp_unslash($_POST['genre'])) : '';
    $budget      = isset($_POST['budget']) ? sanitize_text_field(wp_unslash($_POST['budget'])) : '';
    $timeline    = isset($_POST['timeline']) ? sanitize_text_field(wp_unslash($_POST['timeline'])) : '';
    $reference   = isset($_POST['reference_url']) ? esc_url_raw(wp_unslash($_POST['reference_url'])) : '';
    $message     = isset($_POST['message']) ? sanitize_textarea_field(wp_unslash($_POST['message'])) : '';

    if ($name === '') $tbsp_errors[] = 'Please enter your name.';
    if ($email === '' || !is_email($email)) $tbsp_errors[] = 'Please enter a valid email address.';
    if ($service === '') $tbsp_errors[] = 'Please choose a service.';
    if ($message === '') $tbsp_errors[] = 'Tell me a little about your project.';

    if (!$tbsp_errors) {
        $display_project = $project !== '' ? $project : ($artist_name !== '' ? $artist_name : $name);
        $post_id = wp_insert_post(array(
            'post_type'   => 'tb_project',
            'post_status' => 'draft',
            'post_title'  => 'INTAKE — ' . $display_project,
            'post_content'=> $message,
            'post_author' => 0,
        ), true);

        if (is_wp_error($post_id)) {
            $tbsp_errors[] = 'Your request could not be saved. Please try again.';
        } else {
            update_post_meta($post_id, '_tb_intake_request', 1);
            update_post_meta($post_id, '_tb_intake_status', 'reviewing');
            update_post_meta($post_id, '_tb_client_name', $name);
            update_post_meta($post_id, '_tb_artist_name', $artist_name);
            update_post_meta($post_id, '_tb_client_email', $email);
            update_post_meta($post_id, '_tb_client_phone', $phone);
            update_post_meta($post_id, '_tb_service', $service);
            update_post_meta($post_id, '_tb_genre', $genre);
            update_post_meta($post_id, '_tb_budget', $budget);
            update_post_meta($post_id, '_tb_timeline', $timeline);
            update_post_meta($post_id, '_tb_reference_url', $reference);
            update_post_meta($post_id, '_tb_intake_submitted_at', current_time('mysql'));

            $admin_email = get_option('admin_email');
            if ($admin_email && is_email($admin_email)) {
                wp_mail(
                    $admin_email,
                    'TUFF BEATZ — New Project Intake',
                    "New project request received.\n\nClient: {$name}\nArtist: {$artist_name}\nEmail: {$email}\nService: {$service}\nProject: {$project}\nBudget: {$budget}\nTimeline: {$timeline}\n\nReview in WordPress: " . admin_url('post.php?post=' . absint($post_id) . '&action=edit')
                );
            }

            wp_safe_redirect(add_query_arg('submitted', '1', get_permalink()));
            exit;
        }
    }
}

get_header();
?>
<main class="tbsp-page">
    <section class="tbsp-hero">
        <div class="tbsp-shell">
            <p class="tbsp-kicker">TUFF BEATZ • PRIVATE PROJECT INTAKE</p>
            <h1>START A <span>PROJECT.</span></h1>
            <p class="tbsp-lead">Tell me what you are building, where the record needs to go, and what kind of production support you need. Every serious project starts with clarity.</p>
            <div class="tbsp-flow" aria-label="Project process">
                <span>01 Brief</span><i></i><span>02 Review</span><i></i><span>03 Proposal</span><i></i><span>04 Production</span>
            </div>
        </div>
    </section>

    <section class="tbsp-content">
        <div class="tbsp-shell tbsp-grid">
            <aside class="tbsp-aside">
                <p class="tbsp-eyebrow">WORK WITH TUFF BEATZ</p>
                <h2>Built around the record. Not a generic package.</h2>
                <p>Use this intake to give TUFF BEATZ the creative and business context needed to review your project properly.</p>
                <div class="tbsp-service-list">
                    <span>Original Production</span>
                    <span>Beat Production</span>
                    <span>Arrangement</span>
                    <span>Mixing</span>
                    <span>Mastering</span>
                    <span>Vocal Production</span>
                    <span>Song Development</span>
                    <span>Full Production Package</span>
                </div>
                <div class="tbsp-note"><strong>What happens next?</strong><br>Your request enters producer review. Qualified projects can move into proposal, contract, client access and Studio OS.</div>
            </aside>

            <div class="tbsp-card">
                <?php if (isset($_GET['submitted']) && $_GET['submitted'] === '1') : ?>
                    <div class="tbsp-success">
                        <span>REQUEST RECEIVED</span>
                        <h2>Your project is now in review.</h2>
                        <p>Thank you. TUFF BEATZ has received your project brief. If the project is a fit, the next step is proposal and production planning.</p>
                        <a href="<?php echo esc_url(home_url('/')); ?>">Return to TUFF BEATZ</a>
                    </div>
                <?php else : ?>
                    <?php if ($tbsp_errors) : ?>
                        <div class="tbsp-errors" role="alert">
                            <?php foreach ($tbsp_errors as $error) : ?><p><?php echo esc_html($error); ?></p><?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <form method="post" class="tbsp-form" novalidate>
                        <?php wp_nonce_field('tbsp_submit_project', 'tbsp_nonce'); ?>
                        <input class="tbsp-hp" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">

                        <div class="tbsp-section-head"><span>01</span><div><small>IDENTITY</small><h3>Who am I creating with?</h3></div></div>
                        <div class="tbsp-fields tbsp-two">
                            <label>Full Name*<input type="text" name="name" required value="<?php echo isset($name) ? esc_attr($name) : ''; ?>"></label>
                            <label>Artist / Brand Name<input type="text" name="artist_name" value="<?php echo isset($artist_name) ? esc_attr($artist_name) : ''; ?>"></label>
                            <label>Email Address*<input type="email" name="email" required value="<?php echo isset($email) ? esc_attr($email) : ''; ?>"></label>
                            <label>Phone / WhatsApp<input type="text" name="phone" value="<?php echo isset($phone) ? esc_attr($phone) : ''; ?>"></label>
                        </div>

                        <div class="tbsp-section-head"><span>02</span><div><small>SERVICE</small><h3>What do you need?</h3></div></div>
                        <div class="tbsp-fields">
                            <label>Primary Service*
                                <select name="service" required>
                                    <option value="">Choose a service</option>
                                    <?php foreach (array('Original Production','Beat Production','Arrangement','Mixing','Mastering','Vocal Production','Song Development','Full Production Package','Creative / Production Consulting') as $opt) : ?>
                                        <option value="<?php echo esc_attr($opt); ?>" <?php selected(isset($service) ? $service : '', $opt); ?>><?php echo esc_html($opt); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        </div>

                        <div class="tbsp-section-head"><span>03</span><div><small>PROJECT</small><h3>Tell me about the record.</h3></div></div>
                        <div class="tbsp-fields tbsp-two">
                            <label>Project / Song Name<input type="text" name="project_name" value="<?php echo isset($project) ? esc_attr($project) : ''; ?>"></label>
                            <label>Genre / Direction<input type="text" name="genre" placeholder="Afrobeats, Konpa, R&B..." value="<?php echo isset($genre) ? esc_attr($genre) : ''; ?>"></label>
                            <label>Budget Range
                                <select name="budget">
                                    <option value="">Select range</option>
                                    <?php foreach (array('Under $500','$500–$1,000','$1,000–$2,500','$2,500–$5,000','$5,000+','Let’s discuss') as $opt) : ?>
                                        <option value="<?php echo esc_attr($opt); ?>" <?php selected(isset($budget) ? $budget : '', $opt); ?>><?php echo esc_html($opt); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label>Desired Timeline
                                <select name="timeline">
                                    <option value="">Select timeline</option>
                                    <?php foreach (array('ASAP / Rush','1–2 weeks','2–4 weeks','1–2 months','Flexible') as $opt) : ?>
                                        <option value="<?php echo esc_attr($opt); ?>" <?php selected(isset($timeline) ? $timeline : '', $opt); ?>><?php echo esc_html($opt); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                        </div>
                        <div class="tbsp-fields">
                            <label>Reference / Demo Link<input type="url" name="reference_url" placeholder="YouTube, Drive, Dropbox, SoundCloud..." value="<?php echo isset($reference) ? esc_attr($reference) : ''; ?>"></label>
                            <label>Project Brief*<textarea name="message" rows="7" required placeholder="What are you trying to create? What should the listener feel? What stage is the song currently in?"><?php echo isset($message) ? esc_textarea($message) : ''; ?></textarea></label>
                        </div>

                        <div class="tbsp-submit-row">
                            <p>Submitting this form sends your project into TUFF BEATZ producer review. It does not guarantee acceptance or create a payment obligation.</p>
                            <button type="submit" name="tbsp_submit" value="1">SUBMIT PROJECT FOR REVIEW <span>→</span></button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>
<?php get_footer(); ?>
