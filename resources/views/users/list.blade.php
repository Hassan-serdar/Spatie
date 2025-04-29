<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Users') }}
        </h2>
    </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message></x-message>

            <table class="w-full">
                <thead class="bg-gary-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="60">#</th>
                        <th class="px-6 py-3 text-left">Name</th>
                        <th class="px-6 py-3 text-left">Email</th>
                        <th class="px-6 py-3 text-left">roles</th>
                        <th class="px-6 py-3 text-left" width='180'>Created</th>
                        <th class="px-6 py-3 text-center" width='180'>Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if($users)
                    @foreach ($users as $user)
                    <tr>
                        <td class="px-6 py-3 text-left">{{$user->id}}</td>
                        <td class="px-6 py-3 text-left">{{$user->name}}</td>
                        <td class="px-6 py-3 text-left">{{$user->email}}</td>
                        <td class="px-6 py-3 text-left">{{$user->roles->pluck('name')->implode(', ')}}</td>
                        <td class="px-6 py-3 text-left">{{\Carbon\Carbon::parse($user->created_at)->format('d M,Y')}}</td>
                        <td class="px-6 py-3 text-center">
                                <div class="flex my-2">
                                    @can('Edit Users')
                                <a href="{{ route('users.edit', $user->id) }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2 hover:bg-slate-600">
                                    Edit</a>
                                    @endcan

                                    @can('Delete Users')
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="bg-red-700 text-sm rounded-md text-white px-3 py-2 hover:bg-red-500 ml-2">
                                    Delete</button>
                                    @endcan
                                </form>    
                        </td>
                                </div>  

                    </tr>
 
                    @endforeach
                    @endif
                </tbody>
            </table>
            <div class="my-3">
                {{$users->links()}}
            </div>
        </div>
    </div>
</x-app-layout>
