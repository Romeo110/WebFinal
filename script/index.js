gsap.registerPlugin(ScrollTrigger);

const lenis = new Lenis({
    duration: 1,
    easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t))
})

function raf(time) {
    lenis.raf(time)
    requestAnimationFrame(raf)
}

requestAnimationFrame(raf)

const tl = gsap.timeline({
    scrollTrigger: {
        trigger: '.img',
        scrub: true
    }
}).to('.img', {
    stagger: .5,
    y: -500,
    scale: 1,
    scrub: true
});

const header = document.querySelector('h1');
let prevScrollPosition = window.scrollY;

window.addEventListener('scroll', () => {
    const scrollPosition = window.scrollY;

    const hideThreshold = 870;

    if (scrollPosition >= hideThreshold) {
        // Use GSAP to fade out the h1 element
        gsap.to(header, {
            opacity: 0,
            duration: 0.1, // Adjust duration as needed for the fade animation
            onComplete: () => {
                header.style.display = "none"; // Hide the text after fading out
            }
        });
    } else {
        // Use GSAP to fade in the h1 element
        gsap.to(header, {
            opacity: 1,
            duration: 0.3 // Adjust duration as needed for the fade animation
        });

        // Gradually change text color to white as scrolling to 860 pixels
        const maxColorValue = 255; // Maximum color value for RGB
        const textColor = Math.min(maxColorValue, Math.round((scrollPosition / hideThreshold) * maxColorValue));
        header.style.color = `rgb(${textColor}, ${textColor}, ${textColor})`;
    }

    // Check if scrolling up and the header is hidden, then show it again
    if (scrollPosition < prevScrollPosition && header.style.display === "none") {
        header.style.display = "block";
        gsap.to(header, { opacity: 1, duration: 0.3 });
    }

    prevScrollPosition = scrollPosition;
});


// Revealing homepage section on scroll
window.addEventListener('scroll', () => {
    const viewportHeight = window.innerHeight;
    const scrollPosition = window.scrollY;
    const revealThreshold = viewportHeight * .1;

    if (scrollPosition >= revealThreshold) {
        document.querySelector('.homepage').classList.add('active');
    } else {
        document.querySelector('.homepage').classList.remove('active');
    }
});

// Intersection observer for sidebar floating effect
document.addEventListener("DOMContentLoaded", function() {
    const homepageSection = document.getElementById("homepage");
    const sidebar = document.querySelector(".homepage .sidebar");

    const options = {
        threshold: 0.6
    };

    function handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                sidebar.classList.add("floating");
                sidebar.style.opacity = '1';
            } else {
                sidebar.classList.remove("floating");
                sidebar.style.opacity = '0';
            }
        });
    }

    const observer = new IntersectionObserver(handleIntersection, options);
    observer.observe(homepageSection);
});

// Icon rotation and change animation on scroll
const iconContainer = document.getElementById('icon-container');
const icon = iconContainer.querySelector('i');

window.addEventListener('scroll', () => {
    const scrollPosition = window.scrollY;
    const rotationFactor = 0.5;

    icon.style.transform = `rotate(${scrollPosition * rotationFactor}deg)`;

    if (scrollPosition > 200) {
        iconContainer.style.opacity = '0';
        setTimeout(() => {
            icon.className = 'bx bx-wink-smile';
            iconContainer.style.opacity = '1';
        }, 500);
    } else {
        iconContainer.style.opacity = '1';
    }
});

// footer swap
window.addEventListener('scroll', function() {
    var scrollPosition = window.scrollY;
    var windowHeight = window.innerHeight;
    var documentHeight = document.documentElement.scrollHeight;
    var threshold = 200;

    // Check if the scrollbar is at the bottom of the page
    if (scrollPosition + windowHeight >= documentHeight - threshold) {
        // Show animated footer
        document.body.classList.add('show-footer');
    } else {
        // Hide animated footer
        document.body.classList.remove('show-footer');
    }
});

// Trigger the scroll event once to ensure the appropriate footer is initially shown
window.dispatchEvent(new Event('scroll'));