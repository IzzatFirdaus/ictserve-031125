<x-layout.guest>
    <div class="space-y-8 p-6">
        <h1 class="text-2xl font-bold">Component Playground</h1>

        <!-- Buttons -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold">Buttons</h2>
            <div class="flex flex-wrap gap-4">
                <x-ui.button variant="primary">Primary</x-ui.button>
                <x-ui.button variant="secondary">Secondary</x-ui.button>
                <x-ui.button variant="success">Success</x-ui.button>
                <x-ui.button variant="warning">Warning</x-ui.button>
                <x-ui.button variant="danger">Danger</x-ui.button>
                <x-ui.button variant="ghost">Ghost</x-ui.button>
            </div>
            <div class="flex flex-wrap gap-4">
                <x-ui.button size="sm">Small</x-ui.button>
                <x-ui.button size="md">Medium</x-ui.button>
                <x-ui.button size="lg">Large</x-ui.button>
            </div>
            <div class="flex flex-wrap gap-4">
                <x-ui.button loading="true">Loading</x-ui.button>
                <x-ui.button disabled>Disabled</x-ui.button>
            </div>
        </section>

        <!-- Badges -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold">Badges</h2>
            <div class="flex flex-wrap gap-4">
                <x-ui.badge variant="primary">Primary</x-ui.badge>
                <x-ui.badge variant="success">Success</x-ui.badge>
                <x-ui.badge variant="warning">Warning</x-ui.badge>
                <x-ui.badge variant="danger">Danger</x-ui.badge>
                <x-ui.badge variant="gray">Gray</x-ui.badge>
            </div>
        </section>

        <!-- Alerts -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold">Alerts</h2>
            <x-ui.alert variant="info" title="Information">This is an info alert.</x-ui.alert>
            <x-ui.alert variant="success" title="Success">Operation completed successfully.</x-ui.alert>
            <x-ui.alert variant="warning" title="Warning">Please check your input.</x-ui.alert>
            <x-ui.alert variant="danger" title="Error" dismissible="true">Something went wrong.</x-ui.alert>
        </section>

        <!-- Cards -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold">Cards</h2>
            <x-ui.card>
                <x-slot name="header">Card Header</x-slot>
                <p>Card body content goes here.</p>
                <x-slot name="footer">Card Footer</x-slot>
            </x-ui.card>
        </section>

        <!-- Forms -->
        <section class="space-y-4">
            <h2 class="text-xl font-semibold">Forms</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form.input label="Text Input" placeholder="Enter text..." />
                <x-form.input label="Required Input" required />
                <x-form.input label="Error Input" error="This field is invalid" />
                <x-form.select label="Select Option" :options="['1' => 'Option 1', '2' => 'Option 2']" />
                <x-form.textarea label="Textarea" />
                <x-form.checkbox label="Checkbox" />
            </div>
        </section>

        <!-- Modals -->
        <section class="space-y-4" x-data>
            <h2 class="text-xl font-semibold">Modals</h2>
            <x-ui.button @click="$dispatch('open-modal', { name: 'demo-modal' })">Open Modal</x-ui.button>

            <x-ui.modal name="demo-modal" title="Demo Modal">
                <p>This is a modal content.</p>
                <div class="mt-4 flex justify-end">
                    <x-ui.button variant="secondary" @click="$dispatch('close-modal', { name: 'demo-modal' })">Close</x-ui.button>
                </div>
            </x-ui.modal>
        </section>
    </div>
</x-layout.guest>
