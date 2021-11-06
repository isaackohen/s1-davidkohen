<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

@includeWhen(class_exists(\Backpack\DevTools\DevToolsServiceProvider::class), 'backpack.devtools::buttons.sidebar_item')
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('user') }}'><i class='nav-icon la la-question'></i> Users</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('providers') }}'><i class='nav-icon la la-question'></i> Providers</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('promocode') }}'><i class='nav-icon la la-question'></i> Promocodes</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('currency') }}'><i class='nav-icon la la-question'></i> Currencies</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('gameslist') }}'><i class='nav-icon la la-question'></i> Gameslists</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('settings') }}'><i class='nav-icon la la-question'></i> Settings</a></li>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('admin-activity') }}'><i class='nav-icon la la-admin'></i> Admin activities</a></li>
<li class='nav-item'><a class='nav-link' href='{{ backpack_url('log') }}'><i class='nav-icon la la-terminal'></i> Logs</a></li>

<li class='nav-item'><a class='nav-link' href='{{ backpack_url('page') }}'><i class='nav-icon la la-file-o'></i> <span>Pages</span></a></li>