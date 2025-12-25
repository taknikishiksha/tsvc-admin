<!-- Footer -->
<footer class="bg-gray-800 text-gray-300 mt-16">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <!-- Footer Grid -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Brand & Social -->
            <div>
                <div class="flex items-center space-x-3 mb-4">
                    <div class="relative">
                        <img src="{{ asset('photos/tsvc.png') }}" alt="Takniki Shiksha Logo" class="h-12 w-12 rounded-lg" />
                        <div class="absolute -bottom-1 -right-1 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-bold shadow-lg">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-white">Takniki Shiksha Careers</h2>
                        <p class="text-sm text-gray-400">Official Recruitment & Training Portal</p>
                    </div>
                </div>
                <p class="text-gray-400 mb-4">Empowering yoga trainers, aspirants & professionals across India with verified opportunities and certified training programs.</p>
                <div class="flex space-x-4 text-gray-400">
                    <a href="https://www.facebook.com/taknikishikshavidhaancouncil/" class="hover:text-white transition-colors"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://x.com/home" class="hover:text-white transition-colors"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.linkedin.com/company/takniki-shiksha-vidhaan-council/" class="hover:text-white transition-colors"><i class="fab fa-linkedin-in"></i></a>
                    <a href="https://www.instagram.com/taknikishikshavidhaancouncil/" class="hover:text-white transition-colors"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.youtube.com/@TAKNIKI_SHIKSHA" class="hover:text-white transition-colors"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <!-- Quick Links -->
            <div>
                <h3 class="font-semibold text-white mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li><a href="{{ url('/') }}" class="hover:text-white transition-colors">Home</a></li>
                    <li><a href="{{ url('our-mission') }}" class="hover:text-white transition-colors">Our Mission</a></li>
                    <li><a href="{{ url('our-vision') }}" class="hover:text-white transition-colors">Our Vision</a></li>
                    <li><a href="{{ url('general-enquiry') }}" class="hover:text-white transition-colors">Contact Us</a></li>
                    <li><a href="{{ url('apply') }}" class="hover:text-white transition-colors">Apply Now</a></li>
                    <li><a href="{{ url('refund-policy') }}" class="hover:text-white transition-colors">Refund Policy</a></li>
                    <li><a href="{{ url('privacy-policy') }}" class="hover:text-white transition-colors">Privacy Policy</a></li>
                    <li><a href="{{ url('terms-&-conditions') }}" class="hover:text-white transition-colors">Terms & Conditions</a></li>
                    <li><a href="{{ url('https://taknikishiksha.org.in/blogs') }}" class="hover:text-white transition-colors">Blogs</a></li>
                </ul>
            </div>
            <!-- Contact Info -->
            <div>
                <h3 class="font-semibold text-white mb-4">Contact Information</h3>
                <div class="space-y-3">
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-phone text-[#0698ac] mt-1"></i>
                        <div>
                            <p class="font-medium">Phone Numbers</p>
                            <p class="text-sm">011-7186 2234 (Landline)</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-globe text-[#0698ac] mt-1"></i>
                        <div>
                            <p class="font-medium">Official Website</p>
                            <p class="text-sm">
                                <a href="https://www.taknikishiksha.org.in" target="_blank" class="text-[#0698ac] hover:underline">
                                    www.taknikishiksha.org.in
                                </a>
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-envelope text-[#0698ac] mt-1"></i>
                        <div>
                            <p class="font-medium">Email Address</p>
                            <p class="text-sm">enquiry@taknikishiksha.org.in</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <i class="fas fa-map-marker-alt text-[#0698ac] mt-1"></i>
                        <div>
                            <p class="font-medium">Office Address</p>
                            <p class="text-sm">Plot No. 08 (2nd Floor), Tikona Park</p>
                            <p class="text-sm">(Ambedkar Park), Village Badli</p>
                            <p class="text-sm">Opposite Haiderpur Metro Station</p>
                            <p class="text-sm">Delhi - 110042</p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Newsletter & Certifications -->
            <div>
                <h3 class="font-semibold text-white mb-4">Stay Updated</h3>
                <div class="mb-6">
                    <p class="text-gray-400 mb-2">Subscribe to our newsletter</p>
                    <div class="flex">
                        <input type="email" placeholder="Your email" class="flex-1 px-3 py-2 rounded-l-lg focus:outline-none text-gray-800">
                        <button class="bg-[#0698ac] text-white px-4 py-2 rounded-r-lg hover:bg-[#1d5055]">Subscribe</button>
                    </div>
                </div>
                <div>
                    <h4 class="font-medium text-white mb-2">Certifications</h4>
                    <div class="flex space-x-2">
                        <div class="bg-white p-2 rounded text-center w-16">
                            <p class="text-xs font-bold text-gray-800">12AA</p>
                        </div>
                        <div class="bg-white p-2 rounded text-center w-16">
                            <p class="text-xs font-bold text-gray-800">80G</p>
                        </div>
                        <div class="bg-white p-2 rounded text-center w-16">
                            <p class="text-xs font-bold text-gray-800">YCB</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-4 text-center text-gray-400 text-sm">
            &copy; {{ date('Y') }} Takniki Shiksha Vidhaan Council. All rights reserved.
        </div>
    </div>
</footer>

<!-- Logout form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
    @csrf
</form>