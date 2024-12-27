document.addEventListener('DOMContentLoaded', () => {
    const slider = document.querySelector('.slider'); // Elemen slider
    const slides = document.querySelectorAll('.slider .content'); // Semua elemen slide
    const prevButton = document.querySelector('.prev'); // Tombol navigasi "Prev"
    const nextButton = document.querySelector('.next'); // Tombol navigasi "Next"

    const slideWidth = 800; // Lebar setiap slide (harus konsisten dengan CSS)
    const totalSlides = slides.length; // Jumlah total slide
    let currentIndex = 0; // Indeks slide aktif

    // Atur lebar slider secara dinamis berdasarkan jumlah slide
    slider.style.width = `${totalSlides * slideWidth}px`;

    // Fungsi untuk menggeser slider ke slide tertentu
    function slideToIndex(index) {
        slider.style.transform = `translateX(-${index * slideWidth}px)`; // Geser slider
        currentIndex = index;

        // Perbarui status tombol navigasi
        if (currentIndex === 0) {
            prevButton.classList.remove('show');
            prevButton.disabled = true;
        } else {
            prevButton.classList.add('show');
            prevButton.disabled = false;
        }

        if (currentIndex === totalSlides - 1) {
            nextButton.classList.remove('show');
            nextButton.disabled = true;
        } else {
            nextButton.classList.add('show');
            nextButton.disabled = false;
        }
    }

    // Event listener untuk tombol "Prev"
    prevButton.addEventListener('click', (e) => {
        e.preventDefault(); // Mencegah reload halaman
        if (currentIndex > 0) {
            slideToIndex(currentIndex - 1); // Pindah ke slide sebelumnya
        }
    });

    // Event listener untuk tombol "Next"
    nextButton.addEventListener('click', (e) => {
        e.preventDefault(); // Mencegah reload halaman
        if (currentIndex < totalSlides - 1) {
            slideToIndex(currentIndex + 1); // Pindah ke slide berikutnya
        }
    });

    // Inisialisasi posisi slider dan tombol navigasi
    slideToIndex(0);
});

