@extends('layouts.tabler')

@section('content')
<div class="page-body">
    @if($customers->isEmpty())
    <div class="empty">
        <div class="empty-icon">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mood-happy" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M9 9l.01 0" /><path d="M15 9l.01 0" /><path d="M8 13a4 4 0 1 0 8 0h-8" /></svg>
        </div>
        <p class="empty-title">No customers with due or points found</p>
    </div>
    @else
    <div class="container-xl">
        <div class="card">
            <div class="card-header">
                <div>
                    <h3 class="card-title">
                        {{ __('Customer Due & Points') }}
                    </h3>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered card-table table-vcenter text-nowrap datatable">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" class="text-center">{{ __('No.') }}</th>
                            <th scope="col" class="text-center">{{ __('Customer') }}</th>
                            <th scope="col" class="text-center">{{ __('Phone') }}</th>
                            <th scope="col" class="text-center">{{ __('Points') }}</th>
                            <th scope="col" class="text-center">{{ __('Total Due') }}</th>
                            <th scope="col" class="text-center">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($customers as $customer)
                        <tr>
                            <td class="text-center">{{ $loop->iteration }}</td>
                            <td class="text-center">{{ $customer->name }}</td>
                            <td class="text-center">{{ $customer->phone }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $customer->points }} pts</span>
                            </td>
                            <td class="text-center">
                                @if($customer->total_due > 0)
                                    <span class="badge bg-danger">
                                        {{ Number::currency($customer->total_due, 'USD') }}
                                    </span>
                                @else
                                    <span class="badge bg-success">$0.00</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('customers.due.edit', $customer->uuid) }}" class="btn btn-icon btn-outline-warning">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pencil" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" /><path d="M13.5 6.5l4 4" /></svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
