<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">

        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Articles') }}
        </h2>
        <a href="{{ route('articles.create') }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2">Create</a>
    </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-message></x-message>
            <table class="w-full">
                <thead class="bg-gary-50">
                    <tr class="border-b">
                        <th class="px-6 py-3 text-left" width="60">#</th>
                        <th class="px-6 py-3 text-left">Title</th>
                        <th class="px-6 py-3 text-left">Author</th>
                        <th class="px-6 py-3 text-left" width='180'>Created At</th>
                        <th class="px-6 py-3 text-center" width='180'>Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    @if($articles)
                    @foreach ($articles as $article)
                    <tr>
                        <td class="px-6 py-3 text-left">{{$article->id}}</td>
                        <td class="px-6 py-3 text-left">{{$article->title}}</td>
                        <td class="px-6 py-3 text-left">{{$article->author}}</td>
                        <td class="px-6 py-3 text-left">{{\Carbon\Carbon::parse($article->created_at)->format('d M,Y')}}</td>
                        <td class="px-6 py-3 text-center">
                            <div class="flex my-2">
                            <a href="{{ route('articles.edit', $article->id) }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2 hover:bg-slate-600">
                                Edit</a>
                            <form action="{{ route('articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                @csrf
                                @method('delete')
                                <button type="submit" class="bg-red-700 text-sm rounded-md text-white px-3 py-2 hover:bg-red-500 ml-2">
                                Delete</button>
                            </form>
                        </td>
                            </div>
                    </tr>

                    @endforeach
                    @endif
                </tbody>
            </table>
            <div class="my-3">
                {{$articles->links()}}
            </div>
        </div>
    </div>
</x-app-layout>
