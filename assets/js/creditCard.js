window.addEventListener('load', function(e) {

    /**
     * Ajax credit card selection
     */

    let selectedBtn = document.querySelectorAll('.selected_btn_credit_card');

    for (let i = 0; i < selectedBtn.length; i++) {
        selectedBtn[i].addEventListener('click', function() {
            let xhr = new XMLHttpRequest();
            let url = selectedBtn[i].getAttribute('data-url');
            xhr.open('POST', url);
            xhr.send();

            xhr.onload = function () {
                let data = JSON.parse(xhr.response);

                if (data.status === true) {
                    let creditCard = document.getElementById('creditCard_card' + i);
                    if (data.isSelected == true){
                        // reset
                        let cards = document.querySelectorAll('.card_creditCard');
                        cards.forEach((object)=> {
                            object.classList.remove('card_selected');
                        });
                        let buttons = document.querySelectorAll('.selected_btn_credit_card');
                        buttons.forEach((object) => {
                            object.innerHTML =  "<i class='fa-solid fa-check text-success'></i>";
                        });
                        // end reset

                        selectedBtn[i].innerHTML = "<i class='fa-solid fa-xmark text-danger'></i>";
                        creditCard.classList.add('card_selected');
                    } else {
                        creditCard.classList.remove('card_selected');
                        selectedBtn[i].innerHTML = "<i class='fa-solid fa-check text-success'></i>";
                    }

                }
            };
        })
    }

    /**
     * End ajax credit card selection
     */

    /**
     * Credit card form
     */

    document.forms['credit_card'].addEventListener('submit', function(e) {
        let error;
        const inputs = this;
    
        const errorMessage = {
            number: "Caractères : Min.16 - Max.16, seul les chiffres sont autorisés",
            name: "Caractères : Min.3 - Max.255, seul les lettres, les tirets et les espaces sont autorisés",
            expiration: "Le format doit correspondre à cet exemple : 12/26",
            securityCode: "Caractères : Min.3 - Max.3, seul les chiffres sont autorisés",
        };
    
        const setRedColor = (element) => {
            element.style.color = 'red';
        };
    
        const setErrorField = (className, errorTarget) => {
            e.preventDefault();
            let domSelector = document.querySelector(className);
            error = errorMessage[errorTarget];
            domSelector.innerHTML = error;
            setRedColor(domSelector);
        };
    
        for (let i = 0; i < inputs.length; i++) {
    
            if(!inputs[i].value.trim()) {
                e.preventDefault();
                error = 'veuillez remplir tous les champs';
                alert(error);
                break;
            }
        }
        
        if (!inputs['credit_card[number]'].value.match(/^\d{16}$/)) {
            setErrorField('.number-error', 'number');
        }
        
        if (!inputs['credit_card[name]'].value.match(/^[a-z\-\s]{3,255}$/i)) {
            console.log('ici');
            setErrorField('.nameCard-error', 'name');
        }

        if (!inputs['credit_card[expiration]'].value.match(/^(\d{2})(\/)(\d{2})$/)) {
            setErrorField('.expiration-error', 'expiration');
        }

        if (!inputs['credit_card[securityCode]'].value.match(/^\d{3}$/)) {
            setErrorField('.securityCode-error', 'securityCode');
        }
    
    })
})