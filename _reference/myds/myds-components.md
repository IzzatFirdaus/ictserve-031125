# MYDS Component Library for ICTServe

This document provides implementation guidelines for MYDS-compliant components used throughout the ICTServe system. All components must follow these exact patterns for consistency and accessibility.

## Button Components

### Primary Button

```html
<x-myds.button
  variant="primary"
  size="md"
  wire:click="submit"
  wire:loading.attr="disabled"
  class="min-w-[120px]"
>
  <span wire:loading.remove>Submit Application</span>
  <span wire:loading>Processing...</span>
</x-myds.button>
```

### Destructive Button

```html
<x-myds.button
  variant="danger"
  size="md"
  wire:click="delete"
  wire:confirm="Are you sure you want to delete this item?"
  class="min-w-[100px]"
>
  Delete
</x-myds.button>
```

### Secondary Button

```html
<x-myds.button variant="secondary" size="md" wire:click="cancel">
  Cancel
</x-myds.button>
```

## Form Input Components

### Text Input with Label

```html
<div class="space-y-1">
  <label for="purpose" class="font-inter text-xs font-medium text-gray-600">
    Purpose of Loan <span class="text-danger-600">*</span>
  </label>
  <x-myds.input
    id="purpose"
    type="text"
    wire:model.live="purpose"
    placeholder="Enter the purpose for this equipment loan"
    required
    aria-describedby="purpose-help"
    class="w-full"
  />
  <p id="purpose-help" class="font-inter text-xs text-gray-500">
    Provide a clear description of why you need this equipment
  </p>
  @error('purpose')
  <p class="font-inter text-xs text-danger-600" role="alert">{{ $message }}</p>
  @enderror
</div>
```

### Select Dropdown

```html
<div class="space-y-1">
  <label
    for="equipment-type"
    class="font-inter text-xs font-medium text-gray-600"
  >
    Equipment Type <span class="text-danger-600">*</span>
  </label>
  <x-myds.select
    id="equipment-type"
    wire:model.live="equipmentType"
    required
    aria-describedby="equipment-type-help"
  >
    <option value="">Select equipment type</option>
    <option value="laptop">Laptop</option>
    <option value="projector">Projector</option>
    <option value="camera">Camera</option>
    <option value="microphone">Microphone</option>
  </x-myds.select>
  <p id="equipment-type-help" class="font-inter text-xs text-gray-500">
    Choose the type of equipment you need to borrow
  </p>
  @error('equipmentType')
  <p class="font-inter text-xs text-danger-600" role="alert">{{ $message }}</p>
  @enderror
</div>
```

### Checkbox with Label

```html
<div class="flex items-start space-x-3">
  <x-myds.checkbox
    id="terms-agreement"
    wire:model="termsAgreed"
    required
    aria-describedby="terms-help"
  />
  <div class="flex-1">
    <label
      for="terms-agreement"
      class="font-inter text-sm font-normal text-gray-700 cursor-pointer"
    >
      I agree to the terms and conditions for equipment loans
      <span class="text-danger-600">*</span>
    </label>
    <p id="terms-help" class="font-inter text-xs text-gray-500 mt-1">
      You must accept the terms to proceed with your loan application
    </p>
  </div>
</div>
@error('termsAgreed')
<p class="font-inter text-xs text-danger-600" role="alert">{{ $message }}</p>
@enderror
```

### Date Input

```html
<div class="space-y-1">
  <label
    for="loan-start-date"
    class="font-inter text-xs font-medium text-gray-600"
  >
    Loan Start Date <span class="text-danger-600">*</span>
  </label>
  <x-myds.input
    id="loan-start-date"
    type="date"
    wire:model.live="loanStartDate"
    min="{{ date('Y-m-d') }}"
    required
    aria-describedby="start-date-help"
  />
  <p id="start-date-help" class="font-inter text-xs text-gray-500">
    Select when you need to start using the equipment
  </p>
  @error('loanStartDate')
  <p class="font-inter text-xs text-danger-600" role="alert">{{ $message }}</p>
  @enderror
</div>
```

### Textarea

