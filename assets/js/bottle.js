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

    // FORM
    let form = document.forms['bottle'];
    if (form) {
        document.forms['bottle'].addEventListener('submit', function(e) {
            let error;
            const inputs = this;
        
            const errorMessage = {
                name: 'Caractères : Min.1 - Max.255',
                year: 'Caractères : Max.5, seul les chiffres sont autorisés',
                origin: 'Caractères : Max.50, seul les lettres sont autorisées',
                vine: 'Caractères : Max.255, seul les lettres sont autorisées',
                enbottler: 'Caractères : Max.255, seul les lettres sont autorisées',
                price : 'Caractères : Max.10, seul les chiffres sont autorisés',
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
        
                if(!inputs[i].value.trim() && inputs[i].getAttribute('name') == 'bottle[name]') {
                    e.preventDefault();
                    error = 'veuillez saisir un nom de bouteille';
                    alert(error);
                    break;
                }
            }
            
            if (!inputs['bottle[name]'].value.match(/^.{1,255}$/i)) {
                setErrorField('.name-error', 'name');
            }
    
            if (!inputs['bottle[year]'].value.match(/^\d{0,5}$/)) {
                setErrorField('.year-error', 'year');
            }
    
            if (!inputs['bottle[origin]'].value.match(/^[a-z]{0,50}$/i)) {
                setErrorField('.origin-error', 'origin');
            }
    
            if (!inputs['bottle[vine]'].value.match(/^[a-z\s\-éèêëâäîï]{0,255}$/i)) {
                setErrorField('.vine-error', 'vine');
            }
    
            if (!inputs['bottle[enbottler]'].value.match(/^[a-z\s\-éèêëâäîï]{0,255}$/i)) {
                setErrorField('.enbottler-error', 'enbottler');
            }
    
            if (!inputs['bottle[price]'].value.match(/^\d{0,10}$/)) {
                setErrorField('.price-error', 'price');
            }
    
        })
    }
    
})