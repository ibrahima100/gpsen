@props([
    'countryName' => 'country_id',
    'cityName' => 'city_id',
    'selectedCountryId' => null,
    'selectedCityId' => null,
    'countryLabel' => 'Pays',
    'cityLabel' => 'Ville',
    'required' => true,
])

@php
    $countries = \App\Models\Country::orderBy('name')->get();
    $initialCities = $selectedCountryId 
        ? \App\Models\City::where('country_id', $selectedCountryId)->orderBy('name')->get() 
        : collect();
@endphp

<div x-data="{
    countryId: '{{ $selectedCountryId }}',
    cityId: '{{ $selectedCityId }}',
    cities: {{ Js::from($initialCities) }},
    loading: false,
    fetchCities() {
        if (!this.countryId) {
            this.cities = [];
            this.cityId = '';
            return;
        }
        this.loading = true;
        fetch('{{ route('api.locations.cities') }}?country_id=' + this.countryId)
            .then(res => res.json())
            .then(data => {
                this.cities = data;
                if (!this.cities.some(c => c.id == this.cityId)) {
                    this.cityId = '';
                }
            })
            .catch(() => { this.cities = []; })
            .finally(() => { this.loading = false; });
    }
}" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <!-- Choix du Pays -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1">
            {{ $countryLabel }} @if($required)<span class="text-red-500">*</span>@endif
        </label>
        <select name="{{ $countryName }}" 
                x-model="countryId" 
                @change="fetchCities()" 
                {{ $required ? 'required' : '' }}
                class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange">
            <option value="">Sélectionnez un pays</option>
            @foreach($countries as $c)
                <option value="{{ $c->id }}" {{ $selectedCountryId == $c->id ? 'selected' : '' }}>
                    {{ $c->name }} ({{ $c->code }})
                </option>
            @endforeach
        </select>
    </div>

    <!-- Choix de la Ville -->
    <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-gp-ink mb-1 flex items-center justify-between">
            <span>{{ $cityLabel }} @if($required)<span class="text-red-500">*</span>@endif</span>
            <span x-show="loading" class="text-[10px] text-gp-orange font-semibold">Chargement...</span>
        </label>
        <select name="{{ $cityName }}" 
                x-model="cityId" 
                {{ $required ? 'required' : '' }}
                :disabled="!countryId || loading"
                class="w-full text-xs rounded-gp border-gp-border focus:ring-gp-orange focus:border-gp-orange disabled:bg-gp-sand/40 disabled:cursor-not-allowed">
            <option value="">Sélectionnez une ville</option>
            <template x-for="city in cities" :key="city.id">
                <option :value="city.id" x-text="city.name" :selected="city.id == cityId"></option>
            </template>
        </select>
    </div>
</div>