```html
<div class="space-y-1">
  <label
    for="problem-description"
    class="font-inter text-xs font-medium text-gray-600"
  >
    Problem Description <span class="text-danger-600">*</span>
  </label>
  <textarea
    id="problem-description"
    wire:model.live="problemDescription"
    rows="4"
    placeholder="Describe the ICT problem you are experiencing in detail..."
    required
    aria-describedby="description-help"
    class="w-full px-3 py-2 border border-gray-300 rounded-md font-inter text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-600"
  ></textarea>
  <p id="description-help" class="font-inter text-xs text-gray-500">
    Provide as much detail as possible to help our technicians understand the
    issue
  </p>
  @error('problemDescription')
  <p class="font-inter text-xs text-danger-600" role="alert">{{ $message }}</p>
  @enderror
</div>
```

## Notification Components

### Success Callout

```html
<x-myds.callout variant="success" class="mb-6">
  <div class="flex items-start space-x-3">
    <svg
      class="w-5 h-5 text-success-600 mt-0.5"
      fill="currentColor"
      viewBox="0 0 20 20"
    >
      <path
        fill-rule="evenodd"
        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
        clip-rule="evenodd"
      />
    </svg>
    <div>
      <h3 class="font-inter text-sm font-medium text-success-800">
        Application Submitted Successfully
      </h3>
      <p class="font-inter text-sm text-success-700 mt-1">
        Your loan application has been submitted and is now pending approval.
        You will receive notifications about status updates.
      </p>
    </div>
  </div>
</x-myds.callout>
```

### Error Callout

```html
<x-myds.callout variant="danger" class="mb-6">
  <div class="flex items-start space-x-3">
    <svg
      class="w-5 h-5 text-danger-600 mt-0.5"
      fill="currentColor"
      viewBox="0 0 20 20"
    >
      <path
        fill-rule="evenodd"
        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
        clip-rule="evenodd"
      />
    </svg>
    <div>
      <h3 class="font-inter text-sm font-medium text-danger-800">
        Application Error
      </h3>
      <p class="font-inter text-sm text-danger-700 mt-1">
        There was an error processing your application. Please check the form
        and try again.
      </p>
    </div>
  </div>
</x-myds.callout>
```

### Warning Callout

```html
<x-myds.callout variant="warning" class="mb-6">
  <div class="flex items-start space-x-3">
    <svg
      class="w-5 h-5 text-warning-600 mt-0.5"
      fill="currentColor"
      viewBox="0 0 20 20"
    >
      <path
        fill-rule="evenodd"
        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
        clip-rule="evenodd"
      />
    </svg>
    <div>
      <h3 class="font-inter text-sm font-medium text-warning-800">
        Equipment Return Reminder
      </h3>
      <p class="font-inter text-sm text-warning-700 mt-1">
        Your loan period ends in 3 days. Please prepare to return the equipment
        on time to avoid penalties.
      </p>
    </div>
  </div>
</x-myds.callout>
```

## Status Badge Components

### Status Badges

```html
<!-- Approved Status -->
<span
  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800"
>
  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
    <path
      fill-rule="evenodd"
      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
      clip-rule="evenodd"
    />
  </svg>
  Approved
</span>

<!-- Pending Status -->
<span
  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800"
>
  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
    <path
      fill-rule="evenodd"
      d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
      clip-rule="evenodd"
    />
  </svg>
  Pending
</span>

<!-- Rejected Status -->
<span
  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-800"
>
  <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
    <path
      fill-rule="evenodd"
      d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
      clip-rule="evenodd"
    />
  </svg>
  Rejected
</span>
```

## Card/Panel Components

### Information Card

```html
<div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">
  <div class="flex items-start justify-between">
    <div class="flex-1">
      <h3 class="font-poppins text-lg font-medium text-gray-900">
        Loan Application #LA-2024-001
      </h3>
      <p class="font-inter text-sm text-gray-600 mt-1">Dell Laptop - 2 units</p>
      <div class="flex items-center space-x-4 mt-3">
        <span class="font-inter text-xs text-gray-500"
          >Submitted: 12 Jan 2024</span
        >
        <span
          class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-warning-100 text-warning-800"
        >
          Pending Approval
        </span>
      </div>
    </div>
    <button
      class="text-primary-600 hover:text-primary-700 font-inter text-sm font-medium"
    >
      View Details
    </button>
  </div>
</div>
```

## Table Components

### Responsive Data Table

