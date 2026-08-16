<div
    x-data="{
        visible: false,
        title: '',
        message: '',
        confirmLabel: 'Confirm',
        cancelLabel: 'Cancel',
        danger: true,
        _resolve: null,
        open(detail) {
            this.title = detail.title || 'Please confirm';
            this.message = detail.message || 'Are you sure?';
            this.confirmLabel = detail.confirmLabel || 'Confirm';
            this.cancelLabel = detail.cancelLabel || 'Cancel';
            this.danger = detail.danger !== false;
            this._resolve = detail.resolve || null;
            this.visible = true;
        },
        confirm() {
            this.visible = false;
            if (this._resolve) this._resolve(true);
            this._resolve = null;
        },
        cancel() {
            this.visible = false;
            if (this._resolve) this._resolve(false);
            this._resolve = null;
        },
    }"
    x-on:adm-confirm-open.window="open($event.detail)"
    x-on:livewire:navigating.window="cancel()"
    class="adm-confirm-overlay"
    :class="{ 'is-open': visible }"
    x-on:click.self="cancel()"
    x-on:keydown.escape.window="cancel()"
>
    <div class="adm-confirm-modal">
        <h3 x-text="title"></h3>
        <p x-text="message"></p>
        <div class="adm-confirm-btns">
            <button type="button" class="adm-confirm-btn adm-confirm-btn-gray" x-on:click="cancel()" x-text="cancelLabel"></button>
            <button type="button" class="adm-confirm-btn" :class="danger ? 'adm-confirm-btn-danger' : 'adm-confirm-btn-primary'" x-on:click="confirm()" x-text="confirmLabel"></button>
        </div>
    </div>
</div>

<style>
.adm-confirm-overlay {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(15, 23, 42, .55);
    backdrop-filter: blur(2px);
    align-items: center;
    justify-content: center;
    padding: 20px;
    font-family: Inter, ui-sans-serif, system-ui, -apple-system, "Segoe UI", sans-serif;
}
.adm-confirm-overlay.is-open {
    display: flex;
}
.adm-confirm-modal {
    background: #ffffff;
    border: 1px solid rgba(15, 23, 42, .1);
    border-radius: 14px;
    padding: 24px;
    width: 420px;
    max-width: 95vw;
    box-shadow: 0 20px 60px rgba(0, 0, 0, .35);
}
html.dark .adm-confirm-modal {
    background: #1e293b;
    border-color: rgba(255, 255, 255, .1);
}
.adm-confirm-modal h3 {
    font-size: 15px;
    font-weight: 750;
    color: #0f172a;
    margin: 0 0 10px;
}
html.dark .adm-confirm-modal h3 {
    color: #e2e8f0;
}
.adm-confirm-modal p {
    font-size: 12.5px;
    line-height: 1.6;
    color: #64748b;
    margin: 0;
}
.adm-confirm-btns {
    display: flex;
    justify-content: flex-end;
    gap: 8px;
    margin-top: 20px;
}
.adm-confirm-btn {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    border: none;
    font-family: inherit;
    transition: opacity .15s;
}
.adm-confirm-btn:hover {
    opacity: .85;
}
.adm-confirm-btn-gray {
    background: #f1f5f9;
    color: #0f172a;
    border: 1px solid rgba(15, 23, 42, .14);
}
html.dark .adm-confirm-btn-gray {
    background: #263245;
    color: #e2e8f0;
    border-color: rgba(255, 255, 255, .13);
}
.adm-confirm-btn-danger {
    background: #dc2626;
    color: #fff;
}
.adm-confirm-btn-primary {
    background: #6366f1;
    color: #fff;
}
</style>

<script>
if (!window.admConfirm) {
    window.admConfirm = function (message, opts) {
        opts = opts || {};
        return new Promise(function (resolve) {
            window.dispatchEvent(new CustomEvent('adm-confirm-open', {
                detail: {
                    title: opts.title || 'Please confirm',
                    message: message,
                    confirmLabel: opts.confirmLabel || 'Confirm',
                    cancelLabel: opts.cancelLabel || 'Cancel',
                    danger: opts.danger !== false,
                    resolve: resolve,
                },
            }));
        });
    };

    document.addEventListener('livewire:init', function () {
        Livewire.hook('directive.init', function (payload) {
            var el = payload.el;
            var directive = payload.directive;
            if (directive.value !== 'confirm') return;

            var message = directive.expression.replaceAll('\\n', '\n');
            if (message === '') message = 'Are you sure?';
            var isDanger = !message.toLowerCase().startsWith('unblock')
                && !message.toLowerCase().startsWith('verify');

            el.__livewire_confirm = function (action, instead) {
                window.admConfirm(message, { danger: isDanger }).then(function (ok) {
                    if (ok) action(); else instead();
                });
            };
        });
    });
}
</script>
