import Chart from 'chart.js/auto';

/**
 * Registrar/editar trabajo de mecánico: al elegir un producto (o cambiar la
 * cantidad), el monto se autocompleta con el precio del producto, pero el
 * admin puede modificarlo a mano después sin que se vuelva a pisar.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-product-select]').forEach((select) => {
        const form = select.closest('form');
        if (!form) return;
        const qty = form.querySelector('[data-product-qty]');
        const amount = form.querySelector('[data-product-amount]');
        if (!qty || !amount) return;

        const recalc = () => {
            const option = select.options[select.selectedIndex];
            const price = option ? parseFloat(option.dataset.price || '0') : 0;
            if (!price) return;
            const quantity = parseInt(qty.value, 10) || 1;
            amount.value = (price * quantity).toFixed(2);
        };

        select.addEventListener('change', recalc);
        qty.addEventListener('change', recalc);
    });
});

/**
 * Formularios de búsqueda/orden/filtro del admin (listados de categorías,
 * productos, promociones, etc.): con data-autosubmit, los inputs de texto
 * mandan el form solos 400ms después de que el admin deja de tipear (sin
 * Enter ni botón), y los select/checkbox lo mandan apenas cambian.
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[data-autosubmit]').forEach((form) => {
        let timer = null;

        form.querySelectorAll('input[type="text"], input[type="search"], input[type="number"]').forEach((input) => {
            input.addEventListener('input', () => {
                clearTimeout(timer);
                timer = setTimeout(() => form.requestSubmit(), 400);
            });
        });

        form.querySelectorAll('select, input[type="checkbox"], input[type="radio"]').forEach((field) => {
            field.addEventListener('change', () => form.requestSubmit());
        });
    });
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-chart]').forEach((canvas) => {
        try {
            const config = JSON.parse(canvas.dataset.chart);
            new Chart(canvas, config);
        } catch (e) {
            console.error('No se pudo dibujar el gráfico', e);
        }
    });
});

/**
 * Modal de confirmación propio para acciones destructivas (eliminar producto,
 * categoría, promoción, etc.), en vez del confirm() nativo del navegador.
 * Cualquier form con data-confirm="mensaje" queda cubierto automáticamente.
 */
document.addEventListener('DOMContentLoaded', () => {
    const dialog = document.createElement('dialog');
    dialog.className = 'w-full max-w-sm rounded-2xl p-0 backdrop:bg-marca-negro/50';
    dialog.innerHTML = `
        <div class="p-5">
            <p data-confirm-message class="text-sm text-marca-negro"></p>
            <div class="mt-5 flex justify-end gap-3">
                <button type="button" data-confirm-cancel class="rounded-lg border border-marca-gris-oscuro/20 px-4 py-2 text-xs font-semibold text-marca-negro transition hover:border-marca-amarillo">Cancelar</button>
                <button type="button" data-confirm-ok class="rounded-lg bg-marca-rojo px-4 py-2 text-xs font-semibold text-marca-blanco transition hover:bg-marca-negro">Eliminar</button>
            </div>
        </div>
    `;
    document.body.appendChild(dialog);

    let pendingForm = null;

    dialog.querySelector('[data-confirm-cancel]').addEventListener('click', () => {
        pendingForm = null;
        dialog.close();
    });

    dialog.querySelector('[data-confirm-ok]').addEventListener('click', () => {
        const form = pendingForm;
        pendingForm = null;
        dialog.close();
        if (form) {
            form.dataset.confirmed = '1';
            form.submit();
        }
    });

    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement)) return;
        const message = form.getAttribute('data-confirm');
        if (!message || form.dataset.confirmed) return;

        e.preventDefault();
        dialog.querySelector('[data-confirm-message]').textContent = message;
        pendingForm = form;
        dialog.showModal();
    });
});
