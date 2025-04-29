<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">

            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Articles  Edit') }}
            </h2>
            <a href="{{ route('articles.index') }}" class="bg-slate-700 text-sm rounded-md text-white px-3 py-2">Articles List</a>
        </div>    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('articles.update',$article->id) }}" method="POST">
                        @csrf
                        @if ($errors->any())
                            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                <ul class="list-disc pl-5">
                                 @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                 @endforeach
                            </ul>
                            </div>
                        @endif 
                        <div>
                            <label for="title" class="text-lg font-medium">Title</label>
                            <div class="my-3">
                                <input name="title" value="{{old('title',$article->title)}}" type="text" placeholder="Enter Title"
                                 class="border-gray-300 shadow-sm w-1/2 rounded-lg"> 
                            </div>

                             <label for="name" class="text-lg font-medium">Content</label>
                             <div class="my-3">
                                <textarea name="text" placeholder="Content" id="text" cols="150" rows="10"
                                    class="border-gray-300 shadow-sm w-1/2 rounded-lg">{{old('text',$article->text)}}</textarea>
                              </div>

                              <label for="author" class="text-lg font-medium">Author</label>
                              <div class="my-3">
                                  <input name="author" value="{{old('author',$article->author)}}" type="text" placeholder="Enter Author"
                                   class="border-gray-300 shadow-sm w-1/2 rounded-lg">
                               </div>
  
                            <button type="submit" class="bg-slate-700 text-sm rounded-md text-white px-5 py-3">Update</button>
                        </div>
                    </form> 
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
