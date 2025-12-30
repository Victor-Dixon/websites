// JavaScript to inspect header styles
// Run this in browser console on both pages to compare

console.log('=== HEADER STYLES INSPECTION ===');

const header = document.querySelector('.site-header');
if (header) {
    const styles = window.getComputedStyle(header);
    console.log('Header Background:', styles.background);
    console.log('Header Background Color:', styles.backgroundColor);
    console.log('Header Position:', styles.position);
    console.log('Header Z-Index:', styles.zIndex);
}

const navLinks = document.querySelectorAll('.main-navigation a');
console.log('Navigation Links Found:', navLinks.length);
navLinks.forEach((link, index) => {
    const styles = window.getComputedStyle(link);
    console.log(`Link ${index + 1} (${link.textContent.trim()}):`);
    console.log('  Color:', styles.color);
    console.log('  Font Weight:', styles.fontWeight);
    console.log('  Font Size:', styles.fontSize);
    console.log('  Padding:', styles.padding);
    console.log('  Background:', styles.background);
});

