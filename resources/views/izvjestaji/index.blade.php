<x-app-layout>

  <div class="py-10 max-w-7xl mx-auto px-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Izvještaji</h1>
    </div>

    <div class="space-y-8">
      <!-- Studenti -->
      <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200 p-4 relative">
        <h2 class="text-lg font-semibold mb-3">Studenti</h2>

        <form method="GET" class="mb-4 flex items-center gap-3">
          <?php $q = http_build_query(request()->except('_token')); ?>
            <div>
            <label class="block text-xs text-gray-600">Godina</label>
            <select name="year" onchange="submitFormClean(this.form)" class="border rounded px-2 py-1 pr-8 text-sm w-28 appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($students as $s)
                <option value="{{ $s->year }}" @if(isset($filterYear) && $filterYear == $s->year) selected @endif>{{ $s->year }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-600">Nivo</label>
            <select name="nivo" onchange="submitFormClean(this.form)" class="border rounded px-2 py-1 pr-8 text-sm w-36 appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($nivoOptions as $n)
                <option value="{{ $n->id }}" @if(isset($filterNivo) && $filterNivo == $n->id) selected @endif>{{ $n->naziv }}</option>
              @endforeach
            </select>
          </div>
          <div class="pt-5">
            <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">Filtriraj</button>
          </div>
        </form>

        <div class="mb-4">
          <div class="flex gap-6 items-start">
            <div class="w-1/2 h-48">
              <canvas id="studentsChart" class="w-full h-full"></canvas>
            </div>
            <div class="w-1/2 flex gap-4">
              <div class="w-1/2 h-24">
                <h3 class="text-sm font-medium">Pol</h3>
                <canvas id="studentsGenderChart" class="w-full h-full"></canvas>
              </div>
              <div class="w-1/2 h-24">
                <h3 class="text-sm font-medium">Nivo studija</h3>
                <canvas id="studentsNivoChart" class="w-full h-full"></canvas>
              </div>
            </div>
          </div>
        </div>
        <div class="overflow-x-auto flex justify-center">
          <table class="table-fixed text-sm w-auto mx-auto">
            <thead>
              <tr>
                <th class="py-1 px-2 w-24 text-center">Godina</th>
                <th class="py-1 px-2 w-20 text-center">Muško</th>
                <th class="py-1 px-2 w-20 text-center">Žensko</th>
                <th class="py-1 px-2 w-20 text-center">Ukupno</th>
              </tr>
            </thead>
            <tbody>
              @forelse($students as $row)
                <tr>
                  <td class="py-1 px-2 text-center">{{ $row->year }}</td>
                  <td class="py-1 px-2 bg-blue-50 text-center"><span class="text-blue-700 font-medium">{{ $row->musko ?? 0 }}</span></td>
                  <td class="py-1 px-2 bg-red-50 text-center"><span class="text-red-700 font-medium">{{ $row->zensko ?? 0 }}</span></td>
                  <td class="py-1 px-2 text-center">{{ $row->total }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="py-1 px-2 text-gray-500 text-center">Nema podataka</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="absolute bottom-4 right-4">
          <?php $query = http_build_query(request()->except('_token')); ?>
          <a href="{{ route('izvjestaji.export', 'students') }}{{ $query ? '?'.$query : '' }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            Izvezi
          </a>
        </div>
      </div>

      <!-- Prepisi -->
      <div id="prepisi-section" class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200 p-4 relative">
        <h2 class="text-lg font-semibold mb-3">Prepisi</h2>

        <form method="GET" class="mb-3 flex items-center gap-3">
          @foreach(request()->except('fakultet','_token') as $k => $v)
            <input type="hidden" name="{{ $k }}" value="{{ $v }}" />
          @endforeach
          <div>
            <label class="block text-xs text-gray-600">Fakultet</label>
            <select name="fakultet" onchange="submitWithAnchor(this.form,'prepisi-section')" class="border rounded px-2 py-1 pr-8 text-sm w-56 appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($fakulteti as $f)
                <option value="{{ $f->id }}" @if(isset($filterFakultet) && $filterFakultet == $f->id) selected @endif>{{ $f->naziv }}</option>
              @endforeach
            </select>
          </div>
        </form>

        <div class="mb-4">
          <canvas id="prepisiChart" height="120"></canvas>
        </div>
        <div class="overflow-x-auto flex justify-center">
          <table class="table-fixed text-sm w-auto mx-auto">
            <thead>
              <tr>
                <th class="py-1 px-2 w-28 text-center">Godina</th>
                <th class="py-1 px-2 w-40 text-center">Fakultet</th>
                <th class="py-1 px-2 w-20 text-center">Broj</th>
              </tr>
            </thead>
            <tbody>
              @forelse($prepisi as $row)
                <tr>
                  <td class="py-1 px-2 text-center">{{ $row->year }}</td>
                  <td class="py-1 px-2 text-center">{{ $row->fakultet }}</td>
                  <td class="py-1 px-2 text-center">{{ $row->total }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="py-1 px-2 text-gray-500 text-center">Nema podataka</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="absolute bottom-4 right-4">
          <?php $query = http_build_query(request()->except('_token')); ?>
          <a href="{{ route('izvjestaji.export', 'prepisi') }}{{ $query ? '?'.$query : '' }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            Izvezi
          </a>
        </div>
      </div>

      <!-- Mobilnost -->
      <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200 p-4 relative">
        <h2 class="text-lg font-semibold mb-3">Mobilnost</h2>
        <div class="mb-4 h-40">
          <canvas id="mobilnostChart" class="w-full h-full"></canvas>
        </div>
        <div class="overflow-x-auto flex justify-center">
          <table class="table-fixed text-sm w-auto mx-auto">
            <thead>
              <tr>
                <th class="py-2 px-2">Godina</th>
                <th class="py-1 px-2 w-10 text-center">Ukupno</th>
                <th class="py-1 px-2 w-10 text-center">Muško</th>
                <th class="py-1 px-2 w-16 text-center">Žensko</th>
                <th class="py-1 px-2 w-20 text-center">Procenat Muško (%)</th>
                <th class="py-1 px-2 w-20 text-center">Procenat Žensko (%)</th>
                <th class="py-1 px-2 w-16 text-center">Master</th>
                <th class="py-1 px-2 w-16 text-center">Osnovne</th>
              </tr>
            </thead>
            <tbody>
              @forelse($mobilnosti as $row)
                <tr>
                  <td class="py-1 px-2 text-center">{{ $row->year }}</td>
                  <td class="py-1 px-2 text-center">{{ $row->total }}</td>
                  <td class="py-1 px-2 text-center">{{ $row->musko }}</td>
                  <td class="py-1 px-2 text-center">{{ $row->zensko }}</td>
                  <td class="py-1 px-2 text-center">{{ $row->procenat_musko }}%</td>
                  <td class="py-1 px-2 text-center">{{ $row->procenat_zensko }}%</td>
                  <td class="py-1 px-2 text-center">{{ $row->master }}</td>
                  <td class="py-1 px-2 text-center">{{ $row->osnovne }}</td>
                </tr>
              @empty
                <tr><td colspan="8" class="py-2 text-gray-500">Nema podataka</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="absolute bottom-4 right-4">
          <a href="{{ route('izvjestaji.export', 'mobilnost') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            Izvezi
          </a>
        </div>
      </div>
    </div>
  </div>

</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Prepare data from PHP into JS
  const studentsData = {!! json_encode($students->map(function($r){ return ['year'=>$r->year,'total'=>$r->total]; })) !!};
  const prepisiData = {!! json_encode($prepisi->map(function($r){ return ['year'=>$r->year,'total'=>$r->total]; })) !!};
  const mobilnostiData = {!! json_encode($mobilnosti->map(function($r){ return [
    'year'=>$r->year,
    'total'=>$r->total,
    'musko'=>$r->musko,
    'zensko'=>$r->zensko,
    'procenat_musko'=>$r->procenat_musko,
    'procenat_zensko'=>$r->procenat_zensko,
    'master'=>$r->master,
    'osnovne'=>$r->osnovne
  ]; })) !!};
  const studentsByGender = {!! json_encode($studentsByGender ?? collect()); !!};
  const studentsByNivo = {!! json_encode($byNivo ?? collect()); !!};
  const cumulativeData = {!! json_encode($cumulative ?? collect()); !!};

  function initBarChart(ctxId, labels, datasets, options = {}){
    const ctx = document.getElementById(ctxId).getContext('2d');
    return new Chart(ctx, {
      type: 'bar',
      data: { labels, datasets },
      options: Object.assign({
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true } },
        datasets: {
          bar: {
            // width controls: lower value = thinner bars
            barThickness: 20,
            maxBarThickness: 20,
            categoryPercentage: 0.6,
            barPercentage: 0.8
          }
        }
      }, options)
    });
  }

  // Students chart
  (function(){
    const labels = studentsData.map(d => d.year);
    const totals = studentsData.map(d => d.total);
    const cumulative = cumulativeData.map(d => d.cumulative);
    initBarChart('studentsChart', labels, [{
      label: 'Ukupno',
      data: totals,
      backgroundColor: '#3b82f6'
    },{
      label: 'Kumulativno',
      data: cumulative,
      type: 'line',
      borderColor: '#111827',
      backgroundColor: '#111827',
      fill: false,
      tension: 0.2,
      yAxisID: 'y'
    }]);
  })();

  // Students gender doughnut
  (function(){
    const labels = studentsByGender.map(d => d.pol == 1 ? 'Muško' : 'Žensko');
    const data = studentsByGender.map(d => d.total);
    const ctx = document.getElementById('studentsGenderChart').getContext('2d');
    new Chart(ctx, { type: 'doughnut', data: { labels, datasets: [{ data, backgroundColor: ['#2563eb','#ef4444'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } } });
  })();

  // Students nivo bar
  (function(){
    // Ensure both levels always present: Osnovne and Master (0 if missing)
    const nivoMap = {};
    studentsByNivo.forEach(d => { nivoMap[d.label] = d.total; });
    const labels = ['Osnovne', 'Master'];
    const data = labels.map(l => nivoMap[l] ?? 0);
    const canvas = document.getElementById('studentsNivoChart');
    if (canvas && canvas.getContext) {
      const ctx = canvas.getContext('2d');
      new Chart(ctx, { type: 'bar', data: { labels, datasets: [{ label: 'Broj', data, backgroundColor: ['#10b981','#f59e0b'] }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } } });
    }
  })();

  // Prepisi chart
  (function(){
    const labels = prepisiData.map(d => d.year);
    const totals = prepisiData.map(d => d.total);
    initBarChart('prepisiChart', labels, [{
      label: 'Ukupno',
      data: totals,
      backgroundColor: '#10b981'
    }]);
  })();

  // Mobilnost chart (grouped musko/zensko)
  (function(){
    const labels = mobilnostiData.map(d => d.year);
    const musko = mobilnostiData.map(d => d.musko);
    const zensko = mobilnostiData.map(d => d.zensko);

    initBarChart('mobilnostChart', labels, [
      { label: 'Muško', data: musko, backgroundColor: '#2563eb' },
      { label: 'Žensko', data: zensko, backgroundColor: '#ef4444' }
    ], {
      scales: {
        x: { stacked: false },
        y: { stacked: false, beginAtZero: true }
      },
      plugins: {
        tooltip: { mode: 'index', intersect: false },
        legend: { position: 'bottom' }
      },
      responsive: true,
      maintainAspectRatio: false
    });
  })();
</script>

<script>
  function submitWithAnchor(form, anchorId){
    try{
      // store current scroll position so we can restore after reload
      sessionStorage.setItem('reports-scroll', window.scrollY || window.pageYOffset || 0);
      var action = form.action || window.location.pathname + window.location.search;
      action = action.split('#')[0] + '#' + anchorId;
      form.action = action;
    }catch(e){ }
    form.submit();
  }

  function submitFormClean(form){
    try{
      // remove any fragment from URL so subsequent submits don't jump to a section
      var action = form.action || window.location.pathname + window.location.search;
      form.action = action.split('#')[0];
      // store scroll so user returns to same spot
      sessionStorage.setItem('reports-scroll', window.scrollY || window.pageYOffset || 0);
    }catch(e){ }
    form.submit();
  }

  document.addEventListener('DOMContentLoaded', function(){
    try{
      const pos = sessionStorage.getItem('reports-scroll');
      if(pos !== null){
        window.scrollTo({ top: parseInt(pos,10), left: 0 });
        sessionStorage.removeItem('reports-scroll');
      }
    }catch(e){ }
  });
</script>
