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
                  {{ $user->name }}<form class="inline" action="{{ route('trip-users.destroy', [$trip, $user]) }}" method="POST">@method('DELETE') @csrf <button class="inline-flex items-center justify-center rounded-full bg-white text-red-500 border-2 border-red-500 w-6 h-6 p-2 font-bold" id="remove-user-{{ $user->id }}" type="submit">&times;</button></form>
                  @else
                  {{ $user->name }}<form class="inline" action="{{ route('trip-users.destroy', [$trip, $user]) }}" method="POST">@method('DELETE') @csrf <button class="inline-flex items-center justify-center rounded-full bg-white text-red-500 border-2 border-red-500 w-6 h-6 p-2 font-bold" id="remove-user-{{ $user->id }}" type="submit">&times;</button></form>,
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
    </div>
  </div>
</x-app-layout>