```html
<div
  class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden"
>
  <table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
      <tr>
        <th
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
        >
          Application ID
        </th>
        <th
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
        >
          Equipment
        </th>
        <th
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
        >
          Status
        </th>
        <th
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
        >
          Date
        </th>
        <th
          class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
        >
          Actions
        </th>
      </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
      @forelse($applications as $application)
      <tr class="hover:bg-gray-50">
        <td
          class="px-6 py-4 whitespace-nowrap font-inter text-sm text-gray-900"
        >
          {{ $application->id }}
        </td>
        <td
          class="px-6 py-4 whitespace-nowrap font-inter text-sm text-gray-600"
        >
          {{ $application->equipment_type }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap">
          <span
            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                        @if($application->status === 'approved') bg-success-100 text-success-800
                        @elseif($application->status === 'pending') bg-warning-100 text-warning-800
                        @else bg-danger-100 text-danger-800 @endif"
          >
            {{ ucfirst($application->status) }}
          </span>
        </td>
        <td
          class="px-6 py-4 whitespace-nowrap font-inter text-sm text-gray-600"
        >
          {{ $application->created_at->format('d M Y') }}
        </td>
        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
          <button
            wire:click="viewApplication({{ $application->id }})"
            class="text-primary-600 hover:text-primary-700 font-inter text-sm"
          >
            View
          </button>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="px-6 py-8 text-center">
          <p class="font-inter text-sm text-gray-500">No applications found</p>
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
```

## Modal/Dialog Components

### Confirmation Dialog

```html
<div
  x-data="{ open: @entangle('showDeleteDialog') }"
  x-show="open"
  class="fixed inset-0 z-50 overflow-y-auto"
  style="display: none;"
>
  <div
    class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0"
  >
    <div
      class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
      @click="open = false"
    ></div>

    <div
      class="inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg"
    >
      <div class="flex items-start space-x-3">
        <div class="flex-shrink-0">
          <svg
            class="w-6 h-6 text-danger-600"
            fill="currentColor"
            viewBox="0 0 20 20"
          >
            <path
              fill-rule="evenodd"
              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
              clip-rule="evenodd"
            />
          </svg>
        </div>
        <div class="flex-1">
          <h3 class="font-poppins text-lg font-medium text-gray-900">
            Confirm Deletion
          </h3>
          <p class="font-inter text-sm text-gray-600 mt-2">
            Are you sure you want to delete this application? This action cannot
            be undone.
          </p>
        </div>
      </div>

      <div class="flex justify-end space-x-3 mt-6">
        <x-myds.button variant="secondary" @click="open = false">
          Cancel
        </x-myds.button>
        <x-myds.button variant="danger" wire:click="confirmDelete">
          Delete Application
        </x-myds.button>
      </div>
    </div>
  </div>
</div>
```

## Loading States

### Button Loading State

```html
<x-myds.button
  variant="primary"
  wire:click="submitApplication"
  wire:loading.attr="disabled"
  class="min-w-[140px]"
>
  <span wire:loading.remove>Submit Application</span>
  <span wire:loading class="flex items-center">
    <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
      <circle
        class="opacity-25"
        cx="12"
        cy="12"
        r="10"
        stroke="currentColor"
        stroke-width="4"
      ></circle>
      <path
        class="opacity-75"
        fill="currentColor"
        d="m12 6c3.31 0 6 2.69 6 6 0 3.31-2.69 6-6 6s-6-2.69-6-6c0-3.31 2.69-6 6-6z"
      ></path>
    </svg>
    Processing...
  </span>
</x-myds.button>
```

### Content Loading State

```html
<div
  wire:loading.flex
  wire:target="loadApplications"
  class="flex items-center justify-center py-8"
>
  <svg
    class="w-6 h-6 text-primary-600 animate-spin mr-3"
    fill="none"
    viewBox="0 0 24 24"
  >
    <circle
      class="opacity-25"
      cx="12"
      cy="12"
      r="10"
      stroke="currentColor"
      stroke-width="4"
    ></circle>
    <path
      class="opacity-75"
      fill="currentColor"
      d="m12 6c3.31 0 6 2.69 6 6 0 3.31-2.69 6-6 6s-6-2.69-6-6c0-3.31 2.69-6 6-6z"
    ></path>
  </svg>
  <span class="font-inter text-sm text-gray-600">Loading applications...</span>
</div>
```

These components ensure consistent implementation of MYDS principles throughout the ICTServe system while maintaining accessibility and user experience standards.
