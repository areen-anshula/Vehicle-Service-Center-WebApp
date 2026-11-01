<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>AutoPulse | Automotive Service</title>
    <link href="./output.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/homepage.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css" integrity="sha512-QeR2VH+lsBE5LSAe1Q5EnTBbe7XTBubt8dG93Y7gidSgdMCr8nVqKcfKAMyN96SV8KDbZVTDXChatu5G2KQGzg==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="../src/output.css" rel="stylesheet">
</head>
<body class="font-poppins !bg-[#F5F5F5]">

<!-- Navigation Bar -->
    
<header
    class="sticky top-0 z-50 flex min-h-[70px] w-full flex-wrap items-center justify-between bg-black px-4 md:px-[6%] py-3 shadow-md">

    <!-- Logo -->
    <div class="shrink-0">
        <img
            src="../MEDIA/AutoPulse Automotive Service Logo.png"
            alt="Logo"
            class="w-[110px] md:w-[125px]">
    </div>

    <!-- Navigation -->
    <nav class="hidden md:flex items-center gap-14">
        <a href="#" class="text-[#E89000] font-medium text-[10px] sm:text-[12px] md:text-[13px] lg:text-[15px]">Home</a>
        <a href="#about" class="text-white hover:text-[#E89000] text-[10px] sm:text-[12px] md:text-[13px] lg:text-[15px]">About Us</a>
        <a href="#services" class="text-white hover:text-[#E89000] text-[10px] sm:text-[12px] md:text-[13px] lg:text-[15px]">Services</a>
        <a href="#contact" class="text-white hover:text-[#E89000] text-[10px] sm:text-[12px] md:text-[13px] lg:text-[15px]">Contact</a>
    </nav>

    <!-- Buttons -->
    <div class="flex items-center gap-8">
        <a href="login.php"
           class="hidden sm:flex text-sm font-semibold text-white hover:text-[#E89000]">
            <i class="fa-solid fa-user mr-2"></i>
            Login
        </a>

        <a href="book_appointment.php"
           class="rounded-md bg-[#E89000] px-3 py-2 text-xs md:text-sm font-semibold text-white hover:bg-[#FFA500]">
            <i class="fa-solid fa-calendar mr-2"></i>
            Book Appointment
        </a>
    </div>
</header>

<!-- // Navigation Bar -->


<!-- Banner image  -->
<section class="banner-image">
    <div class="relative w-full">
        <!-- Banner Image -->
        <img
            class="h-[600px] w-full object-cover"
            src="../media/banner-image1.jpg"
            alt="Vehicle Service Center">

        <!-- Dark Overlay -->
        <div class="absolute inset-0 bg-black/55"></div>

        <!-- Banner Content -->
        <div class="absolute inset-0 flex items-center">
            <div class="max-w-[650px] pl-[6%]">

                <!-- Tagline -->
                <div class="mb-5 flex items-center gap-3">
                    <span class="h-[2px] w-8 bg-[#E89000]"></span>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#E89000]">
                        Your Vehicle • Our Priority
                    </p>
                </div>

                <!-- Heading -->
                <h1 class="mb-4 text-[25px] sm:text-[30px] md:text-[35px] lg:text-[40px] font-bold leading-tight text-white">
                    Professional Vehicle
                    <br>
                    <span class="text-[#E89000]">
                        Service & Care
                    </span>
                </h1>

                <!-- Description -->
                <p class="mb-8 max-w-[500px] text-[10px] sm:text-[12px] md:text-[13px] lg:text-[15px] leading-relaxed text-gray-300">
                    Keep your vehicle running at its best with our expert
                    service, modern facilities and genuine spare parts.
                </p>

                <!-- Buttons -->
                <div class="flex flex-wrap gap-4">

                    <a href="book_appointment.php"
                        class="flex items-center gap-2 rounded-md bg-[#E89000] px-6 py-3 font-semibold text-[10px] sm:text-[11px] md:text-[12px] lg:text-[13px] text-white transition duration-300 hover:bg-[#FFA500]">

                        <i class="fa-solid fa-calendar"></i>
                        Book Appointment
                    </a>

                    <a href="#services"
                        class="flex items-center gap-2 rounded-md border border-[#E89000] bg-black/40 text-[10px] sm:text-[11px] md:text-[12px] lg:text-[13px] px-6 py-3 font-semibold text-white transition duration-300 hover:bg-[#E89000]">
                        Explore Our Services
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- // Banner image  -->

