<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ $trip->title }}
    </h2>
  </x-slot>

  <div class="py-12 w-full">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col h-full">
      <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 flex justify-between bg-white border-b border-gray-200">
          <div id="user-list">
            <!-- TODO: make this more interactive, alpine it / move to header? -->
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
        showForm: false,
        newRegion: '',
        display: (() => location.hash ? location.hash.split(',') : ['#overview'])(),
      }" class="mt-6 flex flex-1 bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 flex flex-wrap w-full bg-white border-b border-gray-200">
          <template x-if="display.includes('#overview')">
            <div id="regions" class="w-full lg:w-1/5 xl:w-1/6 pr-6">
              <template x-for="region in trip.regions">
                <div cy-id="region-row" class="py-2 gap-3 group">
                  <a x-bind:id="'link-region-' + region.id" x-bind:href="'#region,'+region.id" x-text="region.title" @click="display = ['#region', region.id]"></a>
                </div>
              </template>
              <template x-if="! showForm">
                <x-primary-button x-on:click="showForm = true" id="add-region">add region</x-primary-button>
              </template>
              <template x-if="showForm">
                <div class="w-full">
                  <div>
                    <x-input-label for="region-title" :value="__('Region')" />
                    <x-text-input autofocus x-init="$watch('newRegion', title => $store.search.region(title))" class="mt-1 w-full" type="text" id="region-title" x-model="newRegion">
                    </x-text-input>
                  </div>
                  <div id="region-result" class="flex flex-wrap gap-3 mt-3">
                    <template x-for="region in $store.search.results.regions">
                      <button class="rounded-full bg-green-300 text-xs px-2 py-1" x-bind:title="region.display_name" x-text="region.display_name.split(', ')[0]" @click="$store.region.create(
                          '{{ route('trips.regions.store', $trip) }}',
                          '{{ csrf_token() }}',
                          region
                        ).then(data => {
                          trip.regions = data
                          newRegion = ''
                          showForm = !showForm
                        })"></button>
                    </template>
                  </div>
                </div>
              </template>
            </div>
          </template>
          <template x-if="display.includes('#region')">
            <div class="w-full lg:w-1/5 xl:w-1/6 pr-6" x-data="{
              region: (() => trip.regions.find(r => r.id == display[1]))(),
            }">
              <h1 x-bind:id="'header-region-' + region.id" class="-mx-4 py-2 text-gray-500 flex items-center justify-between gap-2">
                <div class="gap-2 truncate">
                  <a id="back-to-trip-overview" href="#overview" @click="display = ['#overview']" class="w-6 h-6 inline-flex items-center flex-shrink-0 justify-center rounded-md border border-gray-200 text-gray-200 hover:text-gray-500">&lt;</a>
                  <span class="text-xs uppercase tracking-widest truncate" x-text="region.title"></span>
                </div>
                <template x-if="!display.includes('edit')">
                  <a x-bind:id="'edit-region-' + region.id" class="w-6 h-6 inline-flex items-center justify-center flex-shrink-0 rounded-md border border-gray-200 text-gray-200 hover:text-gray-500" x-bind:href="`#region,${region.id},edit`" @click="display = ['#region', region.id, 'edit']">e</a>
                </template>
                <template x-if="display.includes('edit')">
                  <a class="w-6 h-6 inline-flex items-center justify-center flex-shrink-0 rounded-md border border-gray-200 text-gray-200 hover:text-gray-500" x-bind:href="`#region,${region.id}`" @click="display = ['#region', region.id]">&times;</a>
                </template>
              </h1>
              <template x-if="display.includes('edit')">
                <div x-bind:id="'edit-region-' + region.id" class="group -mx-4">
                  <x-input-label for="title" :value="__('Title')" />
                  <x-text-input x-model="region.title" class="block px-4 py-2 w-full" type="text" name="title" x-bind:id="`region-title-${region.id}`" @change="e => $store.region.update(
                      '{{ route('trips.regions.store', $trip) }}/'+region.id,
                      '{{ csrf_token() }}',
                      { title: e.target.value}
                    ).then(data => trip.regions = data)" required autofocus />
                  <x-input-label class="mt-2" for="arrival_at" :value="__('Arrival at')" />
                  <x-text-input x-model="region.arrival_at" class="block px-4 py-2 w-full" type="datetime-local" step="1" name="arrival_at" x-bind:id="`region-arrival_at-${region.id}`" @change="e => $store.region.update(
                      '{{ route('trips.regions.store', $trip) }}/'+region.id,
                      '{{ csrf_token() }}',
                      { arrival_at: e.target.value}
                    ).then(data => trip.regions = data)" />
                  <form x-bind:action="'{{ route('trips.regions.store', $trip) }}/'+region.id" method="post">
                    @csrf
                    @method('DELETE')
                    <button class="hidden group-hover:inline-flex items-center justify-center rounded-full bg-white text-red-500 border-2 border-red-500 w-6 h-6 p-2 font-bold">&times;</button>
                  </form>
                </div>
              </template>
              <template x-if="!display.includes('edit')">
                <div class="-mx-4 pl-2">
                  <p>list places in region</p>
                  <button class="items-center border-2 px-4 py-1 rounded-lg">{{ __("add place") }}</button>
                </div>
                <!-- TODO: add place to region -->
              </template>
            </div>
          </template>
          <div class="w-full lg:w-4/5 xl:w-5/6">
            <div id="map" class="w-full h-full" x-init="$watch('trip', (trip => $store.map.update(trip))); $nextTick(() => $store.map.show(trip));"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
