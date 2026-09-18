// "Agregar al carrito" sin recargar la página.
// Intercepta cualquier <form data-cart-add>, lo manda por fetch y actualiza
// el badge del navbar + muestra un toast. Si JS falla, el form se envía normal.

function toast(message, ok = true) {
    let root = document.getElementById('toast-root');
    if (!root) {
        root = document.createElement('div');
        root.id = 'toast-root';
        root.className = 'fixed bottom-4 right-4 z-[100] flex flex-col items-end gap-2 px-4 sm:px-0';
        document.body.appendChild(root);
    }

    const el = document.createElement('div');
    el.setAttribute('role', 'status');
    el.className =
        'pointer-events-auto max-w-sm rounded-xl px-4 py-3 text-sm font-semibold shadow-lg transition ' +
        (ok ? 'bg-marca-negro text-marca-blanco' : 'bg-marca-rojo text-marca-blanco');
    el.textContent = message;
    root.appendChild(el);

    setTimeout(() => {
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 300);
    }, 2600);
}

function updateCount(count) {
    document.querySelectorAll('[data-cart-count]').forEach((badge) => {
        badge.textContent = count;
        badge.hidden = !count;
    });
}

document.addEventListener('submit', async (event) => {
    const form = event.target.closest('form[data-cart-add]');
    if (!form) return;

    event.preventDefault();

    const button = form.querySelector('[type="submit"]');
    const token = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    if (button) button.disabled = true;

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: new FormData(form),
        });

        const data = await response.json().catch(() => ({}));

        if (!response.ok) {
            toast(data.message || 'No se pudo agregar el producto.', false);
            return;
        }

        updateCount(data.count ?? 0);
        toast(data.message || 'Producto agregado al carrito.');
    } catch (error) {
        toast('No se pudo conectar. Revisá tu conexión e intentá de nuevo.', false);
    } finally {
        if (button) button.disabled = false;
    }
});
