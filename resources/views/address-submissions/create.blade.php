<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Submit Address Change') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('address-submissions.store') }}" class="space-y-6">
                        @csrf

                        <div>
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', auth()->user()->name)" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', auth()->user()->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="date_of_birth" :value="__('Date of Birth')" />
                            <x-text-input id="date_of_birth" name="date_of_birth" type="date" class="mt-1 block w-full" :value="old('date_of_birth', auth()->user()->date_of_birth)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('date_of_birth')" />
                        </div>

                        <div>
                            <x-input-label for="phonenumber" :value="__('Phone Number')" />
                            <x-text-input id="phonenumber" name="phonenumber" type="text" class="mt-1 block w-full" :value="old('phonenumber', auth()->user()->phonenumber)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('phonenumber')" />
                        </div>

                        <div>
                            <x-input-label for="street" :value="__('Street')" />
                            <x-text-input id="street" name="street" type="text" class="mt-1 block w-full" :value="old('street', auth()->user()->street)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('street')" />
                        </div>

                        <div>
                            <x-input-label for="number" :value="__('Number')" />
                            <x-text-input id="number" name="number" type="text" class="mt-1 block w-full" :value="old('number', auth()->user()->number)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('number')" />
                        </div>

                        <div>
                            <x-input-label for="zipcode" :value="__('Zipcode')" />
                            <x-text-input id="zipcode" name="zipcode" type="text" class="mt-1 block w-full" :value="old('zipcode', auth()->user()->zipcode)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('zipcode')" />
                        </div>

                        <div>
                            <x-input-label for="city" :value="__('City')" />
                            <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city', auth()->user()->city)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('city')" />
                        </div>

                        <div>
                            <x-input-label for="other_people" :value="__('Other People Moving Together')" />
                            <x-textarea id="other_people" name="other_people" class="mt-1 block w-full">{{ old('other_people') }}</x-textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('other_people')" />
                        </div>

                        <div>
                            <x-input-label for="note" :value="__('Note')" />
                            <x-textarea id="note" name="note" class="mt-1 block w-full">{{ old('note') }}</x-textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('note')" />
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Submit') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
