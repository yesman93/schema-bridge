/**
 * Modalio - Lazy Singleton Modal Manager for Lumio
 *
 * Manages dynamic iframe modals, confirmation dialogs, and alerts.
 * Automatically appends modal HTML on first use. Includes support for
 * sizing, keyboard handling, and progress indication during load.
 *
 */
Object.defineProperty(window, 'Modalio', {

    /**
     * Prevents reconfiguration of the 'progressbar' property on window.
     * Ensures the singleton definition cannot be changed or deleted.
     *
     * @type {Boolean}
     */
    configurable: false,

    /**
     * Hides the 'progressbar' property from enumeration (e.g. for...in or Object.keys()).
     * Makes it invisible in most reflective operations.
     *
     * @type {Boolean}
     */
    enumerable: false,

    /**
     * Getter function implementing lazy singleton creation.
     * First access to window.Modalio initializes the Progressbar instance,
     * subsequent accesses return the cached singleton.
     *
     * @returns {Modalio} Singleton instance of the modal management.
     */
    get: (function () {

        /**
         * Internal cached instance of the Modalio singleton.
         * Lazily initialized upon first access to `window.Modalio`.
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
                 * Modal container ID selector.
                 *
                 * @type {string}
                 */
                modal_id: 'general_modal',

                /**
                 * Confirmation modal ID.
                 *
                 * @type {string}
                 */
                confirm_modal_id: 'confirm_modal',

                /**
                 * Alert modal ID.
                 *
                 * @type {string}
                 */
                alert_modal_id: 'alert_modal',

                /**
                 * Opens iframe modal with the given URL.
                 *
                 * @param {String} url
                 * @param {String} size_class Bootstrap modal size class (e.g. modal-lg)
                 *
                 * @returns {void}
                 */
                open(url, size_class = 'modal-md') {

                    this._ensure_modal_html();

                    Progressbar.start();

                    const modal = document.getElementById(this.modal_id);
                    const iframe = modal.querySelector('iframe');
                    const dialog = modal.querySelector('.modal-dialog');

                    iframe.src = url;
                    dialog.className = `modal-dialog modal-dialog-centered ${size_class}`;

                    iframe.onload = () => {
                        this._resize();
                        Progressbar.stop();
                    };

                    const modal_instance = bootstrap.Modal.getOrCreateInstance(modal);
                    modal_instance.show();
                },

                /**
                 * Resize iframe modal content based on iframe body height.
                 *
                 * @returns {void}
                 */
                _resize() {

                    const modal = document.getElementById(this.modal_id);
                    const iframe = modal.querySelector('iframe');

                    if (!iframe || !iframe.contentWindow || !iframe.contentWindow.document) {
                        return;
                    }

                    setTimeout(() => {
                        const height = iframe.contentWindow.document.body.scrollHeight;
                        iframe.style.height = height + 'px';
                        iframe.style.width = '100%';
                    }, 300);
                },

                /**
                 * Opens a confirmation modal.
                 *
                 * @param {String} message
                 * @param {Function|null} on_ok
                 * @param {Function|null} on_cancel
                 *
                 * @returns {void}
                 */
                confirm(message, on_ok = null, on_cancel = null) {

                    this._ensure_confirm_modal();

                    const modal = document.getElementById(this.confirm_modal_id);
                    const modal_body = modal.querySelector('.modal-body');
                    const btn_ok = modal.querySelector('.btn-confirm-ok');
                    const btn_cancel = modal.querySelector('.btn-confirm-cancel');

                    modal_body.innerHTML = message;

                    btn_ok.onclick = () => {

                        this.close_confirm();

                        if (typeof on_ok === 'function') {
                            on_ok();
                        }
                    };

                    btn_cancel.onclick = () => {

                        this.close_confirm();

                        if (typeof on_cancel === 'function') {
                            on_cancel();
                        }
                    };

                    bootstrap.Modal.getOrCreateInstance(modal).show();
                },

                /**
                 * Opens an alert modal.
                 *
                 * @param {String} message
                 * @param {Function|null} on_close
                 *
                 * @returns {void}
                 */
                alert(message, on_close = null) {

                    this._ensure_alert_modal();

                    const modal = document.getElementById(this.alert_modal_id);
                    const modal_body = modal.querySelector('.modal-body');
                    const btn_close = modal.querySelector('.btn-confirm-cancel');

                    modal_body.innerHTML = message;

                    btn_close.onclick = () => {

                        this.close_alert();

                        if (typeof on_close === 'function') {
                            on_close();
                        }
                    };

                    bootstrap.Modal.getOrCreateInstance(modal).show();
                },

                /**
                 * Closes the iframe modal.
                 *
                 * @returns {void}
                 */
                close() {

                    const modal = document.getElementById(this.modal_id);
                    const iframe = modal.querySelector('iframe');

                    iframe.src = '';
                    iframe.removeAttribute('style');
                    bootstrap.Modal.getOrCreateInstance(modal).hide();
                },

                /**
                 * Closes the confirmation modal.
                 *
                 * @returns {void}
                 */
                close_confirm() {
                    const modal = document.getElementById(this.confirm_modal_id);
                    bootstrap.Modal.getOrCreateInstance(modal).hide();
                },

                /**
                 * Closes the alert modal.
                 *
                 * @returns {void}
                 */
                close_alert() {
                    const modal = document.getElementById(this.alert_modal_id);
                    bootstrap.Modal.getOrCreateInstance(modal).hide();
                },

                /**
                 * Initializes event listeners for modals.
                 * Handles ESC and ENTER keys for confirm/alert modals.
                 *
                 * @returns {void}
                 */
                _init_keyboard_controls() {

                    document.addEventListener('keydown', (e) => {

                        const is_confirm_visible = document.getElementById(this.confirm_modal_id)?.classList.contains('show');
                        const is_alert_visible = document.getElementById(this.alert_modal_id)?.classList.contains('show');

                        if (e.key === 'Escape') {

                            if (is_confirm_visible) {
                                this.close_confirm();
                            }

                            if (is_alert_visible) {
                                this.close_alert();
                            }
                        }

                        if (e.key === 'Enter') {

                            if (is_confirm_visible) {

                                const btn_ok = document.getElementById(this.confirm_modal_id).querySelector('.btn-confirm-ok');
                                if (btn_ok) {
                                    btn_ok.click();
                                }
                            }
                            if (is_alert_visible) {

                                const btn_close = document.getElementById(this.alert_modal_id).querySelector('.btn-confirm-cancel');
                                if (btn_close) {
                                    btn_close.click();
                                }
                            }
                        }
                    });
                },

                /**
                 * Ensures that iframe modal HTML structure is in the DOM.
                 *
                 * @returns {void}
                 */
                _ensure_modal_html() {

                    if (document.getElementById(this.modal_id)) {
                        return;
                    }

                    const html = `
                        <div class="modal fade" id="${this.modal_id}" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <iframe src="" scrolling="no" class="general-modal-iframe rounded-3" frameborder="0"></iframe>
                                </div>
                            </div>
                        </div>`;

                    document.body.insertAdjacentHTML('beforeend', html);
                },

                /**
                 * Ensure the confirm modal exists in the DOM.
                 *
                 * @private
                 * @returns {void}
                 */
                _ensure_confirm_modal() {

                    if (document.querySelector('#confirm_modal')) {
                        return;
                    }

                    const html = `
                        <div class="modal fade" id="confirm_modal" tabindex="-1" role="dialog" aria-labelledby="confirm_modal_title" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body text-center"></div>
                                    <div class="modal-footer justify-content-center">
                                        <button type="button" class="btn text-bg-success btn-confirm-ok" data-dismiss="modal">Ano</button>
                                        <button type="button" class="btn text-bg-danger btn-confirm-cancel">Ne</button>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    document.body.insertAdjacentHTML('beforeend', html);
                },

                /**
                 * Ensure the alert modal exists in the DOM.
                 *
                 * @private
                 * @returns {void}
                 */
                _ensure_alert_modal() {

                    if (document.querySelector('#alert_modal')) {
                        return;
                    }

                    const html = `
                        <div class="modal fade" id="alert_modal" tabindex="-1" role="dialog" aria-labelledby="alert_modal_title" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-body text-center"></div>
                                    <div class="modal-footer justify-content-center">
                                        <button type="button" class="btn text-bg-primary mb-1 btn-confirm-cancel">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                    document.body.insertAdjacentHTML('beforeend', html);
                }

            };

            // Run keyboard listener setup
            _instance._init_keyboard_controls();

            return _instance;
        };

    })()

});
