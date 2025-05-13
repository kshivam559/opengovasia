<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package OpenGov_Asia
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

if (post_password_required()) {
	return;
}
?>

<div id="blog-comment" class="panel border-top pt-2 mt-8 xl:mt-9">
	<?php if (have_comments()): ?>
		<h4 class="h5 xl:h4 mb-5 xl:mb-6">
			<?php printf(esc_html__('Comments (%d)', 'opengovasia'), get_comments_number()); ?>
		</h4>

		<div class="spacer-half"></div>

		<ol class="comment-list">
			<?php
			wp_list_comments([
				'style' => 'ol',
				'short_ping' => true,
				'avatar_size' => 70,
				'callback' => 'custom_comment_callback'
			]);
			?>
		</ol>

		<?php the_comments_navigation(); ?>
	<?php endif; ?>

	<?php if (!comments_open() && get_comments_number()): ?>
		<p class="no-comments"> <?php esc_html_e('Comments are closed.', 'opengovasia'); ?> </p>
	<?php endif; ?>

	<div class="spacer-single"></div>

	<div id="comment-form-wrapper" class="panel pt-2">

		<div class="comment_form_holder">
			<?php
			$commenter = wp_get_current_commenter();
			$consent = isset($commenter['comment_author_email']) && !empty($commenter['comment_author_email']) ? 'checked="checked"' : '';

			comment_form([
				'class_form' => 'vstack gap-2',
				'title_reply' => esc_html__('Leave a Comment', 'opengovasia'),
				'submit_button' => '<button class="btn btn-primary btn-sm w-full mb-3" type="submit">Post Comment</button>',
				'comment_field' => '<textarea class="form-control h-250px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30" name="comment" placeholder="Your comment" required></textarea>',
				'fields' => [
					'author' => '<input class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30" name="author" type="text" placeholder="Full name" required value="' . esc_attr(isset($_COOKIE['comment_author_' . COOKIEHASH]) ? $_COOKIE['comment_author_' . COOKIEHASH] : '') . '">',
					'email' => '<input class="form-control form-control-sm h-40px w-full fs-6 bg-white dark:bg-opacity-0 dark:text-white dark:border-gray-300 dark:border-opacity-30" name="email" type="email" placeholder="Your email" required value="' . esc_attr(isset($_COOKIE['comment_author_email_' . COOKIEHASH]) ? $_COOKIE['comment_author_email_' . COOKIEHASH] : '') . '">',
					'cookies' => '
					<div class="comment-form-cookies-consent unicore-checkbox flex items-center gap-2">
						<input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" ' . (isset($_COOKIE['comment_author_' . COOKIEHASH]) ? 'checked' : '') . ' class="form-check-input hidden m-narrow" required>
						
						<label for="wp-comment-cookies-consent" class="form-label" style="display:inline;">By posting this comment, I agree to the <a class="text-none text-primary" href="/terms-services/">Terms of Service</a> and <a class="text-none text-primary" href="/privacy-policy/">Privacy Policy</a>.</label>
					</div>
					',
				]
			]);
			?>
		</div>
	</div>
</div>

<?php
/**
 * Custom Comment Callback Function
 */

function custom_comment_callback($comment, $args, $depth)
{

	?>
	<li id="comment-<?php comment_ID(); ?>">
		<div class="avatar">
			<img src="<?php echo get_avatar_url($comment, ['size' => 70]); ?>" alt="">

		</div>
		<div class="comment-info">
			<span class="c_name"><?php echo get_comment_author($comment); ?>
			</span>
			<span class="c_date id-color"><?php echo get_comment_date(); ?></span>
			<span class="c_reply">
				<?php comment_reply_link(array_merge($args, array(
					'depth' => $depth,
					'max_depth' => $args['max_depth'],
					'before' => '<span class="reply-link">',
					'after' => '</span>',
				))); ?>
			</span>
			<div class="clearfix"></div>
		</div>
		<div class="comment">
			<?php comment_text(); ?>
		</div>
	</li>
	<?php
}
