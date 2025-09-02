/**
 * JS for filters
 *
 */



document.addEventListener('DOMContentLoaded', function () {

    // Cancel list view filter
    const filter_cancels = document.querySelectorAll('.filter-cancel');
    if (filter_cancels) {

        filter_cancels.forEach( filter_cancel => {

            filter_cancel.addEventListener('click', e => {

                e.preventDefault();

                const filter = e.target.closest('.filter-cancel')?.dataset.filterName || null;
                const uri = document.querySelector('input[name="__fulluri"]')?.value || null;
                if (filter && uri) {

                    fetch('/lumio/filter/remove', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: new URLSearchParams({ uri, filter })
                    })
                    .then(response => response.text())
                    .then(data => {

                        if (typeof data === 'string' && data.length) {
                            window.location.href = data;
                        }

                    })
                    .catch(error => {

                    });
                }
            });
        });
    }

});

/**
 * Set filters
 *
 * @param {String} name - Filter name
 * @param {Array|Object|String|Number} value - Filter value
 *
 * @returns {void}
 */
function set_filter(name, value) {

    if (typeof name === 'undefined' || typeof value === 'undefined') {
        return;
    }

    if (typeof value === 'object' || typeof value === 'array') {
        value = JSON.stringify(value);
    }

    const uri = document.querySelector('input[name="__fulluri"]')?.value || null;
    if (uri) {

        fetch('/lumio/filter/set', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                uri,
                filter_name: name,
                filter_value: value
            })
        })
        .then(response => response.text())
        .then(data => {

            if (typeof data === 'string' && data.length) {
                window.location.href = data;
            }

        })
        .catch(error => {

        });
    }
}

