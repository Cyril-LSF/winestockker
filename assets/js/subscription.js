window.addEventListener('load', function(e) {
    // FORM
    document.forms['subscription'].addEventListener('submit', function(e) {
        let error;
        const inputs = this;
    
        const errorMessage = {
            name: 'Caractères : Min.3 - Max.255, seul les lettres, les chiffres, les tirets et les espaces sont autorisés',
            price: 'Caractères : Min.1 - Max.50, seul les chiffres sont autorisés',
            priceInCents: 'Caractères : Min.1 - Max.50, seul les chiffres sont autorisés',
            duration: 'Caractères : Min.1, seul les chiffres sont autorisées',
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
                error = 'veuillez remplir tous les champs obligatoires';
                alert(error);
                break;
            }
        }
        
        if (!inputs['subscription[name]'].value.match(/^[\w\s\-àáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð]{3,255}$/i)) {
            setErrorField('.name-error', 'name');
        }

        if (!inputs['subscription[price]'].value.match(/^\d{1,50}$/)) {
            setErrorField('.price-error', 'price');
        }

        if (!inputs['subscription[priceInCents]'].value.match(/^\d{1,50}$/)) {
            setErrorField('.priceInCents-error', 'priceInCents');
        }

        if (!inputs['subscription[duration]'].value.match(/^\d+$/)) {
            setErrorField('.duration-error', 'duration');
        }

    })
})