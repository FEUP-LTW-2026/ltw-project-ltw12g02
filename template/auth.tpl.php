<?php
declare(strict_types = 1);

function drawLoginForm(array $messages): void { ?>
    <main>
        <section class="form_page">
            <header class="form_intro">
                <h1>Log In</h1>
                <p>Welcome back! Access your PowerPIT account.</p>
            </header>

            <div class="form_box">
                <form class="powerpit_form" action="../actions/action_login.php" method="post">
                    <label>
                        Email:
                        <input type="email" name="email" required>
                    </label>

                    <label>
                        Password:
                        <input type="password" name="password" required>
                    </label>

                    <button type="submit">Log in</button>
                </form>

                <p class="form_switch">
                    Don't have an account?
                    <a class="form_link" href="register.php">Register</a>
                </p>
            </div>
        </section>
    </main>
<?php } ?>


<?php
function drawRegisterForm(array $messages): void { ?>
    <main>
        <section class="form_page">
            <header class="form_intro">
                <h1>Create Account</h1>
                <p>Join PowerPIT and start training with real energy.</p>
            </header>

            <div class="form_box">
                <form class="powerpit_form" action="actions/action_create_account.php" method="post">
                    <label>
                        Full Name:
                        <input type="text" name="name" required>
                    </label>

                    <label>
                        Username:
                        <input type="text" name="username" required>
                    </label>

                    <label>
                        Email:
                        <input type="email" name="email" required>
                    </label>

                    <label>
                        Password:
                        <input type="password" name="password" required>
                    </label>

                    <label>
                        Confirm Password:
                        <input type="password" name="confirm_password" required>
                    </label>

                    <button type="submit">Create account</button>
                </form>

                <p class="form_switch">
                    Already have an account?
                    <a class="form_link" href="login.php">Log In</a>
                </p>
            </div>
        </section>
    </main>
<?php } ?>