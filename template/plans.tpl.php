<?php
declare(strict_types = 1);

require_once(__DIR__ . '/../database/users.class.php');

function drawPlans($db, $user): void { ?>
    <main class="marketing-page plans">
        <section class="content-section classes_carousel_section">
            <header>
                <p class="classes_label">Find the plans that suits your needs the best</p>
                <h1>Membership plans</h1>
            </header>

            <div class="grid trainers_grid">
                <article class="catalog-card card trainer_card">
                        <div class="trainer_card_content">
                            <span class="plans-corner-icon"><i class="fa fa-user" aria-hidden="true"></i></span>
                            <h2>Basic</h2>
                            <p class="trainer_bio">Startup plan</p>

                            <p class="plans-price">00&euro;<span>/month</span></p>

                            <ul class="trainer_certifications">
                                <li>
                                    <span class="plans-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    Gyms access.
                                </li>
                                <li>
                                    <span class="plans-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    Locker room access.
                                </li>
                                <li>
                                    <span class="plans-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    Free fitness assessment.
                                </li>
                            </ul>

                            <form
                                action="../actions/action_plans.php"
                                method="post"
                            >

                                <input
                                    type="hidden"
                                    name="membership"
                                    value="basic"
                                >

                                <?php if ($user->getPlan() === 'basic') { ?>
                                    <span class="btn light small disabled">Current Plan</span>
                                <?php } else { ?>
                                    <button 
                                        type="submit" 
                                        class="btn small light"
                                    >
                                        Choose Plan
                                    </button>
                                <?php } ?>
                            </form>
                        </div>
                </article>


                <article class="catalog-card card trainer_card">
                        <div class="trainer_card_content">
                            <span class="plans-corner-icon"><i class="fa fa-plus" aria-hidden="true"></i></span>
                            <h2>Plus</h2>
                            <p class="trainer_bio">Enhanced plan</p>

                            <p class="plans-price">00&euro;<span>/month</span></p>

                            <ul class="trainer_certifications">
                                <li>
                                    <span class="plans-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    Everything in <strong>Basic</strong>.
                                </li>
                                <li>
                                    <span class="plans-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    Themed group classes.
                                </li>
                                <li>
                                    <span class="plans-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    Class schedule and history.
                                </li>
                            </ul>

                            <form
                                action="../actions/action_plans.php"
                                method="post"
                            >

                                <input
                                    type="hidden"
                                    name="membership"
                                    value="plus"
                                >

                                <?php if ($user->getPlan() === 'plus') { ?>
                                    <span class="btn light small disabled">Current Plan</span>
                                <?php } else { ?>
                                    <button 
                                        type="submit" 
                                        class="btn small light"
                                    >
                                        Choose Plan
                                    </button>
                                <?php } ?>
                            </form>
                        </div>
                </article>


                <article class="catalog-card card trainer_card">
                        <div class="trainer_card_content">
                            <span class="plans-corner-icon"><i class="fa fa-diamond" aria-hidden="true"></i></span>
                            <h2>Premium</h2>
                            <p class="trainer_bio">Professional plan</p>

                            <p class="plans-price">00&euro;<span>/month</span></p>

                            <ul class="trainer_certifications">
                                <li>
                                    <span class="plans-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    Everything in <strong>Plus</strong>.
                                </li>
                                <li>
                                    <span class="plans-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    Personal classes with a certified trainer.
                                </li>
                                <li>
                                    <span class="plans-icon"><i class="fa fa-check" aria-hidden="true"></i></span>
                                    Equipment Reservation.
                                </li>
                            </ul>

                            <form
                                action="../actions/action_plans.php"
                                method="post"
                            >

                                <input
                                    type="hidden"
                                    name="membership"
                                    value="premium"
                                >

                                <?php if ($user->getPlan() === 'premium') { ?>
                                    <span class="btn light small disabled">Current Plan</span>
                                <?php } else { ?>
                                    <button 
                                        type="submit" 
                                        class="btn small light"
                                    >
                                        Choose Plan
                                    </button>
                                <?php } ?>
                            </form>
                        </div>
                </article>
            </div>
            
        </section>
    </main>
<?php }