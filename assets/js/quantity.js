window.addEventListener('load', function(e) {
    document.forms['quantity'].addEventListener('submit', function(e) {
        let error;
        const inputs = this;
    
        const errorMessage = {
            quantity: 'Caractères : Min.1 - Max.7, seul les chiffres sont autorisés',
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
                error = 'veuillez saisir une quantité';
                alert(error);
                break;
            }
        }
        
        if (!inputs['quantity[quantity]'].value.match(/^\d+$/)) {
            setErrorField('.quantity-error', 'quantity');
        }

    })
})