document.addEventListener('DOMContentLoaded', function () {
    const AccountButton = document.getElementById("account");
    const LoginBox = document.getElementById("ManageAccount");
    const SearchBar = document.getElementById("Search");
    const Sort = document.getElementById("sort");
    const houseAdElements = document.querySelectorAll('.HouseAd');
    var LoginIsOpen = false;

    houseAdElements.forEach(function (houseAd) {
        houseAd.addEventListener('click', function () {
            const adId = houseAd.getAttribute('data-adid');
            if (adId) {
                window.location.href = '/project/Ad.php?AdId=' + adId;
            }
        });
    });

    AccountButton.addEventListener("click", () => {
        LoginIsOpen = !LoginIsOpen;

        if (LoginIsOpen) {
            LoginBox.style.display = "block";
        } else {
            LoginBox.style.display = "none";
        }
    });

    document.getElementById('sort').addEventListener('change', () => {
        document.getElementById('SearchBar').submit();
    });
});
