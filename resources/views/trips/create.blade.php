<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
      {{ __('Start a new trip') }}
    </h2>
  </x-slot>

  <form method="POST" action="{{ route('trips.store') }}">
    @csrf

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
          <div class="p-6 bg-white border-b border-gray-200">
            <x-input-label for="title" :value="__('Title')" />
            <div class="flex mt-1">
              <x-text-input id="title" class="block px-4 py-2 rounded-r-none flex-grow" type="text" name="title" :value="old('title')" required autofocus />
              <x-primary-button class="rounded-l-none -ml-px" id="create">
                {{ __('Start') }}
              </x-primary-button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</x-app-layout>
