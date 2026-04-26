import codeSnap from './codeSnap.js';

document.addEventListener('alpine:init', () => {
    Alpine.data('codeSnap', codeSnap);
});