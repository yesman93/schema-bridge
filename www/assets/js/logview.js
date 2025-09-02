/**
 * JS for log view
 *
 *
 */


document.addEventListener('DOMContentLoaded', function () {

    // Channel change
    const channel = document.querySelector('#channel');
    if (channel) {

        channel.addEventListener('change', e => {
            const value = e.target.closest('[name="channel"]').value;
            window.location.href = value;
        });
    }

    // Log file selection
    const log_files = document.querySelectorAll('[name="log_file"]');
    if (log_files) {

        log_files.forEach(radio => {
            radio.addEventListener('change', e => {
                if (e.target.checked) {
                    window.location.href = e.target.value;
                }
            });
        });
    }

    // Group by request ID
    const groupby_requestid = document.querySelector('[name="group_by_requestid"]');
    if (groupby_requestid) {

        groupby_requestid.addEventListener('change', e => {
            if (e.target.checked) {
                window.location.href = e.target.dataset.uriOn;
            } else {
                window.location.href = e.target.dataset.uriOff;
            }
        });
    }

    // Log level filter
    const levels = document.querySelectorAll('[name^="levels"]');
    if (levels) {

        levels.forEach(checkbox => {

            checkbox.addEventListener('change', e => {
                const checked_levels = harvest_levels();
                set_filter( 'levels', checked_levels );
            });
        });
    }


});

/**
 * Harvest checked log levels
 *
 *
 * @returns {Array}
 */
function harvest_levels() {

    let levels = [];

    document.querySelectorAll('[name^="levels"]')?.forEach(checkbox => {

        if (checkbox.checked) {
            levels.push(checkbox.value);
        }
    });

    return levels;
}



