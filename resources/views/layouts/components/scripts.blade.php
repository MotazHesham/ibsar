
          <!-- Scroll To Top -->
          <div class="scrollToTop">
               <span class="arrow"><i class="ti ti-arrow-narrow-up fs-20"></i></span>
          </div>
          <div id="responsive-overlay"></div>
          <!-- Scroll To Top -->

          <!-- Popper JS -->
          <script src="{{asset('assets/libs/@popperjs/core/umd/popper.min.js')}}"></script>

          <!-- Bootstrap JS -->
          <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

          <!-- Node Waves JS-->
          <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>

          <!-- Simplebar JS -->
          <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
          <script src="{{asset('assets/js/simplebar.js')}}"></script>

          <!-- Auto Complete JS -->
          <script src="{{asset('assets/libs/@tarekraafat/autocomplete.js/autoComplete.min.js')}}"></script>

          <!-- Color Picker JS -->
          <script src="{{asset('assets/libs/@simonwep/pickr/pickr.es5.min.js')}}"></script>

          <!-- Date & Time Picker JS -->
          <script src="{{asset('assets/libs/flatpickr/flatpickr.min.js')}}"></script>


          <!-- Jquery Cdn -->
          <script src="{{ asset('dashboard_offline/js/jquery.min.js') }}"></script>
          
          <!-- Hijri Date Picker JS -->
          <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment-with-locales.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/moment-hijri@2.1.2/moment-hijri.min.js"></script>
          <script src="https://cdn.jsdelivr.net/npm/bootstrap-hijri-datepicker@1.0.2/dist/js/bootstrap-hijri-datetimepicker.min.js"></script>

          <!-- Datatables Cdn -->
          <script src="{{ asset('dashboard_offline/js/jquery.dataTables.min.js') }}"></script>
          <script src="{{ asset('dashboard_offline/js/dataTables.bootstrap4.min.js') }}"></script>
          <script src="{{ asset('dashboard_offline/js/dataTables.buttons.min.js') }}"></script>
          <script src="{{ asset('dashboard_offline/js/buttons.flash.min.js') }}"></script>
          <script src="{{ asset('dashboard_offline/js/buttons.html5.min.js') }}"></script>
          <script src="{{ asset('dashboard_offline/js/buttons.print.min.js') }}"></script>
          <script src="{{ asset('dashboard_offline/js/buttons.colVis.min.js') }}"></script>
          <script src="{{ asset('dashboard_offline/js/pdfmake.min.js') }}"></script>
          <script src="{{ asset('dashboard_offline/js/vfs_fonts.js') }}"></script>
          <script src="{{ asset('dashboard_offline/js/jszip.min.js') }}"></script>
          <script src="{{ asset('dashboard_offline/js/dataTables.select.min.js') }}"></script>

          <!-- Filepond JS -->
          <script src="{{ asset('assets/libs/filepond/filepond.min.js')}}"></script>
          <script src="{{ asset('assets/libs/filepond-plugin-image-preview/filepond-plugin-image-preview.min.js')}}"></script>
          <script src="{{ asset('assets/libs/filepond-plugin-file-validate-type/filepond-plugin-file-validate-type.min.js')}}"></script> 
          <script src="{{ asset('assets/libs/filepond-plugin-file-validate-size/filepond-plugin-file-validate-size.min.js')}}"></script> 

          <!-- Dropzone JS -->
          <script src="{{asset('assets/libs/dropzone/dropzone-min.js')}}"></script>

          <!-- Vanilla-Wizard JS -->
          <script src="{{asset('assets/libs/vanilla-wizard/js/wizard.min.js')}}"></script>
          <script src="{{asset('assets/js/form-wizard.js')}}"></script>
          
          <!-- Select2 Cdn -->
          <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> 

          <!-- Toastify JS -->
          <script src="{{asset('assets/libs/toastify-js/src/toastify.js')}}"></script>

          <!-- SweetAlert2 JS -->
          <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>

          <!-- Sortable JS -->
          <script src="{{asset('assets/libs/sortablejs/Sortable.min.js')}}"></script>

          <!-- Quill Editor JS -->
          <script src="{{asset('assets/libs/quill/quill.js')}}"></script>
          @include('utilities.archiving')
          <script>
               $(document).ready(function () {
                    window._token = $('meta[name="csrf-token"]').attr('content')
               });
          </script>
          <!-- datatables -->
          <script>
               $(function() {
                    let copyButtonTrans = '{{ trans('global.datatables.copy') }}'
                    let csvButtonTrans = '{{ trans('global.datatables.csv') }}'
                    let excelButtonTrans = '{{ trans('global.datatables.excel') }}'
                    let pdfButtonTrans = '{{ trans('global.datatables.pdf') }}'
                    let printButtonTrans = '{{ trans('global.datatables.print') }}'
                    let colvisButtonTrans = '{{ trans('global.datatables.colvis') }}'
                    let selectAllButtonTrans = '{{ trans('global.select_all') }}'
                    let selectNoneButtonTrans = '{{ trans('global.deselect_all') }}'
          
                    let languages = {
                         'ar': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/Arabic.json',
                         'en': 'https://cdn.datatables.net/plug-ins/1.10.19/i18n/English.json'
                    };
          
                    $.extend(true, $.fn.dataTable.Buttons.defaults.dom.button, {
                         className: 'btn'
                    })
                    $.extend(true, $.fn.dataTable.defaults, {
                         language: {
                              url: languages['{{ app()->getLocale() }}']
                         },
                         columnDefs: [{
                              orderable: false,
                              className: 'select-checkbox',
                              targets: 0
                         }, {
                              orderable: false,
                              searchable: false,
                              targets: -1
                         }],
                         select: {
                              style: 'multi+shift',
                              selector: 'td:first-child'
                         }, 
                         order: [],
                         scrollX: true,
                         pageLength: 100,
                         dom: '<"row"<"col-sm-12"<"d-flex justify-content-between align-items-center flex-wrap"<"d-flex align-items-center"lB><"d-flex align-items-center"f>>>>rtip<"actions">',
                         buttons: [{
                                   extend: 'selectAll',
                                   className: 'btn-primary-light rounded-pill',
                                   text: selectAllButtonTrans,
                                   exportOptions: {
                                   columns: ':visible'
                                   },
                                   action: function(e, dt) {
                                   e.preventDefault()
                                   dt.rows().deselect();
                                   dt.rows({
                                        search: 'applied'
                                   }).select();
                                   }
                              },
                              {
                                   extend: 'selectNone',
                                   className: 'btn-primary-light rounded-pill',
                                   text: selectNoneButtonTrans,
                                   exportOptions: {
                                   columns: ':visible'
                                   }
                              },
                              {
                                   extend: 'copy',
                                   className: 'btn-light rounded-pill',
                                   text: copyButtonTrans,
                                   exportOptions: {
                                   columns: ':visible'
                                   }
                              },
                              {
                                   extend: 'csv',
                                   className: 'btn-light rounded-pill',
                                   text: csvButtonTrans,
                                   exportOptions: {
                                   columns: ':visible'
                                   }
                              },
                              {
                                   extend: 'excel',
                                   className: 'btn-light rounded-pill',
                                   text: excelButtonTrans,
                                   exportOptions: {
                                   columns: ':visible'
                                   },
                                   customize: function(xlsx) {
                                   var sheet = xlsx.xl.worksheets['sheet1.xml'];
                                   $('row:first c', sheet).attr('s', '2');
                                   }
                              },
                              {
                                   extend: 'print',
                                   className: 'btn-light rounded-pill',
                                   text: printButtonTrans,
                                   exportOptions: {
                                   columns: ':visible'
                                   }
                              }, 
                         ]
                    }); 

               });
          </script>

          <!-- ajax modal -->
          <script>
               
               function showAjaxModal(url,data) {
                    // Show the modal
                    $('#ajaxModal').modal('show');
                    
                    // Show loading spinner in modal content
                    $('#ajaxModal .modal-content').html(`
                         <div class="modal-header">
                              <h5 class="modal-title">Loading...</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                         </div>
                         <div class="modal-body text-center py-5">
                              <div class="spinner-border text-primary" role="status">
                                   <span class="visually-hidden">Loading...</span>
                              </div>
                              <div class="mt-3">Please wait...</div>
                         </div>
                    `);
                    
                    // Make AJAX request
                    $.ajax({
                         url: url,
                         type: 'POST',
                         data: {...data, _token: '{{ csrf_token() }}'},
                         success: function(response) {
                              // Load response into modal content
                              $('#ajaxModal .modal-content').html(null); 
                              $('#ajaxModal .modal-content').html(response.html); 
                         },
                         error: function(xhr, status, error) {
                            // Show error message
                              $('#ajaxModal .modal-content').html(`
                                   <div class="modal-header">
                                        <h5 class="modal-title text-danger">Error</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                   </div>
                                   <div class="modal-body">
                                        <div class="alert alert-danger">
                                        <i class="ti ti-alert-circle me-2"></i>
                                        Failed to load content. Please try again.
                                        </div>
                                        <small class="text-muted">Error: ${error}</small>
                                   </div>
                                   <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                   </div>
                              `);
                         }
                    });
               }

               function showAjaxOffcanvas(url, data, position = "left" , offCanvasId = 'ajaxOffcanvas') {
                    $('#' + offCanvasId).offcanvas('show'); 
                    $('#' + offCanvasId).html(`
                         <div class="offcanvas-header border-bottom border-block-end-dashed">
                              <h5 class="offcanvas-title" id="${offCanvasId}Label"></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                         </div>
                         <div class="offcanvas-body p-0">
                              <div class="text-center py-5">
                                   <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                   </div>
                                   <div class="mt-3">Please wait...</div>
                              </div>
                         </div>
                    `);
                    $.ajax({
                         url: url,
                         type: 'POST',
                         data: {...data, _token: '{{ csrf_token() }}'},
                         success: function(response) {
                              $('#' + offCanvasId).html(response.html);
                         },
                         error: function(xhr, status, error) {
                              $('#' + offCanvasId).html(`
                                   <div class="offcanvas-header border-bottom border-block-end-dashed">
                                        <h5 class="offcanvas-title text-danger">Error</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                                   </div>
                                   <div class="offcanvas-body p-0">
                                        <div class="text-center py-5">
                                             <div class="alert alert-danger">
                                                  <i class="ti ti-alert-circle me-2"></i>
                                                  Failed to load content. Please try again.
                                             </div>
                                             <small class="text-muted">Error: ${error}</small>
                                        </div>
                                   </div>    
                              `);  
                         }
                    });
               }

               // Add event listener for sidebar toggle
               $(document).on('click', '[data-nav-layout="vertical"]', function() {
                    setTimeout(function() {
                         $('table[class*="datatable-"]').DataTable().columns.adjust();
                    }, 400);
               }); 
               
               $(document).ready(function() {
                    $('.select2').select2();
                    $('.select-all').click(function () {
                         let $select2 = $(this).parent().siblings('.select2')
                         $select2.find('option').prop('selected', 'selected')
                         $select2.trigger('change')
                    })
                    $('.deselect-all').click(function () {
                         let $select2 = $(this).parent().siblings('.select2')
                         $select2.find('option').prop('selected', '')
                         $select2.trigger('change')
                    })

                    // Check for stored toast message
                    const toastMessage = localStorage.getItem('toastMessage');
                    const toastType = localStorage.getItem('toastType');
                    if (toastMessage) {
                         showToast(toastMessage, toastType, 'top');
                         // Clear the stored message
                         localStorage.removeItem('toastMessage');
                         localStorage.removeItem('toastType');
                    }
               });
               
               function updateStatuses(el, type, model) {
                    if (el.checked) {
                         var status = 1;
                    } else {
                         var status = 0;
                    }
                    $.post('{{ route('admin.updateStatuses') }}', {
                         _token: '{{ csrf_token() }}',
                         id: el.value,
                         status: status,
                         type: type,
                         model: model,
                    }, function(data) {
                         if (data == 1) {
                              showToast('{{ trans("global.flash.success") }}', 'success', 'top');
                         } else {
                              showToast('{{ trans("global.flash.error") }}', 'danger', 'top');
                         }
                    });
               }

               function initTooltip(el) {
                    $(el).tooltip('show');
               }
          </script>

          <!-- flatpickr -->
          <script> 
               function initializeFlatpickr(id) { 
                    if (document.getElementById(id) && !document.getElementById(id)._flatpickr) {
                         flatpickr("#" + id, {
                              dateFormat: '{{ config('panel.date_format') }}',
                              allowInput: true
                         });
                    }
               } 
               function initializeHijriDatePicker(id) {
                    if (document.getElementById(id)) {
                         $("#" + id).hijriDatePicker({
                              locale: "ar-sa",
                              format: "DD-MM-YYYY",
                              hijriFormat: "iDD/iMM/iYYYY",
                              dayViewHeaderFormat: "MMMM YYYY",
                              hijriDayViewHeaderFormat: "iMMMM iYYYY",
                              showSwitcher: false,
                              allowInputToggle: true,
                              showTodayButton: false,
                              useCurrent: true,
                              isRTL: false, 
                              keepOpen: false,
                              hijri: true, 
                              showClear: true,
                              showTodayButton: true,
                              showClose: true,
                         });
                    }
               }
          </script>

          <!-- show toast -->
          <script>
               // Check for session flash messages and display them
               @if (session('successMessage'))
                    showToast('{{ session('successMessage') }}', 'success', '');
               @endif

               @if (session('errorMessage'))
                    showToast('{{ session('errorMessage') }}', 'error', '');
               @endif

               @if (session('warningMessage'))
                    showToast('{{ session('warningMessage') }}', 'warning', '');
               @endif

               @if (session('infoMessage'))
                    showToast('{{ session('infoMessage') }}', 'info', '');
               @endif
               function showToast(message, type = 'success', position = 'top') {
                    backgroundColor = 'var(--primary-color)';
                    if (type === 'error') {
                         backgroundColor = 'red';
                    }else if (type === 'warning') {
                         backgroundColor = 'yellow';
                    }else if (type === 'info') {
                         backgroundColor = 'blue';
                    }else if (type === 'success') {
                         backgroundColor = 'green';
                    }
                    Toastify({
                         text: message,
                         duration: 3000,
                         newWindow: true,
                         close: true,
                         gravity: position, 
                         backgroundColor: backgroundColor, 
                    }).showToast();
               }
          </script>

          <!-- modal ajax submit -->
          <script>
               function modalAjaxSubmit(e, modalId = 'ajaxModal') {
                    if (e) {
                         e.preventDefault();
                    }
                    
                    var form = $('#' + modalId + ' form'); 
                    var url = form.attr('action');
                    var formData = new FormData(form[0]);
                    var method = form.attr('method');
                    
                    var submitBtn = form.find('button[type="submit"]');
                    var originalBtnHtml = submitBtn.html();
                    submitBtn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                    submitBtn.prop('disabled', true);

                    // Clear previous validation errors
                    form.find('.is-invalid').removeClass('is-invalid');
                    form.find('.invalid-feedback').remove();
                    
                    $.ajax({
                         url: url,
                         type: method,
                         data: formData,
                         processData: false,
                         contentType: false,
                         success: function(response){
                              // If response contains html property, update the specified wrapper
                              if(response.html) {
                                   if(response.wrapper) {
                                        $(response.wrapper).html(response.html);
                                   }else if(response.append){
                                        $(response.append).append(response.html);
                                   }else if(response.replace){
                                        $(response.replace).replaceWith(response.html);
                                   }else if(response.reload) {
                                        location.reload();
                                   }
                              }
                              
                              $('#' + modalId).modal('hide');
                              submitBtn.html(originalBtnHtml);
                              submitBtn.prop('disabled', false);
                              
                              // Show success message if provided
                              if(response.message) {
                                   showToast(response.message, 'success', 'top');
                              }
                              
                              // Reset the form
                              form[0].reset();
                              // Clear any select2 fields
                              form.find('select.select2').val(null).trigger('change');
                              // Clear any file inputs
                              form.find('input[type="file"]').val('');
                              // Clear any filepond instances
                              form.find('.filepond--root').each(function() {
                                   if (this.pond) {
                                        this.pond.removeFiles();
                                   }
                              });
                         },
                         error: function(xhr, status, error){
                              if (xhr.status === 422) {
                                   // Handle validation errors
                                   var errors = xhr.responseJSON.errors;
                                   
                                   $.each(errors, function(field, messages) {
                                        var input = form.find('[name="' + field + '"]');
                                        input.addClass('is-invalid');
                                        
                                        // Add error message
                                        var errorHtml = '<div class="invalid-feedback">' + messages[0] + '</div>';
                                        input.after(errorHtml);
                                   });
                                   
                                   // Show general error message
                                   if(xhr.responseJSON.message) {
                                        showToast(xhr.responseJSON.message, 'error', 'top');
                                   }
                              } else {
                                   // Handle other errors
                                   console.log(xhr.responseText);
                                   showToast('An error occurred. Please try again.', 'error', 'top');
                              }
                              submitBtn.html(originalBtnHtml);
                              submitBtn.prop('disabled', false);
                         }
                    });
               }

               function ajaxDeleteFromTable(url, recordId, trId = null) {
                    Swal.fire({
                         title: '{{ trans('global.areYouSure') }}', 
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#d33',
                         cancelButtonColor: '#3085d6',
                         cancelButtonText: '{{ trans('global.no') }}',
                         confirmButtonText: '{{ trans('global.yes') }}',
                    }).then((result) => {
                         if (result.isConfirmed) { 
                              var method = 'POST';
                              var formData = new FormData();
                              formData.append('_token', '{{ csrf_token() }}');
                              formData.append('id', recordId);

                              $.ajax({
                              url: url,
                              type: method,
                              data: formData,
                              processData: false,
                              contentType: false,
                              success: function(response) {
                                   if(trId) {
                                        $('#' + trId).fadeOut(400, function() {
                                             $(this).remove();
                                        });
                                   }
                                   if (response.message) {
                                        showToast(response.message, 'success', 'top');
                                   }
                              },
                              error: function(xhr, status, error) {
                                   showToast('An error occurred. Please try again.', 'error', 'top');
                              }
                              });
                         }
                    });
               }
          </script>

          <!-- filepond -->
          <script>
               
               FilePond.registerPlugin(
                    FilePondPluginImagePreview,
                    FilePondPluginFileValidateSize,
                    FilePondPluginFileValidateType
               );

               function initFilePond(id, url, name, preview = null, file_name = null, check = false) {  
                    var element = document.getElementById(id + '-filepond');
                    var form = element.closest('form');
                    if (check){  
                         if (window['pond' + id]) return;
                    }
                    window['pond' + id] = FilePond.create(
                         element, {
                              labelIdle: 'Drag & Drop your picture or <span class="filepond--label-action">Browse</span>',
                              imagePreviewHeight: 170,
                              stylePanelLayout: 'compact circle',
                              styleLoadIndicatorPosition: 'center bottom',
                              styleButtonRemoveItemPosition: 'center bottom',

                              // Disable all image transformations to preserve original file
                              allowImageTransform: false,
                              allowImageResize: false,
                              allowImageCrop: false,
                              allowImageEdit: false,

                              acceptedFileTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'],
                              maxFileSize: '5MB',
                              maxFiles: 1,
                              allowRevert: true,
                              allowRemove: true,
                              allowImagePreview: true,

                              labelFileTypeNotAllowed: 'File of invalid type. Only JPEG, PNG, GIF are allowed.',
                              fileValidateTypeLabelExpectedTypes: 'Expects {allButLastType} or {lastType}',
                              labelMaxFileSizeExceeded: 'File is too large',
                              labelMaxFileSize: 'Maximum file size is 5MB',

                              files: preview ? [ 
                                   {
                                        source: preview,
                                        options: {
                                             type: 'local',
                                        }
                                   } 
                              ] : [],

                              // Server configuration for AJAX upload
                              server: {
                                   process: (fieldName, file, metadata, load, error, progress, abort) => {
                                        var formData = new FormData();

                                        // Upload the original file as-is without any modifications
                                        formData.append('file', file, file.name);
                                        formData.append('size', 5);
                                        formData.append('width', 4096);
                                        formData.append('height', 4096);

                                        var request = new XMLHttpRequest();
                                        request.open('POST', url);
                                        request.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                                        request.upload.onprogress = (e) => {
                                             progress(e.lengthComputable, e.loaded, e.total);
                                        };

                                        request.onload = function() {
                                             if (request.status >= 200 && request.status < 300) {
                                                  var response = JSON.parse(request.responseText);

                                                  // Remove existing hidden input if any
                                                  var existingInput = form.querySelector(
                                                       `input[name="${name}"]`);
                                                  if (existingInput) {
                                                       existingInput.remove();
                                                  }

                                                  // Create new hidden input with response data
                                                  var input = document.createElement('input');
                                                  input.type = 'hidden';
                                                  input.name = name;
                                                  input.value = response.name;
                                                  form.appendChild(input);

                                                  load(response.name);
                                             } else {
                                                  error('Upload failed');
                                                  console.log(request.responseText);
                                             }
                                        };

                                        request.onerror = function() {
                                             error('Upload failed');
                                        };

                                        request.send(formData);

                                        return {
                                             abort: () => {
                                                  request.abort();
                                                  abort();
                                             }
                                        };
                                   },
                                   revert: (uniqueFileId, load, error) => {
                                        var input = form.querySelector(
                                             `input[name="${name}"]`);
                                        if (input) {
                                             input.remove();
                                        }
                                        load();
                                   },
                                   load: (source, load) => {
                                        // source here comes from the files array, see my first post
                                        fetch(source).then(res => res.blob()).then(load);
                                   },
                              },
                              oninit: () => {
                                   if(preview && file_name) {
                                        var input = document.createElement('input');
                                        input.type = 'hidden';
                                        input.name = name;
                                        input.value = file_name ?? '';
                                        form.appendChild(input);
                                   }
                              }
                         },
                    );
               }
          </script> 

          <!-- delete record -->
          <script>
               function deleteRecord(url, recordId) { 
                    Swal.fire({
                         title: '{{ trans('global.areYouSure') }}', 
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#d33',
                         cancelButtonColor: '#3085d6',
                         cancelButtonText: '{{ trans('global.no') }}',
                         confirmButtonText: '{{ trans('global.yes') }}',
                    }).then((result) => {
                         if (result.isConfirmed) { 
                              var method = 'POST';
                              var formData = new FormData();
                              formData.append('_token', '{{ csrf_token() }}');
                              formData.append('_method', 'DELETE'); 

                              $.ajax({
                                   url: url,
                                   type: method,
                                   data: formData,
                                   processData: false,
                                   contentType: false,
                                   success: function(response) { 
                                        localStorage.setItem('toastMessage', '{{ trans('global.flash.deleted', ['title' => '']) }}');
                                        localStorage.setItem('toastType', 'success');
                                        location.reload();
                                   },
                                   error: function(xhr, status, error) {
                                        showToast('An error occurred. Please try again.', 'error', 'top');
                                   }
                              });
                         }
                    });
               }
          </script>

          @yield('scripts')
          @stack('stack-scripts')