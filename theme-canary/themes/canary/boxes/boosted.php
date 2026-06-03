<?php
if (!$db->hasTable('boosted_creature') || !$db->hasTable('boosted_boss')) {
    return;
}

$creature = $db->query("SELECT `boostname`, `looktype`, `lookfeet`, `looklegs`, `lookhead`, `lookbody`, `lookaddons`, `lookmount` FROM `boosted_creature`")->fetch();
$boss = $db->query("SELECT `boostname`, `looktypeEx`, `looktype`, `lookfeet`, `looklegs`, `lookhead`, `lookbody`, `lookaddons`, `lookmount` FROM `boosted_boss`")->fetch();

if (!$creature || !$boss) {
    return;
}

$creatureImage = $config['outfit_images_url'] . '?id=' . $creature['looktype'] . '&addons=' . $creature['lookaddons'] . '&head=' . $creature['lookhead'] . '&body=' . $creature['lookbody'] . '&legs=' . $creature['looklegs'] . '&feet=' . $creature['lookfeet'] . '&mount=' . $creature['lookmount'];
$bossImage = ((int)$boss['looktypeEx'] !== 0)
    ? $config['item_images_url'] . $boss['looktypeEx'] . '.gif'
    : $config['outfit_images_url'] . '?id=' . $boss['looktype'] . '&addons=' . $boss['lookaddons'] . '&head=' . $boss['lookhead'] . '&body=' . $boss['lookbody'] . '&legs=' . $boss['looklegs'] . '&feet=' . $boss['lookfeet'] . '&mount=' . $boss['lookmount'];
?>
<div class="eclipse-rightbox eclipse-boosted">
    <div class="eclipse-rightbox-title">BOOSTED</div>
    <div class="eclipse-rightbox-content eclipse-boosted-grid">
        <div class="eclipse-boosted-item eclipse-boosted-boss">
            <div class="eclipse-boosted-frame"><img src="<?= $bossImage ?>" alt="Boss boosted"></div>
            <strong>BOSS</strong>
            <span><?= ucwords(strtolower(trim($boss['boostname']))); ?></span>
        </div>
        <div class="eclipse-boosted-item eclipse-boosted-creature">
            <div class="eclipse-boosted-frame"><img src="<?= $creatureImage ?>" alt="Creature boosted"></div>
            <strong>CREATURE</strong>
            <span><?= ucwords(strtolower(trim($creature['boostname']))); ?></span>
        </div>
    </div>
</div>
