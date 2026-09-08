{{-- Pretraga studenta na studentskoj platformi + autofill forme.
     Props: status (mobilnost|prepis|null), fieldPrefix (prazan za studentsku formu) --}}
@props(['status' => null, 'current' => null])

@if(config('platforma.enabled'))
<div class="mb-6 border border-indigo-200 bg-indigo-50 rounded-xl p-4"
     x-data="platformaSearch({ status: @js($status), current: @js($current) })"
     @click.outside="open = false">
    <div class="flex items-center justify-between mb-2">
        <label class="block text-sm font-semibold text-indigo-900">
            Pronađi studenta na studentskoj platformi
        </label>
        <template x-if="linked">
            <span class="inline-flex items-center gap-1 text-xs font-medium px-2 py-0.5 rounded-full bg-green-100 text-green-800">
                Povezan: <span x-text="linked.ime + ' ' + linked.prezime + ' (' + (linked.br_indexa || '-') + (linked.nivo_studija_naziv ? ', ' + linked.nivo_studija_naziv : '') + ')'"></span>
            </span>
        </template>
    </div>
    <div class="relative">
        <input type="text" x-model="q" @input.debounce.300ms="search()" @focus="open = results.length > 0"
               placeholder="Indeks, JMBG ili ime i prezime..." autocomplete="off"
               class="w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pr-10">
        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
            <svg x-show="loading" class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
        </div>
        <div x-show="open" x-cloak class="absolute z-30 mt-1 w-full bg-white shadow-xl max-h-72 rounded-lg py-1 ring-1 ring-black ring-opacity-5 overflow-auto text-sm">
            <template x-if="results.length === 0 && !loading">
                <div class="px-4 py-2 text-gray-500">Nema rezultata.</div>
            </template>
            <template x-for="r in results" :key="r.kljuc">
                <div @click="select(r)" class="px-4 py-2 hover:bg-indigo-50 cursor-pointer flex justify-between items-center">
                    <div>
                        <div class="font-medium text-gray-800" x-text="r.ime + ' ' + r.prezime"></div>
                        <div class="text-xs text-gray-500">
                            <span x-text="r.br_indexa || 'bez indeksa'"></span>
                            · <span x-text="r.platforma_fakultet || '-'"></span>
                            · <span x-text="r.nivo_studija_naziv || '-'"></span>
                            · <span x-text="r.godina_studija ? r.godina_studija + '. godina' : '-'"></span>
                            · <span x-text="r.platforma_akademska_godina || ''"></span>
                        </div>
                    </div>
                    <span x-show="r.lokalni_id" class="text-xs px-2 py-0.5 rounded bg-gray-100 text-gray-600">već u sistemu</span>
                </div>
            </template>
        </div>
    </div>
    <p x-show="error" x-text="error" class="mt-2 text-xs text-red-600"></p>
    <p class="mt-2 text-xs text-indigo-700">Izborom studenta polja se popune sa platforme.</p>

    <input type="hidden" name="platforma_student_id" :value="linked ? linked.platforma_student_id : ''">
    <input type="hidden" name="platforma_upis_id" :value="linked ? linked.platforma_upis_id : ''">
</div>

<script>
    function platformaSearch(opts) {
        return {
            q: '', results: [], open: false, loading: false, error: null,
            linked: opts.current || null,
            async search() {
                this.error = null;
                if (this.q.trim().length < 2) { this.results = []; this.open = false; return; }
                this.loading = true;
                try {
                    const res = await fetch(`{{ route('platforma.studenti') }}?q=${encodeURIComponent(this.q.trim())}`, { headers: { 'Accept': 'application/json' } });
                    const data = await res.json();
                    if (!res.ok) throw new Error(data.message || 'Greška pri pretrazi.');
                    this.results = data;
                    this.open = true;
                } catch (e) {
                    this.error = e.message;
                    this.results = [];
                } finally {
                    this.loading = false;
                }
            },
            select(r) {
                this.linked = r;
                this.open = false;
                this.q = `${r.ime} ${r.prezime}`;
                const set = (name, value) => {
                    const el = document.querySelector(`[name="${name}"]`);
                    if (!el || value === null || value === undefined) return;
                    if (el.type === 'radio') {
                        const radio = document.querySelector(`[name="${name}"][value="${value}"]`);
                        if (radio) { radio.checked = true; radio.dispatchEvent(new Event('change', { bubbles: true })); }
                        return;
                    }
                    el.value = value;
                    el.dispatchEvent(new Event('input', { bubbles: true }));
                    el.dispatchEvent(new Event('change', { bubbles: true }));
                };
                set('ime', r.ime); set('prezime', r.prezime); set('br_indexa', r.br_indexa);
                set('jmbg', r.jmbg); set('email', r.email); set('telefon', r.telefon);
                set('pol', r.pol); set('godina_studija', r.godina_studija); set('nivo_studija_id', r.nivo_studija_id);
                if (opts.status) set('status', opts.status);
                window.dispatchEvent(new CustomEvent('platforma-student-selected', { detail: r }));
            }
        }
    }
</script>
@endif
