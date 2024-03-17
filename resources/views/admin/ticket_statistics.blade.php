@extends('admin.layouts.app')

@section('panel')
    @can('admin.ticket.statistic.widget')
        <div class="row gy-4 mt-2">
            <div class="col-xxl-3 col-lg-4 col-sm-6 new-tikcets-widget">
                <x-widget style="5" link="admin.new.tickets" icon="las la-ticket-alt" title="New Tickets" value=""
                    bg="primary" />
            </div><!-- dashboard-w1 end -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 clients-replies-widget">
                <x-widget style="5" link="admin.clients.replies" icon="las la-user" title="Clients Replies" value=""
                    bg="1" />
            </div><!-- dashboard-w1 end -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 staff-replies-widget">
                <x-widget style="5" link="admin.staff.replies" icon="las la-user-tie" title="Staff Replies" value=""
                    bg="2" />
            </div><!-- dashboard-w1 end -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 ticket-without-replies">
                <x-widget style="5" link="admin.without.replies" icon="las la-reply" title="Tickets Withouts Reply"
                    value="" bg="3" />
            </div><!-- dashboard-w1 end -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 average-first-response">
                <x-widget style="4"  title="Average First Response" value="Hours" bg="3" />
            </div><!-- dashboard-w1 end -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 total-tickets">
                <x-widget style="4"  title="Total tickets" value="" bg="1" />
            </div><!-- dashboard-w1 end -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 total-staffs">
                <x-widget style="4"  title="Total Staffs" value="" bg="7" />
            </div><!-- dashboard-w1 end -->
            <div class="col-xxl-3 col-lg-4 col-sm-6 total-departments">
                <x-widget style="4"  title="Total Department" value="" bg="2" />
            </div><!-- dashboard-w1 end -->
        </div>
    @endcan

    <!-- chart -->

    <div class="row mt-2 gy-4">
        @can('admin.first.response.chart')
            <div class="col-lg-6 ">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="mb-3">@lang('Average First Reply Time')</h4>
                        <div class="ms-auto">
                            <select class="form--control form-select time-range-avg-first-res">
                                <option value="">@lang('-Select One-')</option>
                                <option value="day">@lang('Last Day')</option>
                                <option value="week">@lang('Last Week')</option>
                                <option selected value="month">@lang('Last Month')</option>
                                <option value="year">@lang('Last Year')</option>
                                <option value="all">@lang('All')</option>
                            </select>
                        </div>
                    </div>
                    <div class="fast-avg-response"></div>
                </div>
            </div>
        @endcan

        @can('admin.submitted.hours.chart')
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="mb-3">@lang('Tickets Submitted By Hours')</h4>
                        <div class="ms-auto">
                            <select class="form--control form-select time-range-submitted-by-hours">
                                <option value="">@lang('-Select One-')</option>
                                <option value="day">@lang('Last Day')</option>
                                <option value="week">@lang('Last Week')</option>
                                <option selected value="month">@lang('Last Month')</option>
                                <option value="year">@lang('Last Year')</option>
                                <option value="all">@lang('All')</option>
                            </select>
                        </div>
                    </div>
                    <div class="ticket_submitted_by_hour mx-3"></div>
                </div>
            </div>
        @endcan

        @can('admin.department.wise.tickets.chart')
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="mb-3">@lang('Department Wise Ticket')</h4>
                        <div class="ms-auto">
                            <select class="form--control form-select time-range-department-wise-ticket">
                                <option value="">@lang('-Select One-')</option>
                                <option value="day">@lang('Last Day')</option>
                                <option value="week">@lang('Last Week')</option>
                                <option selected value="month">@lang('Last Month')</option>
                                <option value="year">@lang('Last Year')</option>
                                <option value="all">@lang('All')</option>
                            </select>
                        </div>
                    </div>
                    <div class="department_wise_ticket mx-3"></div>
                </div>
            </div>
        @endcan

        @can('admin.first.reply.staff.chart')
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="mb-3">@lang('Frist Replyl By Staff')</h4>
                        <div class="ms-auto">
                            <select class="form--control form-select time-range-first-reply-by-staff">
                                <option value="">@lang('-Select One-')</option>
                                <option value="day">@lang('Last Day')</option>
                                <option value="week">@lang('Last Week')</option>
                                <option selected value="month">@lang('Last Month')</option>
                                <option value="year">@lang('Last Year')</option>
                                <option value="all">@lang('All')</option>
                            </select>
                        </div>
                    </div>
                    <div class="first_reply_by_staff mx-3"></div>
                </div>
            </div>
        @endcan
    </div>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/js/vendor/chart.js.2.8.0.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/apexcharts.min.js') }}"></script>
    <script>
        "use strict";

        @if (can('admin.first.response.chart'))
            function avgResponseChart(lables, dataLabels) {
                var colors = ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#48FF17'];
                var options = {
                    series: dataLabels,
                    chart: {
                        width: 500,
                        type: 'pie',
                    },
                    labels: lables,
                    colors: colors,
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 300
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                if (window.first_response_tickeet) {
                    window.first_response_tickeet.destroy();
                }
                window.first_response_tickeet = new ApexCharts(document.querySelector(".fast-avg-response"), options);
                window.first_response_tickeet.render();

            }
        @endif

        @can('admin.submitted.hours.chart')
            function ticketSubmittedByHours(ticketSubmittedByHour, ticketSubmittedByHoursPercentage) {
                var options = {
                    series: [{
                        name: 'Parcent',
                        data: ticketSubmittedByHoursPercentage
                    }],
                    chart: {
                        type: 'bar',
                        height: 350
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 4,
                            horizontal: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: ticketSubmittedByHour
                    }
                };

                if (window.tikcet_by_hour_charge) {
                    window.tikcet_by_hour_charge.destroy();
                }
                window.tikcet_by_hour_charge = new ApexCharts(document.querySelector(".ticket_submitted_by_hour"), options);
                window.tikcet_by_hour_charge.render();

            }
        @endcan

        @can('admin.department.wise.tickets.chart')
            function departmentChart(departmentName, departmentPercentage) {
                var options = {
                    series: [{
                        name: 'Ticket Percentage',
                        data: departmentPercentage,
                    }],
                    chart: {
                        height: 350,
                        type: 'bar',
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 10,
                            dataLabels: {
                                position: 'top',
                            },
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return val + "%";
                        },
                        offsetY: -20,
                        style: {
                            fontSize: '12px',
                            colors: ["#304758"]
                        }
                    },
                    xaxis: {
                        categories: departmentName,
                        position: 'top',
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        crosshairs: {
                            fill: {
                                type: 'gradient',
                                gradient: {
                                    colorFrom: '#D8E3F0',
                                    colorTo: '#BED1E6',
                                    stops: [0, 100],
                                    opacityFrom: 0.4,
                                    opacityTo: 0.5,
                                }
                            }
                        },
                        tooltip: {
                            enabled: true,
                        }
                    },
                    yaxis: {
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false,
                        },
                        labels: {
                            show: false,
                            formatter: function(val) {
                                return val + "%";
                            }
                        }
                    },
                    title: {
                        text: 'Department-wise Ticket Percentage',
                        floating: true,
                        offsetY: 330,
                        align: 'center',
                        style: {
                            color: '#444'
                        }
                    }
                };

                if (window.department_wise_chart) {
                    window.department_wise_chart.destroy();
                }
                window.department_wise_chart = new ApexCharts(document.querySelector(".department_wise_ticket"), options);
                window.department_wise_chart.render();
            }
        @endcan

        @can('admin.first.reply.staff.chart')
            function fristReplyByStaff(firstReplyStaffName, staffFirstReplyAvgTime) {
                var colors = ['#008FFB', '#00E396', '#FEB019', '#FF4560', '#775DD0', '#546E7A', '#26a69a', '#D10CE8'];

                var options = {
                    series: [{
                        name: 'Frist reply avg hours',
                        data: staffFirstReplyAvgTime
                    }],
                    chart: {
                        height: 350,
                        type: 'bar',
                        events: {
                            click: function(chart, w, e) {}
                        }
                    },
                    colors: colors,
                    plotOptions: {
                        bar: {
                            columnWidth: '45%',
                            distributed: true,
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    legend: {
                        show: false
                    },
                    xaxis: {
                        categories: firstReplyStaffName,
                        labels: {
                            style: {
                                colors: colors,
                                fontSize: '12px'
                            }
                        }
                    }
                };


                if (window.first_reply_by_admin) {
                    window.first_reply_by_admin.destroy();
                }
                window.first_reply_by_admin = new ApexCharts(document.querySelector(".first_reply_by_staff"), options);
                window.first_reply_by_admin.render();
            }
        @endcan



        @can('admin.ticket.statistic.widget')
            $('.time-range').change(function() {
                var selectedOption = $(this).children("option:selected").val();
                var requestData = {
                    time_range: selectedOption
                };
                $.ajax({
                    method: 'GET',
                    url: '{{ route('admin.ticket.statistic.widget') }}',
                    data: requestData,
                    success: function(response) {
                        let widget = response.widget;
                        $('.new-tikcets-widget').find('.widget-six__number').html(widget.new_tickets);
                        $('.clients-replies-widget').find('.widget-six__number').html(widget
                            .clients_replies);
                        $('.staff-replies-widget').find('.widget-six__number').html(widget
                            .staff_replies);
                        $('.ticket-without-replies').find('.widget-six__number').html(widget
                            .ticket_without_reply);
                        var fristTimeResponse = widget.first_response_times !== undefined ? widget
                            .first_response_times : 0;
                        $('.average-first-response h2').html(fristTimeResponse + " Hours");
                        $('.total-tickets h2').html(widget.total_tickets);
                        $('.total-staffs h2').html(widget.total_staffs);
                        $('.total-departments h2').html(widget.total_departments);
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }).change();
        @endcan

        @can('admin.first.response.chart')
            $('.time-range-avg-first-res').change(function() {
                var selectedOption = $(this).children("option:selected").val();
                var requestData = {
                    time_range: selectedOption
                };
                $.ajax({
                    method: 'GET',
                    url: '{{ route('admin.first.response.chart') }}',
                    data: requestData,
                    success: function(response) {
                        let firstResponseTime = response.firstResponseDayAvgHours;
                        if (Object.keys(firstResponseTime)) {
                            avgResponseChart(Object.keys(firstResponseTime), Object.values(
                                firstResponseTime));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }).change();
        @endcan

        @can('admin.submitted.hours.chart')
            $('.time-range-submitted-by-hours').change(function() {
                var selectedOption = $(this).children("option:selected").val();
                var requestData = {
                    time_range: selectedOption
                };
                $.ajax({
                    method: 'GET',
                    url: '{{ route('admin.submitted.hours.chart') }}',
                    data: requestData,
                    success: function(response) {
                        let ticketSubmittedByHour = response.ticketSubmittedByHours;
                        let ticketSubmittedByHoursPercentage = response
                            .ticketSubmittedByHoursPercentage;


                        if (ticketSubmittedByHour && ticketSubmittedByHoursPercentage) {
                            ticketSubmittedByHours(ticketSubmittedByHour,
                                ticketSubmittedByHoursPercentage)
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }).change();
        @endcan


        @can('admin.department.wise.tickets.chart')
            $('.time-range-department-wise-ticket').change(function() {
                var selectedOption = $(this).children("option:selected").val();
                var requestData = {
                    time_range: selectedOption
                };
                $.ajax({
                    method: 'GET',
                    url: '{{ route('admin.department.wise.tickets.chart') }}',
                    data: requestData,
                    success: function(response) {
                        let departmentName = response.departmentName;
                        let departmentParcentage = response.departmentTicketPercentage;

                        if (departmentName && departmentParcentage) {
                            departmentChart(departmentName, departmentParcentage)
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }).change();
        @endcan

        @can('admin.first.reply.staff.chart')
            $('.time-range-first-reply-by-staff').change(function() {
                var selectedOption = $(this).children("option:selected").val();
                var requestData = {
                    time_range: selectedOption
                };
                $.ajax({
                    method: 'GET',
                    url: '{{ route('admin.first.reply.staff.chart') }}',
                    data: requestData,
                    success: function(response) {
                        let firstReplyStaffName = response.firstReplyStaffName;
                        let staffFirstReplyAvgTime = response.staffFirstReplyAvgTime;

                        if (firstReplyStaffName && staffFirstReplyAvgTime) {
                            fristReplyByStaff(firstReplyStaffName, staffFirstReplyAvgTime)
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }).change();
        @endcan
    </script>
@endpush

@push('breadcrumb-plugins')
    <select class="form--control form-select time-range">
        <option value="">@lang('-Select One-')</option>
        <option value="day">@lang('Last Day')</option>
        <option value="week">@lang('Last Week')</option>
        <option selected value="month">@lang('Last Month')</option>
        <option value="year">@lang('Last Year')</option>
        <option value="all">@lang('All')</option>
    </select>
@endpush
