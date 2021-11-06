<div class="container-fluid">

	@if (!app()->environment('local'))
	<nav class="navbar rounded alert-warning justify-content-center mb-3">
		<span class="text-center text-error font-weight-bold mr-3">Do not install DevTools in production. Have your deploy script run <code class="text-primary">composer install --no-dev</code> instead.</span>
	</nav>
	@endif

	<nav class="navbar navbar-expand-lg navbar-dark bg-gray-800 rounded mb-3">
	  <a class="navbar-brand" href="{{ backpack_url('devtools') }}"><i class='nav-icon la la-toolbox text-warning'></i></a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#devToolsNavBar" aria-controls="devToolsNavBar" aria-expanded="false" aria-label="Toggle navigation">
	    <span class="navbar-toggler-icon"></span>
	  </button>

	  <div class="collapse navbar-collapse" id="devToolsNavBar">
	    <ul class="navbar-nav mr-auto mt-2 mt-lg-0">
	      <li class="nav-item">
	        <a class="nav-link text-warning" href="{{ backpack_url('devtools/model') }}">Models <span class="sr-only">(current)</span></a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link text-warning" href="{{ backpack_url('devtools/migration') }}">Migrations <span class="sr-only">(current)</span></a>
	      </li>
	      <li class="nav-item">
	        <a class="nav-link" href="#">CRUDs</a>
	      </li>
        </ul>
	    <div class="my-2 my-lg-0">
	    	<ul class="navbar-nav mr-auto mt-2 mt-lg-0"><li class="nav-item dropdown">
		        <a class="nav-link" href="#" id="newNavbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		          <i class='nav-icon la la-file-medical'></i> Create
		        </a>
		        <div class="dropdown-menu" aria-labelledby="newNavbarDropdown">
		            <div class="dropdown-header"><strong>Page</strong></div>
			    	<a class="dropdown-item disabled" href="#">CRUD</a>
			    	<a class="dropdown-item disabled" href="#">Custom Page</a>
		            <div class="dropdown-header"><strong>Class</strong></div>
			    	<a class="dropdown-item" href="{{ backpack_url('devtools/migration/create') }}">Migration</a>
			    	<a class="dropdown-item" href="{{ backpack_url('devtools/model/create') }}">Model</a>
			    	<a class="dropdown-item disabled" href="#">Request</a>
			    	<a class="dropdown-item disabled" href="#">Controller</a>
			    	<a class="dropdown-item disabled" href="#">CrudController</a>
			    	<a class="dropdown-item disabled" href="#">Operation Trait</a>
		            <div class="dropdown-header"><strong>View</strong></div>
			    	<a class="dropdown-item disabled" href="#">Button</a>
			    	<a class="dropdown-item disabled" href="#">Column</a>
			    	<a class="dropdown-item disabled" href="#">Field</a>
			    	<a class="dropdown-item disabled" href="#">Filter</a>
			    	<a class="dropdown-item disabled" href="#">Widget</a>
		        </div>
		      </li>
		      <li class="nav-item dropdown">
		        <a class="nav-link text-warning" href="#" id="publishNavbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		          <i class='nav-icon la la-clone'></i> Publish
		        </a>
		        <div class="dropdown-menu" aria-labelledby="publishNavbarDropdown">
			    	<a class="dropdown-item" href="#" data-toggle="modal" data-target="#livewire-publish-modal" data-file-type="button">Button</a>
			    	<a class="dropdown-item" href="#" data-toggle="modal" data-target="#livewire-publish-modal" data-file-type="column">Column</a>
			    	<a class="dropdown-item" href="#" data-toggle="modal" data-target="#livewire-publish-modal" data-file-type="field">Field</a>
			    	<a class="dropdown-item" href="#" data-toggle="modal" data-target="#livewire-publish-modal" data-file-type="filter">Filter</a>
			    	<a class="dropdown-item" href="#" data-toggle="modal" data-target="#livewire-publish-modal" data-file-type="widget">Widget</a>
		        </div>
		      </li>
		      <li class="nav-item dropdown">
		        <a class="nav-link" href="#" id="packageNavbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
		          <i class='nav-icon la la-archive'></i> Package
		        </a>
		        <div class="dropdown-menu" aria-labelledby="packageNavbarDropdown">
			    	<a class="dropdown-item disabled" href="#">Button</a>
			    	<a class="dropdown-item disabled" href="#">Column</a>
			    	<a class="dropdown-item disabled" href="#">Field</a>
			    	<a class="dropdown-item disabled" href="#">Filter</a>
			    	<a class="dropdown-item disabled" href="#">Widget</a>
			    	<a class="dropdown-item disabled" href="#">Operation</a>
			    	<a class="dropdown-item disabled" href="#">CRUD</a>
		        </div>
		      </li>
		    </ul>
	    </div>
	    {{-- <form class="form-inline my-2 mr-lg-0 ml-lg-4">
	      <input class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
	      <button class="btn btn-outline-default my-2 my-sm-0" type="submit">Search</button>
	    </form> --}}
	  </div>
	</nav>

</div>

@include('backpack.devtools::livewire.partials.assets')
