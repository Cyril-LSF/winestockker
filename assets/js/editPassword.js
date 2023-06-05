window.addEventListener('load', function(e) {
    if (document.forms['edit_password']) {
        document.forms['edit_password'].addEventListener('submit', function(e) {
            let error;
            const inputs = this;
        
            const errorMessage = {
                oldPassword: 'Veuillez saisir votre mot de passe actuel',
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
    
            if (inputs['edit_password[oldPassword]']) {
                if(inputs['edit_password[oldPassword]'].value == '') {
                    setErrorField('.oldPassword-error', 'oldPassword');
                }
            }
            
            if (inputs['edit_password[password][first]']) {
                if(!inputs['edit_password[password][first]'].value.match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/i)) {
                    setErrorField('.password-error', 'password');
                }
            
                if(inputs['edit_password[password][second]'].value != inputs['edit_password[password][second]'].value){
                    setErrorField('.confirm-password-error', 'confirmPassword');
                }
            }
            
        
        })
    } else if (document.forms['change_password_form']) {
        document.forms['change_password_form'].addEventListener('submit', function(e) {
            let error;
            const inputs = this;
        
            const errorMessage = {
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
        
                if(!inputs[i].value.trim()) {
                    e.preventDefault();
                    error = 'veuillez remplir tous les champs';
                    alert(error);
                    break;
                }
            }
            
            if (inputs['change_password_form[plainPassword][first]']) {
                if(!inputs['change_password_form[plainPassword][first]'].value.match(/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/i)) {
                    setErrorField('.password-error', 'password');
                }
            
                if(inputs['change_password_form[plainPassword][second]'].value != inputs['change_password_form[plainPassword][first]'].value){
                    setErrorField('.confirm-password-error', 'confirmPassword');
                }
            }
            
        
        })
    }
    
})