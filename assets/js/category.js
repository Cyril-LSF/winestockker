window.addEventListener('load', function(e) {
    document.forms['category'].addEventListener('submit', function(e) {
        let error;
        const inputs = this;
    
        const errorMessage = {
            name: 'Caractères : Min.1 - Max.255',
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
                error = 'veuillez saisir un nom de catégorie';
                alert(error);
                break;
            }
        }
        
        if (!inputs['category[name]'].value.match(/^.{1,255}$/i)) {
            setErrorField('.name-error', 'name');
        }

    })
})