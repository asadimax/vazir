<?php
/**
 * دیدگاه‌ها
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( post_password_required() ) {
	return;
}
?>
<section id="comments" class="comments-area">
	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php echo esc_html( number_format_i18n( get_comments_number() ) ); ?> دیدگاه
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				array(
					'style'       => 'ol',
					'callback'    => 'mfa_comment',
					'avatar_size' => 48,
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			array(
				'prev_text' => 'دیدگاه‌های قدیمی‌تر',
				'next_text' => 'دیدگاه‌های جدیدتر',
			)
		);
		?>
	<?php endif; ?>

	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments">دیدگاه‌ها برای این نوشته بسته شده است.</p>
	<?php endif; ?>

	<?php
	comment_form(
		array(
			'title_reply'          => 'دیدگاه خود را بنویسید',
			'title_reply_to'       => 'پاسخ به %s',
			'cancel_reply_link'    => 'انصراف از پاسخ',
			'label_submit'         => 'ارسال دیدگاه',
			'comment_field'        => '<p class="comment-form-comment"><label for="comment">دیدگاه</label><textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required></textarea></p>',
			'logged_in_as'         => '',
			'comment_notes_before' => '',
		)
	);
	?>
</section>
