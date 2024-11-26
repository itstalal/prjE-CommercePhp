<div class="relative isolate px-6 pt-14 lg:px-8" style="margin-top: 130px;">

    <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden sm:-top-80" aria-hidden="true">
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="bg-gradient-to-br from-blue-900 to-gray-900 opacity-80 w-full h-[150vh]"></div>
                    <img src="assets/images/banner_discount.webp" alt="Luxury Watch Background"
                        class=" w-full h-[150vh] opacity-30 object-cover mix-blend-overlay mx-auto">
                </div>
                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div class="bg-gradient-to-br from-purple-900 to-black opacity-80 w-full h-[150vh]"></div>
                    <img src="assets/images/banner_disount2.webp" alt="Alternate Luxury Background"
                        class=" w-full h-[150vh] opacity-30 object-cover mix-blend-overlay mx-auto">
                </div>
                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div class="bg-gradient-to-br from-green-900 to-gray-700 opacity-80 w-full h-[150vh]"></div>
                    <img src="assets/images/discount3.webp" alt="Another Luxury Background"
                        class=" w-full h-[150vh] opacity-30 object-cover mix-blend-overlay mx-auto">
                </div>
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-3xl text-center py-32 sm:py-48 lg:py-56 text-white">
        <h1 class="text-5xl font-extrabold tracking-tight sm:text-7xl">
            <span class="block text-transparent sm:text-gray-400 distortion-text" data-distort="NOUVEAUX MODÈLES 2024">
                NOUVEAUX MODÈLES 2024
            </span>
            <br>
            <span class="block bg-clip-text bg-gradient-to-r from-yellow-400 to-red-600 distortion-text" style="color: gold;" data-distort="L’Harmonie des Contrastes">
                L’Harmonie des Contrastes
            </span>
        </h1>
    </div>
</div>


<div class="text-center mb-12">
    <h2 class="block text-transparent sm:text-gray-400 distortion-text fs-2">Votre Univers de Luxe</h2>
    <p class="block text-transparent sm:text-gray-400 distortion-text mt-2 fs-4">Choisissez entre nos parfums raffinés et nos montres d’exception.</p>
</div>

<!-- Cartes pour le choix -->
<div class="row justify-content-center gap-5 my-12 mt-4">
    <!-- Carte 1: Parfum -->
    <div class="col-12 col-md-5 col-lg-4">
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden hover:scale-105 transition transform duration-300">
            <img src="assets/images/perfum.webp" alt="Parfum" class="w-full h-56 object-cover">
            <div class="p-6 text-center">
                <h3 class="text-2xl font-semibold text-gray-800">Parfums</h3>
                <p class="mt-2 text-gray-600">Découvrez notre sélection exclusive de parfums.</p>
                <a href="<?= $router->generate('getParfums'); ?>" class="mt-4 inline-block  btn btn-secondary">Voir Plus</a>
            </div>
        </div>
    </div>

    <!-- Carte 2: Montre -->
    <div class="col-12 col-md-5 col-lg-4">
        <div class="bg-white shadow-2xl rounded-2xl overflow-hidden hover:scale-105 transition transform duration-300">
            <img src="assets/images/background_watch.webp" alt="Montre" class="w-full h-56 object-cover">
            <div class="p-6 text-center">
                <h3 class="text-2xl font-semibold text-gray-800">Montres</h3>
                <p class="mt-2 text-gray-600">Explorez nos modèles de montres de luxe.</p>
                <a href="<?= $router->generate('getWatches'); ?>" class="mt-4 inline-block btn btn-secondary">Voir Plus</a>
            </div>
        </div>
    </div>
</div>

<!-- Carrousel -->
<div class="container my-16" style="margin-top: 50px;">
    <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
        <!-- Indicateurs -->
        <div class="carousel-indicators">
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
        </div>

        <!-- Images du carrousel -->
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images/banner_discount.webp" class="d-block w-100 rounded-xl shadow-lg" alt="Banner 1">
            </div>
            <div class="carousel-item">
                <img src="assets/images/banner_disount2.webp" class="d-block w-100 rounded-xl shadow-lg" alt="Banner 2">
            </div>
        </div>

        <!-- Contrôles du carrousel -->
        <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Précédent</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Suivant</span>
        </button>
    </div>
</div>
</div>









<script>
    const swiper = new Swiper('.swiper-container', {
        loop: true,
        autoplay: {
            delay: 2000,
            disableOnInteraction: false,
        },
        effect: 'fade', 
        fadeEffect: {
            crossFade: true,
        },
    });

    const distortionElements = document.querySelectorAll('.distortion-text');
    distortionElements.forEach((element) => {
        gsap.fromTo(
            element, {
                textShadow: '0px 0px 0px transparent',
                opacity: 0,
            }, {
                textShadow: '4px 4px 10px rgba(255, 0, 0, 0.6), -4px -4px 10px rgba(255, 255, 0, 0.6)',
                opacity: 1,
                duration: 2,
                repeat: -1,
                yoyo: true,
                ease: 'power3.inOut',
            }
        );
    });


    const distortionElementse = document.querySelectorAll('.distortion-text');

    distortionElementse.forEach((element) => {
        gsap.fromTo(
            element, {
                textShadow: '0px 0px 0px transparent',
                opacity: 0
            }, {
                textShadow: '4px 4px 10px rgba(255, 0, 0, 0.6), -4px -4px 10px rgba(255, 255, 0, 0.6)',
                opacity: 1,
                duration: 2,
                repeat: -1,
                yoyo: true,
                ease: 'power3.inOut'
            }
        );
    });
</script>