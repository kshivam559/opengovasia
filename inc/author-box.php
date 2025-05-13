<?php
/**
 * Author Box Template
 *
 * This template displays the author box on single posts.
 *
 * @package OpenGovAsia
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

function custom_author_social_fields($user)
{ ?>
    <h3>Social Media Links</h3>
    <table class="form-table">
        <tr>
            <th><label for="twitter">Twitter</label></th>
            <td>
                <input type="text" name="twitter" id="twitter"
                    value="<?php echo esc_attr(get_the_author_meta('twitter', $user->ID)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="linkedin">LinkedIn</label></th>
            <td>
                <input type="text" name="linkedin" id="linkedin"
                    value="<?php echo esc_attr(get_the_author_meta('linkedin', $user->ID)); ?>" class="regular-text">
            </td>
        </tr>
        <tr>
            <th><label for="facebook">Facebook</label></th>
            <td>
                <input type="text" name="facebook" id="facebook"
                    value="<?php echo esc_attr(get_the_author_meta('facebook', $user->ID)); ?>" class="regular-text">
            </td>
        </tr>
    </table>
    <!-- Nonce Field for Security -->
    <?php wp_nonce_field('save_author_social_fields', 'author_social_nonce'); ?>
<?php }
add_action('show_user_profile', 'custom_author_social_fields');
add_action('edit_user_profile', 'custom_author_social_fields');

function save_custom_author_social_fields($user_id)
{
    // Check if the user has permission to edit profile
    if (!current_user_can('edit_user', $user_id))
        return false;

    // Verify nonce for security
    if (!isset($_POST['author_social_nonce']) || !wp_verify_nonce($_POST['author_social_nonce'], 'save_author_social_fields')) {
        return false;
    }

    // Sanitize and update social fields
    update_user_meta($user_id, 'twitter', sanitize_text_field($_POST['twitter']));
    update_user_meta($user_id, 'linkedin', sanitize_text_field($_POST['linkedin']));
    update_user_meta($user_id, 'facebook', sanitize_text_field($_POST['facebook']));
}
add_action('personal_options_update', 'save_custom_author_social_fields');
add_action('edit_user_profile_update', 'save_custom_author_social_fields');

// Display the author box
function opengovasia_author_box()
{
    if (!is_single() || is_page()) {
        return;
    }
    $author_id = get_the_author_meta('ID');

    // Get the author page URL
    $author_url = get_author_posts_url($author_id);

    $twitter = get_the_author_meta('twitter', $author_id);
    $linkedin = get_the_author_meta('linkedin', $author_id);
    $facebook = get_the_author_meta('facebook', $author_id);

    ?>
    <div class="post-author panel py-4 px-3 sm:p-3 xl:p-4 bg-gray-25 dark:bg-opacity-10 rounded lg:rounded-2">
        <div class="row g-4 items-center">
            <div class="col-12 sm:col-5 xl:col-3">
                <figure class="featured-image m-0 ratio ratio-1x1 rounded overflow-hidden bg-gray-25 dark:bg-gray-800">
                    <?php echo get_avatar($author_id, 100); ?>
                </figure>
            </div>
            <div class="col">
                <div class="panel vstack items-start gap-2 md:gap-3">
                    <h4 class="h5 lg:h4 m-0">
                        <a class="text-none hover:text-primary" href="<?php echo esc_url($author_url); ?>">
                            <?php echo get_the_author(); ?>
                        </a>
                    </h4>
                    <p class="fs-6 lg:fs-5"><?php echo get_the_author_meta('description', $author_id); ?></p>
                    <ul class="nav-x gap-1 text-gray-400 dark:text-white">
                        <?php if ($facebook): ?>
                            <li><a href="<?php echo esc_url($facebook); ?>" target="_blank"><i
                                        class="icon-2 unicon-logo-facebook"></i></a></li>
                        <?php endif; ?>
                        <?php if ($twitter): ?>
                            <li><a href="<?php echo esc_url($twitter); ?>" target="_blank"><i
                                        class="icon-2 unicon-logo-x-filled"></i></a></li>
                        <?php endif; ?>
                        <?php if ($linkedin): ?>
                            <li><a href="<?php echo esc_url($linkedin); ?>" target="_blank"><i
                                        class="icon-2 unicon-logo-linkedin"></i></a></li>
                        <?php endif; ?>
                        <?php if (get_the_author_meta('user_url')): ?>
                            <li>
                                <a href="<?php echo esc_url(get_the_author_meta('user_url')); ?>" target="_blank">
                                    <i class="icon-2 unicon-link"></i>
                                </a>
                            </li>
                        <?php endif; ?>

                    </ul>
                </div>
            </div>
        </div>
    </div>
    <?php
}