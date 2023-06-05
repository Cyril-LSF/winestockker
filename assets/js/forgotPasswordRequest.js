window.addEventListener('load', function(e) {
    document.forms['reset_password_request_form'].addEventListener('submit', function(e) {
        let error;
        const inputs = this;
    
        const errorMessage = {
            lastname: 'Caractères : Min.3-Max.45, seul les lettres, les tirets et les espaces sont autorisés',
            firstname: 'Caractères : Min.3-Max.50, seul les lettres, les tirets et les espaces sont autorisés',
            email: 'Veuillez saisir une adresse email valide',
            birthday: 'Vous devez avoir au moins 18 ans pour vous inscrire',
            password: 'le mot de passe doit contenir au moins 8 caractères dont 1 majuscule, 1 minuscule, 1 chiffre et 1 caractère spécial',
            confirmPassword: 'Les mots de passe ne sont pas identique',
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
    
            if(!inputs[i].value.trim() && inputs[i] !== inputs['registration_form[picture]']) {
                e.preventDefault();
                error = 'veuillez remplir tous les champs';
                alert(error);
                break;
            }
        }
    
        if(!inputs['reset_password_request_form[email]'].value.match(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/)) {
            setErrorField('.email-error', 'email');
        }
    })
})