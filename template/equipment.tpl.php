<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/equipment.class.php');

function drawEquipmentPage(array $equipment): void { ?>
    <main class="equipment_page">

        <section class="flex-row dark classes_feature">
            <article class="flex-item main">
                <p class="classes_label">PowerPIT Equipment</p>

                <h1>Train with the right equipment.</h1>

                <p>
                    From cardio machines to strength equipment and functional training tools,
                    PowerPIT gives you everything you need to train with intensity, safety and focus.
                </p>
            </article>

            <aside class="flex-item side">
                <img
                    src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1200&q=80"
                    alt="PowerPIT equipment"
                >
            </aside>
        </section>

        <section class="classes_carousel_section">
            <header>
                <p class="classes_label">Gym equipment</p>
                <h1>Our equipment</h1>
            </header>

            <?php if (empty($equipment)) { ?>
                <article class="card equipment_empty">
                    <h2>No equipment available</h2>
                    <p>There is no equipment registered at the moment.</p>
                </article>
            <?php } else { ?>
                <?php $equipmentByType = groupEquipmentByType($equipment); ?>

                <div class="equipment_groups">
                    <?php foreach ($equipmentByType as $type => $items) { ?>
                        <section class="equipment_group">
                            <header class="equipment_group_header">
                                <p class="classes_label"><?= htmlspecialchars($type) ?></p>
                                <h2><?= count($items) ?> items</h2>
                            </header>

                            <div class="grid equipment_grid">
                                <?php foreach ($items as $item) {
                                    drawEquipmentCard($item);
                                } ?>
                            </div>
                        </section>
                    <?php } ?>
                </div>
            <?php } ?>

        </section>
    </main>
<?php }

function groupEquipmentByType(array $equipment): array {
    $groups = [];

    foreach ($equipment as $item) {
        $type = $item->getType();

        if (!isset($groups[$type])) {
            $groups[$type] = [];
        }

        $groups[$type][] = $item;
    }

    return $groups;
}

function drawEquipmentCard(Equipment $item): void { ?>
    <article class="card equipment_card">
        <p class="profile-member-card-label">
            <?= htmlspecialchars($item->getStatus()) ?>
        </p>

        <h2><?= htmlspecialchars($item->getName()) ?></h2>

        <dl>
            <div class="card-dl-row">
                <dt>Type</dt>
                <dd><?= htmlspecialchars($item->getType()) ?></dd>
            </div>

            <div class="card-dl-row">
                <dt>Quantity</dt>
                <dd><?= htmlspecialchars((string)$item->getQuantity()) ?></dd>
            </div>

            <div class="card-dl-row">
                <dt>Status</dt>
                <dd><?= htmlspecialchars($item->getStatus()) ?></dd>
            </div>
        </dl>
    </article>
<?php } ?>