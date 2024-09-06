<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="../">
    <meta charset="utf-8">
    <meta name="author" content="Softnio">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="base-url" content="">
    <meta name="crsf-token" content="{{ csrf_token() }}">
    <!-- Fav Icon  -->
    <link rel="icon" sizes="16x16" href="{{ asset('icons/fav.png') }}" type="image/png">
    <!-- Page Title  -->
    <title>Marianatek | Admin Dashboard</title>
    <!-- StyleSheets  -->
    <link rel="stylesheet" href="{{ asset('admin-theme/assets/css/dashlite.css?ver=3.1.2?dfgfg') }}">
    <link id="skin-default" rel="stylesheet" href="{{ asset('admin-theme/assets/css/theme.css?ver=3.1.2') }}">

    <!-- jQuery -->
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Bootstrap Link  -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- DataTables CSS and JS CDN -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" ></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- jQuery UI CSS -->
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
    
    <!-- jQuery UI JS -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    @yield('css')

    <style>
        #overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loader {
            height: 50vh;
            width: 50vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .spinner {
            height: 50px;
            width: 50px;
            border: 4px solid rgba(255, 255, 255, 0.4);
            border-top-color: rgba(220, 40, 138, 1);
            border-radius: 50%;
            animation: spin 1s infinite linear;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>

</head>

<body class="nk-body bg-lighter npc-general has-sidebar ">
    <div style="display: none;" id="overlay">
        <div class="loader">
            <div class="spinner"></div>
        </div>
    </div>
    <div class="nk-app-root">
        <!-- main @s -->
        <div class="nk-main ">
            <!-- sidebar @s -->
            <div class="nk-sidebar nk-sidebar-fixed is-dark left-div" data-content="sidebarMenu">
                <div class="nk-sidebar-element nk-sidebar-head">
                    <div class="nk-menu-trigger">
                        <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em class="icon ni ni-arrow-left"></em></a>
                        <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"><em class="icon ni ni-menu"></em></a>
                    </div>
                    <div class="nk-sidebar-brand">
                        <!-- <a href="html/index.html" class="logo-link nk-sidebar-logo"> -->
                            <img class="logo-light logo-img" src="{{ asset('assets/images/logo-icon.png') }}" srcset="{{ asset('assets/images/logo-icon.png') }}" alt="logo">
                            <!--<img class="logo-dark logo-img" src="./images/logo-dark.png" srcset="./images/logo-dark2x.png 2x" alt="logo-dark"> -->
                            <h4 class="text-light">Kickboxing Club Fitness</h4>
                        <!-- </a> -->
                    </div>
                </div><!-- .nk-sidebar-element -->
                <div class="nk-sidebar-element nk-sidebar-body">
                    <div class="nk-sidebar-content">
                        <div class="nk-sidebar-menu" data-simplebar>
                            <ul class="nk-menu main-ul">
                            @if (Auth::user()->hasPermission('7')) 
                                <li class="nk-menu-heading menu-li">
                                    <a href="{{ url('/admin-dashboard') }}"><h6 class="overline-title text-primary-alt">Dashboard</h6></a>
                                </li>
                            @endif
                                <!-- <li class="nk-menu-item has-sub">
                                    <a href="{{ url('/admin-dashboard/total-sales') }}" class="nk-menu-link">
                                        <span class="nk-menu-icon"><i class="fa-solid fa-chart-bar"></i></span>
                                        <span class="nk-menu-text">Sales Stats</span>
                                    </a>
                                </li> -->
                                <li class="nk-menu-item has-sub main-li">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><i class="fa-solid fa-location-dot"></i></span>
                                        <span class="nk-menu-text">Locations</span>
                                    </a>
                                    <ul class="nk-menu-sub ul-sub">
                                        <li class="nk-menu-item li-sub">
                                            <a href="{{ url('/admin-dashboard/locations') }}" class="nk-menu-link"><span class="nk-menu-text">List</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @if (Auth::user()->hasPermission('1')) 
                                    <li class="nk-menu-item has-sub main-li">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><i class="fa-solid fa-ribbon"></i></span>
                                            <span class="nk-menu-text">Memberships</span>
                                        </a>
                                        <ul class="nk-menu-sub ul-sub">
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/memberships') }}" class="nk-menu-link"><span class="nk-menu-text">Memberships</span></a>
                                            </li>
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/memberships-transactions') }}" class="nk-menu-link"><span class="nk-menu-text">Memberships Transactions</span></a>
                                            </li>
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/memberships-instances') }}" class="nk-menu-link"><span class="nk-menu-text">Memberships Instances</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('3'))
                                    <li class="nk-menu-item has-sub main-li">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><i class="fa-solid fa-shopping-cart"></i></span>
                                            <span class="nk-menu-text">Orders</span>
                                        </a>
                                        <ul class="nk-menu-sub ul-sub">
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/orders') }}" class="nk-menu-link"><span class="nk-menu-text">Orders</span></a>
                                            </li>
                                            <!-- <li class="nk-menu-item">
                                                <a href="{{ url('/admin-dashboard/total-sales') }}" class="nk-menu-link"><span class="nk-menu-text">Total Sales</span></a>
                                            </li> -->
                                        </ul>
                                    </li>
                                @endif
                                @if (Auth::user()->hasPermission('4')) 
                                    <li class="nk-menu-item has-sub main-li">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><i class="fa-solid fa-users"></i></span>
                                            <span class="nk-menu-text">All Members</span>
                                        </a>
                                        <ul class="nk-menu-sub ul-sub">
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/users') }}" class="nk-menu-link"><span class="nk-menu-text">All Members</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                <li class="nk-menu-item has-sub main-li">
                                    <a href="#" class="nk-menu-link nk-menu-toggle">
                                        <span class="nk-menu-icon"><i class="fa-solid fa-users"></i></span>
                                        <span class="nk-menu-text">Lead</span>
                                    </a>
                                    <ul class="nk-menu-sub ul-sub">
                                        <!-- <li class="nk-menu-item li-sub">
                                            <a href="{{ url('/admin-dashboard/trials-purchased-not-scheduled') }}" class="nk-menu-link"><span class="nk-menu-text">Trials Purchased and Not Scheduled</span></a>
                                        </li> -->
                                        <li class="nk-menu-item li-sub">
                                            <a href="{{ url('/admin-dashboard/active-trial') }}" class="nk-menu-link"><span class="nk-menu-text">Trials Active and In Trial</span></a>
                                        </li>
                                        <li class="nk-menu-item li-sub">
                                            <a href="{{ url('/admin-dashboard/complete-trials') }}" class="nk-menu-link"><span class="nk-menu-text">Trials Completed and Not Members</span></a>
                                        </li>
                                    </ul>
                                </li>
                                @if (Auth::user()->hasPermission('6')) 
                                    <li class="nk-menu-item has-sub main-li">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><i class="fa-solid fa-briefcase"></i></span>
                                            <span class="nk-menu-text">Employees</span>
                                        </a>
                                        <ul class="nk-menu-sub ul-sub"> 
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/employees') }}" class="nk-menu-link"><span class="nk-menu-text">All employees</span></a>
                                            </li>
                                            <!-- <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/add-pay-rates') }}" class="nk-menu-link"><span class="nk-menu-text">Add Pay Rates</span></a>
                                            </li> -->
                                        </ul>
                                    </li>
                                @endif
                                @if (Auth::user()->hasPermission('5')) 
                                    <li class="nk-menu-item has-sub main-li">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
                                            <span class="nk-menu-text">Billing</span>
                                        </a>
                                        <ul class="nk-menu-sub ul-sub"> 
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/billing-stats') }}" class="nk-menu-link"><span class="nk-menu-text">Billing Stats</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                @if(Auth::user()->hasPermission('2'))
                                    <li class="nk-menu-item has-sub main-li">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><i class="fa-solid fa-money-bill-wave"></i></span>
                                            <span class="nk-menu-text">Payroll</span>
                                        </a>
                                        <ul class="nk-menu-sub ul-sub"> 
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/payroll') }}" class="nk-menu-link"><span class="nk-menu-text">List</span></a>
                                            </li>
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/payroll-stats') }}" class="nk-menu-link"><span class="nk-menu-text">Stats</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                @if(Auth::user()->isEmployee())
                                    <li class="nk-menu-item has-sub main-li">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><i class="fa fa-gear"></i></span>
                                            <span class="nk-menu-text">Membership Sold</span>
                                        </a>
                                        <ul class="nk-menu-sub ul-sub">
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ route('membership.sold') }}" class="nk-menu-link"><span class="nk-menu-text">Membership Sold</span></a>
                                            </li>
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ route('membership.sold.stats') }}" class="nk-menu-link"><span class="nk-menu-text">Membership Sold Stats</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                                @if(Auth::user()->isAdmin())
                                    <li class="nk-menu-item has-sub main-li">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><i class="fa-solid fa-users"></i></span>
                                            <span class="nk-menu-text">Users</span>
                                        </a>
                                        <ul class="nk-menu-sub ul-sub"> 
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/add-user') }}" class="nk-menu-link"><span class="nk-menu-text">Add user</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nk-menu-item has-sub main-li">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><i class="fa-solid fa-users"></i></span>
                                            <span class="nk-menu-text">Memberships tracking</span>
                                        </a>
                                        <ul class="nk-menu-sub ul-sub"> 
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/membership-tracking') }}" class="nk-menu-link"><span class="nk-menu-text">Memberships tracking</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                    <li class="nk-menu-item has-sub main-li">
                                        <a href="#" class="nk-menu-link nk-menu-toggle">
                                            <span class="nk-menu-icon"><i class="fa fa-gear"></i></span>
                                            <span class="nk-menu-text">Settings</span>
                                        </a>
                                        <ul class="nk-menu-sub ul-sub">
                                            <li class="nk-menu-item li-sub">
                                                <a href="{{ url('/admin-dashboard/settings') }}" class="nk-menu-link"><span class="nk-menu-text">Settings</span></a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                         </div>
                    </div>
                </div>
            </div>
            <!-- sidebar @e -->
            <!-- wrap @s -->
            <div class="nk-wrap ">
                <!-- main header @s -->
                <div class="nk-header nk-header-fixed is-light dashboard-head">
                    <div class="container-fluid">
                        <div class="nk-header-wrap">
                            <h3 class="das-heading">Dashboard</h3>
                            <div class="nk-header-news d-none d-xl-block">
                                <div class="nk-news-list">
                                    <a class="nk-news-item" href="#">
                                        <!-- <div class="nk-news-icon">
                                            <em class="icon ni ni-card-view"></em>
                                        </div> -->
                                        <!-- <div class="nk-news-text"> -->
                                            <!-- <p>Do you know the latest update of 2022? <span> A overview of our is now available on YouTube</span></p> -->
                                            <!-- <em class="icon ni ni-external"></em> -->
                                        <!-- </div> -->
                                    </a>
                                </div>
                            </div><!-- .nk-header-news -->
                            <div class="nk-header-tools">
                                <ul class="nk-quick-nav">
                                    <li class="dropdown user-dropdown">
                                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                                            <div class="user-toggle">
                                                <div class="user-avatar sm">
                                                    <img src="{{  asset('icons/fav.png') }}" alt="" srcset="">
                                                </div>
                                                <div class="user-info d-none d-md-block">
                                                    <div class="user-status">Admin</div>
                                                    <div class="user-name dropdown-indicator">Eric</div>
                                                </div>
                                            </div>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1">
                                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                                <div class="user-card">
                                                    <div class="user-avatar">
                                                        <span>ER</span>
                                                    </div>
                                                    <div class="user-info">
                                                        <span class="lead-text">Eric</span>
                                                        <span class="sub-text">{{ auth()->user()->email ?? '' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="html/user-profile-regular.html"><em class="icon ni ni-user-alt"></em><span>View Profile</span></a></li>
                                                    <li><a href="html/user-profile-setting.html"><em class="icon ni ni-setting-alt"></em><span>Account Setting</span></a></li>
                                                    <li><a href="html/user-profile-activity.html"><em class="icon ni ni-activity-alt"></em><span>Login Activity</span></a></li>
                                                    <li><a class="dark-switch" href="#"><em class="icon ni ni-moon"></em><span>Dark Mode</span></a></li>
                                                </ul>
                                            </div> -->
                                            <div class="dropdown-inner">
                                                <ul class="link-list">
                                                    <li><a href="{{ url('/logout') }}"><em class="icon ni ni-signout"></em><span>Sign out</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </li><!-- .dropdown -->
                                </ul><!-- .nk-quick-nav -->
                            </div><!-- .nk-header-tools -->
                        </div><!-- .nk-header-wrap -->
                    </div><!-- .container-fliud -->
                </div>
                @yield('content')
                <div class="nk-footer">
                    <div class="container-fluid">
                        <div class="nk-footer-wrap">
                            <div class="nk-footer-copyright"> &copy;2024 KBXF
                            </div>
                           

                        </div>
                    </div>
                </div>
                <!-- footer @e -->
            </div>
            <!-- wrap @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->

    <!-- JavaScript -->
    <script src="{{ asset('admin-theme/assets/js/bundle.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('admin-theme/assets/js/scripts.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('admin-theme/assets/js/charts/gd-default.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('admin-theme/assets/js/example-toastr.js?ver=3.1.2') }}"></script>
    <script src="{{ asset('assets/js/script.js') }}"></script>

    @yield('js')

    <script>
        $(document).ready(function(){
            $('.dropdown-toggle').click(function(){
                $('.dropdown-menu-s1').toggle();
                disabledEventPropagation(event);
            });

            $('.dropdown-menu-s1').click(function(event) {
                disabledEventPropagation(event);
            });
        })

        $(document).click(function() {
            $(".dropdown-menu-s1").hide();
        });

        function disabledEventPropagation(event) {
            if (event.stopPropagation) {
                event.stopPropagation();
            } else if (window.event) {
                window.event.cancelBubble = true;
            }
        }
    </script>


    @if(Session::get('error'))
    <script>
        toastr.clear();
        NioApp.Toast('{{ Session::get("error") }}', 'error', {position: 'top-right'});
    </script>
    @endif
    @if(Session::get('success'))
    <script>
        toastr.clear();
        NioApp.Toast('{{ Session::get("success") }}', 'info', {position: 'top-right'});
    </script>
    @endif
    
</body>
</html>