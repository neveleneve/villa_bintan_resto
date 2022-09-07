<div class="row pt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <ul class="nav nav-pills nav-fill flex-column flex-sm-row" id="tabs-text" role="tablist">
                    <li class="nav-item">
                        <a {{ Request::is('dashboard') ? null : 'href=' . route('home') }}
                            class="nav-link {{ Request::is('dashboard') ? 'active' : null }}">
                            <i class="fas fa-tachometer-alt mr-1"></i>
                            Dashboard
                        </a>
                    </li>
                    @if (Auth::user()->role == 0)
                        <li class="nav-item">
                            <a {{ Request::is('reservations*') || Request::is('reservation*') ? null : 'href=' . route('adminreservation') }}
                                class="nav-link mb-sm-3 mb-md-0 {{ Request::is('reservations*') || Request::is('reservation*') ? 'active' : null }}">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Reservations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a {{ Request::is('payments*') || Request::is('payment*') ? null : 'href=' . route('adminpayments') }}
                                class="nav-link mb-sm-3 mb-md-0 {{ Request::is('payments*') || Request::is('payment*') ? 'active' : null }}">
                                <i class="fas fa-file-invoice-dollar mr-1"></i>
                                Payments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a {{ Request::is('menus*') || Request::is('menu*') ? null : 'href=' . route('adminmenus') }}
                                class="nav-link mb-sm-3 mb-md-0 {{ Request::is('menus*') || Request::is('menu*') ? 'active' : null }}">
                                <i class="fas fa-clipboard-list mr-1"></i>
                                Menus
                            </a>
                        </li>
                        <li class="nav-item">
                            <a {{ Request::is('categories*') ? null : 'href=' . route('admincategories') }}
                                class="nav-link mb-sm-3 mb-md-0 {{ Request::is('categories*') ? 'active' : null }}">
                                <i class="fas fa-utensils mr-1"></i>
                                Categories
                            </a>
                        </li>
                        <li class="nav-item">
                            <a {{ Request::is('report*') ? null : 'href=' . route('adminreport') }}
                                class="nav-link mb-sm-3 mb-md-0 {{ Request::is('report*') ? 'active' : null }}">
                                <i class="fas fa-book mr-1"></i>
                                Report
                            </a>
                        </li>
                    @else
                        <li class="nav-item">
                            <a {{ Request::is('reservations-list*') || Request::is('reservation-list*') ? null : 'href=' . route('reservationlist') }}
                                class="nav-link mb-sm-3 mb-md-0 {{ Request::is('reservations*') || Request::is('reservation*') ? 'active' : null }}">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Reservations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a {{ Request::is('payments*') || Request::is('payment*') ? null : 'href=' . route('paymentslist') }}
                                class="nav-link mb-sm-3 mb-md-0 {{ Request::is('payments*') || Request::is('payment*') ? 'active' : null }}">
                                <i class="fas fa-file-invoice-dollar mr-1"></i>
                                Payments
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>
</div>
