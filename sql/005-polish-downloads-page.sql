-- Polish the public Downloads page layout and add launcher trust guidance.
-- Apply with: mysql canary < sql/005-polish-downloads-page.sql
UPDATE myaac_pages
SET
  title = 'Baixar Cliente',
  body = '<div class="eclipse-download-page">
	<div class="download-shell">
		<div class="download-hero">
			<div class="download-hero-copy">
				<span class="download-eyebrow">Launcher oficial</span>
				<h2 class="download-title">Baixar Cliente Eclipse OT</h2>
				<p class="download-lead">
					Use o launcher oficial para baixar, atualizar e abrir o cliente automaticamente. Ele mant&eacute;m o pacote do Tibia 15.00.249ccc sempre alinhado com o servidor.
				</p>
				<div class="download-version-row">
					<span>Client 15.00.249ccc-r2</span>
					<span>Launcher 1.0.1</span>
				</div>
			</div>

			<div class="download-primary-panel">
				<div class="download-file-meta">
					<strong>EclipseLauncher.exe</strong>
					<span>Op&ccedil;&atilde;o recomendada. Baixe, extraia o .zip e abra o launcher para instalar o jogo.</span>
				</div>
				<div class="download-actions">
					<a class="download-button" href="/downloads/eclipse-launcher.zip">Baixar Launcher</a>
					<a class="download-button secondary" href="/downloads/eclipse-client-15.00.249ccc.zip">Baixar Cliente Completo</a>
				</div>
			</div>
		</div>

		<div class="download-grid">
			<div class="download-card">
				<strong><span class="download-step">1</span>Baixe o launcher</strong>
				<span>Extraia o arquivo em uma pasta nova e abra o EclipseLauncher.exe.</span>
			</div>
			<div class="download-card">
				<strong><span class="download-step">2</span>Atualize o cliente</strong>
				<span>O launcher baixa o cliente corrigido e salva a vers&atilde;o local automaticamente.</span>
			</div>
			<div class="download-card">
				<strong><span class="download-step">3</span>Entre no jogo</strong>
				<span>Crie sua conta, escolha sua voca&ccedil;&atilde;o e comece sua jornada no Eclipse OT.</span>
			</div>
		</div>

		<div class="download-note">
			<strong>Aviso do Windows:</strong>
			<span>O Windows pode exibir alerta porque o launcher ainda n&atilde;o possui assinatura digital com reputa&ccedil;&atilde;o SmartScreen. Baixe sempre por esta p&aacute;gina oficial.</span>
		</div>
	</div>
</div>',
  php = 0,
  enable_tinymce = 0,
  access = 0,
  hide = 0
WHERE name = 'downloads';
