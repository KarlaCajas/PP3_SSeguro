<svg {{ $attributes }} viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
    <defs>
        <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#3B82F6;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#8B5CF6;stop-opacity:1" />
        </linearGradient>
    </defs>
    
    <!-- Background circle -->
    <circle cx="32" cy="32" r="30" fill="url(#logoGradient)" stroke="#fff" stroke-width="2"/>
    
    <!-- Shopping cart icon -->
    <g transform="translate(16, 16)">
        <!-- Cart body -->
        <path d="M4 4h4l2 12h16l2-8H12" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        
        <!-- Cart wheels -->
        <circle cx="10" cy="20" r="1.5" fill="#fff"/>
        <circle cx="20" cy="20" r="1.5" fill="#fff"/>
        
        <!-- Cart handle -->
        <path d="M4 4L2 2H0" stroke="#fff" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
        
        <!-- Dollar sign -->
        <g transform="translate(14, 6)">
            <circle cx="4" cy="4" r="3" fill="#fff" opacity="0.9"/>
            <text x="4" y="7" font-family="Arial, sans-serif" font-size="5" font-weight="bold" text-anchor="middle" fill="url(#logoGradient)">$</text>
        </g>
    </g>
</svg>
