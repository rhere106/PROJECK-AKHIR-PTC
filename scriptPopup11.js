document.querySelector(".info").addEventListener("click", function(){
    document.querySelector(".popup").style.display = "flex";
});
document.querySelector(".del").addEventListener("click", function(){
    document.querySelector(".popup").style.display = "none";
})




 // Menampilkan popup konfirmasi
 function showPopup() {
    document.getElementById('sampah').style.display = 'flex';
}

// Menutup popup tanpa menghapus
function closePopup() {
    document.getElementById('sampah').style.display = 'none';
}

