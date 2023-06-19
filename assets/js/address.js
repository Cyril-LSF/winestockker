window.addEventListener('load', function(e) {
    // Show/hidde form

    let btn = document.getElementById('add_address');
    let form = document.getElementById('address_form');

    let hidde = (element) => {
        element.classList.add('d-none');
    };

    let show = (element) => {
        element.classList.remove('d-none');
    }

    btn.addEventListener('click', function(e) {
        if (form.classList.contains('d-none')) {
            show(form);
        } else {
            hidde(form);
        }
    })

    // End show/hidde form

    let selectedBtn = document.querySelectorAll('.selected_btn');

    for (let i = 0; i < selectedBtn.length; i++) {
        selectedBtn[i].addEventListener('click', function() {
            let xhr = new XMLHttpRequest();
            let url = selectedBtn[i].getAttribute('data-url');
            xhr.open('POST', url);
            xhr.send();

            xhr.onload = function () {
            let data = JSON.parse(xhr.response);

                if (data.status === true) {
                    let addressCard = document.getElementById('address_card' + i);
                    if (data.isSelected == true){
                        // reset
                        let cards = document.querySelectorAll('.card_people');
                        cards.forEach((object)=> {
                            object.classList.remove('alert-success');
                        });
                        let buttons = document.querySelectorAll('.selected_btn');
                        buttons.forEach((object) => {
                            object.innerHTML =  "<i class='fa-solid fa-check fa-2x text-success'></i>";
                        });
                        // end reset

                        selectedBtn[i].innerHTML = "<i class='fa-solid fa-xmark fa-2x text-danger'></i>";
                        addressCard.classList.add('alert-success');
                    } else {
                        addressCard.classList.remove('alert-success');
                        selectedBtn[i].innerHTML = "<i class='fa-solid fa-check fa-2x text-success'></i>";
                    }

                }
            };
        })
    }
})