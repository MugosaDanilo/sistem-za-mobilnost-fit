{{-- Legenda za zelenu tačku pored zapisa povezanih sa studentskom platformom --}}
@if(config('platforma.enabled'))
<span class="inline-flex items-center gap-1.5 text-xs text-gray-500">
    <span class="inline-block w-2 h-2 rounded-full bg-green-500"></span>
    povezano sa studentskom platformom
</span>
@endif
