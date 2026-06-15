<style>
    .rank{
        width: 180px;
        max-height: 360px;
    }
    .rank_header{
        height: 45px;
        width: 180px;
        background-image: url('<?= $template_path; ?>/images/themeboxes/box_top.png');
        font-family: Verdana;
        font-weight: bold;
        color: #d5c3af;
        line-height: 65px;
    }
    .rank_bottom{
        height: 30px;
        width: 180px;
        margin-top: -20px;
        background-image: url('<?php echo $template_path ?>/images/themeboxes/box_bottom.png');
    }
    .rank_content{
        padding: 0px 10px;
        width: 160px;
        max-height: 290px;
        background-image: url('<?php echo $template_path ?>/images/themeboxes/box_bg.png');
    }
    .rank_player{
        font-family: Verdana;
        color: #d5c3af;
        text-align: left;
        display: flex;
        align-items: center;
        padding: 10px 5px;
    }
    .rank_outfit{
        position: absolute;
        width: 64px;
        height: 64px;
        background-position: bottom right;
        left: -15px;
        margin-top: -30px;
    }
    .rank_text{
        margin-left: 45px;
        text-overflow: ellipsis;
        overflow: hidden;
        white-space: nowrap;
    }
    .rank_text a{
        text-decoration: none;
        color: #d5c3af;
    }
    .rank_button{
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
    .rank_button:hover{
        background: url('<?php echo $template_path ?>/images/themeboxes/button_over.png');
        color: #fff;
    }
</style>
<div class="rank">
    <div class="rank_header">Highscores</div>
    <div class="rank_content">
        <?php
        $topPlayers = getTopPlayers(5);
        $vipPlayerIds = [];
        $topPlayerIds = array_values(array_filter(array_map(static function ($player) {
            return isset($player['id']) ? (int) $player['id'] : 0;
        }, $topPlayers)));

        if (!empty($topPlayerIds)) {
            $vipRows = $db->query(
                'SELECT p.`id` FROM `players` p JOIN `accounts` a ON a.`id` = p.`account_id` ' .
                'WHERE p.`id` IN (' . implode(',', $topPlayerIds) . ') AND a.`premdays` > 0'
            )->fetchAll();

            foreach ($vipRows as $vipRow) {
                $vipPlayerIds[(int) $vipRow['id']] = true;
            }
        }

        foreach($topPlayers as $player){
            $outfit_url = '';
            if ($config['online_outfit']){
                $outfit_url = $config['outfit_images_url'] . '?id=' . $player['looktype'] . ( !empty( $player['lookaddons'] ) ? '&addons=' . $player['lookaddons'] : '' ) . '&head=' . $player['lookhead'] . '&body=' . $player['lookbody'] . '&legs=' . $player['looklegs'] . '&feet=' . $player['lookfeet'];
                $player['outfit'] = $outfit_url;
            }
            $player_voc = $config['vocations'][$player['vocation']];
            $vocationName = strtolower($player_voc);
            $vocationBanner = '';

            foreach (['knight', 'paladin', 'monk', 'sorcerer', 'druid'] as $baseVocation) {
                if (str_contains($vocationName, $baseVocation)) {
                    $vocationBanner = $baseVocation;
                    break;
                }
            }
        ?>
        <div class="rank_player<?= $vocationBanner !== '' ? ' is-vocation-' . $vocationBanner : ''; ?><?= isset($vipPlayerIds[(int) $player['id']]) ? ' is-vip-account' : ''; ?>">
            <?php if (isset($vipPlayerIds[(int) $player['id']])) { ?>
                <span class="rank-vip-ribbon">VIP</span>
            <?php } ?>
            <div class="rank_outfit" style="background-image: url('<?php echo $player['outfit'] ?>')"></div>
            <div class="rank_text">
                <a href="<?php echo getPlayerLink($player['name'], false) ?>"><b><?php echo $player['name'] ?></b></a><br>
                <small>Level: <?php echo $player['level'] ?> / <?php echo $player_voc ?></small>
            </div>
        </div>
        <?php } ?>
        <a href="<?= getLink('highscores'); ?>">
            <button type="button" class="rank_button">View Highscores</button>
        </a>
    </div>
    <div class="rank_bottom"></div>
</div>
