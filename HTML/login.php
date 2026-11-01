<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutoPulse | Mechanic</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4.3.3"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.3.1/css/all.min.css">
</head>

<body class="font-poppins">

    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- LEFT SIDE -->

        <section
            class="relative flex w-full lg:w-1/2 items-center overflow-hidden" style="background: linear-gradient(rgba(0,0,0,.75),rgba(0,0,0,.55)), url('../media/login-bg.jpg'); background-size:cover; background-position:center;">
            <div class="relative z-10 px-8 py-16 sm:px-12 lg:px-16">
                <div class="flex items-center gap-4">
                    <img src="../" alt="">
                    <div>
                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white">Auto<span class="text-[#E89000]">Pulse</span></h1>
                        <p class="mt-2 text-sm sm:text-base text-gray-300">Professional Vehicle Service Center</p>
                    </div>
                </div>

                <div class="mt-8 h-1 w-24 bg-[#E89000]"></div>
                <div class="mt-10 max-w-xl">
                    <h2 class="text-white font-bold leading-tight text-3xl sm:text-4xl lg:text-5xl">Trusted CareFor Every Journe</h2>
                    <p class="mt-6 text-gray-200 leading-8 text-sm sm:text-base">
                        Manage customer appointments, vehicle repair records,
                        service schedules, inventory tracking and workshop
                        operations through one centralized platform designed
                        for professional automotive service centers.
                    </p>
                </div>
            </div>

            <!-- Decorative Accent -->

            <div class="absolute bottom-0 left-0 h-0 w-0 border-b-[140px] border-r-[140px] border-b-[#E89000] border-r-transparent"></div>
        </section>

        <!-- RIGHT SIDE -->

        <section class="flex w-full lg:w-1/2 items-center justify-center bg-[#F8F8F8]  px-6 py-12  sm:px-10 lg:px-16">
            <div class="w-full max-w-md">
                <!-- Heading -->
                <h2  class="text-3xl sm:text-4xl lg:text-5xl font-bold text-black">Welcome<span class="text-[#E89000]">Back</span></h2>
                <p class="mt-3 text-gray-500">Sign in to access the AutoPulse service portal</p>
                <!-- Form -->
                <form  action="login_process.php" method="POST" class="mt-10 space-y-6">
                    <!-- Username -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Username</label>
                        <div class="relative">
                            <i class="fa-regular fa-user absolute left-4 top-1/2-translate-y-1/2 text-gray-400">
                            </i>
                            <input type="text" name="username" required placeholder="Enter your username" class="h-14 w-full rounded-lg border border-gray-300 bg-white pl-12 pr-4 outline-none focus:border-[#E89000] focus:ring-4 focus:ring-orange-100">
                        </div>
                    </div>
                    <!-- Password -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Password</label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-4 top-1/2-translate-y-1/2 text-gray-400"></i>
                            <input type="password" name="password" required placeholder="Enter your password" class="h-14 w-full rounded-lg border border-gray-300 bg-white pl-12 pr-4 outline-none focus:border-[#E89000] focus:ring-4 focus:ring-orange-100">
                        </div>
                    </div>

                    <!-- Remember -->
                    <div class="flex flex-col gap-3 sm:flex-row sm:justify-between sm:items-center">
                        <label class="flex items-center gap-2 text-sm text-gray-600">
                            <input type="checkbox" name="remember" class="accent-[#E89000]"> Remember Me
                        </label>

                        <a href="forgot-password.php" class="text-sm font-medium text-[#E89000] hover:underline"> Forgot Password?</a>

                    </div>
                    <!-- Login Button -->
                    <button type="submit" class="h-14 w-full rounded-lg bg-[#E89000] text-white font-semibold transition duration-300 hover:bg-orange-500"> Login</button>
                </form>

                <!-- Divider -->

                <div class="my-10 flex items-center gap-4">
                    <div class="h-px flex-1 bg-gray-300"></div>
                    <span class="whitespace-nowrap text-sm text-gray-500">Authorized Staff Only</span>
                    <div class="h-px flex-1 bg-gray-300"></div>
                </div>

                <!-- Help -->

                <p class="text-center text-sm text-gray-500">Need assistance?
                    <a href="contact.php" class="font-semibold text-[#E89000] hover:underline">Contact Administrator
                    </a>
                </p>

            </div>

        </section>

    </div>

</body>
</html>