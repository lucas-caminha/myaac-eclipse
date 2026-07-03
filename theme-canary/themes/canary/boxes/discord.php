<style>
    .discord{
        width: 180px;
        height: 110px;
    }
    .discord_header{
        height: 45px;
        width: 180px;
        background-image: url('<?php echo $template_path ?>/images/themeboxes/box_top.png');
        font-family: Verdana;
        font-weight: bold;
        color: #d5c3af;
        line-height: 65px;
    }
    .discord_bottom{
        height: 30px;
        width: 180px;
        margin-top: -20px;
        background-image: url('<?php echo $template_path ?>/images/themeboxes/box_bottom.png');
    }
    .discord_content{
        padding: 0px 10px;
        width: 160px;
        height: 40px;
        background-image: url('<?php echo $template_path ?>/images/themeboxes/box_bg.png');
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .discord_button{
        height: 30px;
        width: 148px;
        border: 0;
        background: url('<?php echo $template_path ?>/images/themeboxes/button.png');
        font-family: Verdana;
        font-weight: 100;
        color: #d5c3af;
        font-size: 12px;
        cursor: pointer;
    }
    .discord_button:hover{
        background: url('<?php echo $template_path ?>/images/themeboxes/button_over.png');
        color: #fff;
    }
</style>
<div class="eclipse-rightbox eclipse-discord-box discord">
    <div class="eclipse-rightbox-title discord_header"><?= htmlspecialchars(t('box.discord.title')) ?></div>
    <div class="eclipse-rightbox-content discord_content">
        <img class="eclipse-discord-icon" src="<?php echo $template_path ?>/images/global/header/icon-discord.svg" alt="<?= htmlspecialchars(t('box.discord.title')) ?>">
        <div class="eclipse-discord-text"><?= htmlspecialchars(t('box.discord.text')) ?></div>
        <a href="<?php echo $config['discord_link']; ?>" target="new">
            <button type="button" class="discord_button"><?= htmlspecialchars(t('box.discord.button')) ?></button>
        </a>
    </div>
    <div class="discord_bottom"></div>
</div>
