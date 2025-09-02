/**
 * JS for forms
 *
 * @author TB
 * @date 2.5.2025
 *
 */


document.addEventListener('DOMContentLoaded', function () {

    init_select2();
    init_choices();

    // Toggle password visibility
    const password_toggles = document.querySelectorAll('.password-input-toggle');
    if (password_toggles) {

        password_toggles.forEach( toggle => {
            toggle.addEventListener('click', e => toggle_password(toggle) );
        });
    }

});



/**
 * Toggle password visibility
 *
 * @author TB
 * @date 2.5.2025
 *
 * @param {Element} toggle
 *
 * @returns {void}
 */
function toggle_password(toggle) {

    if (!toggle) {
        return;
    }

    let input = toggle.closest('.form-group').querySelector('input[type="password"]');
    if (!input) {
        input = toggle.closest('.form-group').querySelector('input[type="text"]');
        if (!input) {
            return;
        }
    }

    const type = input.getAttribute('type');
    if (!type) {
        return;
    }

    if (type === 'password') {

        toggle.classList.add('fa-eye-slash');
        toggle.classList.remove('fa-eye');

        input.setAttribute('type', 'text');

    } else {

        toggle.classList.add('fa-eye');
        toggle.classList.remove('fa-eye-slash');

        input.setAttribute('type', 'password');
    }
}

/**
 * Initialize select2 plugin
 *
 * @author TB
 * @date 3.5.2025
 *
 * @param {String|undefined} selector
 *
 * @returns {void}
 */
function init_select2(selector) {

    selector = typeof selector === 'undefined' ? '.select2-select' : selector;

    const selects = document.querySelectorAll(selector);
    if (selects) {

        selects.forEach( select => {

            let opts = {};

            let is_search = parseInt(select.dataset.showSearch || 0);
            is_search = Number.isNaN(is_search) ? 0 : is_search;
            if (!is_search) {
                opts.minimumResultsForSearch = -1;
            }

            $(select).select2(opts);
        });
    }
}

/**
 * Initialize choices plugin
 *
 * @author TB
 * @date 3.5.2025
 *
 * @param {String|undefined} selector
 *
 * @returns {void}
 */
function init_choices(selector) {

    selector = typeof selector === 'undefined' ? '.choices-select' : selector;

    const selects = document.querySelectorAll(selector);
    if (selects) {

        selects.forEach( select => {

            let opts = {};

            let is_search = parseInt(select.dataset.showSearch || 0);
            is_search = Number.isNaN(is_search) ? 0 : is_search;
            opts.searchEnabled = is_search ? true : false;

            opts.removeItemButton = true;
            opts.shouldSort = false;

            const instance = new Choices(select, opts);
        });
    }
}


