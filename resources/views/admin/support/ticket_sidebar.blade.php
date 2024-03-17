     <div class="sidebar-menu-wrapper">
         <div class="sidebar-menu-inner">
             <div class="sidebar-item">
                 <div class="d-xl-none d-block">
                     <p class="sidebar-close-icon">
                         <i class="fas fa-times"></i>
                     </p>
                 </div>
                 <div class="sidebar-profile">
                     <h6 class="title">@lang('Owner')</h6>
                     <p>{{ __($ticket->user_name) }}</p>
                     <p>{{ __($ticket->user_email) }}</p>
                     <span class="badge bg--success">@lang('Owner')</span>
                 </div>
                 @can('admin.ticket.department.change')
                     <div class="form-group">
                         <label class="form--label">@lang('Department')</label>
                         <select class="form--control form-select department">
                             <option value="">@lang('-Set Department-')</option>
                             @foreach ($departments as $department)
                                 <option value="{{ $department->id }}"
                                     {{ $ticket->department_id == $department->id ? 'selected' : '' }}>
                                     {{ __($department->name) }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                 @endcan

                 @can('admin.ticket.assigned')
                     <div class="form-group">
                         <label class="form--label"> @lang('Assigned To') </label>
                         <select class="form--control form-select mt-2 assignd">
                             <option value="0">@lang('None')</option>
                             @foreach ($ticketDepartment->staffs as $staff)
                                 <option value="{{ $staff->id }}" @selected($ticket->assigned_admin_id == $staff->id)> {{ __($staff->name) }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                 @endcan

                 @can('admin.ticket.priority.change')
                     <div class="form-group">
                         <label class="form--label">@lang('Priority')</label>
                         <select class="form--control form-select priority">
                             <option value="">@lang('-Set Priority-')</option>
                             @foreach ($priorities as $priority)
                                 <option value="{{ $priority->id }}" @selected($ticket->ticket_priority_id == $priority->id)>
                                     {{ __($priority->title) }}
                                 </option>
                             @endforeach
                         </select>
                     </div>
                 @endcan
             </div>
             <div class="sidebar-item">
                 <div class="item">
                     @php
                         $staffNames = $ticket->replies->pluck('admin_name')->unique() ?? [];
                     @endphp
                     <h6 class="title">@lang('Staff Participant')</h6>
                     @foreach ($staffNames as $name)
                         <p class="text">{{ __(keyToTitle($name)) }}</p>
                     @endforeach

                 </div>
                 <div class="item">
                     <h6 class="title">@lang('Ticket Watches')</h6>
                     <p class="text">@lang('None')</p>
                 </div>
                 <div class="item">
                     <h6 class="title">@lang('CC Recipient')</h6>
                     <p class="text">@lang('None')</p>
                 </div>
             </div>
             <div class="sidebar-item">
                 <form method="POST" action="{{ route('admin.ticket.number.filter') }}">
                     @csrf
                     <div class="form-group">
                         <label for="ad" class="form--label">@lang('Ticket Number')</label>
                         <input type="number" name="ticket_number" class="form--control" id="ad">
                     </div>
                     @can('admin.ticket.number.filter')
                         <div class="form-group">
                             <button type="submit" class="btn btn--base w-100">@lang('Filter')</button>
                         </div>
                     @endcan
                 </form>
             </div>
         </div>
     </div>

     @push('style-lib')
         <link rel="stylesheet" href="{{ asset('assets/admin/css/ticket.css') }}">
     @endpush

     @push('script')
         <script>
             "use strict";
             (function($) {
                 var $ticket = @json($ticket);
                 $('.department').on('change', function() {
                     var department_id = $(this).val();
                     $.ajax({
                         type: 'POST',
                         url: '{{ route('admin.ticket.department.change') }}',
                         data: {
                             '_token': '{{ csrf_token() }}',
                             'department_id': department_id,
                             'ticket_id': $ticket.id,
                         },
                         success: function(data, status) {
                             notify('success', 'Department changed successfully');
                         }
                     });
                 });
                 $('.priority').on('change', function() {
                     var ticket_priority_id = $(this).val();
                     $.ajax({
                         type: 'POST',
                         url: '{{ route('admin.ticket.priority.change') }}',
                         data: {
                             '_token': '{{ csrf_token() }}',
                             'ticket_id': $ticket.id,
                             'ticket_priority_id': ticket_priority_id,
                         },
                         success: function(data, status) {
                             notify('success', 'Priority changed successfully');
                         }
                     });
                 });
                 $('.assignd').on('change', function() {
                     var staff_id = $(this).val();
                     $.ajax({
                         type: 'POST',
                         url: '{{ route('admin.ticket.assigned') }}',
                         data: {
                             '_token': '{{ csrf_token() }}',
                             'ticket_id': $ticket.id,
                             'staff_id': staff_id,
                         },
                         success: function(data, status) {
                             notify('success', 'Ticket assigned successfully!');
                         }
                     });
                 });
                 $('.bar-icon').on('click', function() {
                     $('.sidebar-menu-wrapper').addClass("show-sidebar");
                     $('.body-overlay').addClass("show-overlay");
                 })
                 $('.sidebar-close-icon, .body-overlay').on('click', function() {
                     $('.sidebar-menu-wrapper').removeClass("show-sidebar");
                     $('.body-overlay').removeClass("show-overlay");
                 })

             })(jQuery);
         </script>
     @endpush
