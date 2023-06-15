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

    let getSiblings = function (element, bool) {
        // for collecting siblings
        let siblings = []; 
        // first child of the parent node
        let sibling  = element.parentNode.parentNode.parentNode.firstChild;
        // collecting siblings
        while (sibling) {
            if (sibling.nodeType === 1 && sibling !== e) {
                siblings.push(sibling);
            }
            sibling = sibling.nextSibling;
        }
        if (bool == false) {
            element.classList.remove('alert-success');
            return
        }
        for (let i = 0; i < siblings.length; i++) {
            if (bool == true) {
                if (siblings[i].children[0].children[0] !== element) {
                    siblings[i].children[0].children[0].classList.remove('alert-success');
                } else {
                    siblings[i].children[0].children[0].classList.add('alert-success');
                }
            }
        }
    };

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
                        getSiblings(addressCard, true);
                        selectedBtn[i].innerHTML = "<i class='fa-solid fa-xmark fa-2x text-danger'></i>";
                        for (let j = 0; j < selectedBtn.length; j++) {
                            if (i == j) {
                                continue;
                            }
                            selectedBtn[j].innerHTML = "<i class='fa-solid fa-check fa-2x text-success'></i>";
                        }
                    } else {
                        getSiblings(addressCard, false);
                        selectedBtn[i].innerHTML = "<i class='fa-solid fa-check fa-2x text-success'></i>";
                    }

                }
            };
        })
    }
})