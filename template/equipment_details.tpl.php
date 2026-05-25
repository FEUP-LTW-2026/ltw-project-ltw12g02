<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
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

function drawEquipmentDetailsPage(Equipment $equipment, Session $session, int $availableQuantity): void {
    $statusClass = getEquipmentDetailsStatusClass($equipment->getStatus());
    $image = 'equipment' . $equipment->getId() . '.png';

    $canReserve = $session->isLoggedIn()
        && $session->getRole() === 'member'
        && strtolower($equipment->getStatus()) === 'available';
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
                        View the current availability and reserve this equipment for your training session.
                    </p>
                </div>

                <div class="equipment_details_badges">
                    <span class="equipment_status <?= htmlspecialchars($statusClass) ?>">
                        <?= htmlspecialchars($equipment->getStatus()) ?>
                    </span>

                    <strong>
                        <?= htmlspecialchars((string)$availableQuantity) ?> available now
                    </strong>

                    <?php if ($canReserve) { ?>
                        <button
                            type="button"
                            class="btn small light"
                            data-dialog-target="equipment-reservation-dialog"
                        >
                            Reserve equipment
                        </button>
                    <?php } elseif (!$session->isLoggedIn()) { ?>
                        <a class="btn small light" href="../pages/login.php">
                            Login to reserve
                        </a>
                    <?php } elseif ($session->getRole() !== 'member') { ?>
                        <span class="equipment_reservation_hint">
                            Only members can reserve equipment.
                        </span>
                    <?php } ?>
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
                        <dt>Total quantity</dt>
                        <dd><?= htmlspecialchars((string)$equipment->getQuantity()) ?></dd>
                    </div>

                    <div>
                        <dt>Available now</dt>
                        <dd><?= htmlspecialchars((string)$availableQuantity) ?></dd>
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
                        This equipment is currently available. Members can reserve it for a selected
                        time slot in the main training area.
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

        <?php if ($canReserve) {
            drawEquipmentReservationDialog($equipment, $availableQuantity);
        } ?>

    </main>
<?php } ?>


<?php
function drawEquipmentReservationDialog(Equipment $equipment, int $availableQuantity): void {
    $minimumDateTime = date('Y-m-d\TH:i');
?>
    <dialog id="equipment-reservation-dialog" class="popup-dialog">
        <section class="card popup-card">
            <button
                type="button"
                class="popup-close"
                data-dialog-close
                aria-label="Close reservation dialog"
            >
                &times;
            </button>

            <header class="popup-header">
                <p class="profile-member-card-label">PowerPIT Equipment</p>
                <h1>Reserve Equipment</h1>
                <p>Choose the date, start time and duration of your reservation.</p>
            </header>

            <dl>
                <div class="card-dl-row">
                    <dt>Equipment</dt>
                    <dd><?= htmlspecialchars($equipment->getName()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Type</dt>
                    <dd><?= htmlspecialchars($equipment->getType()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Total units</dt>
                    <dd><?= htmlspecialchars((string)$equipment->getQuantity()) ?></dd>
                </div>

                <div class="card-dl-row">
                    <dt>Available now</dt>
                    <dd><?= htmlspecialchars((string)$availableQuantity) ?></dd>
                </div>
            </dl>

            <form
                class="popup-form"
                action="../actions/action_equipment_reservation.php"
                method="post"
            >
                <input
                    type="hidden"
                    name="equipment_id"
                    value="<?= htmlspecialchars((string)$equipment->getId()) ?>"
                >

                <label>
                    Reservation date and time
                    <input
                        type="datetime-local"
                        name="reservation_datetime"
                        min="<?= htmlspecialchars($minimumDateTime) ?>"
                        required
                    >
                </label>

                <label>
                    Reservation duration
                    <select name="duration" required>
                        <option value="30">30 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="90">1 hour 30 minutes</option>
                        <option value="120">2 hours</option>
                    </select>
                </label>

                <div class="popup-actions">
                    <button
                        type="button"
                        class="btn small"
                        data-dialog-close
                    >
                        Cancel
                    </button>

                    <button type="submit" class="btn small light">
                        Confirm reservation
                    </button>
                </div>
            </form>
        </section>
    </dialog>
<?php } ?>


<?php
function drawEquipmentNotFoundPage(): void { ?>
    <main class="equipment_details_page">
        <section class="card equipment_not_found">
            <h1>Equipment not found</h1>

            <p>
                The equipment you are trying to view does not exist or is no longer available.
            </p>

            <a class="btn small light" href="../pages/equipment.php">
                Back to equipment
            </a>
        </section>
    </main>
<?php } ?>