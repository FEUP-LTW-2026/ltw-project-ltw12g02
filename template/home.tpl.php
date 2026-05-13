<?php
declare(strict_types = 1);

function drawHomepage(): void { ?>
    <main>
        <section class="image-bg">
            <div id="main-header">
                <h6 class="highlight-text">Power. Discipline. Results.</h6>
                <h1>Train with <br><span class="highlight-text">real energy</span></h1>
                <p>In PowerPIT you find training, focus and the right environment to evolve. Group classes, bodybuilding zones, professional trainers and modern equipment.</p>

                <div class="index-buttons">
                    <a href="register.php" class="btn small light">Sign up</a>
                    <a href="#classes" class="btn small">Explore classes</a>  
                </div> 
            </div>
        </section>

        <section class="flex-row light" id="aboutus">
            <article class="flex-item main">
                <h1>About us</h1>
                <p>We are PowerPIT, a space built for people who want to train with purpose.
                    From beginners to experienced athletes, we provide the environment, support, and energy you need to improve every day.
                    More than a gym, we’re a community focused on progress.</p>
                <a href="#aboutus" class="btn small light">Learn more</a>
            </article>

            <aside class="flex-item side">
                <img src="https://picsum.photos/400/400" alt="GymGoers" height="400" width="400">
            </aside> 
        </section>

        <section class="flex-row dark" id="classes">
            <aside class="flex-item side">
                <img src="https://picsum.photos/400/400" alt="GymGoers" height="400" width="400">
            </aside>

            <article class="flex-item main">
                <h1>Classes</h1>
                <p>Our classes are designed to challenge, motivate, and deliver results.
                    From high-intensity workouts to strength and mobility sessions, there’s something for every level and goal.
                    Train with others, stay consistent, and push your limits.</p>
                <a href="#classes" class="btn small light">Learn more</a>
            </article> 
        </section>

        <section class="flex-row light" id="trainers">
            <article class="flex-item main">
                <h1>Trainers</h1>
                <p>Our certified trainers are here to guide you every step of the way.
                    With experience across strength, conditioning, and performance, they help you train smarter and safer.
                    Get personalized support and real results.</p>
                <a href="#trainers" class="btn small light">Learn more</a>
            </article>

            <aside class="flex-item side">
                <img src="https://picsum.photos/400/400" alt="GymGoers" height="400" width="400">
            </aside> 
        </section>

        <section class="flex-row dark" id="equipment">
            <aside class="flex-item side">
                <img src="https://picsum.photos/400/400" alt="GymGoers" height="400" width="400">
            </aside>

            <article class="flex-item main">
                <h1>Equipment</h1>
                <p>Train with modern, high-quality equipment built for performance.
                    From free weights to advanced machines, everything is designed to support your progress.
                    No waiting, no compromises, just focus on your workout.</p>
                <a href="#equipment" class="btn small light">Learn more</a>
            </article> 
        </section>
    </main>
<?php } ?>