<!-- About Us Section -->
<section id="about" class="bg-white py-8 md:py-16">
    <div class="mx-auto max-w-7xl px-5 md:px-[1%]">

        <div class="grid grid-cols-1 items-center gap-16 lg:grid-cols-2">

            <!-- Left Image -->
            <div>
                <img src="../media/trust-mechanic.webp" alt="Vehicle Service" class="h-[400px] w-full rounded-xl object-cover shadow-md">
            </div>

            <!-- Right Content -->
            <div>

                <!-- Section Label -->
                <p class="mb-2 text-[12px] font-bold uppercase tracking-widest text-[#E89000]">
                    About Us
                </p>

                <!-- Heading -->
                <h2 class="mb-4 font-bold leading-tight text-[#111827] text-[25px] sm:text-[28px] md:text-[33px] lg:text-[38px]">Trusted Care for<br>Every Journey</h2>

                <!-- Description -->
                <p class="mb-10 leading-7 text-gray-600 text-[10px] sm:text-[12px] md:text-[13px] lg:text-[15px]">
                    We are a professional vehicle service center dedicated to keeping your car safe, reliable and running smoothly. With
                    experienced mechanics, modern diagnostic tools and genuine spare parts, we deliver high-quality service you can trust.
                </p>

                <!-- Features -->
                <div class="grid grid-cols-2 gap-6 md:grid-cols-4">

                    <!-- Feature 1 -->
                    <div class="text-center">
                        <div class="mb-3 text-4xl text-[#E89000]">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>

                        <h4 class="text-[14px] font-semibold text-gray-800">Experienced<br>Mechanics
                        </h4>
                    </div>

                    <!-- Feature 2 -->
                    <div class="text-center md:border-l md:border-gray-200">
                        <div class="mb-3 text-4xl text-[#E89000]">
                            <i class="fa-solid fa-gear"></i>
                        </div>

                        <h4 class="text-[14px] font-semibold text-gray-800">
                            Modern
                            <br>
                            Equipment
                        </h4>
                    </div>

                    <!-- Feature 3 -->
                    <div class="text-center md:border-l md:border-gray-200">
                        <div class="mb-3 text-4xl text-[#E89000]">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>

                        <h4 class="text-[14px] font-semibold text-gray-800">
                            Genuine
                            <br>
                            Spare Parts
                        </h4>
                    </div>

                    <!-- Feature 4 -->
                    <div class="text-center md:border-l md:border-gray-200">
                        <div class="mb-3 text-4xl text-[#E89000]">
                            <i class="fa-solid fa-users"></i>
                        </div>

                        <h4 class="text-[14px] font-semibold text-gray-800">
                            Customer
                            <br>
                            Satisfaction
                        </h4>
                    </div>

                </div>
            </div>

        </div>

    </div>
</section>


<!-- Services Section -->

