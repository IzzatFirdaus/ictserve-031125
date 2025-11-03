{{--
/**
 * Component name: UI Card
 * Description: Content container card component with header, body, and footer sections for organizing related information.
 * @author Pasukan BPM MOTAC
 * @trace D03-FR-006.1 (UI components)
 * @trace D03-FR-006.2 (Content structure)
 * @trace D04 §6.1 (User Interface)
 * @trace D10 §7 (Component Documentation)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §8 (MOTAC Branding)
 * @version 1.0.0
 * @created 2025-11-03
 */
--}}
 *
 * Reusable Blade component for consistent UI patterns
 *
 * @trace D04 §6.1
 * @trace D10 §7
 * @trace D12 §9
 * @trace D14 §8
 * @wcag WCAG 2.2 Level AA
 * @browsers Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 * @version 1.0.0
 * @author Pasukan BPM MOTAC
 * @created 2025-11-03
 * @updated 2025-11-03
 */
--}}
{{--
/**
 * Component: WCAG Compliant Card Container
 * Description: Accessible card component with proper semantic structure
 * Author: Pasukan BPM MOTAC
 * Requirements: 6.1, 6.2, 14.1, 22.2
 * WCAG Level: AA (SC 1.4.3, 2.1.1)
 * Version: 1.0.0
 * Created: 2025-11-03
 */
--}}

@props([
    'title' => null,
    'footer' => null,
    'padding' => true,
])

<div class="bg-white rounded-lg shadow-md overflow-hidden {{ $attributes->get('class', '') }}"
    {{ $attributes->except('class') }}>
    @if ($title)
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
        </div>
    @endif

    <div class="{{ $padding ? 'p-6' : '' }}">
        {{ $slot }}
    </div>

    @if ($footer)
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endif
</div>
