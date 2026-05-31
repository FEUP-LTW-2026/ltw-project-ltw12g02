<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/equipment.class.php');

function getAdminEquipmentStatusClass(string $status): string {
    $status = strtolower($status);

    if ($status === 'available') {
        return 'available';
    }

    if ($status === 'maintenance') {
        return 'maintenance';
    }

    return 'unavailable';
}

function getAdminEquipmentImagePath(Equipment $item): ?string {
    $filename = 'equipment' . $item->getId() . '.png';
    $filesystemPath = __DIR__ . '/../assets/equipment/' . $filename;

    if (!file_exists($filesystemPath)) {
        return null;
    }

    return '../assets/equipment/' . $filename;
}

function groupAdminEquipmentByType(array $equipment): array {
    $groups = [];

    foreach ($equipment as $item) {
        $type = $item->getType();

        if (!isset($groups[$type])) {
            $groups[$type] = [];
        }

        $groups[$type][] = $item;
    }

    ksort($groups);

    return $groups;
}

function drawAdminEquipmentPage(array $equipment): void { ?>
    <main class="page-shell admin-page admin-equipment-page">

        <section class="hero-panel admin-manage-hero">
            <p class="admin-label">PowerPIT Admin</p>

            <h1>Manage Equipment</h1>

            <p>
                Manage equipment in the main training area. Add new items, upload photos,
                update availability status and remove equipment from the platform.
            </p>
        </section>

        <section class="section-card card admin-equipment-add-card">
            <div class="admin-equipment-add-intro">
                <p class="admin-label">Inventory</p>
                <h2>Add equipment</h2>
                <p>
                    Register a new item, define its quantity and optionally upload a PNG photo.
                </p>
            </div>

            <form
                class="toolbar-form admin-equipment-form"
                action="../actions/action_add_equipment.php"
                method="post"
                enctype="multipart/form-data"
            >
                <label>
                    Name
                    <input
                        type="text"
                        name="name"
                        placeholder="Example: Treadmill"
                        required
                    >
                </label>

                <label>
                    Type
                    <input
                        type="text"
                        name="type"
                        placeholder="Example: Cardio"
                        required
                    >
                </label>

                <label>
                    Quantity
                    <input
                        type="number"
                        name="quantity"
                        min="0"
                        value="1"
                        required
                    >
                </label>

                <label>
                    Status
                    <select name="status" required>
                        <option value="available">Available</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </label>

                <label class="admin-equipment-photo-field">
                    Photo
                    <input
                        type="file"
                        name="photo"
                        accept="image/png"
                    >
                </label>

                <div class="admin-form-actions">
                    <button type="submit" class="btn small light">
                        Add equipment
                    </button>
                </div>
            </form>
        </section>

        <section class="admin-equipment-section">
            <header class="admin-section-header admin-equipment-main-header">
                <div>
                    <h2>Current equipment</h2>
                </div>

                <p>Equipment grouped by training type.</p>
            </header>

            <?php if (empty($equipment)) { ?>
                <article class="card admin-empty-card">
                    <h3>No equipment registered</h3>
                    <p>There are no equipment items in the system yet.</p>
                </article>
            <?php } else { ?>
                <div class="equipment_groups admin-equipment-groups">
                    <?php foreach (groupAdminEquipmentByType($equipment) as $type => $items) { ?>
                        <?php drawAdminEquipmentGroup($type, $items); ?>
                    <?php } ?>
                </div>
            <?php } ?>
        </section>

    </main>
<?php } ?>


<?php
function drawAdminEquipmentGroup(string $type, array $items): void { ?>
    <section class="content-group equipment_group admin-equipment-group">
        <header class="equipment_group_header">
            <div>
                <p class="classes_label"><?= htmlspecialchars($type) ?></p>
            </div>

            <h2><?= htmlspecialchars((string)count($items)) ?> items</h2>
        </header>

        <div class="equipment_gallery admin-equipment-grid">
            <?php foreach ($items as $item) {
                drawAdminEquipmentCard($item);
            } ?>
        </div>
    </section>
<?php } ?>


<?php
function drawAdminEquipmentCard(Equipment $item): void {
    $statusClass = getAdminEquipmentStatusClass($item->getStatus());
    $imagePath = getAdminEquipmentImagePath($item);
?>
    <article class="admin-equipment-item">

        <div class="image-card equipment_gallery_card">
            <?php if ($imagePath !== null) { ?>
                <img
                    src="<?= htmlspecialchars($imagePath) ?>"
                    alt="<?= htmlspecialchars($item->getName()) ?>"
                >
            <?php } else { ?>
                <div class="admin-equipment-placeholder">
                    <i class="fa fa-th" aria-hidden="true"></i>
                </div>
            <?php } ?>

            <div class="equipment_gallery_overlay">
                <span class="status-pill equipment_status <?= htmlspecialchars($statusClass) ?>">
                    <?= htmlspecialchars($item->getStatus()) ?>
                </span>

                <div>
                    <p><?= htmlspecialchars($item->getType()) ?></p>
                    <h2><?= htmlspecialchars($item->getName()) ?></h2>
                </div>

                <strong>
                    <?= htmlspecialchars((string)$item->getQuantity()) ?> units
                </strong>
            </div>
        </div>

        <div class="admin-equipment-controls">
            <form
                action="../actions/action_update_equipment_status.php"
                method="post"
            >
                <input
                    type="hidden"
                    name="equipment_id"
                    value="<?= htmlspecialchars((string)$item->getId()) ?>"
                >

                <select name="status" required>
                    <option
                        value="available"
                        <?= $item->getStatus() === 'available' ? 'selected' : '' ?>
                    >
                        Available
                    </option>

                    <option
                        value="maintenance"
                        <?= $item->getStatus() === 'maintenance' ? 'selected' : '' ?>
                    >
                        Maintenance
                    </option>

                    <option
                        value="unavailable"
                        <?= $item->getStatus() === 'unavailable' ? 'selected' : '' ?>
                    >
                        Unavailable
                    </option>
                </select>

                <button type="submit" class="btn small light">
                    Save
                </button>
            </form>

            <form
                class="admin-equipment-delete-form"
                action="../actions/action_delete_equipment.php"
                method="post"
                onsubmit="return confirm('Are you sure you want to remove this equipment?');"
            >
                <input
                    type="hidden"
                    name="equipment_id"
                    value="<?= htmlspecialchars((string)$item->getId()) ?>"
                >

                <button type="submit" class="btn small danger">
                    Remove
                </button>
            </form>
        </div>

    </article>
<?php } ?>