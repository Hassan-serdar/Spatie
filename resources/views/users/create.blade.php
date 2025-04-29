<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Users Edit') }}
            </h2>
            <a href="{{ route('users.index') }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2">Users List</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('users.store') }}" method="POST">
                        @csrf
                        <div>
                            @if ($errors->any())
                            <div class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                <ul class="list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                            <label for="name" class="text-lg font-medium">Name</label>
                            <div class="my-3">
                                <input name="name" value="{{old('name')}}" type="text" placeholder="Enter Name"
                                 class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                            </div>
                            <label for="email" class="text-lg font-medium">Email</label>
                            <div class="my-3">
                                <input name="email" value="{{old('email')}}" type="text" placeholder="Enter Email"
                                 class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                            </div>

                            <label for="password" class="text-lg font-medium">Password</label>
                            <div class="my-3">
                                <input name="password" value="{{old('password')}}" type="password" placeholder="Enter Password"
                                 class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                            </div>
                            <label for="password_confirmation" class="text-lg font-medium">Password</label>
                            <div class="my-3">
                                <input name="password_confirmation" value="{{old('password_confirmation')}}" type="password" placeholder="Rewrite Password"
                                 class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                            </div>
                            <div class="grid grid-cols-4 mb-3">
                                @if ($roles->isNotEmpty())
                                    @foreach ($roles as $role)
                                        <div class="mt-3">
                                            <input
                                            type="checkbox" id="role-{{$role->id}}"  class="rounded" name="role[]" value="{{ $role->name }}">
                                            <label for="role-{{$role->id}}">{{ $role->name }}</label>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="submit" class="bg-slate-700 text-sm rounded-md text-white px-5 py-3">Submit</button>
                        </div>
                    </form> 
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
