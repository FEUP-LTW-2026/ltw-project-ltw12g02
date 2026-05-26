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

function drawAdminEquipmentPage(array $equipment): void { ?>
    <main class="admin-users-page admin-equipment-page">

        <section class="admin-users-hero">
            <p class="admin-label">PowerPIT Admin</p>

            <h1>Manage Equipment</h1>

            <p>
                Manage equipment in the main training area. Add new items, upload photos,
                update availability status and remove equipment from the platform.
            </p>
        </section>

        <section class="card admin-edit-user-card">
            <header class="admin-section-header">
                <div>
                    <p class="admin-label">Inventory</p>
                    <h2>Add equipment</h2>
                </div>

                <p>Create a new equipment item available in the gym.</p>
            </header>

            <form
                class="admin-equipment-form"
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
                    Availability status
                    <select name="status" required>
                        <option value="available">Available</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="unavailable">Unavailable</option>
                    </select>
                </label>

                <label>
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

        <section class="card admin-edit-user-card">
            <header class="admin-section-header">
                <div>
                    <p class="admin-label">Equipment List</p>
                    <h2>Current equipment</h2>
                </div>

                <p>Update availability or remove equipment items.</p>
            </header>

            <?php if (empty($equipment)) { ?>
                <article class="admin-empty-card">
                    <h3>No equipment registered</h3>
                    <p>There are no equipment items in the system yet.</p>
                </article>
            <?php } else { ?>
                <div class="admin-equipment-list">
                    <?php foreach ($equipment as $item) {
                        drawAdminEquipmentRow($item);
                    } ?>
                </div>
            <?php } ?>
        </section>

    </main>
<?php } ?>


<?php
function drawAdminEquipmentRow(Equipment $item): void {
    $statusClass = getAdminEquipmentStatusClass($item->getStatus());
    $imagePath = getAdminEquipmentImagePath($item);
?>
    <article class="admin-equipment-row">

        <div class="admin-equipment-main">
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

            <div>
                <strong><?= htmlspecialchars($item->getName()) ?></strong>

                <div class="admin-equipment-meta">
                    <span class="admin-tag">
                        <?= htmlspecialchars($item->getType()) ?>
                    </span>

                    <span>
                        <?= htmlspecialchars((string)$item->getQuantity()) ?> units
                    </span>
                </div>
            </div>
        </div>

        <span class="equipment_status <?= htmlspecialchars($statusClass) ?>">
            <?= htmlspecialchars($item->getStatus()) ?>
        </span>

        <form
            class="admin-inline-form"
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

            <button type="submit" class="btn small">
                Save
            </button>
        </form>

        <form
            class="admin-delete-form"
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

    </article>
<?php } ?>