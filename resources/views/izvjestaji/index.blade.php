<x-app-layout>
  <div class="py-10 max-w-7xl mx-auto px-6">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-3xl font-bold text-gray-900">Izvještaji</h1>

      <div class="flex gap-1 bg-gray-50 rounded-lg p-1">
        <button class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent" data-tab="studenti">Studenti</button>
        <button class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent" data-tab="prepisi">Prepisi</button>
        <button class="tab-btn px-4 py-2 text-sm font-medium border-b-2 border-transparent" data-tab="mobilnost">Mobilnost</button>
      </div>
    </div>

    <div class="bg-white shadow-sm rounded-xl border border-gray-200 p-4">

      <!-- ================= STUDENTI ================= -->
      <div id="tab-content-studenti" class="tab-content hidden mb-12">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold m-0 p-0">Studenti</h2>
        </div>

        <!-- FILTRI -->
        <form method="GET" class="mb-4 flex items-center gap-3">
  <div>
    <label class="block text-xs text-gray-600">Godina</label> 
    <select name="year"
            onchange="this.form.submit()"
            class="border rounded px-2 py-1 pr-8 text-sm w-28 appearance-none bg-no-repeat bg-right">
      <option value="">Sve</option>
      @foreach($students as $s)
        <option value="{{ $s->year }}"
          @if(isset($filterYear) && $filterYear == $s->year) selected @endif>
          {{ $s->year }}
        </option>
      @endforeach
    </select>
  </div>

  <div>
    <label class="block text-xs text-gray-600">Nivo</label>
    <select name="nivo"
            onchange="this.form.submit()"
            class="border rounded px-2 py-1 pr-8 text-sm w-36 appearance-none bg-no-repeat bg-right">
      <option value="">Sve</option>
      @foreach($nivoOptions as $n)
        <option value="{{ $n->id }}"
          @if(isset($filterNivo) && $filterNivo == $n->id) selected @endif>
          {{ $n->naziv }}
        </option>
      @endforeach
    </select>
  </div>
