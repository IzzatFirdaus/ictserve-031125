@props(['id', 'label', 'description' => null])

<div class="flex items-center justify-between">
	<div class="flex flex-col">
		<label for="{{ $id }}" class="text-sm font-medium text-gray-900 dark:text-gray-100">
			{{ $label }}
		</label>
		@if($description)
			<span class="text-xs text-gray-500 dark:text-gray-400">
				{{ $description }}
			</span>
		@endif
	</div>
	<button type="button" role="switch" aria-checked="false" x-data="{ on: @entangle($attributes->wire('model')) }"
		:aria-checked="on.toString()" @click="on = !on" :class="on ? 'bg-indigo-600' : 'bg-gray-200'"
		class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-2"
		{{ $attributes->except(['wire:model', 'wire:model.live']) }}>
		<span aria-hidden="true" :class="on ? 'translate-x-5' : 'translate-x-0'"
			class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
	</button>
</div>