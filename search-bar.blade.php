<div class="{{ $mobile ?? false ? 'block md:hidden w-full px-4' : 'hidden md:flex flex-1 max-w-lg mx-8' }}">
    <div class="relative w-full">
        <input type="text" placeholder="Search courses, jobs, teachers..." class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#0698ac] focus:border-transparent">
        <button class="absolute right-0 top-0 h-full px-3 text-gray-500 hover:text-[#0698ac]">
            <i class="fas fa-search"></i>
        </button>
    </div>
</div>