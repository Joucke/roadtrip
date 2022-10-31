<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">
			{{ __('Dashboard') }}
		</h2>
	</x-slot>

	<div class="py-12 w-full">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
				<div class="p-6 bg-white border-b border-gray-200">
					You're logged in!
				</div>
			</div>

			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
				<div class="p-6 bg-white border-b border-gray-200">
					<a href="{{ route('trips.create') }}" id="create-trip">{{ __('Start a new trip!') }}</a>
				</div>
			</div>

      @if ($trip)
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
				<div class="p-6 bg-white border-b border-gray-200">
					<a href="{{ route('trips.show', $trip) }}" id="continue-{{ $trip->id }}">{{ __('Continue with') }} {{ $trip->title }}</a>
				</div>
			</div>
      @endif
		</div>
	</div>
</x-app-layout>
