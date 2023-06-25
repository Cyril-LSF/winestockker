window.addEventListener('load', function(e) {

    // QUANTITY
    let quantityBtn = document.querySelectorAll('.edit-quantity');
    for (let i = 0; i < quantityBtn.length; i++) {
        quantityBtn[i].addEventListener('click', function() {
            let xhr = new XMLHttpRequest();
            let url = quantityBtn[i].getAttribute('data-url');
            xhr.open('POST', url);
            xhr.send();

            xhr.onload = function () {
                let data = JSON.parse(xhr.response);

                if (data.status === true) {
                    let bottleQuantity = quantityBtn[i].parentNode.children[1];
                    bottleQuantity.innerText = data.quantity
                }
            };
        })
    }
    // END QUANTITY
})