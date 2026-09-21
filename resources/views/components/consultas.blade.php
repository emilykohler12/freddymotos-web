{{-- Seccion "Consultas" - Home. Formulario general de contacto (no vinculado a taller ni a nada). --}}

@php
    $field = 'w-full rounded-lg border border-marca-gris-oscuro/20 px-3 py-2.5 text-sm text-marca-negro placeholder:text-marca-gris-oscuro/40 focus:border-marca-amarillo focus:outline-none focus:ring-2 focus:ring-marca-amarillo/40';
@endphp

<section id="consultas" class="w-full scroll-mt-4 bg-marca-gris-claro py-14 sm:py-20">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">

        <div class="mb-8 text-center">
            <h2 class="text-3xl font-extrabold tracking-tight text-marca-negro sm:text-4xl">
                Consultas
            </h2>
            <p class="mt-2 text-sm text-marca-gris-oscuro">
                Dejanos tus datos y te contactamos a la brevedad.
            </p>
        </div>

        @if (session('consulta_status'))
            <div id="consulta-status" class="mb-6 rounded-xl bg-marca-amarillo/20 px-4 py-3 text-center text-sm font-semibold text-marca-negro">
                {{ session('consulta_status') }}
            </div>
            <script>
                setTimeout(function () {
                    var msg = document.getElementById('consulta-status');
                    if (!msg) return;
                    msg.style.transition = 'opacity .3s';
                    msg.style.opacity = '0';
                    setTimeout(function () { msg.remove(); }, 300);
                }, 30000);
            </script>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-xl bg-marca-rojo/10 px-4 py-3 text-sm font-medium text-marca-rojo">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('workshop.inquiries.store') }}" class="grid grid-cols-1 gap-4 rounded-2xl bg-marca-blanco p-6 shadow-sm sm:grid-cols-2">
            @csrf
            <div>
                <label for="consulta-name" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Nombre</label>
                <input type="text" id="consulta-name" name="name" value="{{ old('name') }}" required class="{{ $field }}">
            </div>
            <div>
                <label for="consulta-phone" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Teléfono</label>
                <input type="tel" id="consulta-phone" name="phone" value="{{ old('phone') }}" required class="{{ $field }}">
            </div>
            <div class="sm:col-span-2">
                <label for="consulta-email" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Email (opcional)</label>
                <input type="email" id="consulta-email" name="email" value="{{ old('email') }}" class="{{ $field }}">
            </div>
            <div class="sm:col-span-2">
                <label for="consulta-message" class="mb-1 block text-xs font-semibold uppercase tracking-wide text-marca-gris-oscuro">Contanos qué necesitás</label>
                <textarea id="consulta-message" name="message" rows="4" required class="{{ $field }}">{{ old('message') }}</textarea>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full rounded-full bg-marca-amarillo px-6 py-3 text-sm font-bold text-marca-negro transition hover:bg-marca-rojo hover:text-marca-blanco">
                    Enviar consulta
                </button>
            </div>
        </form>
    </div>
</section>
