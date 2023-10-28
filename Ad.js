const housePhoto = document.getElementById("Photo");
const normalHouseParent = housePhoto.parentElement;
var PhotoExpanded = false;
var Elements = document.body.children;

housePhoto.addEventListener('click', () => {
    if (window.innerWidth > 600){
        PhotoExpanded = !PhotoExpanded;
        document.body.append(housePhoto);
        if (PhotoExpanded) {
            housePhoto.className = "HousePhotoExpanded";
            for (let i = 0; i < Elements.length; i++) {
                if (getComputedStyle(Elements[i]).display !== "none") {
                    Elements[i].style.filter = 'blur(10px)';
                }
            }
        } else {
            housePhoto.className = "HousePhoto";
            normalHouseParent.append(housePhoto);
            for (let i = 0; i < Elements.length; i++) {
                Elements[i].style.filter = 'none';
            }
        }
        housePhoto.style.filter = 'none';
    }
});
