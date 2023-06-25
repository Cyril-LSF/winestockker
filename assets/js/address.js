window.addEventListener('load', function(e) {

    /**
     * Ajax address selection
     */

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

    /**
     * End ajax address selection
     */

    /**
     * Address form
     */

    document.forms['address'].addEventListener('submit', function(e) {
        let error;
        const inputs = this;
    
        const errorMessage = {
            name: "Caractères : Min.3 - Max.255",
            streetNumber: "Caractères : Min.1 - Max.10, seul les chiffres sont autorisés",
            streetNumberExtension: "Seul les lettres sont autorisées",
            streetType: "Types acceptés : Rue, Impasse, Quai, Boulevard",
            streetName: "Caractères : Min.3 - Max.100",
            complement: "Caractères : Max.255",
            postalcode: "Caractères : Min.5 - Max.10, seul les chiffres sont autorisés",
            city: "Caractères : Min.3 - Max.200, seul les lettres et les tirets sont autorisés",
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
        
        if (!inputs['address[name]'].value.match(/^.{3,255}$/i)) {
            setErrorField('.name-error', 'name');
        }
    
        if(!inputs['address[streetNumber]'].value.match(/^\d{1,10}$/)) {
            setErrorField('.streetNumber-error', 'streetNumber');
        }
    
        if(!inputs['address[streetNumberExtension]'].value.match(/^[a-zA-Z]*$/)) {
            setErrorField('.streetNumberExtension-error', 'streetNumberExtension');
        }

        switch (inputs['address[streetType]'].value) {
            case 'rue':
            case 'impasse':
            case 'quai':
            case 'boulevard':
                break;
            default:
                setErrorField('.streetType-error', 'streetType');
                break;
        }
        
        if(!inputs['address[streetName]'].value.match(/^.{3,100}$/i)) {
            setErrorField('.streetName-error', 'streetName');
        }

        if(!inputs['address[complement]'].value.match(/^.{0,255}$/i)) {
            setErrorField('.complement-error', 'complement');
        }

        if(!inputs['address[postalcode]'].value.match(/^\d{5,10}$/)) {
            setErrorField('.postalcode-error', 'postalcode');
        }

        if(!inputs['address[city]'].value.match(/^[a-z\-]{3,200}$/i)) {
            setErrorField('.city-error', 'city');
        }
    
    })
})