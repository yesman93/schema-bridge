/**
 * Progressbar utility (lazy-loaded singleton)
 *
 * This module provides a progress bar functionality for web applications.
 *
 *
 */
Object.defineProperty(window, 'Progressbar', {

    /**
     * Prevents reconfiguration of the 'progressbar' property on window.
     * Ensures the singleton definition cannot be changed or deleted.
     *
     *
     * @type {Boolean}
     */
    configurable: false,

    /**
     * Hides the 'progressbar' property from enumeration (e.g. for...in or Object.keys()).
     * Makes it invisible in most reflective operations.
     *
     *
     * @type {Boolean}
     */
    enumerable: false,

    /**
     * Getter function implementing lazy singleton creation.
     * First access to window.Progressbar initializes the Progressbar instance,
     * subsequent accesses return the cached singleton.
     *
     *
     * @returns {Progressbar} Singleton instance of the progressbar.
     */
    get: (function () {

        /**
         * Internal cached instance of the Progressbar singleton.
         * Lazily initialized upon first access to `window.Progressbar`.
         *
         *
         * @type {Object|null}
         */
        let _instance = null;

        return function () {

            if (_instance) {
                return _instance;
            }

            _instance = {

                /**
                 * Default progress duration in milliseconds.
                 *
                 *
                 * @type {Number}
                 */
                duration_default: 5000,

                /**
                 * Interval ID for updating progress.
                 *
                 *
                 * @type {Number|null}
                 */
                _interval: null,

                /**
                 * Current progress (in %).
                 *
                 *
                 * @type {Number}
                 */
                _progress: 0,

                /**
                 * Start the progress bar.
                 *
                 *
                 * @param {Number} duration
                 * @param {String} label
                 * @param {Boolean} transparent
                 * @param {String} color
                 *
                 * @returns {void}
                 */
                start(duration = this.duration_default, label = '', transparent = false, color = 'bg-dark') {

                    // Clear previous
                    this.stop();

                    const backdrop_class = transparent ? ' is-transparent ' : '';
                    const backdrop = document.createElement('div');
                    backdrop.className = `progress-backdrop${backdrop_class}`;

                    const modal = document.createElement('div');
                    modal.className = 'progress-modal p-3 rounded';

                    if (label) {

                        const caption = document.createElement('div');
                        caption.className = 'progress-caption text-left fs-12 fw-bolder mb-2 opacity-75';
                        caption.innerText = label;
                        modal.appendChild(caption);
                    }

                    const progress = document.createElement('div');
                    progress.className = 'progress';

                    const progress_bar = document.createElement('div');
                    progress_bar.className = `progress-bar ${color}`;
                    progress_bar.role = 'progressbar';
                    progress_bar.style.width = '0%';
                    progress_bar.setAttribute('aria-valuenow', '0');
                    progress_bar.setAttribute('aria-valuemin', '0');
                    progress_bar.setAttribute('aria-valuemax', '100');
                    progress_bar.innerText = '0 %';

                    progress.appendChild(progress_bar);
                    modal.appendChild(progress);
                    backdrop.appendChild(modal);

                    document.body.appendChild(backdrop);
                    document.body.style.overflow = 'hidden';

                    this._progress = 0;

                    this._interval = setInterval(() => {

                        this._progress += 1;
                        if (this._progress >= 100) return;

                        progress_bar.style.width = `${this._progress}%`;
                        progress_bar.setAttribute('aria-valuenow', this._progress);
                        progress_bar.innerText = `${this._progress} %`;
                    }, duration / 100);
                },

                /**
                 * Update the caption text.
                 *
                 *
                 * @param {String} label
                 *
                 * @returns {void}
                 */
                update_label(label) {

                    const caption = document.querySelector('.progress-caption');
                    if (caption) {
                        caption.innerText = label;
                    } else {

                        const modal = document.querySelector('.progress-modal');
                        if (modal) {

                            const new_caption = document.createElement('div');
                            new_caption.className = 'progress-caption text-left fs-12 fw-bolder mb-2 opacity-75';
                            new_caption.innerText = label;
                            modal.prepend(new_caption);
                        }
                    }
                },

                /**
                 * Stop and remove the progress indicator.
                 *
                 *
                 * @param {Function} on_complete
                 *
                 * @returns {void}
                 */
                stop(on_complete) {
                    const backdrop = document.querySelector('.progress-backdrop');
                    const progress_bar = document.querySelector('.progress-bar');

                    if (!backdrop || !progress_bar) return;

                    progress_bar.style.width = '100%';
                    progress_bar.setAttribute('aria-valuenow', '100');
                    setTimeout(() => progress_bar.innerText = '100 %', 50);

                    setTimeout(() => {
                        backdrop.remove();
                        document.body.style.overflow = '';
                        if (typeof on_complete === 'function') {
                            on_complete();
                        }
                    }, 300);

                    clearInterval(this._interval);
                    this._interval = null;
                    this._progress = 0;
                }
            };

            return _instance;
        };

    })()

});



