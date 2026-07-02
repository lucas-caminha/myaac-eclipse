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
$creatureName = ucwords(strtolower(trim($creature['boostname'])));
$bossName = ucwords(strtolower(trim($boss['boostname'])));
$creatureLink = getLink('monsters') . '?name=' . rawurlencode($creatureName);
$bossLink = getLink('bosses') . '?name=' . rawurlencode($bossName);
$boostedSponsorLink = getLink('boosted-sponsor');
?>
<div class="eclipse-rightbox eclipse-boosted">
    <div class="eclipse-rightbox-title"><?= htmlspecialchars(t('box.boosted.title')) ?></div>
    <div class="eclipse-rightbox-content eclipse-boosted-grid">
        <div class="eclipse-boosted-item eclipse-boosted-boss">
            <a class="eclipse-boosted-card-link" href="<?= htmlspecialchars($bossLink) ?>" title="<?= htmlspecialchars(t('box.boosted.open_boss', ['name' => $bossName])) ?>">
                <div class="eclipse-boosted-frame"><img src="<?= $bossImage ?>" alt="<?= htmlspecialchars(t('box.boosted.boss')) ?> boosted"></div>
                <strong><?= htmlspecialchars(t('box.boosted.boss')) ?></strong>
                <span><?= htmlspecialchars($bossName) ?></span>
            </a>
        </div>
        <div class="eclipse-boosted-item eclipse-boosted-creature">
            <a class="eclipse-boosted-card-link" href="<?= htmlspecialchars($creatureLink) ?>" title="<?= htmlspecialchars(t('box.boosted.open_creature', ['name' => $creatureName])) ?>">
                <div class="eclipse-boosted-frame"><img src="<?= $creatureImage ?>" alt="<?= htmlspecialchars(t('box.boosted.creature')) ?> boosted"></div>
                <strong><?= htmlspecialchars(t('box.boosted.creature')) ?></strong>
                <span><?= htmlspecialchars($creatureName) ?></span>
            </a>
        </div>
        <div class="eclipse-boosted-footer">
            <a class="eclipse-boosted-action" href="<?= htmlspecialchars($boostedSponsorLink) ?>"><?= htmlspecialchars(t('box.boosted.next')) ?></a>
        </div>
    </div>
</div>
