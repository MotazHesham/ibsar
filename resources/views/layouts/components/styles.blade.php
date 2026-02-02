<!-- Choices JS -->
<script src="{{ asset('assets/libs/choices.js/public/assets/scripts/choices.min.js') }}"></script>

<!-- Bootstrap Css -->
<link id="style" href="{{ asset('assets/libs/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

<!-- Node Waves Css -->
<link href="{{ asset('assets/libs/node-waves/waves.min.css') }}" rel="stylesheet">

<!-- Simplebar Css -->
<link href="{{ asset('assets/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">

<!-- Color Picker Css -->
<link rel="stylesheet" href="{{ asset('assets/libs/@simonwep/pickr/themes/nano.min.css') }}">

<!-- Choices Css -->
<link rel="stylesheet" href="{{ asset('assets/libs/choices.js/public/assets/styles/choices.min.css') }}">

<!-- FlatPickr CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/flatpickr/flatpickr.min.css') }}">  

<!-- Hijri Date Picker CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-hijri-datepicker@1.0.2/dist/css/bootstrap-datetimepicker.min.css">

<!-- Auto Complete CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/@tarekraafat/autocomplete.js/css/autoComplete.css') }}">

<!-- Datatables Cdn -->
<link rel="stylesheet" href="{{ asset('dashboard_offline/css/jquery.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('dashboard_offline/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ asset('dashboard_offline/css/buttons.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('dashboard_offline/css/select.dataTables.min.css') }}">

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

<!-- Filepond CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/filepond/filepond.min.css') }}">
<link rel="stylesheet"
    href="{{ asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.css') }}">
<link rel="stylesheet"
    href="{{ asset('assets/libs/filepond-plugin-image-edit/filepond-plugin-image-edit.min.css') }}">

<!-- Dropzone CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/dropzone/dropzone.css') }}">

<!-- Toastify CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/toastify-js/src/toastify.css') }}">

<!-- Sweetalerts CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}">

<!-- Quill Editor CSS -->
<link rel="stylesheet" href="{{ asset('assets/libs/quill/quill.snow.css') }}">
<link rel="stylesheet" href="{{ asset('assets/libs/quill/quill.bubble.css') }}">

<style>
    /* Datatables */
    .dataTables_wrapper .dt-buttons {
        float: left;
        margin-left: 1rem;
    }

    .dt-buttons .btn {
        margin-right: 5px;
    }

    [dir="rtl"] .dataTables_wrapper .dt-buttons {
        float: right;
        margin-left: 0;
        margin-right: 1rem;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button{
        padding:0 !important;
    }
    [dir="rtl"] .dt-buttons .btn {
        margin-right: 0;
        margin-left: 5px;
    }

    table.dataTable tbody td.select-checkbox::before,
    table.dataTable tbody td.select-checkbox::after,
    table.dataTable tbody th.select-checkbox::before,
    table.dataTable tbody th.select-checkbox::after {
        top: 50%;
    }
    table.dataTable thead th {
        border-bottom: 1px solid #c8ced3;
    }

    .dataTables_wrapper.no-footer .dataTables_scrollBody {
        border-bottom: 1px solid #c8ced3;
    }

    /* Form Validation */
    .is-invalid {
        border-color: #dc3545;
    }

    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875em;
        margin-top: 0.25rem;
    }

    /* Dropzone */ 
    [data-theme-mode="dark"] .dropzone .dz-preview.dz-image-preview  {
        background: rgba(0, 0, 0, 0.2); 
        color: #ffffff;
    }  

    .help-block {
        font-size: 0.875em;
        color: #6c757d;
    }
</style>