</form>

        <!-- GRAFICI -->
        <div class="flex gap-6 mb-6">
          <div class="w-1/2 h-52 border border-gray-200 rounded-lg overflow-hidden">
            <div class="text-center font-medium text-gray-700 bg-gray-50 border-b border-gray-200 py-2">Godišnje</div>
            <div class="h-full bg-gray-50 p-4">
              <canvas id="studentsChart"></canvas>
            </div>
          </div>

          <div class="w-1/2 flex gap-4">
            <div class="w-1/2 h-28 border border-gray-200 rounded-lg overflow-hidden">
              <div class="text-center font-medium text-gray-700 bg-gray-50 border-b border-gray-200 py-2 text-sm">Pol</div>
              <div class="h-full bg-gray-50 p-2">
                <canvas id="studentsGenderChart"></canvas>
              </div>
            </div>
            <div class="w-1/2 h-28 border border-gray-200 rounded-lg overflow-hidden">
              <div class="text-center font-medium text-gray-700 bg-gray-50 border-b border-gray-200 py-2 text-sm">Nivo studija</div>
              <div class="h-full bg-gray-50 p-2">
                <canvas id="studentsNivoChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- TABELA STUDENTI -->
        <div class="overflow-x-auto flex justify-center mb-4">
          <table class="table-fixed text-sm w-auto border border-gray-200">
            <thead>
              <tr class="bg-gray-100 h-7">
                <th class="px-3 border">Godina</th>
                <th class="px-3 border">Muško</th>
                <th class="px-3 border">Žensko</th>
                <th class="px-3 border">Ukupno</th>
              </tr>
            </thead>
            <tbody>
              @forelse($students as $row)
                <tr class="h-7">
                  <td class="px-3 text-center border">{{ $row->year }}</td>
                  <td class="px-3 text-center border bg-blue-50 text-blue-700">{{ $row->musko ?? 0 }}</td>
                  <td class="px-3 text-center border bg-red-50 text-red-700">{{ $row->zensko ?? 0 }}</td>
                  <td class="px-3 text-center border font-medium">{{ $row->total }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="px-3 py-2 text-center text-gray-500 border">Nema podataka</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- DUGME IZVEZI -->
        <div class="flex justify-end">
          <?php $query = http_build_query(request()->except('_token')); ?>
          <a href="{{ route('izvjestaji.export', 'students') }}{{ $query ? '?'.$query : '' }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            Izvezi
          </a>
        </div>
      </div>

      <!-- ================= PREPISI ================= -->
      <div id="tab-content-prepisi" class="tab-content hidden mb-12">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold m-0 p-0">Prepisi</h2>
        </div>

        <!-- FILTRI -->
        <form method="GET" class="mb-4 flex items-center gap-3">
          <div>
            <label class="block text-xs text-gray-600">Godina</label>
            <select name="year" onchange="this.form.submit()" class="border rounded px-2 py-1 pr-8 text-sm w-28 appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($prepisi as $p)
                <option value="{{ $p->year }}" @if(isset($filterYear) && $filterYear == $p->year) selected @endif>{{ $p->year }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-600">Fakultet</label>
            <select name="fakultet" onchange="this.form.submit()" class="border rounded px-2 py-1 pr-8 text-sm w-56 appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($fakulteti as $f)
                <option value="{{ $f->id }}" @if(isset($filterFakultet) && $filterFakultet == $f->id) selected @endif>{{ $f->naziv }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-600">Nivo</label>
            <select name="nivo" onchange="this.form.submit()" class="border rounded px-2 py-1 pr-8 text-sm w-36 appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($nivoOptions as $n)
                <option value="{{ $n->id }}" @if(isset($filterNivo) && $filterNivo == $n->id) selected @endif>{{ $n->naziv }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-600">Država</label>
            <select name="drzava" onchange="this.form.submit()" class="border rounded px-2 py-1 pr-8 text-sm appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($drzave as $d)
                <option value="{{ $d }}" @if($filterDrzava == $d) selected @endif>{{ $d }}</option>
              @endforeach
            </select>
          </div>
        </form>

        <div class="flex gap-6 mb-6">
          <div class="w-2/3 h-44 border border-gray-200 rounded-lg overflow-hidden">
            <div class="text-center font-medium text-gray-700 bg-gray-50 border-b border-gray-200 py-2">Godišnje</div>
            <div class="h-full bg-gray-50 p-4">
            <canvas id="prepisiChart"></canvas>
            </div>
          </div>
          <div class="w-1/3 h-44 border border-gray-200 rounded-lg overflow-hidden">
            <div class="text-center font-medium text-gray-700 bg-gray-50 border-b border-gray-200 py-2">Pol</div>
            <div class="h-full bg-gray-50 p-4">
            <canvas id="prepisiGenderChart"></canvas>
            </div>
          </div>
        </div>

        <!-- TABELA PREPISI -->
        <div class="overflow-x-auto flex justify-center mb-4">
          <table class="table-fixed text-sm w-auto border border-gray-200">
            <thead>
              <tr class="bg-gray-100 h-7">
                <th class="px-3 py-1 border">Godina</th>
                <th class="px-3 py-1 border">Fakultet</th>
                <th class="px-3 py-1 border text-center">Ukupno</th>
                <th class="px-3 py-1 border text-center">Muško</th>
                <th class="px-3 py-1 border text-center">Žensko</th>
              </tr>
            </thead>
            <tbody>
              @forelse($prepisi as $row)
                <tr class="h-7">
                  <td class="px-3 border">{{ $row->year }}</td>
                  <td class="px-3 border">{{ $row->fakultet }}</td>
                  <td class="px-3 border text-center">{{ $row->total }}</td>
                  <td class="px-3 border text-center">{{ $row->musko ?? 0 }}</td>
                  <td class="px-3 border text-center">{{ $row->zensko ?? 0 }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="px-3 py-2 text-center text-gray-500 border">Nema podataka</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- DUGME IZVEZI -->
        <div class="flex justify-end">
          <?php $query = http_build_query(request()->except('_token')); ?>
          <a href="{{ route('izvjestaji.export', 'prepisi') }}{{ $query ? '?'.$query : '' }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            Izvezi
          </a>
        </div>
      </div>

      <!-- ================= MOBILNOST ================= -->
      <div id="tab-content-mobilnost" class="tab-content hidden mb-12">
        <div class="flex items-center justify-between mb-4">
          <h2 class="text-lg font-semibold m-0 p-0">Mobilnost</h2>
        </div>

        <!-- FILTRI -->
        <form method="GET" class="mb-4 flex items-center gap-3" id="mobilnostFilterForm">
          <div>
            <label class="block text-xs text-gray-600">Godina</label>
            <select name="year" onchange="this.form.submit()" class="border rounded px-2 py-1 pr-8 text-sm w-28 appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($mobilnosti as $m)
                <option value="{{ $m->year }}" @if(isset($filterYear) && $filterYear == $m->year) selected @endif>{{ $m->year }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-600">Fakultet</label>
            <select name="fakultet" onchange="this.form.submit()" class="border rounded px-2 py-1 pr-8 text-sm w-56 appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($fakulteti as $f)
                <option value="{{ $f->id }}" @if(isset($filterFakultet) && $filterFakultet == $f->id) selected @endif>{{ $f->naziv }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-600">Nivo</label>
            <select name="nivo" onchange="this.form.submit()" class="border rounded px-2 py-1 pr-8 text-sm w-36 appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($nivoOptions as $n)
                <option value="{{ $n->id }}" @if(isset($filterNivo) && $filterNivo == $n->id) selected @endif>{{ $n->naziv }}</option>
              @endforeach
            </select>
          </div>
          <div>
            <label class="block text-xs text-gray-600">Država</label>
            <select name="drzava" onchange="this.form.submit()" class="border rounded px-2 py-1 pr-8 text-sm appearance-none bg-no-repeat bg-right">
              <option value="">Sve</option>
              @foreach($drzave as $d)
                <option value="{{ $d }}" @if($filterDrzava == $d) selected @endif>{{ $d }}</option>
              @endforeach
            </select>
          </div>
        </form>

        <div class="flex gap-6 mb-6">
          <div class="w-2/3 h-44 border border-gray-200 rounded-lg overflow-hidden">
            <div class="text-center font-medium text-gray-700 bg-gray-50 border-b border-gray-200 py-2">Godišnje</div>
            <div class="h-full bg-gray-50 p-4"> 
            <canvas id="mobilnostChart"></canvas>
            </div>
          </div>
          <div class="w-1/3 h-44 border border-gray-200 rounded-lg overflow-hidden">
            <div class="text-center font-medium text-gray-700 bg-gray-50 border-b border-gray-200 py-2">Pol</div>
            <div class="h-full bg-gray-50 p-4">
            <canvas id="mobilnostGenderChart"></canvas>
            </div>
          </div>
        </div>

        <!-- TABELA MOBILNOST -->
        <div class="overflow-x-auto flex justify-center mb-4">
          <table class="table-fixed text-sm w-auto border border-gray-200">
            <thead>
              <tr class="bg-gray-100 h-7">
                <th class="py-1 px-2 border">Godina</th>
                <th class="py-1 px-2 border">Država</th>
                <th class="py-1 px-2 w-10 text-center border">Ukupno</th>
                <th class="py-1 px-2 w-10 text-center border">Muško</th>
                <th class="py-1 px-2 w-16 text-center border">Žensko</th>
                <th class="py-1 px-2 w-16 text-center border">%Muško</th>
                <th class="py-1 px-2 w-16 text-center border">%Žensko</th>
                <th class="py-1 px-2 w-12 text-center border">Master</th>
                <th class="py-1 px-2 w-12 text-center border">Osnovne</th>
              </tr>
            </thead>
            <tbody>
              @forelse($mobilnosti as $row)
                <tr class="h-7">
                  <td class="py-1 px-2 border">{{ $row->year }}</td>
                  <td class="py-1 px-2 border">{{ $row->drzava }}</td>
                  <td class="py-1 px-2 text-center border">{{ $row->total }}</td>
                  <td class="py-1 px-2 text-center border">{{ $row->musko }}</td>
                  <td class="py-1 px-2 text-center border">{{ $row->zensko }}</td>
                  <td class="py-1 px-2 text-center border">{{ $row->procenat_musko }}%</td>
                  <td class="py-1 px-2 text-center border">{{ $row->procenat_zensko }}%</td>
                  <td class="py-1 px-2 text-center border">{{ $row->master }}</td>
                  <td class="py-1 px-2 text-center border">{{ $row->osnovne }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="px-3 py-2 text-center text-gray-500 border">Nema podataka</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- DUGME IZVEZI -->
        <div class="flex justify-end">
          <?php $query = http_build_query(request()->except('_token')); ?>
          <a href="{{ route('izvjestaji.export', 'mobilnost') }}{{ $query ? '?'.$query : '' }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            Izvezi
          </a>
        </div>
      </div>

    </div>
  </div>
</x-app-layout>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {

  /* ================= TABOVI ================= */
  const tabs = document.querySelectorAll('.tab-btn');
  const contents = document.querySelectorAll('.tab-content');
  let activeTab = sessionStorage.getItem('active-tab') || 'studenti';

  function showTab(tab){
    contents.forEach(c => c.classList.add('hidden'));
    document.getElementById('tab-content-' + tab).classList.remove('hidden');

    tabs.forEach(b => {
      b.style.borderBottomColor = b.dataset.tab === tab ? '#2563eb' : 'transparent';
      b.style.color = b.dataset.tab === tab ? '#2563eb' : '#374151';
    });

    sessionStorage.setItem('active-tab', tab);
    resizeCharts();
  }

  tabs.forEach(b => b.addEventListener('click', () => showTab(b.dataset.tab)));
  showTab(activeTab);

  /* ================= PODACI ================= */
  const studentsData = @json($students->map(fn($r)=>['year'=>$r->year,'total'=>$r->total]));
  const cumulativeData = @json($cumulative ?? []);
  const studentsByGender = @json($studentsByGender ?? []);
  const studentsByNivo = @json($byNivo ?? []);
  const prepisiData = @json($prepisi->map(fn($r)=>['year'=>$r->year,'total'=>$r->total]));
  const prepisiGenderData = @json($prepisiGenderData ?? []);
  const mobilnostiData = @json($mobilnosti->map(fn($r)=>['year'=>$r->year,'musko'=>$r->musko,'zensko'=>$r->zensko]));
  const mobilnostiGenderData = @json($mobilnostiGenderData ?? []);

  /* ================= HELPER ================= */
  function initBar(id, labels, datasets){
    return new Chart(document.getElementById(id),{
      type:'bar',
      data:{ labels, datasets },
      options:{
        responsive:true,
        maintainAspectRatio:false,
        scales:{
          x:{ offset:true, grid:{display:false} },
          y:{ beginAtZero:true, ticks:{stepSize:1} }
        },
        plugins:{ legend:{position:'bottom'} }
      }
    });
  }

  /* ================= GRAFICI ================= */

  initBar('studentsChart', studentsData.map(d=>d.year), [
    { label:'Ukupno', data:studentsData.map(d=>d.total), backgroundColor:'#3b82f6', borderRadius:6, maxBarThickness:18 },
    { label:'Kumulativno', data:cumulativeData.map(d=>d.cumulative ?? 0), type:'line', borderColor:'#111827', tension:.25, maxBarThickness:18 }
  ]);

  new Chart(document.getElementById('studentsGenderChart'), {
    type:'doughnut',
    data:{
      labels:['Muško','Žensko'],
      datasets:[{
        data: studentsByGender.map(d=>d.total),
        backgroundColor:['#2563eb','#ef4444']
      }]
    },
    options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
  });

  const nivoMap = {};
  studentsByNivo.forEach(n => nivoMap[n.label] = n.total);

  initBar('studentsNivoChart', ['Osnovne','Master'], [
    { label:'Broj', data:['Osnovne','Master'].map(l=>nivoMap[l] ?? 0), backgroundColor:['#10b981','#f59e0b'], borderRadius:6, maxBarThickness:18 }
  ]);

  initBar('prepisiChart', prepisiData.map(d=>d.year), [
    { label:'Ukupno', data:prepisiData.map(d=>d.total), backgroundColor:'#10b981', borderRadius:6, maxBarThickness:18 }
  ]);

  new Chart(document.getElementById('prepisiGenderChart'), {
    type:'doughnut',
    data:{
      labels:['Muško','Žensko'],
      datasets:[{
        data: prepisiGenderData.map(d=>d.total),
        backgroundColor:['#2563eb','#ef4444']
      }]
    },
    options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
  });

  initBar('mobilnostChart', mobilnostiData.map(d=>d.year), [
    { label:'Muško', data:mobilnostiData.map(d=>d.musko), backgroundColor:'#2563eb', borderRadius:6, maxBarThickness:18 },
    { label:'Žensko', data:mobilnostiData.map(d=>d.zensko), backgroundColor:'#ef4444', borderRadius:6, maxBarThickness:18 }
  ]);

  new Chart(document.getElementById('mobilnostGenderChart'), {
    type:'doughnut',
    data:{
      labels:['Muško','Žensko'],
      datasets:[{
        data: mobilnostiGenderData.map(d=>d.total),
        backgroundColor:['#2563eb','#ef4444']
      }]
    },
    options:{ responsive:true, maintainAspectRatio:false, plugins:{legend:{position:'bottom'}} }
  });

  /* ================= RESIZE ================= */
  function resizeCharts(){
    [
      'studentsChart',
      'studentsGenderChart',
      'studentsNivoChart',
      'prepisiChart',
      'prepisiGenderChart',
      'mobilnostChart',
      'mobilnostGenderChart'
    ].forEach(id=>{
      const c = Chart.getChart(id);
      if(c) c.resize();
    });
  }

});
</script>

<script>
  function submitFormClean(form){
    try{
      var action = form.action || window.location.pathname + window.location.search;
      form.action = action.split('#')[0];
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











