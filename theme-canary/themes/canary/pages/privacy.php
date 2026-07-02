<?php
defined('MYAAC') or die('Direct access not allowed!');

$title = t('privacy.page_title');
?>
<div class="eclipse-privacy-page">
	<section class="privacy-hero">
		<span>LGPD</span>
		<p><?= htmlspecialchars(t('privacy.hero')) ?></p>
	</section>

	<section class="privacy-section">
		<h3><?= htmlspecialchars(t('privacy.data_title')) ?></h3>
		<div class="privacy-grid">
			<article><strong><?= htmlspecialchars(t('privacy.data.account')) ?></strong><p><?= htmlspecialchars(t('privacy.data.account_text')) ?></p></article>
			<article><strong><?= htmlspecialchars(t('privacy.data.game')) ?></strong><p><?= htmlspecialchars(t('privacy.data.game_text')) ?></p></article>
			<article><strong><?= htmlspecialchars(t('privacy.data.donations')) ?></strong><p><?= htmlspecialchars(t('privacy.data.donations_text')) ?></p></article>
			<article><strong><?= htmlspecialchars(t('privacy.data.payments')) ?></strong><p><?= htmlspecialchars(t('privacy.data.payments_text')) ?></p></article>
		</div>
	</section>

	<section class="privacy-section">
		<h3><?= htmlspecialchars(t('privacy.why_title')) ?></h3>
		<ul>
			<li><?= htmlspecialchars(t('privacy.why.1')) ?></li>
			<li><?= htmlspecialchars(t('privacy.why.2')) ?></li>
			<li><?= htmlspecialchars(t('privacy.why.3')) ?></li>
			<li><?= htmlspecialchars(t('privacy.why.4')) ?></li>
			<li><?= htmlspecialchars(t('privacy.why.5')) ?></li>
		</ul>
	</section>

	<section class="privacy-section">
		<h3><?= htmlspecialchars(t('privacy.optional_title')) ?></h3>
		<p><?= htmlspecialchars(t('privacy.optional_1')) ?></p>
		<p><?= htmlspecialchars(t('privacy.optional_2')) ?></p>
	</section>

	<section class="privacy-section">
		<h3><?= htmlspecialchars(t('privacy.sharing_title')) ?></h3>
		<p><?= htmlspecialchars(t('privacy.sharing_text')) ?></p>
	</section>

	<section class="privacy-section">
		<h3><?= htmlspecialchars(t('privacy.rights_title')) ?></h3>
		<p><?= htmlspecialchars(t('privacy.rights_text')) ?></p>
		<div class="privacy-actions">
			<a class="eclipse-btn" href="<?= getLink('account/privacy') ?>"><?= htmlspecialchars(t('privacy.manage_data')) ?></a>
			<a class="eclipse-btn eclipse-btn-secondary" href="<?= getLink('account/change-info') ?>"><?= htmlspecialchars(t('privacy.update_registration')) ?></a>
		</div>
	</section>

	<section class="privacy-section">
		<h3><?= htmlspecialchars(t('privacy.contact_title')) ?></h3>
		<p><?= htmlspecialchars(t('privacy.contact_text')) ?></p>
	</section>
</div>
