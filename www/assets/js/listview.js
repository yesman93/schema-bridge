/**
 * JS for listview
 *
 */




document.addEventListener('DOMContentLoaded', function () {

    load_pagination();

    const search_query = document.querySelector('.search-query-input');
    if (search_query) {

        search_query.addEventListener('input', e => {

            const eraser = document.querySelector('.listview-search-eraser');
            const value = e.target.value || '';
            if (eraser) {

                if (value.length) {
                    eraser.classList.remove('d-none');
                } else {
                    eraser.classList.add('d-none');
                }
            }
        });
    }

});

function load_pagination() {

    const pagination = document.querySelector('#listview_pagination');
    const uri = document.querySelector('input[name="__fulluri"]')?.value || null;
    if (!pagination || !uri) {
        return;
    }

    fetch('/lumio/listview/pagination', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ uri })
    })
    .then(response => response.json())
    .then(data => {

        const { controls, counts_info } = data;

        if (pagination) {
            pagination.parentElement.innerHTML = controls;
        }

        const info = document.querySelector('#listview_counts_info');
        if (info) {
            info.innerHTML = counts_info;
            info.classList.remove('placeholder');
        }

    })
    .catch(error => {
    console.log('listview pagination: error: ', error);

    });
}

