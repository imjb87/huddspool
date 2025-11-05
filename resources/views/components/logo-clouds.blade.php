@props(['compact' => false])

@php
    $paddingClasses = 'py-24 sm:py-32';
@endphp

<div {{ $attributes->class(['bg-white', $paddingClasses]) }}>
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
      <h2 class="text-center text-lg font-semibold leading-8 text-gray-900">Proudly sponsored by</h2>
      <div class="mx-auto mt-10 grid max-w-lg grid-cols-4 items-center gap-x-8 gap-y-10 sm:max-w-xl sm:grid-cols-6 sm:gap-x-10 lg:mx-0 lg:max-w-none lg:grid-cols-6">
        <a href="https://www.thepooltableguru.co.uk/" target="_blank" rel="noopener noreferrer" class="col-span-2 w-full object-contain lg:col-span-1">
          <img class="col-span-2 max-h-20 w-full object-contain lg:col-span-1 px-3" src="{{ asset('images/sponsors/thepooltableguru.jpg') }}" alt="The Pool Table Guru" width="158" height="48">
        </a>
        <a href="https://www.eagle-roofing.co.uk/" target="_blank" rel="noopener noreferrer" class="col-span-2 max-h-20 w-full object-contain lg:col-span-1">
          <img class="col-span-2 max-h-20 w-full object-contain lg:col-span-1 px-3" src="{{ asset('images/sponsors/eagleroofing-logo.png') }}" alt="Eagle Roofing" width="158" height="48">
        </a>
        <a href="https://www.thebiggerboat.co.uk/" target="_blank" rel="noopener noreferrer" class="col-span-2 max-h-20 w-full object-contain lg:col-span-1">
          <img class="col-span-2 max-h-20 w-full object-contain lg:col-span-1 px-3" src="{{ asset('images/sponsors/tbb-logo.svg') }}" alt="The Bigger Boat" width="158" height="48">
        </a>
        <a href="https://www.nrkfabrication.co.uk/" target="_blank" rel="noopener noreferrer" class="col-span-2 w-full object-contain lg:col-span-1">
          <img class="col-span-2 w-full object-contain lg:col-span-1 px-3" src="{{ asset('images/sponsors/nrkfabrication-logo.jpg') }}" alt="NRK Fabrication" width="158" height="48">
        </a>
        <a href="https://www.levelshuddersfield.co.uk/" target="_blank" rel="noopener noreferrer" class="col-span-2 max-h-20 w-full object-contain lg:col-span-1">
          <img class="col-span-2 max-h-20 w-full object-contain lg:col-span-1 px-3" src="{{ asset('images/sponsors/levelshuddersfield.svg') }}" alt="Levels Huddersfield" width="158" height="48">
        </a>
        <a href="https://www.facebook.com/ukplasticsandglazingltd" target="_blank" rel="noopener noreferrer" class="col-span-2 max-h-20 w-full object-contain lg:col-span-1">
          <img class="col-span-2 max-h-20 w-full object-contain lg:col-span-1 px-3" src="{{ asset('images/sponsors/ukplasticsandglazing-logo.jpeg') }}" alt="UK Plastics & Glazing Ltd" width="158" height="48">
        </a>
      </div>
    </div>
  </div>
  
