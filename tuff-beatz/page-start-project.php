<?php
/**
 * Template Name: Start a Project
 */
if (!defined('ABSPATH')) exit;
get_header();

$error = sanitize_key(wp_unslash($_GET['portal_error'] ?? ''));
$messages = array(
    'security' => 'Your session could not be verified. Please try again.',
    'registration_fields' => 'Enter your name, a valid email, and a password with at least 10 characters.',
    'email_exists' => 'An account already exists with that email. Please log in instead.',
    'registration_failed' => 'We could not create your account. Please try again.',
    'required_fields' => 'Please complete the required project fields.',
    'submit_failed' => 'Your project request could not be submitted. Please try again.',
);
?>
<main class="tb-portal" id="main">
  <section class="tb-portal-hero">
    <div class="container tb-portal-hero__inner">
      <p class="eyebrow">TUFF BEATZ CLIENT PORTAL</p>
      <h1>START A <span>PROJECT</span></h1>
      <p>Tell me what you are building, where you want the record to go, and what you need from TUFF BEATZ. Every project begins inside a secure Artist / Client account.</p>
      <div class="tb-portal-trust"><span>PRIVATE INTAKE</span><span>DIRECT PROJECT REVIEW</span><span>PRODUCTION-READY WORKFLOW</span></div>
    </div>
  </section>

  <section class="tb-portal-body section">
    <div class="container">
      <?php if ($error && isset($messages[$error])) : ?>
        <div class="tb-portal-alert tb-portal-alert--error"><?php echo esc_html($messages[$error]); ?></div>
      <?php endif; ?>
      <?php if (isset($_GET['welcome'])) : ?>
        <div class="tb-portal-alert">Your account is ready. You can now submit your project.</div>
      <?php endif; ?>
      <?php if (isset($_GET['submitted'])) : ?>
        <div class="tb-portal-alert">Project received. TUFF BEATZ will review your request and follow up with you.</div>
      <?php endif; ?>

      <?php if (!is_user_logged_in()) : ?>
        <div class="tb-auth-grid">
          <article class="tb-portal-card tb-login-card">
            <p class="tb-card-kicker">RETURNING CLIENT / ARTIST</p>
            <h2>Log in first</h2>
            <p>Your project form remains locked until you are signed in.</p>
            <?php
            wp_login_form(array(
                'redirect' => home_url('/start-a-project/'),
                'remember' => true,
                'label_username' => 'Email or Username',
                'label_password' => 'Password',
                'label_remember' => 'Keep me signed in',
                'label_log_in' => 'Enter Project Portal',
            ));
            ?>
            <a class="tb-small-link" href="<?php echo esc_url(wp_lostpassword_url(home_url('/start-a-project/'))); ?>">Forgot your password?</a>
          </article>

          <article class="tb-portal-card tb-register-card">
            <p class="tb-card-kicker">FIRST TIME HERE</p>
            <h2>Create your account</h2>
            <p>Choose the account that best describes how you work with TUFF BEATZ.</p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="tb-portal-form">
              <input type="hidden" name="action" value="tb_register_portal_account">
              <?php wp_nonce_field('tb_register_account', 'tb_register_nonce'); ?>
              <input class="tb-honeypot" type="text" name="company_website" tabindex="-1" autocomplete="off" aria-hidden="true">
              <div class="tb-field">
                <label for="display_name">Full / Professional Name *</label>
                <input id="display_name" name="display_name" type="text" required autocomplete="name">
              </div>
              <div class="tb-field">
                <label for="register_email">Email *</label>
                <input id="register_email" name="email" type="email" required autocomplete="email">
              </div>
              <div class="tb-field">
                <label for="register_password">Password *</label>
                <input id="register_password" name="password" type="password" minlength="10" required autocomplete="new-password">
                <small>Minimum 10 characters.</small>
              </div>
              <div class="tb-field">
                <label for="account_type">I am joining as</label>
                <select id="account_type" name="account_type">
                  <option value="tb_artist">Artist</option>
                  <option value="tb_client">Client / Representative</option>
                </select>
              </div>
              <button class="btn btn-gold tb-submit" type="submit">Create Account & Continue</button>
            </form>
          </article>
        </div>

        <div class="tb-locked-preview">
          <div class="tb-lock-mark">🔒</div>
          <div><strong>Project submission is locked.</strong><span>Log in or create an Artist / Client account above to unlock the full intake form.</span></div>
        </div>

      <?php else :
        $user = wp_get_current_user();
        $requests = new WP_Query(array(
            'post_type' => 'tb_request',
            'post_status' => 'publish',
            'author' => $user->ID,
            'posts_per_page' => 6,
            'orderby' => 'date',
            'order' => 'DESC',
        ));
      ?>
        <div class="tb-portal-userbar">
          <div>
            <span>SIGNED IN AS</span>
            <strong><?php echo esc_html($user->display_name); ?></strong>
            <small><?php echo esc_html($user->user_email); ?></small>
          </div>
          <a class="btn btn-outline" href="<?php echo esc_url(wp_logout_url(home_url('/start-a-project/'))); ?>">Log Out</a>
        </div>

        <div class="tb-project-grid">
          <article class="tb-portal-card tb-project-form-card">
            <p class="tb-card-kicker">PROJECT INTAKE</p>
            <h2>Tell me about the record</h2>
            <p>The more detail you give me here, the faster I can understand the creative direction, scope, and production needs.</p>

            <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" class="tb-portal-form tb-project-form">
              <input type="hidden" name="action" value="tb_submit_project_request">
              <?php wp_nonce_field('tb_submit_project', 'tb_project_nonce'); ?>

              <div class="tb-form-row">
                <div class="tb-field"><label for="artist_name">Artist / Client Name *</label><input id="artist_name" name="artist_name" type="text" value="<?php echo esc_attr($user->display_name); ?>" required></div>
                <div class="tb-field"><label for="project_title">Project / Song Title *</label><input id="project_title" name="project_title" type="text" required></div>
              </div>

              <div class="tb-form-row">
                <div class="tb-field">
                  <label for="service">What do you need? *</label>
                  <select id="service" name="service" required>
                    <option value="">Select a service</option>
                    <option>Full Song Production</option>
                    <option>Beat Production</option>
                    <option>Mixing & Mastering</option>
                    <option>Vocal Production / Recording</option>
                    <option>Arrangement / Live Musicians</option>
                    <option>Production Consultation</option>
                    <option>Other / Custom Project</option>
                  </select>
                </div>
                <div class="tb-field">
                  <label for="project_type">Project Type</label>
                  <select id="project_type" name="project_type"><option>Single</option><option>EP</option><option>Album</option><option>Video / Film Music</option><option>Other</option></select>
                </div>
              </div>

              <div class="tb-form-row tb-form-row--3">
                <div class="tb-field"><label for="genre">Genre / Style</label><input id="genre" name="genre" type="text" placeholder="Kompa, Afrobeats, R&B..."></div>
                <div class="tb-field">
                  <label for="budget">Estimated Budget</label>
                  <select id="budget" name="budget">
                    <option value="">Select range</option>
                    <option>Under $1,000</option><option>$1,000–$2,999</option><option>$3,000–$5,999</option><option>$6,000–$9,999</option><option>$10,000+</option><option>Need a quote</option>
                  </select>
                </div>
                <div class="tb-field"><label for="target_date">Target Release / Deadline</label><input id="target_date" name="target_date" type="date"></div>
              </div>

              <div class="tb-field"><label for="reference_url">Reference / Demo Link</label><input id="reference_url" name="reference_url" type="url" placeholder="Spotify, YouTube, Drive, Dropbox, SoundCloud..."></div>
              <div class="tb-field"><label for="details">Creative Direction & Project Details *</label><textarea id="details" name="details" rows="8" required placeholder="Tell me the sound you want, what is already recorded, what you need TUFF BEATZ to handle, references, deadlines, and anything else I should know."></textarea></div>
              <div class="tb-field tb-upload-field">
                <label for="project_file">Optional Project File</label>
                <input id="project_file" name="project_file" type="file" accept=".mp3,.wav,.m4a,.zip,.pdf,audio/*,application/zip,application/pdf">
                <small>Demo, rough mix, brief, or ZIP. File size is limited by the WordPress server upload limit.</small>
              </div>

              <label class="tb-consent"><input type="checkbox" required><span>I confirm that the information and files I submit are mine to share for project evaluation.</span></label>
              <button class="btn btn-gold tb-submit" type="submit">Submit Project for Review</button>
            </form>
          </article>

          <aside class="tb-portal-side">
            <article class="tb-portal-card tb-process-card">
              <p class="tb-card-kicker">WHAT HAPPENS NEXT</p>
              <ol><li><span>01</span><div><strong>Submit</strong><small>Your request enters the private TUFF BEATZ project queue.</small></div></li><li><span>02</span><div><strong>Review</strong><small>I review your scope, sound, timeline, and production needs.</small></div></li><li><span>03</span><div><strong>Project Plan</strong><small>We align on deliverables, budget, schedule, and next steps.</small></div></li><li><span>04</span><div><strong>Production</strong><small>Approved projects move into the production workflow.</small></div></li></ol>
            </article>

            <article class="tb-portal-card tb-history-card">
              <p class="tb-card-kicker">YOUR REQUESTS</p>
              <h3>Recent projects</h3>
              <?php if ($requests->have_posts()) : ?>
                <div class="tb-request-list">
                  <?php while ($requests->have_posts()) : $requests->the_post(); $status = get_post_meta(get_the_ID(), '_tb_request_status', true) ?: 'new'; ?>
                    <div class="tb-request-item"><div><strong><?php the_title(); ?></strong><small><?php echo esc_html(get_the_date('M j, Y')); ?></small></div><span class="tb-status tb-status--<?php echo esc_attr($status); ?>"><?php echo esc_html(tuff_beatz_request_status_label($status)); ?></span></div>
                  <?php endwhile; wp_reset_postdata(); ?>
                </div>
              <?php else : ?>
                <p class="tb-empty-state">You have not submitted a project yet. Your requests will appear here after submission.</p>
              <?php endif; ?>
            </article>
          </aside>
        </div>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php get_footer(); ?>
