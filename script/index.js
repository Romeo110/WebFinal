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
    })
    .to('.img', {
        stagger: .5,
        y: -500,
        scale: 1,
        scrub: true
    })



// Get the div element
const header = document.querySelector('h1');

// Add a scroll event listener to the window
window.addEventListener('scroll', () => {
    // Get the viewport height
    const viewportHeight = window.innerHeight;

    // Get the current scroll position
    const scrollPosition = window.scrollY;

    // Calculate the scroll position for 50% of the viewport
    const scrollThreshold = viewportHeight / 2;

    // Check if the scroll position is greater than or equal to the threshold
    if (scrollPosition >= scrollThreshold) {
        // Add the 'scroll' class to change the text color
        header.classList.add('active');
    } else {
        // Remove the 'scroll' class if the scroll position is less than the threshold
        header.classList.remove('active');
    }
});

// Add a scroll event listener to the window
window.addEventListener('scroll', () => {
    // Get the viewport height
    const viewportHeight = window.innerHeight;

    // Get the current scroll position
    const scrollPosition = window.scrollY;

    // Calculate the scroll position for revealing the new homepage
    const revealThreshold = viewportHeight / 2;

    // Check if the scroll position is greater than or equal to the threshold
    if (scrollPosition >= revealThreshold) {
        // Add the 'active' class to reveal the new homepage
        document.querySelector('.homepage').classList.add('active');
    } else {
        // Remove the 'active' class to hide the new homepage
        document.querySelector('.homepage').classList.remove('active');
    }
});

// Wait for the DOM content to load
document.addEventListener("DOMContentLoaded", function() {
    // Get the homepage section
    const homepageSection = document.querySelector(".homepage");

    // Get the h1 element
    const h1Element = document.querySelector("h1");

    // Function to check if the user has scrolled to the homepage section
    function isScrolledIntoView(element) {
        const rect = element.getBoundingClientRect();
        const elemHeight = rect.bottom - rect.top;
        return (
            rect.top >= -elemHeight / 2 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) + elemHeight / 2
        );
    }


    // Function to handle scroll events
    function handleScroll() {
        // Check if the homepage section is in view
        if (isScrolledIntoView(homepageSection)) {
            // Hide the h1 element
            h1Element.style.display = "none";
        } else {
            // Show the h1 element
            h1Element.style.display = "block";
        }
    }

    // Add scroll event listener to the window
    window.addEventListener("scroll", handleScroll);

    // Trigger initial check on page load
    handleScroll();
});

document.addEventListener("DOMContentLoaded", function() {
    const homepageSection = document.getElementById("homepage");
    const sidebar = document.querySelector(".homepage .sidebar");

    // Options for the intersection observer
    const options = {
        threshold: 0.6 // Trigger when 50% of the element is visible
    };

    // Callback function to handle intersection changes
    function handleIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                sidebar.classList.add("floating");
                sidebar.style.opacity = '1'; // Make sidebar visible when in view
            } else {
                sidebar.classList.remove("floating");
                sidebar.style.opacity = '0'; // Hide sidebar when out of view
            }
        });
    }

    // Create an intersection observer
    const observer = new IntersectionObserver(handleIntersection, options);

    // Observe the homepage section
    observer.observe(homepageSection);
});