<section id="services" class="bg-gray-50 py-26 px-6">
  <div class="max-w-7xl mx-auto">
    <!-- Section Header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
      <div>
        <p class="text-[#E89000] text-xs font-bold uppercase tracking-wider mb-1">Our Services</p>
        <h2 class="text-2xl md:text-3xl font-bold text-gray-900">Comprehensive Vehicle Services</h2>
      </div>
      <a href="services.html" class="text-[#E89000] text-sm font-medium hover:text-[#FFA500] flex items-center gap-2">
        View All Services <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>

    <!-- Service Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">

      <!-- Engine Service -->
      <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition duration-300">
        <div class="h-24 overflow-hidden">
          <img src="../MEDIA/engine_repair.jpg" alt="Engine Service" class="w-full h-full object-cover hover:scale-105 transition duration-300">
        </div>
        <div class="p-3">
          <div class="text-[#E89000] text-2xl mb-2"><i class="fa-solid fa-gears"></i></div>
          <h3 class="text-s font-bold text-gray-900 mb-1">Engine Service</h3>
          <p class="text-[11px] leading-4 text-gray-500 min-h-10">Complete engine check-up, cleaning and performance tuning.</p>
          <p class="text-[11px] text-gray-500 mt-3">From <span class="text-[#E89000] font-bold text-xs ml-1">LKR 5,000</span></p>
        </div>
      </div>

      <!-- Wheel & Tyre Service -->
      <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition duration-300">
        <div class="h-24 overflow-hidden">
          <img src="../MEDIA/wheel service.avif" alt="Wheel and Tyre Service" class="w-full h-full object-cover hover:scale-105 transition duration-300">
        </div>
        <div class="p-3">
          <div class="text-[#E89000] text-2xl mb-2"><i class="fa-solid fa-compact-disc"></i></div>
          <h3 class="text-s font-bold text-gray-900 mb-1">Wheel & Tyre Service</h3>
          <p class="text-[11px] leading-4 text-gray-500 min-h-10">Tyre change, balancing, alignment and rotation.</p>
          <p class="text-[11px] text-gray-500 mt-3">From <span class="text-[#E89000] font-bold text-xs ml-1">LKR 2,000</span></p>
        </div>
      </div>

      <!-- Brake Repair -->
      <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition duration-300">
        <div class="h-24 overflow-hidden">
          <img src="../MEDIA/brake_repair.avif" alt="Brake Repair" class="w-full h-full object-cover hover:scale-105 transition duration-300">
        </div>
        <div class="p-3">
          <div class="text-[#E89000] text-2xl mb-2"><i class="fa-solid fa-gauge-high"></i></div>
          <h3 class="text-s font-bold text-gray-900 mb-1">Brake Repair</h3>
          <p class="text-[11px] leading-4 text-gray-500 min-h-10">Inspection, replacement and brake system service.</p>
          <p class="text-[11px] text-gray-500 mt-3">From <span class="text-[#E89000] font-bold text-xs ml-1">LKR 3,000</span></p>
        </div>
      </div>

      <!-- Vehicle Cleaning -->
      <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition duration-300">
        <div class="h-24 overflow-hidden">
          <img src="../MEDIA/vehicle_cleaning.jpeg" alt="Vehicle Cleaning" class="w-full h-full object-cover hover:scale-105 transition duration-300">
        </div>
        <div class="p-3">
          <div class="text-[#E89000] text-2xl mb-2"><i class="fa-solid fa-car"></i></div>
          <h3 class="text-s font-bold text-gray-900 mb-1">Vehicle Cleaning</h3>
          <p class="text-[11px] leading-4 text-gray-500 min-h-10">Interior & exterior cleaning, polishing and detailing.</p>
          <p class="text-[11px] text-gray-500 mt-3">From <span class="text-[#E89000] font-bold text-xs ml-1">LKR 1,500</span></p>
        </div>
      </div>

      <!-- Battery Service -->
      <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition duration-300">
        <div class="h-24 overflow-hidden">
          <img src="../MEDIA/battery service.avif" alt="Battery Service" class="w-full h-full object-cover hover:scale-105 transition duration-300">
        </div>
        <div class="p-3">
          <div class="text-[#E89000] text-2xl mb-2"><i class="fa-solid fa-car-battery"></i></div>
          <h3 class="text-s font-bold text-gray-900 mb-1">Battery Service</h3>
          <p class="text-[11px] leading-4 text-gray-500 min-h-10">Battery check, replacement and electrical system check.</p>
          <p class="text-[11px] text-gray-500 mt-3">From <span class="text-[#E89000] font-bold text-xs ml-1">LKR 2,500</span></p>
        </div>
      </div>

      <!-- AC Service -->
      <div class="bg-white rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition duration-300">
        <div class="h-24 overflow-hidden">
          <img src="../MEDIA/AC service.webp" alt="AC Service" class="w-full h-full object-cover hover:scale-105 transition duration-300">
        </div>
        <div class="p-3">
          <div class="text-[#E89000] text-2xl mb-2"><i class="fa-solid fa-snowflake"></i></div>
          <h3 class="text-s font-bold text-gray-900 mb-1">AC Service</h3>
          <p class="text-[11px] leading-4 text-gray-500 min-h-10">Cooling system check, recharge and repair.</p>
          <p class="text-[11px] text-gray-500 mt-3">From <span class="text-[#E89000] font-bold text-xs ml-1">LKR 2,000</span></p>
        </div>
      </div>

    </div>
  </div>
