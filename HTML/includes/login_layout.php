<?php
/**
 * Login page design shared by the four login pages.
 *
 * The including page must set:
 *   $role         'customer' | 'receptionist' | 'mechanic' | 'admin'
 *   $portalName   e.g. 'Customer'
 *   $portalIcon   Font Awesome class, e.g. 'fa-user'
 *   $subtitle     text under the heading
 *   $error        error message from handle_login() or null
 */
$roles = [
    'customer'     => ['Customer',     'fa-user'],
    'receptionist' => ['Receptionist', 'fa-headset'],
    'mechanic'     => ['Mechanic',     'fa-wrench'],
    'admin'        => ['Admin',        'fa-user-shield'],
];
$oldEmail = $_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['email'] ?? '') : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | <?= e($portalName) ?> Login</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { poppins: ['Poppins', 'sans-serif'] } } } }
    </script>
</head>

<body class="font-poppins">
<div class="min-h-screen flex flex-col lg:flex-row">

    <!-- LEFT SIDE -->
    <section class="relative flex w-full lg:w-1/2 items-center overflow-hidden bg-black">
        <div class="relative z-10 px-8 py-16 sm:px-12 lg:px-16">
            <div class="flex items-center gap-4">
                <img src="<?= e(app_url('../media/AutoPulse Automotive Service Logo.png')) ?>" alt="AutoPulse logo" class="w-[110px] md:w-[125px]">
                <div>
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white">Auto<span class="text-[#E89000]">Pulse</span></h1>
                    <p class="text-sm font-semibold uppercase tracking-wide text-[#E89000] pt-3">Your Vehicle • Our Priority</p>
                </div>
            </div>
            <div class="mt-10 max-w-xl">
                <h1 class="mb-4 text-[25px] sm:text-[30px] md:text-[35px] lg:text-[40px] font-bold leading-tight text-white">Professional Vehicle<br><span class="text-[#E89000]">Service & Care</span></h1>
                <p class="mb-8 max-w-[500px] text-[13px] lg:text-[15px] leading-relaxed text-gray-300">
                    Keep your vehicle running at its best with our expert
                    service, modern facilities and genuine spare parts.
                </p>
            </div>
        </div>
        <div class="absolute bottom-0 left-0 h-0 w-0 border-b-[140px] border-r-[140px] border-b-[#E89000] border-r-transparent"></div>
    </section>

    <!-- RIGHT SIDE -->
    <section class="flex w-full lg:w-1/2 items-center justify-center bg-[#F8F8F8] px-6 py-12 sm:px-10 lg:px-16">
        <div class="w-full max-w-md">

            <!-- Portal badge -->
            <span class="inline-flex items-center gap-2 rounded-full bg-orange-100 px-4 py-1.5 text-xs font-semibold uppercase tracking-wide text-[#E89000]">
                <i class="fa-solid <?= e($portalIcon) ?>"></i> <?= e($portalName) ?> Portal
            </span>

            <h2 class="mt-5 text-3xl sm:text-4xl lg:text-5xl font-bold text-black">Welcome<span class="text-[#E89000]">&nbsp;Back</span></h2>
            <p class="mt-3 text-gray-500"><?= e($subtitle) ?></p>

            <?php if (!empty($error)): ?>
                <div role="alert" class="mt-6 flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                    <span><?= e($error) ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="mt-8 space-y-6" novalidate>
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">

                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input id="email" type="email" name="email" required autocomplete="email"
                               value="<?= e($oldEmail) ?>" placeholder="Enter your email"
                               class="h-12 w-full rounded-lg border border-gray-300 bg-white pl-12 pr-4 outline-none focus:border-[#E89000] focus:ring-4 focus:ring-orange-100">
                    </div>
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-gray-700">Password</label>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input id="password" type="password" name="password" required autocomplete="current-password"
                               placeholder="Enter your password"
                               class="h-12 w-full rounded-lg border border-gray-300 bg-white pl-12 pr-12 outline-none focus:border-[#E89000] focus:ring-4 focus:ring-orange-100">
                        <button type="button" aria-label="Show password"
                                onclick="const p=document.getElementById('password');const show=p.type==='password';p.type=show?'text':'password';this.firstElementChild.className='fa-regular '+(show?'fa-eye-slash':'fa-eye');"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#E89000]">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="h-14 w-full rounded-lg bg-[#FFA500] text-white font-semibold transition duration-300 hover:bg-[#E89000]">
                    Login as <?= e($portalName) ?>
                </button>
            </form>

            <?php if ($role === 'customer'): ?>
                <p class="mt-6 text-center text-sm text-gray-500">Not registered?
                    <a href="register.php" class="font-semibold text-[#E89000] hover:underline">Register here</a>
                </p>
            <?php endif; ?>

            <!-- Switch portal -->
            <div class="my-8 h-px bg-gray-300"></div>
            <p class="mb-3 text-center text-xs font-medium uppercase tracking-wide text-gray-400">Login to another portal</p>
            <div class="flex flex-wrap justify-center gap-2">
                <?php foreach ($roles as $key => [$name, $icon]): if ($key === $role) continue; ?>
                    <a href="<?= e(app_url(ROLE_LOGIN[$key])) ?>"
                       class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-medium text-gray-600 transition hover:border-[#E89000] hover:text-[#E89000]">
                        <i class="fa-solid <?= e($icon) ?>"></i> <?= e($name) ?>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </section>
</div>
</body>
</html>
