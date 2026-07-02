<?php
/**
 * Custom downloads page for Eclipse OT.
 */
defined('MYAAC') or die('Direct access not allowed!');
$title = t('downloads.page_title');

$clientVersion = '15.11.c9d1cf-r1';
$launcherVersion = '1.0.1';
$launcherUrl = '/downloads/eclipse-launcher.zip';
$clientUrl = '/downloads/eclipse-client-15.11.c9d1cf.zip';
?>

<style>
	.eclipse-download-page,
	.eclipse-download-page * {
		color: #1f0804 !important;
		font-weight: 800;
		text-shadow: none !important;
		box-sizing: border-box;
	}

	.eclipse-download-page .download-eyebrow {
		display: inline-block;
		margin-bottom: 10px;
		padding: 5px 9px;
		border: 1px solid rgba(91, 22, 9, .35);
		border-radius: 4px;
		background: linear-gradient(180deg, #6d1a0e 0%, #2b0604 100%);
		color: #fff8dc !important;
		-webkit-text-fill-color: #fff8dc !important;
		font: 900 11px Verdana, Arial, sans-serif;
		letter-spacing: .08em;
		text-transform: uppercase;
		text-shadow: 0 1px 1px #000 !important;
	}

	#News:has(.eclipse-download-page) > img.Title[src*="headline-downloads"] {
		display: none !important;
	}

	#News:has(.eclipse-download-page) > .BorderTitleText::after {
		content: "<?php echo addslashes(t('downloads.page_title')); ?>";
		display: flex;
		align-items: center;
		height: 100%;
		padding-left: 14px;
		color: #f7e7bd !important;
		font: 900 18px Georgia, "Times New Roman", serif;
		text-shadow: 0 2px 0 #1c0905, 0 0 8px rgba(255, 176, 69, .55) !important;
	}

	.eclipse-download-page .download-shell {
		background: linear-gradient(180deg, #f6dfa9 0%, #dfba72 66%, #c99448 100%);
		border: 2px solid #a66a23;
		border-radius: 5px;
		box-shadow: inset 0 0 0 1px rgba(255, 246, 202, .7), 0 10px 26px rgba(0, 0, 0, .42);
		padding: 16px;
	}

	.eclipse-download-page .download-hero {
		display: grid;
		grid-template-columns: minmax(0, 1fr) auto;
		gap: 16px;
		align-items: center;
		padding: 18px;
		border: 1px solid rgba(119, 72, 28, .48);
		border-radius: 5px;
		background: linear-gradient(180deg, #fff0bd 0%, #e8c27a 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 224, .85), 0 6px 16px rgba(71, 39, 9, .25);
	}

	.eclipse-download-page .download-title {
		margin: 0 0 8px;
		font: 900 24px Georgia, "Times New Roman", serif;
		color: #4d1209 !important;
	}

	.eclipse-download-page .download-lead {
		margin: 0;
		line-height: 1.5;
		font-size: 14px;
	}

	.eclipse-download-page .download-actions {
		display: grid;
		gap: 10px;
		min-width: 230px;
	}

	.eclipse-download-page .download-button {
		display: block;
		padding: 11px 15px;
		border: 1px solid #ffe1a0;
		border-radius: 4px;
		background: linear-gradient(180deg, #ff9b1f 0%, #df6505 100%);
		box-shadow: inset 0 1px 0 rgba(255, 255, 255, .48), 0 3px 9px rgba(73, 31, 2, .34);
		color: #fff7d4 !important;
		font: 900 13px Arial, sans-serif;
		text-align: center;
		text-transform: uppercase;
		text-decoration: none;
		text-shadow: 0 1px 1px #4c1600 !important;
	}

	.eclipse-download-page .download-button.secondary {
		background: linear-gradient(180deg, #173f54 0%, #08202d 100%);
		border-color: #d69a3d;
		color: #fff1bd !important;
	}

	.eclipse-download-page .download-grid {
		display: grid;
		grid-template-columns: repeat(2, minmax(0, 1fr));
		gap: 12px;
		margin-top: 14px;
	}

	.eclipse-download-page .download-card,
	.eclipse-download-page .download-note {
		border: 1px solid rgba(118, 70, 26, .46);
		border-radius: 5px;
		background: linear-gradient(180deg, #f8e5b0 0%, #e3bd74 100%);
		box-shadow: inset 0 1px 0 rgba(255, 252, 226, .72), 0 4px 12px rgba(64, 36, 9, .2);
		padding: 13px;
	}

	.eclipse-download-page .download-card strong {
		display: block;
		margin-bottom: 6px;
		color: #4d1209 !important;
		font-size: 14px;
	}

	.eclipse-download-page .download-card span,
	.eclipse-download-page .download-note span {
		display: block;
		font-size: 13px;
		line-height: 1.45;
	}

	.eclipse-download-page .download-step {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 24px;
		height: 24px;
		margin-right: 7px;
		border-radius: 50%;
		background: #5a130a;
		color: #fff0b8 !important;
		font: 900 13px Arial, sans-serif;
	}

	.eclipse-download-page .download-note {
		margin-top: 14px;
		background: linear-gradient(180deg, #fff2c4 0%, #e9c27a 100%);
	}

	.eclipse-download-page .download-note strong {
		color: #4d1209 !important;
	}

	@media (max-width: 860px) {
		.eclipse-download-page .download-hero,
		.eclipse-download-page .download-grid {
			grid-template-columns: 1fr;
		}

		.eclipse-download-page .download-actions {
			min-width: 0;
		}
	}
</style>

<div class="eclipse-download-page">
	<div class="download-shell">
		<div class="download-hero">
			<div class="download-hero-copy">
				<span class="download-eyebrow"><?php echo htmlspecialchars(t('downloads.eyebrow')); ?></span>
				<p class="download-lead">
					<?php echo htmlspecialchars(t('downloads.lead', ['client_version' => $clientVersion])); ?>
				</p>
				<div class="download-version-row">
					<span><?php echo htmlspecialchars(t('downloads.client_version', ['version' => $clientVersion])); ?></span>
					<span><?php echo htmlspecialchars(t('downloads.launcher_version', ['version' => $launcherVersion])); ?></span>
				</div>
			</div>
			<div class="download-primary-panel">
				<div class="download-file-meta">
					<strong><?php echo htmlspecialchars(t('downloads.file_name')); ?></strong>
					<span><?php echo htmlspecialchars(t('downloads.file_meta')); ?></span>
				</div>
				<div class="download-actions">
					<a class="download-button" href="<?php echo htmlspecialchars($launcherUrl); ?>"><?php echo htmlspecialchars(t('downloads.button_launcher')); ?></a>
					<a class="download-button secondary" href="<?php echo htmlspecialchars($clientUrl); ?>"><?php echo htmlspecialchars(t('downloads.button_client')); ?></a>
				</div>
			</div>
		</div>

		<div class="download-grid">
			<div class="download-card">
				<strong><span class="download-step">1</span><?php echo htmlspecialchars(t('downloads.step_1_title')); ?></strong>
				<span><?php echo htmlspecialchars(t('downloads.step_1_text')); ?></span>
			</div>
			<div class="download-card">
				<strong><span class="download-step">2</span><?php echo htmlspecialchars(t('downloads.step_2_title')); ?></strong>
				<span><?php echo htmlspecialchars(t('downloads.step_2_text')); ?></span>
			</div>
			<div class="download-card">
				<strong><span class="download-step">3</span><?php echo htmlspecialchars(t('downloads.step_3_title')); ?></strong>
				<span><?php echo htmlspecialchars(t('downloads.step_3_text')); ?></span>
			</div>
		</div>

		<div class="download-note">
			<strong><?php echo htmlspecialchars(t('downloads.warning_title')); ?></strong>
			<span><?php echo htmlspecialchars(t('downloads.warning_text')); ?></span>
		</div>
	</div>
</div>
