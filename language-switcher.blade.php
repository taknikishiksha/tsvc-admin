<div class="relative group">
    <button class="flex items-center space-x-1 text-gray-700 hover:text-[#0698ac]">
        <i class="fas fa-globe"></i>
        <span>EN</span>
        <i class="fas fa-chevron-down text-xs"></i>
    </button>
    <div class="absolute right-0 mt-2 w-32 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
        @foreach(['en' => 'English', 'hi' => '[translate:हिन्दी]'] as $locale => $name)
            <a href="{{ route('language.switch', $locale) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">{{ $name }}</a>
        @endforeach
    </div>
</div>