</section>




<!-- Why Choose Us -->
<section class="relative overflow-hidden bg-[#080808] py-16 md:py-20">

    <!-- Background Decoration -->
    <div class="absolute -right-32 -top-32 h-96 w-96 rounded-full bg-[#E89000]/10 blur-3xl"></div>
    <div class="absolute -bottom-32 -left-32 h-80 w-80 rounded-full bg-[#E89000]/5 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-5 md:px-[6%]">

        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-[1.5fr_1fr] lg:gap-16">

            <!-- Left Content -->
            <div>

                <!-- Section Heading -->
                <div class="mb-10">
                    <div class="mb-3 flex items-center gap-3">
                        <span class="h-[2px] w-8 bg-[#E89000]"></span>
                        <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#E89000]">
                            Why Choose Us
                        </p>
                    </div>

                    <h2 class="max-w-xl text-3xl font-bold leading-tight text-white md:text-4xl lg:text-5xl text-[25px] sm:text-[30px] md:text-[35px] lg:text-[40px]">
                        Your Vehicle Deserves
                        <span class="text-[#E89000]"><br>The Best</span>
                    </h2>

                    <p class="mt-4 max-w-lg text-sm leading-7 text-gray-400">
                        We combine professional expertise, quality parts and reliable service to keep your vehicle safe and performing at its best.
                    </p>
                </div>

                <!-- Benefits Grid -->
                <div class="grid grid-cols-1 gap-7 sm:grid-cols-2 lg:grid-cols-4">

                    <!-- Benefit 1 -->
                    <div class="group border-b border-white/10 pb-6 sm:border-b-0 sm:border-r sm:pr-5 lg:pr-5">

                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-xl bg-[#E89000]/10 text-2xl text-[#E89000] transition duration-300 group-hover:bg-[#E89000] group-hover:text-white">
                            <i class="fa-solid fa-medal"></i>
                        </div>

                        <h3 class="mb-3 text-base font-bold text-white">
                            Skilled Professionals
                        </h3>

                        <p class="text-sm leading-6 text-gray-400">
                            Experienced mechanics delivering professional workmanship and reliable vehicle care.
                        </p>

                    </div>

                    <!-- Benefit 2 -->
                    <div class="group border-b border-white/10 pb-6 sm:border-b-0 lg:border-r lg:pr-5">

                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-xl bg-[#E89000]/10 text-2xl text-[#E89000] transition duration-300 group-hover:bg-[#E89000] group-hover:text-white">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>

                        <h3 class="mb-3 text-base font-bold text-white">
                            Reliable Service
                        </h3>

                        <p class="text-sm leading-6 text-gray-400">
                            Quality maintenance and repairs with attention to detail and customer satisfaction.
                        </p>

                    </div>

                    <!-- Benefit 3 -->
                    <div class="group border-b border-white/10 pb-6 sm:border-b-0 sm:border-r sm:pr-5 lg:pr-5">

                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-xl bg-[#E89000]/10 text-2xl text-[#E89000] transition duration-300 group-hover:bg-[#E89000] group-hover:text-white">
                            <i class="fa-solid fa-tags"></i>
                        </div>

                        <h3 class="mb-3 text-base font-bold text-white">
                            Transparent Pricing
                        </h3>

                        <p class="text-sm leading-6 text-gray-400">
                            Honest pricing, clear estimates and no unexpected service charges.
                        </p>

                    </div>

                    <!-- Benefit 4 -->
                    <div class="group">

                        <div class="mb-5 flex h-14 w-14 items-center justify-center rounded-xl bg-[#E89000]/10 text-2xl text-[#E89000] transition duration-300 group-hover:bg-[#E89000] group-hover:text-white">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>

                        <h3 class="mb-3 text-base font-bold text-white">
                            Easy Appointments
                        </h3>

                        <p class="text-sm leading-6 text-gray-400">
                            Convenient booking options to schedule your vehicle service without hassle.
                        </p>

                    </div>

                </div>

            </div>

            <!-- Right Image -->
            <div class="relative mx-auto w-full max-w-lg lg:max-w-none">

                <!-- Orange Accent -->
                <div class="absolute -right-3 -top-3 h-full w-full rounded-2xl border-2 border-[#E89000]"></div>

                <!-- Image -->
                <div class="relative overflow-hidden rounded-2xl bg-gray-900">
                    <img
                        src="../media/why-us.webp"
                        alt="Professional vehicle maintenance and automotive service"
                        class="h-[300px] w-full object-cover sm:h-[380px] lg:h-[420px]">

                    <!-- Image Overlay -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent"></div>

                    <!-- Image Caption -->
                    <div class="absolute bottom-0 left-0 p-6">
                        <p class="mb-2 text-xs font-bold uppercase tracking-widest text-[#E89000]">
                            "Your Vehicle • Our Priority"
                        </p>
                        <h3 class="text-1xl font-bold text-white">
                            Professional Care.<br>
                            Reliable Performance.
                        </h3>
                    </div>
                </div>

            </div>

        </div>

    </div>

</section>


<!-- Contact Section -->
<section id="contact" class="bg-white py-16 md:py-20">
    <div class="mx-auto max-w-7xl px-5 md:px-[6%]">

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_1fr_1.2fr] gap-10 lg:gap-12 items-center">

            <!-- Left Content -->
            <div class="flex flex-col justify-center">
                <p class="mb-2 text-xs font-bold uppercase tracking-widest text-[#E89000]">
                    Contact Us
                </p>
                <h2 class="mb-4 text-3xl md:text-4xl font-bold leading-tight text-[#111827]">
                    Get in Touch
                </h2>
                <p class="text-sm md:text-[15px] leading-7 text-gray-600">
                    We're here to help! Reach out to us for any inquiries, bookings, or support. Our team will get back to you as soon as possible.
                </p>
            </div>

            <!-- Contact Information -->
            <div class="flex flex-col justify-center space-y-6 lg:pl-4">

                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-xl text-[#E89000]">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">123 Service Road</p>
                        <p class="text-xs md:text-sm text-gray-500">Colombo 05, Sri Lanka</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-xl text-[#E89000]">
                        <i class="fa-solid fa-phone"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Phone Number</p>
                        <p class="text-xs md:text-sm text-gray-500">+94 11 234 5678</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-xl text-[#E89000]">
                        <i class="fa-solid fa-envelope"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Email Address</p>
                        <p class="text-xs md:text-sm text-gray-500">info@vehiclecenter.lk</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-orange-50 text-xl text-[#E89000]">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Opening Hours</p>
                        <p class="text-xs md:text-sm text-gray-500">Mon - Sat: 8:00 AM - 6:00 PM</p>
                        <p class="text-xs md:text-sm text-gray-500">Sunday: Closed</p>
                    </div>
                </div>

            </div>

            <!-- Contact Form -->
            <div class="w-full rounded-xl bg-white p-6 md:p-7 shadow-lg border border-gray-100">
                <h3 class="mb-5 text-xl font-bold text-gray-800">Send Us a Message</h3>

                <form class="space-y-4">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <input
                            type="text"
                            placeholder="Your Name"
                            class="h-12 w-full rounded-lg border border-gray-300 px-4 text-sm outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">

                        <input
                            type="email"
                            placeholder="Your Email"
                            class="h-12 w-full rounded-lg border border-gray-300 px-4 text-sm outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100">
                    </div>

                    <textarea
                        rows="5"
                        placeholder="Your Message"
                        class="w-full resize-none rounded-lg border border-gray-300 p-4 text-sm outline-none transition focus:border-[#E89000] focus:ring-2 focus:ring-orange-100"></textarea>

                    <button
                        type="submit"
                        class="flex h-12 w-full cursor-pointer items-center justify-center gap-3 rounded-lg bg-[#FFA500] font-semibold text-white transition hover:bg-[#E89000]">
                        <i class="fa-solid fa-paper-plane"></i>
                        Send Message
                    </button>

                </form>
            </div>

        </div>
    </div>
</section>


<!-- Footer -->
<footer style="background-color: #000; color: #fff;">

    <!-- Main Footer -->
    <div style="max-width: 1280px; margin: auto; padding: 48px 24px;">

        <div style="
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 24px;
            width: 100%;
        ">

            <!-- Logo & Description -->
            <div>
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="../media/AutoPulse Automotive Service Logo.png" alt="AutoPulse Logo" style="width: 56px;">

                    <div>
                        <h3 style="font-size: 18px; font-weight: bold; margin: 0;">
                            Vehicle
                        </h3>
                        <span style="color: #E89000; font-weight: bold;">
                            Service Center
                        </span>
                    </div>
                </div>

                <p style="margin-top: 20px; font-size: 14px; line-height: 1.8; color: #9ca3af;">
                    Providing reliable vehicle maintenance, repair services and professional automotive care to keep your vehicle performing at its best.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 style="color: #E89000; font-size: 18px; margin: 0 0 20px;" class="ps-10">
                    Quick Links
                </h3>

                <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; line-height: 2.5; color: #9ca3af;" class="ps-10">
                    <li class="ps-10"><a href="#" style="color: inherit; text-decoration: none;">Home</a></li>
                    <li class="ps-10"><a href="#about" style="color: inherit; text-decoration: none;">About Us</a></li>
                    <li class="ps-10"><a href="#services" style="color: inherit; text-decoration: none;">Services</a></li>
                    <li class="ps-10"><a href="#contact" style="color: inherit; text-decoration: none;">Contact</a></li>
                </ul>
            </div>

            <!-- Our Services -->
            <div>
                <h3 style="color: #E89000; font-size: 18px; margin: 0 0 20px;">
                    Our Services
                </h3>

                <ul style="list-style: none; padding: 0; margin: 0; font-size: 14px; line-height: 2.5; color: #9ca3af;">
                    <li>Engine Service</li>
                    <li>Brake Repair</li>
                    <li>Wheel Alignment</li>
                    <li>Battery Service</li>
                    <li>AC Service</li>
                </ul>
            </div>

            <!-- Contact Info -->
            <div>
                <h3 style="color: #E89000; font-size: 18px; margin: 0 0 20px;">
                    Contact Info
                </h3>

                <div style="font-size: 14px; color: #9ca3af; line-height: 1.8;">
                    <p style="margin: 0 0 14px;">
                        <i class="fa-solid fa-location-dot" style="color: #E89000; margin-right: 8px;"></i>
                        123 Service Road, Colombo, Sri Lanka
                    </p>

                    <p style="margin: 0 0 14px;">
                        <i class="fa-solid fa-phone" style="color: #E89000; margin-right: 8px;"></i>
                        +94 11 234 5678
                    </p>

                    <p style="margin: 0;">
                        <i class="fa-solid fa-envelope" style="color: #E89000; margin-right: 8px;"></i>
                        info@autopulse.lk
                    </p>
                </div>

                <!-- Social Icons -->
                <div style="display: flex; gap: 12px; margin-top: 20px;">
                    <a href="#" style="background: #111827; color: #E89000; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>

                    <a href="#" style="background: #111827; color: #E89000; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        <i class="fa-brands fa-instagram"></i>
                    </a>

                    <a href="#" style="background: #111827; color: #E89000; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        <i class="fa-brands fa-youtube"></i>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <!-- Bottom Bar -->
    <div style="border-top: 1px solid #1f2937;">
        <div style="
            max-width: 1280px;
            margin: auto;
            padding: 20px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 13px;
            color: #6b7280;
        ">
            <p style="margin: 0;">
                &copy; 2026 AutoPulse Vehicle Service Center. All rights reserved. Created by Group 18.
            </p>

            <div style="display: flex; gap: 20px;">
                <a href="#" style="color: inherit; text-decoration: none;">Privacy Policy</a>
                <a href="#" style="color: inherit; text-decoration: none;">Terms of Service</a>
            </div>
        </div>
    </div>

</footer>


  
</body>
</html>
