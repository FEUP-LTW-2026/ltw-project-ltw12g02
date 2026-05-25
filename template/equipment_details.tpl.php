<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/equipment.class.php');

function getEquipmentDetailsStatusClass(string $status): string {
    $status = strtolower($status);

    if ($status === 'available') {
        return 'available';
    }

    if ($status === 'maintenance') {
        return 'maintenance';
    }

    return 'unavailable';
}

function drawEquipmentDetailsPage(Equipment $equipment): void {
    $statusClass = getEquipmentDetailsStatusClass($equipment->getStatus());
    $image = 'equipment' . $equipment->getId() . '.png';
?>
    <main class="equipment_details_page">

        <section class="equipment_details_hero">
            <img
                src="../assets/equipment/<?= htmlspecialchars($image) ?>"
                alt="<?= htmlspecialchars($equipment->getName()) ?>"
            >

            <div class="equipment_details_hero_overlay">
                <div>
                    <p class="classes_label"><?= htmlspecialchars($equipment->getType()) ?></p>

                    <h1><?= htmlspecialchars($equipment->getName()) ?></h1>

                    <p>
                        View the current availability and details of this equipment before planning your workout.
                    </p>
                </div>

                <div class="equipment_details_badges">
                    <span class="equipment_status <?= htmlspecialchars($statusClass) ?>">
                        <?= htmlspecialchars($equipment->getStatus()) ?>
                    </span>

                    <strong><?= htmlspecialchars((string)$equipment->getQuantity()) ?> units</strong>
                </div>
            </div>
        </section>

        <section class="equipment_details_content">

            <article class="card equipment_details_card">
                <p class="classes_label">Details</p>
                <h2>Equipment information</h2>

                <dl class="equipment_details_list">
                    <div>
                        <dt>Name</dt>
                        <dd><?= htmlspecialchars($equipment->getName()) ?></dd>
                    </div>

                    <div>
                        <dt>Type</dt>
                        <dd><?= htmlspecialchars($equipment->getType()) ?></dd>
                    </div>

                    <div>
                        <dt>Quantity</dt>
                        <dd><?= htmlspecialchars((string)$equipment->getQuantity()) ?></dd>
                    </div>

                    <div>
                        <dt>Status</dt>
                        <dd><?= htmlspecialchars($equipment->getStatus()) ?></dd>
                    </div>
                </dl>
            </article>

            <article class="card equipment_details_card equipment_details_note">
                <p class="classes_label">Usage</p>
                <h2>Usage notes</h2>

                <?php if (strtolower($equipment->getStatus()) === 'available') { ?>
                    <p>
                        This equipment is currently available and can be used during your workout,
                        depending on gym occupancy.
                    </p>
                <?php } elseif (strtolower($equipment->getStatus()) === 'maintenance') { ?>
                    <p>
                        This equipment is currently under maintenance. Please choose another option
                        until it becomes available again.
                    </p>
                <?php } else { ?>
                    <p>
                        This equipment is currently unavailable. Please check again later or ask a
                        staff member for more information.
                    </p>
                <?php } ?>
            </article>

        </section>

    </main>
<?php } ?>


<?php
function drawEquipmentNotFoundPage(): void { ?>
    <main class="equipment_details_page">
        <section class="card equipment_not_found">
            <h1>Equipment not found</h1>

            <p>
                The equipment you are trying to view does not exist or is no longer available.
            </p>

            <a class="button" href="../pages/equipment.php">Back to equipment</a>
        </section>
    </main>
<?php } ?>