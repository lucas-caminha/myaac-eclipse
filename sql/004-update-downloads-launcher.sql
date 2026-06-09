-- Update the public Downloads page with Eclipse OT launcher links.
-- Apply with: mysql canary < sql/004-update-downloads-launcher.sql
UPDATE myaac_pages
SET
  title = 'Baixar Cliente',
  body = '<div class="eclipse-download-page">
	<div class="download-shell">
		<div class="download-hero">
			<div>
				<h2 class="download-title">Baixar Cliente</h2>
				<p class="download-lead">
					Use o cliente oficial Tibia 15.00.249ccc para jogar no Eclipse OT.
					Baixe o launcher para instalar e atualizar o cliente automaticamente.
				</p>
			</div>
			<div class="download-actions">
				<a class="download-button" href="/downloads/eclipse-launcher.zip">Baixar Launcher</a>
				<a class="download-button secondary" href="/downloads/eclipse-client-15.00.249ccc.zip">Baixar Cliente Completo</a>
			</div>
		</div>

		<div class="download-grid">
			<div class="download-card">
				<strong><span class="download-step">1</span>Baixe o launcher</strong>
				<span>Extraia o arquivo, abra o EclipseLauncher.exe e deixe ele baixar ou atualizar o cliente automaticamente.</span>
			</div>
			<div class="download-card">
				<strong><span class="download-step">2</span>Entre no jogo</strong>
				<span>Crie sua conta, escolha sua voca&ccedil;&atilde;o e comece sua jornada.</span>
			</div>
		</div>

		<div class="download-note">
			<strong>Observa&ccedil;&atilde;o:</strong>
			<span>Se o navegador bloquear o download, confirme que deseja manter o arquivo. Baixe sempre pelos links oficiais desta p&aacute;gina.</span>
		</div>
	</div>
</div>',
  php = 0,
  enable_tinymce = 0,
  access = 0,
  hide = 0
WHERE name = 'downloads';
