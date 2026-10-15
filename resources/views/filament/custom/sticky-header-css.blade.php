<style>
    /* Custom CSS to make the Filament Table header sticky */
    .fi-ta-content table thead tr {
        position: sticky;
        top: 0;
        z-index: 1;
        background-color: rgb(249 250 251); /* Tailwind gray-50 for light mode */
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }
    
    .dark .fi-ta-content table thead tr {
        background-color: rgb(24 24 27); /* Tailwind zinc-900 for dark mode */
    }
    
    /* Ensure the table container handles the scroll correctly */
    .fi-ta-content {
        max-height: 70vh !important;
        overflow-y: auto !important;
    }
    
    /* Ensure the table header toolbar (where the filter dropdown lives) is strictly above the table content */
    .fi-ta-header-toolbar {
        position: relative;
        z-index: 20;
    }
</style>

