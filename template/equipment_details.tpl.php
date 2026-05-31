<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../utils/session.php');
require_once(__DIR__ . '/../database/users.class.php');
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

function drawEquipmentAvailabilityBadge(Equipment $equipment, int $availableQuantity): void {
    $status = strtolower($equipment->getStatus());

    if ($status === 'available') { ?>
        <strong>
            <?= htmlspecialchars((string)$availableQuantity) ?> available now
        </strong>
    <?php } elseif ($status === 'maintenance') { ?>
        <strong>
            Temporarily under maintenance
        </strong>
    <?php } else { ?>
        <strong>
            Currently unavailable
        </strong>
    <?php }
}

function drawEquipmentDetailsList(Equipment $equipment, int $availableQuantity): void {
    $status = strtolower($equipment->getStatus());
?>
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
            <dt>Status</dt>
            <dd><?= htmlspecialchars($equipment->getStatus()) ?></dd>
        </div>

        <?php if ($status === 'available') { ?>
            <div>
                <dt>Total quantity</dt>
                <dd><?= htmlspecialchars((string)$equipment->getQuantity()) ?></dd>
            </div>

            <div>
                <dt>Available now</dt>
                <dd><?= htmlspecialchars((string)$availableQuantity) ?></dd>
            </div>
        <?php } elseif ($status === 'maintenance') { ?>
            <div>
                <dt>Availability</dt>
                <dd>Paused for maintenance</dd>
            </div>

            <div>
                <dt>Reservation</dt>
                <dd>Not available right now</dd>
            </div>
        <?php } else { ?>
            <div>
                <dt>Availability</dt>
                <dd>Not available for reservation</dd>
            </div>
        <?php } ?>
    </dl>
<?php }

function drawEquipmentDetailsPage(Equipment $equipment, Session $session, ?Users $user, int $availableQuantity): void {
    $status = strtolower($equipment->getStatus());
    $statusClass = getEquipmentDetailsStatusClass($equipment->getStatus());
    $image = 'equipment' . $equipment->getId() . '.png';

    $isLoggedIn = $session->isLoggedIn() && $user !== null;
    $isMember = $isLoggedIn && $user->getRole() === 'member';

    $hasPremiumPlan = $isLoggedIn
        && method_exists($user, 'getPlan')
        && $user->getPlan() === 'premium';

    $canReserve = $isMember
        && $hasPremiumPlan
        && $status === 'available';
?>
    <main class="details-page equipment_details_page">

        <section class="image-hero equipment_details_hero">
            <img
                src="../assets/equipment/<?= htmlspecialchars($image) ?>"
                alt="<?= htmlspecialchars($equipment->getName()) ?>"
            >

            <div class="equipment_details_hero_overlay">
                <div>
                    <p class="classes_label"><?= htmlspecialchars($equipment->getType()) ?></p>

                    <h1><?= htmlspecialchars($equipment->getName()) ?></h1>

                    <?php if ($status === 'available') { ?>
                        <p>
                            View the current availability and reserve this equipment for your training session.
                        </p>
                    <?php } elseif ($status === 'maintenance') { ?>
                        <p>
                            This equipment is temporarily under maintenance and cannot be reserved right now.
                        </p>
                    <?php } else { ?>
                        <p>
                            This equipment is currently unavailable for reservations.
                        </p>
                    <?php } ?>
                </div>

                <div class="equipment_details_badges">
                    <span class="status-pill equipment_status <?= htmlspecialchars($statusClass) ?>">
                        <?= htmlspecialchars($equipment->getStatus()) ?>
                    </span>

                    <?php drawEquipmentAvailabilityBadge($equipment, $availableQuantity); ?>

                    <?php if ($canReserve) { ?>
                        <button
                            type="button"
                            class="btn small light"
                            data-dialog-target="equipment-reservation-dialog"
                        >
                            Reserve equipment
                        </button>
                    <?php } elseif (!$isLoggedIn) { ?>
                        <a class="btn small light" href="../pages/login.php">
                            Login to reserve
                        </a>
                    <?php } elseif (!$isMember) { ?>
                        <span class="equipment_reservation_hint">
                            Only members can reserve equipment.
                        </span>
                    <?php } elseif (!$hasPremiumPlan) { ?>
                        <span class="equipment_reservation_hint">
                            Upgrade your plan to reserve equipment.
                        </span>
                    <?php } ?>
                </div>
            </div>
        </section>

        <section class="equipment_details_content">

            <article class="section-card card equipment_details_card">
                <p class="classes_label">Details</p>
                <h2>Equipment information</h2>

                <?php drawEquipmentDetailsList($equipment, $availableQuantity); ?>
            </article>

            <article class="section-card card equipment_details_card equipment_details_note">
                <p class="classes_label">Usage</p>
                <h2>Usage notes</h2>

                <?php if ($status === 'available') { ?>
                    <p>
                        This equipment is currently available. Premium members can reserve it for a selected
                        time slot in the main training area.
                    </p>
                <?php } elseif ($status === 'maintenance') { ?>
                    <p>
                        This equipment is being checked or repaired by the team. Reservations are paused
                        until it is marked as available again.
                    </p>
                <?php } else { ?>
                    <p>
                        This equipment is not available for use at the moment. Please choose another option
                        or ask a staff member for more information.
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
    <dialog id="equipment-reservation-dialog" class="modal popup-dialog">
        <section class="modal-card card popup-card">
            <button
                type="button"
                class="popup-close"
                data-dialog-close
                aria-label="Close reservation dialog"
            >
                &times;
            </button>

            <header class="popup-header">
                <p class="title-label">PowerPIT Equipment</p>
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
                class="form-stack popup-form"
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

                <div class="actions-row popup-actions">
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
    <main class="details-page equipment_details_page">
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