@php($title = 'Support tickets')

@extends('layouts.app')

@section('content')
<div class="pt-[80px]">
    <div class="mx-auto max-w-3xl px-4 py-8 sm:py-16 lg:px-8">
        <div class="rounded-xl bg-white shadow-sm border border-gray-200">
            <div class="border-b border-gray-200 px-6 py-5">
                <h1 class="text-xl font-semibold text-gray-900">Support</h1>
                <p class="mt-1 text-sm text-gray-600">Send a message to the admin team and we will get back to you.</p>
            </div>
            <div class="px-6 py-6">
                @if (session('success'))
                    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('support.tickets.store') }}" class="space-y-5">
                    @csrf
                    <div class="hidden">
                        <label for="support-website" class="sr-only">Website</label>
                        <input id="support-website" name="website" type="text" autocomplete="off" tabindex="-1">
                    </div>
                    <div>
                        <label for="support-name" class="block text-sm font-semibold text-gray-900">Name</label>
                        <input id="support-name" name="name" type="text" autocomplete="name"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600"
                            value="{{ $name }}">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="support-email" class="block text-sm font-semibold text-gray-900">Email</label>
                        <input id="support-email" name="email" type="email" autocomplete="email"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600"
                            value="{{ $email }}">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="support-message" class="block text-sm font-semibold text-gray-900">Message</label>
                        <textarea id="support-message" name="message" rows="6"
                            class="mt-2 block w-full rounded-lg border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600">{{ $supportMessage }}</textarea>
                        @error('message')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-center justify-end">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-lg bg-green-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2">
                            Send support request
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
