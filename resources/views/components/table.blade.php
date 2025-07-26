@props(['head' => null, 'body' => null])
<div class="align-middle min-w-full overflow-x-auto shadow overflow-hidden sm:rounded-lg
            bg-white text-gray-900
            dark:bg-gray-900 dark:text-gray-100">
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead>
            <tr class="bg-gray-40 dark:bg-gray-800">
                {{ $head }}
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700">
            {{ $body }}
        </tbody>
    </table>
</div>
