<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ $trip->title }}
    </h2>
  </x-slot>

  <div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 flex justify-between bg-white border-b border-gray-200">
          <div id="user-list">
            @if ($trip->users->count() > 1)
            {{ __('Trip participants') }}:
            @foreach ($trip->users as $user)
            @if ($user->is(auth()->user()))
            @if ($loop->last)
            {{ $user->name }}
            @else
            {{ $user->name }},
            @endif
            @else
            @if ($loop->last)
            <div class="group inline">{{ $user->name }}
              <form class="inline" action="{{ route('trip-users.destroy', [$trip, $user]) }}" method="POST">@method('DELETE') @csrf <button class="hidden group-hover:inline-flex items-center justify-center rounded-full bg-white text-red-500 border-2 border-red-500 w-6 h-6 p-2 font-bold" id="remove-user-{{ $user->id }}" type="submit">&times;</button></form>
            </div>
            @else
            <div class="group inline">{{ $user->name }}
              <form class="inline" action="{{ route('trip-users.destroy', [$trip, $user]) }}" method="POST">@method('DELETE') @csrf <button class="hidden group-hover:inline-flex items-center justify-center rounded-full bg-white text-red-500 border-2 border-red-500 w-6 h-6 p-2 font-bold" id="remove-user-{{ $user->id }}" type="submit">&times;</button></form>,
            </div>
            @endif
            @endif
            @endforeach
            @else
            {{ __('You are the only participant') }}
            @endif
          </div>
          @unless ($users->isEmpty())
          <form action="{{ route('trip-users.store', $trip) }}" method="POST" class="flex">
            @csrf
            <select name="user_id" id="user" class="rounded-l-md px-8 py-2 rounded-r-none">
              <option value="">-- {{ __('add user') }} --</option>
              @foreach ($users as $user)
              @unless(auth()->user()->is($user))
              <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endunless
              @endforeach
            </select>
            <x-primary-button class="rounded-l-none -ml-px" id="add-user">
              {{ __('Add') }}
            </x-primary-button>
          </form>
          @endunless
        </div>
      </div>
      <div x-data="{
        trip: {{ $trip }},
        regions: [],
        showForm: false,
        newRegion: {title: ''},
      }" class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 justify-between bg-white border-b border-gray-200">
          <template x-if="! showForm">
            <x-primary-button x-on:click="showForm = true" id="add-region">add region</x-primary-button>
          </template>
          <template x-if="showForm">
            <div class="flex gap-3 items-end">
              <div>
                <x-input-label for="region_title" :value="__('Region')" />
                <x-text-input autofocus x-init="$watch('newRegion', r => {
                  clearTimeout($store.timeout)
                  $store.timeout = setTimeout(() => {
                    if (r.title == '') {
                      regions = []
                      return
                    }
                    fetch(`/geocode-search?q=${r.title}`)
                      .then(resp => resp.json())
                      .then(data => {
                        console.log(data)
                        regions = data.filter(r => ['natural', 'boundary'].includes(r.class) || ['region', 'mountain_range'].includes(r.type))
                      })
                  }, 300);
                })" class="mt-1" type="text" id="region_title" x-model="newRegion.title">
                </x-text-input>
              </div>
              <div id="region_result" class="flex gap-3">
                <template x-for="region in regions">
                  <button class="rounded-full bg-green-300 px-4 py-2" x-bind:title="region.display_name" x-text="region.display_name.split(', ')[0]" x-on:mouseover="() => {
                      console.dir({region})
                    }" x-on:click="() => {
                      const data = {
                        title: region.display_name.split(', ')[0],
                        lat: region.lat,
                        long: region.lon,
                        box: JSON.stringify(region.boundingbox),
                      }
                      fetch('{{ route('trips.regions.store', $trip) }}', {
                        method: 'POST',
                        headers: {
                          'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({data, _token: '{{ csrf_token() }}'})
                      }).then(resp => resp.json())
                      .then(d => trip.regions = d);
                    }"></button>
                </template>
              </div>
            </div>
          </template>
          <div id="regions">
            <template x-for="region in trip.regions">
              <div class="flex py-2">
                <p class="w-1/5" x-text="region.title"></p>
                <input class="text-xs rounded-lg" type="datetime-local" step="1" name="arrival_at" x-bind:value="region.arrival_at" x-on:change="({target}) => {
                    fetch('{{ route('trips.regions.store', $trip) }}/'+region.id, {
                      method: 'PATCH',
                      headers: {
                        'Content-Type': 'application/json'
                      },
                      body: JSON.stringify({arrival_at: target.value, _token: '{{ csrf_token() }}'}),
                    }).then(response => response.json())
                    .then(data => trip.regions = data)
                  }">
              </div>
            </template>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
